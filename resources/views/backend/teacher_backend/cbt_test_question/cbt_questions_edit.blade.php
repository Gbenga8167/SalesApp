@extends('backend.teacher_backend.teacher_dashboard')
@section('teacher')

<div class="container-fluid" style="background-color:white; width:100%">

<!-- start page title -->
<div class="row" >
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">EDIT CBT TEST</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">EDIT</a></li>
                    <li class="breadcrumb-item active"> CBT Tests</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->
<div class="container-fluid">


 @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <h4 class="card-title" style="background-color:dodgerblue; padding:15px 5px; color:#fff; text-align:center">
        Edit Questions for: {{$cbtTest->subject->subject_name}} <!--({{ $cbtTest->title }})!-->
        - {{$cbtTest->class->class_name}} - {{$cbtTest->term}} - {{$cbtTest->session}} 
    </h4>


    

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Question Text</th>
                    <th>Option A</th>
                    <th>Option B</th>
                    <th>Option C</th>
                    <th>Option D</th>
                    <th>Correct Option</th>
                    <th>Mark</th>
                    <th>Action</th>
                </tr>
            </thead>`
            <tbody>
                @foreach($cbtTest->questions as $index => $question)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{strtoupper($question->question_text)}}</td>
                    <td>{{strtoupper( $question->option_a )}}</td>
                    <td>{{ strtoupper($question->option_b) }}</td>
                    <td>{{strtoupper( $question->option_c )}}</td>
                    <td>{{strtoupper( $question->option_d) }}</td>
                    <td>{{ strtoupper($question->correct_option) }}</td>
                    <td>{{ $question->mark }}</td>
                    <td>
                        <div class="d-flex">
                            <a href="{{ route('update.specific.questions', $question->id) }}" class="btn btn-warning me-2">Edit</a>
                            <form action="{{ route('cbt.questions.delete', $question->id) }}" method="post" class="delete-form" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    // JS (SweetAlert only submits form)
    $(document).on('submit', '.delete-form', function(e){
    e.preventDefault();
    let form = this;

    Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});

</script>


<script>
    /* MASS POP-UP DELETE ALL 
$(document).on('submit', '.delete-all-form', function(e){
    e.preventDefault();
    let form = this;

    Swal.fire({
        title: 'Delete ALL records?',
        text: "This will delete ALL assigned students, classes and subjects!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete all!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});

*/
</script>



<script>

// DELETE ALL TEXT FOR SWEET ALERT TYPING DELETE BEFORE DATA DELETION
$(document).on('submit', '.delete-all-form', function(e){
    e.preventDefault();
    let form = this;
    Swal.fire({
    title: 'Type DELETE to confirm',
    input: 'text',
    showCancelButton: true,
    preConfirm: (value) => {
        if (value !== 'DELETE') {
            Swal.showValidationMessage('You must type DELETE');
        }
        return value;
    }
}).then((result) => {
    if (result.isConfirmed) {
        form.submit();
    }
});
});

</script>

@endsection