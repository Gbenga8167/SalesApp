@extends('backend.teacher_backend.teacher_dashboard')
@section('teacher')

<div class="container my-4">

    <h4 class="text-center text-purple-700 mb-4">
        📊 CBT Results
    </h4>


        {{-- ================= SUCCESS / ERROR MESSAGE ================= --}}
    @if(session('message'))
        <div class="alert alert-{{ session('alert-type') == 'error' ? 'danger' : 'success' }}">
            {{ session('message') }}
        </div>
    @endif

    
    {{-- ================= FILTER FORM ================= --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold bg-light">
            🔍 Filter Results
        </div>

        <div class="card-body">
            <form action="{{ route('teacher.cbt.results.fetch') }}" method="POST" id="results-filter-form">
                @csrf

                <div class="mb-3">
                    <label for="class_id" class="form-label fw-semibold">Class</label>
                    <select name="class_id" id="class_id" class="form-select" required>
                        <option value="">-- Select Class --</option>
                        @foreach($assignedClasses as $class)
                            <option value="{{ $class->id }}"
                                {{ isset($selectedClass) && $selectedClass == $class->id ? 'selected' : '' }}>
                                {{ $class->class_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="subject_id" class="form-label fw-semibold">Subject</label>
                    <select name="subject_id" id="subject_id" class="form-select" required>
                        <option value="">-- Select Subject --</option>

                        @if(isset($selectedSubject) && $selectedSubject)
                            @php
                                $teacher = Auth::user()->teacher;
                                $subjects = \App\Models\Subject::whereHas('assignedTeachers', function($q) use ($teacher, $selectedClass){
                                    $q->where('teacher_id', $teacher->id)
                                      ->where('student_classes_id', $selectedClass);
                                })->get();
                            @endphp

                            @foreach($subjects as $sub)
                                <option value="{{ $sub->id }}"
                                    {{ $sub->id == $selectedSubject ? 'selected' : '' }}>
                                    {{ $sub->subject_name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <button type="submit" class="btn btn-info">
                     Fetch Results
                </button>
            </form>
        </div>
    </div>
    {{-- ================= END FILTER FORM ================= --}}

    {{-- ================= RESULTS TABLE ================= --}}
    @if(isset($results) && $results->isNotEmpty())

        {{-- Selected Info --}}
        <div class="alert alert-info shadow-sm">
            <strong>Selected Class:</strong>
            {{ optional($results->first()->test->class)->class_name }} <br>

            <strong>Selected Subject:</strong>
            {{ optional($results->first()->test->subject)->subject_name }}
        </div>

        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <th>Class</th>
                            <th>Assessment Type</th>
                            <th>Score</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($results as $key => $result)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ ucwords(strtolower($result->student->name)) }}</td>
                            <td>{{ strtoupper($result->test->class->class_name) }}</td>
                            <td>{{ ucfirst($result->test->assessment_type) }}</td>
                            <td class="fw-bold">{{ $result->score }}</td>
                            <td>
                                <form id="retake-form-{{ $result->id }}"
                                      action="{{ route('teacher.cbt.retake', $result->id) }}"
                                      method="POST">
                                    @csrf

                                    <button type="button"
                                            class="btn btn-warning btn-sm"
                                            onclick="confirmRetake({{ $result->id }})">
                                        🔄 Retake
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>

    @elseif(isset($results))
        <div class="alert alert-info mt-3">
            No results found for selected class and subject.
        </div>
    @endif

</div>

{{-- ================= SCRIPTS ================= --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
function confirmRetake(attemptId) {
    Swal.fire({
        title: 'Allow Retake?',
        text: "This will delete the student's attempt and answers.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, allow retake!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('retake-form-' + attemptId).submit();
        }
    });
}

// AJAX: Load subjects when class changes
$('#class_id').change(function () {
    let classId = $(this).val();
    let subjectSelect = $('#subject_id');

    subjectSelect.html('<option value="">Loading subjects...</option>');

    if (classId) {
        $.get('/teacher/cbt-results/subjects/' + classId, function (data) {
            subjectSelect.html('<option value="">-- Select Subject --</option>');
            data.forEach(subject => {
                subjectSelect.append(
                    `<option value="${subject.id}">${subject.subject_name}</option>`
                );
            });
        });
    } else {
        subjectSelect.html('<option value="">-- Select Subject --</option>');
    }
});
</script>

@endsection
