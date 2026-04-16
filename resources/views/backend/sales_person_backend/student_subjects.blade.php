@extends('backend.student_backend.student_dashboard')
@section('student')

<div class="container mt-4">
    {{-- Colorful Page Heading --}}
    <h5 class=" fw-bold text-center p-3 rounded-3 shadow-sm"
        style="background: linear-gradient(90deg, #e24a4aff, #df5e85ff); color: #fff;">
        📘 My Subjects 
    </h5>

    @if($subjects->isEmpty())
        <div class="alert alert-warning shadow-sm rounded">
            No subjects assigned for this term and session.
        </div>
    @else
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body">
                {{-- Colorful Total Subjects --}}
                <h5 class="fw-bold">
                    Total Subjects: 
                    <span class="badge rounded-pill px-3 py-2"
                          style="background: linear-gradient(135deg, #36d1dc, #5b86e5); font-size: 1rem;">
                        {{ $subjects->count() }}
                    </span>
                </h5>

                <ul class="list-group mt-3">
                    @foreach($subjects as $subject)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $subject->subject_name }}
                            <span class="badge bg-success">Assigned</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
</div>
@endsection
