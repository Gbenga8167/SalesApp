@extends('backend.student_backend.student_dashboard')
@section('student')

@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\AssignClassSubjectStudent;
use App\Models\CbtTest;

// 1. Logged-in student
$student = \App\Models\Student::where('user_id', Auth::id())->first();

// 2. Current term & session
$currentTerm = DB::table('terms')->where('is_current', 1)->value('name');
$currentSession = DB::table('academic_sessions')->where('is_current', 1)->value('name');

// 3. Total Subjects (for this student in current term/session)
$totalSubjects = AssignClassSubjectStudent::where('student_id', $student->id)
    //->where('term', $currentTerm)
    //->where('session', $currentSession)
    ->distinct('subject_id')
    ->count('subject_id');

// 4. Total CBT Tests (available for this student’s class in current term/session)
// Get the student's class for the current term & session
$studentClassId = AssignClassSubjectStudent::where('student_id', $student->id)
    //->where('term', $currentTerm)
    //->where('session', $currentSession)
    ->value('student_classes_id');

// If found, count CBT for that class
 $totalCBTTests = CbtTest::where('student_classes_id', $studentClassId)
            ->where('term', $currentTerm)
            ->where('session', $currentSession)
            ->count();



// 6. Recent CBT Tests
$recentCBT = CbtTest::where('student_classes_id', $studentClassId)
    ->where('term', $currentTerm)
    ->where('session', $currentSession)
    ->latest()
    ->take(5)
    ->get();

// Greeting logic
$hour = now()->format('H');
if ($hour < 12) {
    $greeting = "Good Morning";
} elseif ($hour < 17) {
    $greeting = "Good Afternoon";
} else {
    $greeting = "Good Evening";
}

// Get class name
$studentClassName = \App\Models\StudentClasses::where('id', $studentClassId)->value('class_name');


use App\Models\CbtAttempt;

// ================= PERFORMANCE DATA =================

// Base Query (Filter by student + completed + current term/session via CBT test)
$attemptsQuery = CbtAttempt::where('student_id', $student->id)
    ->where('status', 'completed')
    ->whereHas('test', function ($query) use ($currentTerm, $currentSession) {
        $query->where('term', $currentTerm)
              ->where('session', $currentSession);
    });

// Total Attempts
$totalAttempts = $attemptsQuery->count();

// Pass Count
$passCount = (clone $attemptsQuery)
    ->where('score', '>=', 50)
    ->count();

// Fail Count
$failCount = (clone $attemptsQuery)
    ->where('score', '<', 50)
    ->count();

// Average Score
$averageScore = round($attemptsQuery->avg('score') ?? 0, 1);

// Highest Score
$highestScore = $attemptsQuery->max('score') ?? 0;


// ===== Score Trend (Last 6 Tests) =====
$scoreTrend = (clone $attemptsQuery)
    ->latest()
    ->take(6)
    ->get()
    ->reverse();

$trendLabels = [];
$trendScores = [];

foreach ($scoreTrend as $attempt) {
    $trendLabels[] = $attempt->created_at->format('d M');
    $trendScores[] = $attempt->score;
}


@endphp

{{-- ================= CSS EFFECTS ================= --}}
<style>
    /*taller cards*/

.dashboard-card {
    min-height: 160px; /* Increased height */
}

.dashboard-card {
    min-width: 160px; /* Increased height */
}

.dashboard-card .card-body {
    padding: 1.8rem; /* More spacing inside */
}

.dashboard-card h3 {
    font-size: 1.9rem; /* Bigger number */
}

    .zoom-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .zoom-card:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }

    /*Animation Styles*/
        .animate-card {
        opacity: 0;
        transform: translateY(30px);
        animation: popUp 0.8s ease forwards;
        animation-delay: var(--delay, 0s);
    }
    @keyframes popUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }


    /*Header Class Animation Styles*/
    .animate-badges span {
        opacity: 0;
        transform: translateY(10px);
        animation: fadeUp 0.6s ease forwards;
    }
    .animate-badges span:nth-child(1) { animation-delay: 0.2s; }
    .animate-badges span:nth-child(2) { animation-delay: 0.4s; }
    .animate-badges span:nth-child(3) { animation-delay: 0.6s; }

    @keyframes fadeUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>


