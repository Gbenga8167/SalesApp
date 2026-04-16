@extends('backend.student_backend.student_dashboard')
@section('student')

<div class="container-fluid bg-white" style="width:100%">
    <!-- Page title -->
    <div class="row">
        <div class="col-12">
            <h4 class="p-2 bg-primary text-white rounded text-center">Student | Profile</h4>
        </div>
    </div>

    <div class="card-body">
        {{-- Alert messages --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- CBT Tests Table -->
        <div class="container-fluid mt-3">
            @if($cbtTests->isEmpty())
                <p class="bg-light p-4 text-danger text-center" style="font-size:18px;">
                    No CBT tests available at the moment
                </p>
            @else
                @php
                    // Get the logged-in student record
                    $student = \App\Models\Student::where('user_id', \Illuminate\Support\Facades\Auth::id())->first();
                    $studentId = $student?->id;
                @endphp

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-primary">
                            <tr>
                                <th>Class</th>
                                <th>Subject</th>
                                <th>Assessment</th>
                                <th>Total Scores</th>
                                <th>Start Time</th>
                                <th>Duration</th>
                                <th>End Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cbtTests as $test)
                                @php
                                    // Check if this student has attempted this test
                                    $attempt = \Illuminate\Support\Facades\DB::table('cbt_attempts')
                                        ->where('student_id', $studentId)
                                        ->where('cbt_test_id', $test->id)
                                        ->latest()
                                        ->first();
                                @endphp

                                <tr>
                                    <td>{{ strtoupper($test->class->class_name) }}</td>
                                    <td>{{ ucwords(strtolower($test->subject->subject_name)) }}</td>
                                    <td>{{ $test->assessment_type }}</td>  
                                    <td align="center">
                                    <b>
                                        @if($attempt && $attempt->score !== null)
                                            {{ $attempt->score }}
                                        @else
                                            -
                                        @endif
                                    </b>
                                </td>

                                    <td>{{ $test->start_time }}</td>                                  
                                    <td>{{ $test->duration_minutes }} mins</td>
                                    <td>{{ $test->end_time ?? 'No End Time' }}</td>
                                    <td>
                                        @if(!$attempt)
                                            {{-- No attempt yet --}}
                                            <a href="{{ route('student.cbt.test', $test->id) }}" class="btn btn-primary btn-sm">Start Test</a>
                                        @elseif($attempt->status === 'in_progress')
                                            <a href="{{ route('student.cbt.test', $test->id) }}" class="btn btn-warning btn-sm">In Progress</a>
                                        @elseif($attempt->status === 'completed')
                                            <button class="btn btn-success btn-sm" disabled>Completed</button>
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled>{{ ucfirst($attempt->status) }}</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
