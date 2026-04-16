@extends('backend.teacher_backend.teacher_dashboard')
@section('teacher')

@php

// START CODE TO Get all class IDs assigned to this teacher AND Count all students in those classes

use App\Models\AssignedClassSubjectTeacher;
use App\Models\AssignClassSubjectStudent;

$teacher = \App\Models\Teacher::where('user_id', Auth::id())->first();
 
// 1) Current Term & Session
$currentTerm = DB::table('terms')->where('is_current', 1)->value('name');
$currentSession = DB::table('academic_sessions')->where('is_current', 1)->value('name');

$teacherId = \App\Models\Teacher::where('user_id', Auth::id())->first();

// Step 1: Get all class IDs assigned to this teacher
$teacherClassIds = AssignedClassSubjectTeacher::where('teacher_id', $teacherId->id)
        //->where('term', $currentTerm)
        //->where('session', $currentSession)
        ->pluck('student_classes_id')
        ->toArray();

// Step 2: Count all students in those classes
$studentCount = AssignClassSubjectStudent::whereIn('student_classes_id', $teacherClassIds)
        //->where('term', $currentTerm)
        //->where('session', $currentSession)
    ->distinct('student_id') // avoid duplicates if a student is in multiple subjects
    ->count('student_id');

    // END CODE TO Get all class IDs assigned to this teacher AND Count all students in those classes
@endphp


@php

// START student performance and grade

use Illuminate\Support\Facades\DB;

// 1) Current Term & Session
$currentTerm = DB::table('terms')->where('is_current', 1)->value('name');
$currentSession = DB::table('academic_sessions')->where('is_current', 1)->value('name');

// 2) Get Teacher row (by user_id) and its teacher_id
$teacherRow = DB::table('teachers')->where('user_id', Auth::id())->first();
$teacherId = $teacherRow->id ?? null;

// 3) Class & Subject IDs assigned to this teacher (pivot: assigned_class_subject_teachers)
$classIds = [];
$subjectIds = [];

if ($teacherId) {
    $pivot = DB::table('assigned_class_subject_teachers')
        ->where('teacher_id', $teacherId)
        ->pluck('student_classes_id', 'id');
    $classIds = array_values($pivot->toArray());

    $subjectIds = DB::table('assigned_class_subject_teachers')
        ->where('teacher_id', $teacherId)
        ->pluck('subject_id')
        ->toArray();
}




