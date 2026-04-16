@extends('backend.teacher_backend.teacher_dashboard')
@section('teacher')

<h4 class="mb-3 text-center">
    CSV Preview – {{ $cbtTest->subject->subject_name }} 
     - {{$cbtTest->class->class_name}}

</h4>

@if($errors)
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach($errors as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif


         <div class="card-body table-responsive">
          <table class="table table-bordered table-striped">
              <thead class="table-dark">
                  <tr>
                      <th>#</th>
                      <th>Question</th>
                      <th>A</th>
                      <th>B</th>
                      <th>C</th>
                      <th>D</th>
                      <th>Correct</th>
                      <th>Mark</th>
                  </tr>
              </thead>
              <tbody>
                 @foreach($validatedRows as $row)
          <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ $row['question_text'] }}</td>
              <td>{{ $row['option_a'] }}</td>
                        <td>{{ $row['option_b'] }}</td>
              <td>{{ $row['option_c'] }}</td>
              <td>{{ $row['option_d'] }}</td>
                        <td class="fw-bold text-danger">{{ strtoupper($row['correct_option']) }}</td>
              <td>{{ $row['mark'] }}</td>
          </tr>
          @endforeach
          
              </tbody>
          </table>

        </div>

@if(count($errors) === 0)
<form method="POST" action="{{ route('cbt.questions.csv.confirm', $cbtTest->id) }}">
    @csrf
    <button class="btn btn-success w-100 mt-3">
        ✅ Confirm & Save Questions
    </button>
</form>
@else
<div class="alert alert-warning mt-3">
    Fix CSV errors before saving.
</div>
@endif

@endsection
