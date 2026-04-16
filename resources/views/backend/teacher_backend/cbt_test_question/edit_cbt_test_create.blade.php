@extends('backend.teacher_backend.teacher_dashboard')
@section('teacher')

<div class="container-fluid">

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">CBT TEST</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Create</a></li>
                    <li class="breadcrumb-item active">Edit CBT Tests </li>
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

            @if(session('success'))
             <div class="alert alert-success">
             {{session('success')}}
             </div>
             @endif

             <h4 class="card-title">Edit CBT Tests</h4>
                <form action="{{ route('update.cbt.test.create', $cbtTest->id) }}" method="post">
                @csrf

                    <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Instructions</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" name="title" value="{{ old('title', $cbtTest->title) }}">

                                           
                         @error('title')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror

                    </div>
                   </div>
                   <!-- end row -->





                   <div class="row mb-3" style="display:none">
                    <label for="example-text-input" class="col-sm-2 col-form-label" style="font-size:15px">Class</label>
                    <div class="col-sm-10">
                    <select  name="class_id" class="form-select" aria-label="Default select example">
                        
                    <option value="">-- Select Class --</option>
                    @foreach($assignedClasses as $class)
                        <option {{$cbtTest->student_classes_id == $class->id? 'selected' : ''}} value="{{$class->id}}">{{$class->class_name}}</option>
                         @endforeach    
                        </select>

                                               
                         @error('class_id')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                   
                </div>
                     <!-- end row -->


                   <div class="row mb-3"  style="display:none">
                    <label for="example-text-input" class="col-sm-2 col-form-label" style="font-size:15px">Subject</label>
                    <div class="col-sm-10">
                    <select  name="subject_id" class="form-select" aria-label="Default select example">
                         <option selected value="">--Select Subject--</option>

                         @foreach($assignedSubject as $subject)
                        <option {{$cbtTest->subject_id == $subject->id? 'selected' : ''}} value="{{$subject->id}}">{{$subject->subject_name}}</option>
                         @endforeach
                                                    
                        </select>

                                               
                         @error('subject_id')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                   
                </div>
                     <!-- end row -->
                


                     <div class="row mb-3" style="display:none">
                    <label for="example-text-input" class="col-sm-2 col-form-label" style="font-size:15px">Term</label>
                    <div class="col-sm-10">
                    <select  name="term" class="form-select" aria-label="Default select example">
                    <option selected value="">--Select Term--</option>

                    <option value="First Term" {{$cbtTest->term == 'First Term'? 'selected' : ''}}>First Term</option>
                    <option value="Second Term" {{$cbtTest->term == 'Second Term'? 'selected' : ''}}>Second Term</option>
                    <option value="Third Term" {{$cbtTest->term == 'Third Term'? 'selected' : ''}}>Third Term</option>
                        </select>

                                               
                         @error('term')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                   
                </div>


                <div class="row mb-3" style="display:none">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Academic Session</label>
                    <div class="col-sm-10">
                        <select   name="session" class="form-select" aria-label="Default select example">


                         @foreach($sessions as $session)
                        <option value="{{$session->name}}">{{$session->name}}</option>
                         @endforeach   
                        </select>

                                               
                         @error('session')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    </div>
                   <!-- end row -->
                

                   <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Duration(minutes)</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="duration_minutes" value="{{ old('duration_minutes', $cbtTest->duration_minutes) }}">

                                               
                         @error('duration_minutes')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    </div>
                   <!-- end row -->


                   <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Assessment Type</label>
                    <div class="col-sm-10">
                    <select  name="assessment_type" class="form-select" aria-label="Default select example">

                        <option selected value="">--Assessment Type(1st/2nd/3rdTest)--</option>
                        <option value="1st Test" {{$cbtTest->assessment_type == '1st Test'? 'selected' : ''}}>1st Test</option>
                       <option value="2nd Test" {{$cbtTest->assessment_type  == '2nd Test'? 'selected' : ''}}>2nd Test</option>
                       <option value="3rd Test" {{$cbtTest->assessment_type  == '3rd Test'? 'selected' : ''}}>3rd Test</option>
                        <option value="Exam" {{$cbtTest->assessment_type == 'Exam'? 'selected' : ''}}>Exam</option>
            
                        
                        </select>
                                               
                         @error('assessment_type')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    </div>
                   <!-- end row -->

                   <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Start Time</label>
                    <div class="col-sm-10">
                      <input type="datetime-local" class="form-control" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($cbtTest->start_time)->format('Y-m-d\TH:i')) }}">

                                               
                         @error('start_time')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    </div>
                   <!-- end row -->

                   <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">End Time(optional)</label>
                    <div class="col-sm-10">
                        <input type="datetime-local" class="form-control" name="end_time" value="{{ optional($cbtTest->end_time)->format('Y-m-d\TH:i') }}">
                    </div>
                    </div>
                   <!-- end row -->

                <button type="submit" class="btn btn-primary waves-effect waves-light"> Update CBT Test  </button>




</form>


</div>          
 </div>
</div> 
</div>
  
</div>



@endsection

