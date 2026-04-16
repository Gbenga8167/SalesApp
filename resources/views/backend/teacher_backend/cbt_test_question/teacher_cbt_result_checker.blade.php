@extends('backend.teacher_backend.teacher_dashboard')
@section('teacher')

    <div class="container-fluid">

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Result | Upload</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Result</a></li>
                    <li class="breadcrumb-item active"> Upload</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Select Class, Term and Academic Session  </h4>
              
                <form action="{{route('load.result')}}" method="get">
                  

                    @if(session('error'))
                    <div class="alert alert-danger">
                        {{session('error')}}
                    </div>
                    @endif
                    
                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label" style="font-size:15px">Class</label>
                    <div class="col-sm-10">
                    <select  name="class_id" id="class_id" required  aria-label="Default select example" class="form-select">
                         <option selected value="">-- Select Class --</option>

                         @foreach($classes as $class)
                        <option value="{{$class->id}}">{{$class->class_name}}</option>
                         @endforeach
                                                    
                        </select>
                    </div>
                   
                </div> 
                   <!-- end row -->


                   <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label" style="font-size:15px">Subject</label>
                    <div class="col-sm-10">
                    <select  name="subject_id" id="subject_id" required class="form-select" aria-label="Default select example">
                        
                    <option value="">-- Select Subject --</option>
                            
                        </select>
                    </div>
                   
                </div>
                     <!-- end row -->


                   <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label" style="font-size:15px">Term</label>
                    <div class="col-sm-10">
                    <select  name="term" required class="form-select" aria-label="Default select example">
                         <option selected value="">--Select Term--</option>

                         @foreach($terms as $term)
                        <option value="{{$term->name}}">{{$term->name}}</option>
                         @endforeach
                                                    
                        </select>
                    </div>
                   
                </div>
                     <!-- end row -->
                


                     <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label" style="font-size:15px">Session</label>
                    <div class="col-sm-10">
                    <select  name="session" required class="form-select" aria-label="Default select example">
                         <option selected value="">--Select Session--</option>

                         @foreach($sessions as $session)
                        <option value="{{$session->name}}">{{$session->name}}</option>
                         @endforeach
                                                    
                        </select>
                    </div>
                   
                </div>
                

                <!-- end row -->

                <button type="submit" class="btn btn-primary waves-effect waves-light"> Fetch Result  </button>
                
                </form>

               
              
            </div>
            
        </div>
    </div> 
</div>
  
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
//AJAX SCRIPT TO FETCH SUBJECTS DYNAMICALLY
//  ONCE TEACHER SELECT CLASS FOR RESULT UPLOAD

$(document).ready(function(){
    $('#class_id').on('change', function(){
        let classId = $(this).val();
    if(classId)
    $.ajax({

        url:'{{route("teacher.getSubjectByClass")}}',
        type : 'GET',
        data : {class_id : classId},
        success : function(data){
            subjectDropdown = $('#subject_id');

            subjectDropdown.empty()//clear previous subject
            subjectDropdown.append('<option value="">-- Select Subject --</option>');
            $.each(data, function(key, subject){
            subjectDropdown.append('<option value="'+ subject.id + '">'+subject.subject_name+'</option>');

            });
        },

        error: function(){
            alert('failed to fetch subject');
        }


    });
  

    });
});


</script>
  

@endsection



