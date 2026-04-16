@extends('backend.teacher_backend.teacher_dashboard')
@section('teacher')


<div class="container-fluid">

<!-- start page title -->
<div class="row" >
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">CBT TEST</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Create</a></li>
                    <li class="breadcrumb-item active"> CBT Tests</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<!--START CSV FILE UPLOAD -->
<h4 class="card-title" style="background-color:dodgerblue; padding:15px 5px; color:#fff; text-align:center">
    Add Multiple Questions for : {{$cbtTest->subject->subject_name}}
     - {{$cbtTest->class->class_name}} - {{$cbtTest->term}} - {{$cbtTest->session}}</h4>
 

     <div class="card mb-3 p-3 shadow-sm">
        @if(session('message'))
    <div class="alert {{ session('alert-type') == 'success' ? 'alert-success' : 'alert-danger' }}">
        {{ session('message') }}
    </div>
      @endif


      @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
     @endif


     <h5 class="mb-3 text-primary fw-bold">Upload Questions via CSV / Excel</h5>
     <small class="text-muted mb-3" style="font-size:14px;">
       ⚠️Please save your file as <b>CSV UTF-8 (Comma delimited)</b> before uploading.<br>
       Format: question, option_a, option_b, option_c, option_d, correct_answer(a-d)
    </small>

    <form action="{{ route('cbt.questions.upload.csv', $cbtTest->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <input type="file" name="csv_file" class="form-control" accept=".csv" required>
        </div>
               <button type="submit" class="btn btn-success ">
            Upload & Preview
        </button>
    </form>
   
</div>
 <!--END CSV FILE UPLOAD -->


                <form action="{{route('cbt.questions.store', $cbtTest->id)}}" method="post">
                @csrf

                <div id="questions-wrapper" style="background-color:white;">

                @if(session('success'))
               <div class="alert alert-success">
               {{session('success')}}
               </div>
                @endif
                    <div class="question-block border p-3 mb-2 rounded shadow-sm">
                        <h5>Question <span class="question-number">1</span></h5>
                        <div class="form-group">
                            <label for="">Question Text</label>
                            <textarea name="questions[0][question_text]" class="form-control" required placeholder="Enter Question"></textarea>
                        </div>
                        <!-- end row -->

                        <div class="form-group">
                            <label for="">Option A</label>
                            <input type="text" name="questions[0][option_a]" class="form-control" required>
                        </div>
                         <!-- end row -->

                        <div class="form-group">
                            <label for="">Option B</label>
                            <input type="text" name="questions[0][option_b]" class="form-control" required>
                        </div>
                         <!-- end row -->

                         <div class="form-group">
                            <label for="">Option C</label>
                            <input type="text" name="questions[0][option_c]" class="form-control" required>
                        </div>
                         <!-- end row -->

                         <div class="form-group">
                            <label for="">Option D</label>
                            <input type="text" name="questions[0][option_d]" class="form-control" required>
                        </div>
                         <!-- end row -->



                         <div class="form-group">
                        <label for="">Correct Option</label>
                        <select   name="questions[0][correct_option]" required class="form-select" aria-label="Default select example">
                         <option value="">Select correct option</option>   
                         <option value="A">A</option>  
                         <option value="A">B</option>
                         <option value="C">C</option>  
                         <option value="D">D</option>           
                        </select>
                        </div>
                         <!-- end row -->



                         <div class="form-group">
                            <!--<label for="">Mark</label> -->
                            <input type="hidden" name="questions[0][mark]" class="form-control" required placeholder="Enter score" value="1">
                        </div>
                     

                         <!-- end row -->

                    </div>
                   

                </div>

                  <!-- button div -->
                  <div class="d-flex justify-content-start mb-3">
                      <button type="button" id="add-question" class="btn btn-secondary me-2">Add Another Question</button>
                      <button type="button" id="cancel-question" class="btn btn-danger me-2">Cancel Last Question</button>
                      <button type="submit" class="btn btn-primary">Submit All</button>
                  </div>
                <!-- end button div -->
        
        </form>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
    let questionIndex = 1; // Start with index 1 for new questions
    document.getElementById('add-question').addEventListener('click', function () {
        const wrapper = document.getElementById('questions-wrapper');
        const block = document.createElement('div');
        block.classList.add('question-block', 'border', 'p-3', 'mb-4', 'rounded', 'shadow-sm');
        block.innerHTML = ` <hr style="color:dodgerblue; height:2px">
            <h5>Question <span class="question-number">${questionIndex + 1}</span></h5>
            <div class="form-group">
                <label for="">Question Text</label>
                <textarea name="questions[${questionIndex}][question_text]" class="form-control" required placeholder="Enter Question"></textarea>
            </div>
            <div class="form-group">
                <label for="">Option A</label>
                <input type="text" name="questions[${questionIndex}][option_a]" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="">Option B</label>
                <input type="text" name="questions[${questionIndex}][option_b]" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="">Option C</label>
                <input type="text" name="questions[${questionIndex}][option_c]" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="">Option D</label>
                <input type="text" name="questions[${questionIndex}][option_d]" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="">Correct Option</label>
                <select name="questions[${questionIndex}][correct_option]" class="form-select" required>
                    <option value="">Select correct option</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>
            <div class="form-group">
               <!-- <label for="">Mark</label> -->
                <input type="hidden" value="1" name="questions[${questionIndex}][mark]" class="form-control" required placeholder="Enter score">
            </div>
        `;
        wrapper.appendChild(block);
        questionIndex++; // Increment the question index for the next question
    });

     // Cancel question functionality
     document.getElementById('cancel-question').addEventListener('click', function () {
        const wrapper = document.getElementById('questions-wrapper');
        const lastQuestion = wrapper.lastElementChild; // Get the last question block
        if (lastQuestion && wrapper.children.length > 1) {
            wrapper.removeChild(lastQuestion); // Remove the last question block
            questionIndex--; // Decrement the question index
        } else {
            alert('No more questions to remove!');
        }
    });
});
</script>
@endsection