<!-- Student Dashboard -->
<div class="container-fluid">
    <!-- 1. Welcome Header -->
    <div class="row mb-4 animate-card" style="--delay:0s;">
        <div class="col-12">
            <div class="d-flex align-items-center p-4 text-white rounded-4 shadow"
                 style="background: linear-gradient(135deg, #007bff, #00c6ff); border-radius:5px;">
                <div>
                    <h5 class="mb-1 fw-bold" style="color: #fff;">
                        Hi, {{ $student->name }}
                    </h5>
                    <h6 class="mb-0" style="color:#f8f9fa;">
                        Class: {{ strtoupper($studentClassName ?? 'Not Assigned') }} | 
                        Term: {{ $currentTerm }} | 
                        Session: {{ $currentSession }}
                    </h6>
                </div>
            </div>
        </div>
    </div>


    <!-- 2. Quick Stats Cards -->
    <div class="row">

        <!-- Total Subjects -->
        <div class="col-xl-4 col-md-6 animate-card" style="--delay:0.2s;">
            <div class="dashboard-card card shadow-lg border-0 rounded-4 text-white zoom-card" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 fw-semibold">My Subjects</p>
                        <h3 class="fw-bold mb-0" style="color: #fff;">{{ $totalSubjects }}</h3>
                    </div>
                    <div class="avatar-sm bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center">
                        <i class="ri-book-open-line fs-3"></i>
                    </div>
                </div>
            </div>
        </div>


                <!-- Results Availability
        <div class="col-xl-4 col-md-6 animate-card" style="--delay:0.6s;">
            <div class="card shadow-lg border-0 rounded-4 text-white zoom-card" style="background: linear-gradient(135deg, #11998e, #38ef7d);">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 fw-semibold">My Results</p>
                        <h5 class="fw-bold mb-0" style="color: #fff;">
                            yes
                        </h5>
                    </div>
                    <div class="avatar-sm bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center">
                        <i class="ri-bar-chart-2-line fs-3"></i>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- Total CBT Tests -->
        <div class="col-xl-4 col-md-6 animate-card" style="--delay:0.4s;">
            <div class="dashboard-card card shadow-lg border-0 rounded-4 text-white zoom-card" style="background: linear-gradient(135deg, #ff416c, #ff4b2b);">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 fw-semibold"> Test Available</p>
                        <h3 class="fw-bold mb-0" style="color: #fff;">{{ $totalCBTTests}}</h3>
                    </div>
                    <div class="avatar-sm bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center">
                        <i class="ri-file-list-3-line fs-3"></i>
                    </div>
                </div>
            </div>
        </div>




        <!-- Average Score -->
         <div class="col-xl-4 col-md-6 animate-card" style="--delay:0.6s;">
             <div class="dashboard-card card shadow-lg border-0 rounded-4 text-white zoom-card"
                  style="background: linear-gradient(135deg, #11998e, #38ef7d);">
                 <div class="card-body d-flex justify-content-between align-items-center">
                     <div>
                         <p class="mb-1 fw-semibold">Tests Taken</p>
                         <h3 class="fw-bold mb-0" style="color:#fff;">{{ $totalAttempts }}</h3>
                     </div>
                     <i class="ri-book-open-line fs-3"></i>
                 </div>
             </div>
         </div>

</div>

        

    <!-- 3. Recent CBT & Results -->
    <div class="row">
        <div class="col-lg-6 animate-card" style="--delay:0.8s;">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-gradient text-white rounded-top-4" style="background: linear-gradient(135deg, #36d1dc, #5b86e5);">
                    <h6 class="mb-0 fw-bold">Recent CBT Tests</h6>
                </div>
                <div class="card-body">
                    @if($recentCBT->isEmpty())
                        <p class="text-muted">No CBT tests available.</p>
                    @else
                        
                                    <p class="text-primary fw-bold">Attempt cbt</p>
                                    <a href="{{ route('student.index') }}" class="btn btn-sm btn-primary">Take Test</a>
                             
                    @endif
                </div>
            </div>
        </div>
    </div>
    
</div>


@endsection
