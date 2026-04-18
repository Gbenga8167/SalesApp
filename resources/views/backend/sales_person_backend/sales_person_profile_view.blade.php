@extends('backend.sales_person_backend.sales_person_dashboard')
@section('salesperson')

    <div class="container-fluid">

<!-- start page title -->
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">SALES PERSON PROFILE</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Sales Person </a></li>
                    <li class="breadcrumb-item active">Profile</li>
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

                <h4 class="card-title">Sales Person | Pfofile</h4>
            

                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Fullname</label>
                    <div class="col-sm-10">
                        <input class="form-control" readonly type="text" value="{{$sales_person_data->name }}">
                    </div>
                </div>
                <!-- end row -->


                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Username</label>
                    <div class="col-sm-10">
                        <input class="form-control" readonly type="text" value="{{$sales_person_data->user_name }}">
                    </div>
                </div>
                <!-- end row -->

                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Gender</label>
                    <div class="col-sm-10">
                        <input class="form-control" readonly type="text" value="{{$sales_person_data->gender }}">
                    </div>
                </div>
                <!-- end row -->


               
                <!-- end row -->
                <div class="row mb-3">
                    <label for="example-email-input" class="col-sm-2 col-form-label">Photo</label>
                    
                     <!-- Student Profile Photo -->
                    <div class="col-sm-10">
                    <img id="ShowImage" src="{{ empty($sales_person_data->photo)? asset('uploads/no_image.png') : asset('uploads/student_photos/'.$sales_person_data->photo)}}" alt="avatar-4" class="rounded avatar-md">
                 
                    </div>
                </div>
                <!-- end row -->



                <a  href="{{route('sales_person.dashboard')}}">
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



