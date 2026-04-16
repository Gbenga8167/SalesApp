@extends('backend.student_backend.student_dashboard')
@section('student')

    <div class="container-fluid">

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="col-12">
        <h4 class="p-2 bg-primary text-white rounded" style="text-align:center">Student | profile</h4>
    </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Student | Pfofile</h4>
            

                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Fullname</label>
                    <div class="col-sm-10">
                        <input class="form-control" readonly type="text" value="{{$studentphoto->name }}">
                    </div>
                </div>
                <!-- end row -->


                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Username</label>
                    <div class="col-sm-10">
                        <input class="form-control" readonly type="text" value="{{$StudentData->user_name }}">
                    </div>
                </div>
                <!-- end row -->

                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Gender</label>
                    <div class="col-sm-10">
                        <input class="form-control" readonly type="text" value="{{$studentphoto->gender }}">
                    </div>
                </div>
                <!-- end row -->


               
                <!-- end row -->
                <div class="row mb-3">
                    <label for="example-email-input" class="col-sm-2 col-form-label">Photo</label>
                    
                     <!-- Student Profile Photo -->
                    <div class="col-sm-10">
                    <img id="ShowImage" src="{{ empty($studentphoto->photo)? asset('uploads/no_image.png') : asset('uploads/student_photos/'.$studentphoto->photo)}}" alt="avatar-4" class="rounded avatar-md">
                 
                    </div>
                </div>
                <!-- end row -->



                <a  href="{{route('student.dashboard')}}">
                     <button type="submit" class="btn btn-primary waves-effect waves-light">Back</button>
                    </a>

                
                
               
                

              
            </div>
        </div>
    </div> 
</div>

<script>
  $(document).ready(function(){
 $('#image').on("change", function(e){
    var reader = new FileReader();
    reader.onload = function(e){
        $('#ShowImage').attr('src', e.target.result);
    }
    reader.readAsDataURL(e.target.files['0']);
 });

    });
</script>
  



@endsection