// End student performance and grade
@endphp






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


    .dashboard-card {
        transition: all 0.3s ease-in-out;
    }
    .dashboard-card:hover {
        transform: translateY(-8px) scale(1.03);
        box-shadow: 0 12px 25px rgba(0,0,0,0.2) !important;
    }
    .dashboard-card .avatar-sm i {
        transition: transform 0.3s ease-in-out, color 0.3s ease-in-out;
    }
    .dashboard-card:hover .avatar-sm i {
        transform: scale(1.2);
        color: #fff;
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
</style>

<div class="container-fluid">
    <!-- 1. Welcome Header -->
    <div class="row mb-4 animate-card" style="--delay:0s;">
        <div class="col-12">
            <div class="d-flex align-items-center p-4 text-white rounded-4 shadow"
                 style="background: linear-gradient(135deg, #007bff, #00c6ff); border-radius:5px;">
                <div>
                    <h4 class="mb-1 fw-bold" style="color: #fff;">
                       Hi,  {{ $teacher->name }}
                    </h4>
                <small class="text-light" style="font-size:18px;">Welcome back to your dashboard 👋</small><br>
                <small class="text-light align-right" style="font-size:15px;">Term : {{$currentTerm}} <br> Accademic Session : {{$currentSession}}</small><br>

                
                </div>
            </div> 
        </div>
    </div>



    <!-- Quick Stats Cards -->
<div class="row">
    <!-- Total Classes -->
    <div class="col-xl-4 col-md-6 animate-card" style="--delay:0.2s;">
        <div class="card dashboard-card shadow-lg border-0 rounded-4 text-white" style="background: linear-gradient(135deg, #6a11cb, #2575fc);">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 fw-semibold">Classes Assigned</p>
                        <h3 class="fw-bold mb-0" style="color: #fff;">
                            {{ \App\Models\AssignedClassSubjectTeacher::where('teacher_id', $teacher->id)
                                //->where('term', $currentTerm)
                                //->where('session', $currentSession)
                                ->distinct('student_classes_id')
                                ->count('student_classes_id') }}
                        </h3>
                    </div>
                    <div class="avatar-sm bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center">
                        <i class="ri-building-line fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Subjects -->
    <div class="col-xl-4 col-md-6 animate-card" style="--delay:0.4s;">
        <div class="card dashboard-card shadow-lg border-0 rounded-4 text-white" style="background: linear-gradient(135deg, #00b09b, #96c93d);">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 fw-semibold">Subjects Assigned</p>
                        <h3 class="fw-bold mb-0" style="color: #fff;">
                            {{ \App\Models\AssignedClassSubjectTeacher::where('teacher_id', $teacher->id)
                                //->where('term', $currentTerm)
                                //->where('session', $currentSession)
                                ->distinct('subject_id')
                                ->count('subject_id') }}
                        </h3>
                    </div>
                    <div class="avatar-sm bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center">
                        <i class="fas fa-book-open fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Students -->
    <div class="col-xl-4 col-md-6 animate-card" style="--delay:0.6s;">
        <div class="card dashboard-card shadow-lg border-0 rounded-4 text-white" style="background: linear-gradient(135deg, #ff416c, #ff4b2b);">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 fw-semibold" style="color: #fff;">My Students Across all classes</p>
                        <h3 class="fw-bold mb-0" style="color: #fff;">{{ $studentCount }}</h3>
                    </div>
                    <div class="avatar-sm bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center">
                        <i class="ri-user-3-line fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





@php
use App\Models\CbtAttempt;
use App\Models\CbtTest;
use App\Models\Subject;

// 1) Current Term & Session
$currentTerm = DB::table('terms')->where('is_current', 1)->value('name');
$currentSession = DB::table('academic_sessions')->where('is_current', 1)->value('name');

// Get all CBT test IDs for this teacher in current term & session
$teacherTestIds = CbtTest::where('teacher_id', $teacher->id)
    ->where('term', $currentTerm)
    ->where('session', $currentSession)
    ->pluck('id')
    ->toArray();


    


    // ================= Performance Per Subject-Class (Grouped) =================

$performanceData = CbtAttempt::whereIn('cbt_test_id', $teacherTestIds)
    ->where('status','completed')
    ->join('cbt_tests', 'cbt_attempts.cbt_test_id', '=', 'cbt_tests.id')
    ->select(
        'cbt_tests.subject_id',
        'cbt_tests.student_classes_id',
        DB::raw('SUM(CASE WHEN cbt_attempts.score >= 40 THEN 1 ELSE 0 END) as pass_count'),
        DB::raw('SUM(CASE WHEN cbt_attempts.score < 40 THEN 1 ELSE 0 END) as fail_count')
    )
    ->groupBy('cbt_tests.subject_id', 'cbt_tests.student_classes_id')
    ->get();

$performanceLabels = [];
$passCounts = [];
$failCounts = [];

foreach ($performanceData as $row) {

    $subject = \App\Models\Subject::find($row->subject_id);
    $className = DB::table('student_classes')
                    ->where('id', $row->student_classes_id)
                    ->value('class_name');

    $label = ($subject ? $subject->subject_name : 'Unknown Subject')
           . ' - '
           . ($className ?? 'Unknown Class');

    $performanceLabels[] = $label;
    $passCounts[] = $row->pass_count;
    $failCounts[] = $row->fail_count;
}




//Teacher Attempted Tests
$mostAttempted = CbtAttempt::whereIn('cbt_test_id', $teacherTestIds)
    ->where('status','completed')
    ->select('cbt_test_id', DB::raw('count(*) as total'))
    ->groupBy('cbt_test_id')
    ->orderByDesc('total')
    //->take(5)
    ->get();

$topTestNames = [];
$topTestCounts = [];

foreach($mostAttempted as $item){
    $test = CbtTest::find($item->cbt_test_id);
    if($test){
        $subject = Subject::find($test->subject_id);
        $class = DB::table('student_classes')->where('id', $test->student_classes_id)->value('class_name');
        $topTestNames[] = $subject && $class ? $subject->subject_name . ' - ' . $class : $test->title;
        $topTestCounts[] = $item->total;
    }
}
@endphp

<div class="row mt-4">
    <!-- Pass vs Fail -->
    <div class="col-xl-6 col-md-12 animate-card" style="--delay:0.8s;">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0" style="color:#fff;">My Student Performances Across All Classes</h5>
            </div>
            <div class="card-body">
                <canvas id="passFailChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Top 5 Most Attempted Tests -->
    <div class="col-xl-6 col-md-12 animate-card" style="--delay:1s;">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0" style="color:#fff;">Attempted Tests (My Classes)</h5>
            </div>
            <div class="card-body">
                <canvas id="topTestsChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>


    // Group  Chart per teacherClass and Subject
new Chart(document.getElementById('passFailChart'), {
    type: 'bar',
    data: {
        labels: @json($performanceLabels),
        datasets: [
            {
                label: 'Scores ≥ 40',
                data: @json($passCounts),
                backgroundColor: '#28a745'
            },
            {
                label: 'Scores < 40',
                data: @json($failCounts),
                backgroundColor: '#dc3545'
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});



    // My Attempted Tests Chart
    const topTestNames = @json($topTestNames);
    const topTestCounts = @json($topTestCounts);

    new Chart(document.getElementById('topTestsChart'), {
        type: 'bar',
        data: {
            labels: topTestNames,
            datasets: [{
                label: 'Attempts',
                data: topTestCounts,
                backgroundColor: 'rgba(54, 209, 220, 0.7)',
                borderColor: 'rgba(54, 209, 220, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Attempts: ' + context.raw;
                        }
                    }
                }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection