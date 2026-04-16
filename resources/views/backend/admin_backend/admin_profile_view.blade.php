
@extends('backend.admin_backend.admin_dashboard')
@section('admin')

    <div class="container-fluid">

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">ADMIN'S PROFILE</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
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

                                    @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif


                <h4 class="card-title">ADMIN PROFILE - Update </h4>
              
                <form action="{{route('admin.profile.update')}}" method="post" enctype="multipart/form-data">
                    @csrf

                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Username</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="user_name" type="text" value="{{ $adminData->user_name }}">
                    </div>
                </div>
                <!-- end row -->
                <div class="row mb-3">
                    <label for="example-search-input" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="email" type="text" value="{{ $adminData->email }}">
                    </div>
                </div>
                <!-- end row -->
                <div class="row mb-3">
                    <label for="example-email-input" class="col-sm-2 col-form-label">Photo</label>
                    <div class="col-sm-10">
                    <input type="file" class="form-control" id="image" name="photo" >
                    </div>
                </div>
                <!-- end row -->



                <!-- end row -->
                <div class="row mb-3">
                    <label for="example-email-input" class="col-sm-2 col-form-label"></label>
                    <div class="col-sm-10">
                    <img id="ShowImage" src="{{ empty($adminData->photo)? asset('uploads/no_image.png') : asset('uploads/admin_profile/'.$adminData->photo)}}" alt="avatar-4" class="rounded avatar-md">
                    </div>
                </div>
                <!-- end row -->
                

                <button type="submit" class="btn btn-primary waves-effect waves-light">Update Profile</button>
                
                </form>
               
                

              
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



