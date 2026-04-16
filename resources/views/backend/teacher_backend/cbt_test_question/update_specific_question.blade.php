@extends('backend.teacher_backend.teacher_dashboard')
@section('teacher')


<div class="container-fluid" style="background-color:white; width:100%">
    <h4 class="card-title" style="background-color:dodgerblue; padding:15px 5px; color:#fff; text-align:center">
        Update Question
    </h4>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('cbt.questions.update', $question->id) }}" method="post">
        @csrf

        <div class="form-group">
            <label for="question_text">Question Text</label>
            <textarea name="question_text" class="form-control" required>{{ $question->question_text }}</textarea>
        </div>

        <div class="form-group">
            <label for="option_a">Option A</label>
            <input type="text" name="option_a" class="form-control" required value="{{ $question->option_a }}">
        </div>

        <div class="form-group">
            <label for="option_b">Option B</label>
            <input type="text" name="option_b" class="form-control" required value="{{ $question->option_b }}">
        </div>

        <div class="form-group">
            <label for="option_c">Option C</label>
            <input type="text" name="option_c" class="form-control" required value="{{ $question->option_c }}">
        </div>

        <div class="form-group">
            <label for="option_d">Option D</label>
            <input type="text" name="option_d" class="form-control" required value="{{ $question->option_d }}">
        </div>

        <div class="form-group">
            <label for="correct_option">Correct Option</label>
            <select name="correct_option" class="form-select" required>
                <option value="A" {{ $question->correct_option == 'a' ? 'selected' : '' }}>A</option>
                <option value="B" {{ $question->correct_option == 'b' ? 'selected' : '' }}>B</option>
                <option value="C" {{ $question->correct_option == 'c' ? 'selected' : '' }}>C</option>
                <option value="D" {{ $question->correct_option == 'd' ? 'selected' : '' }}>D</option>
            </select>
        </div>	

        <div class="form-group">
            <!--<label for="mark">Mark</label> -->
            <input type="hidden" name="mark" class="form-control" required value="{{ $question->mark }}" >
        </div>

        <button type="submit" class="btn btn-primary mt-3">Update Question</button>
    </form>
</div>


@endsection