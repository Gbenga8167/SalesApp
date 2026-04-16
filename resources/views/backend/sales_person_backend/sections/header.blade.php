<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .school-name {
    font-family: "Poppins", "Segoe UI", Roboto, Arial, sans-serif;
    font-size: 26px;
    font-weight: 600;
    letter-spacing: 3px;    
    color: #ffffff;
    white-space: nowrap;
    opacity: 0.95;
}



    </style>
</head>
<body>
  


<header id="page-topbar">
    <div class="navbar-header d-flex align-items-center justify-content-between">

        <!-- LEFT: LOGO + MENU -->
        <div class="d-flex align-items-center">
            <div class="navbar-brand-box">
                <a href="{{ route('student.dashboard') }}" class="logo d-flex align-items-center text-decoration-none">
                   <!-- <img src="{{ asset('uploads/bg1.jpg') }}"
                         alt="School Logo"
                         style="height:40px; width:auto;">-->
                                  <span style="font-size:25px; color:#fff;">
                                   CBT
                                </span>
                </a>
            </div>

            <button type="button"
                class="btn btn-sm px-3 font-size-24 header-item waves-effect"
                id="vertical-menu-btn">
                <i class="ri-menu-2-line align-middle"></i>
            </button>
        </div>

     <!--  <div class="d-none d-md-flex flex-grow-1 justify-content-center">
    <span class="school-name">
        <label style="color:red">V</label> <label style="color: #f7bf08ff;">G</label> <label style="color:dodgerblue">C</label> International School
    </span>
       </div> -->


        <!-- RIGHT: USER / ACTIONS -->
        <div class="d-flex align-items-center">

            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                    <i class="ri-fullscreen-line"></i>
                </button>
            </div>

            @php
                $id = Auth::user()->id;
                $studentphoto = App\Models\Student::where('user_id', $id)->first();
            @endphp

            <div class="dropdown d-inline-block user-dropdown">
                <button type="button"
                    class="btn header-item waves-effect"
                    id="page-header-user-dropdown"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">

                    <img class="rounded-circle header-profile-user"
                        src="{{ empty($studentphoto->photo) 
                            ? asset('uploads/no_image.png') 
                            : asset('uploads/student_photos/'.$studentphoto->photo) }}"
                        alt="Header Avatar">

                    <span class="d-none d-xl-inline-block ms-1">
                        {{ $studentphoto->name }}
                    </span>

                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="{{ route('student.profile') }}">
                        <i class="ri-user-line align-middle me-1"></i> Profile
                    </a>

                    <div class="dropdown-divider"></div>

                    <a class="dropdown-item text-danger" href="{{ route('student.logout') }}">
                        <i class="ri-shut-down-line align-middle me-1 text-danger"></i> Logout
                    </a>
                </div>
            </div>

        </div>
    </div>
</header>

<!--
<header id="page-topbar">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO 
                        <div class="navbar-brand-box">
                            <a href="{{route('student.dashboard')}}" class="logo \">
                                <span style="font-size:25px; color:#fff;">
                                   CBT
                                </span>
                               
                            </a>

                 
                        </div>

                        <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect" id="vertical-menu-btn">
                            <i class="ri-menu-2-line align-middle"></i>
                        </button>

                        <!-- App Search
                        <form class="app-search d-none d-lg-block">
                            <div class="position-relative">
                                <input type="text" class="form-control" placeholder="Search...">
                                <span class="ri-search-line"></span>
                            </div>
                        </form>

                        
                    </div>

                    <div class="d-flex">

                        <div class="dropdown d-inline-block d-lg-none ms-2">
                            <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ri-search-line"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                                aria-labelledby="page-header-search-dropdown">
                    
                                <form class="p-3">
                                    <div class="mb-3 m-0">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Search ...">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit"><i class="ri-search-line"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        @php
                        
                        $id = Auth::user()->id;
                        $StudentData = App\Models\User::findOrFail(Auth::user()->id);
                        $studentphoto = App\Models\Student::where('user_id', $id)->first();

                        @endphp
                        
                        <div class="dropdown d-none d-lg-inline-block ms-1">
                            <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                                <i class="ri-fullscreen-line"></i>
                            </button>
                        </div>


                        <div class="dropdown d-inline-block user-dropdown">
                            <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="rounded-circle header-profile-user" src="{{ empty($studentphoto->photo)? asset('uploads/no_image.png') : asset('uploads/student_photos/'.$studentphoto->photo)}}" 
                                    alt="Header Avatar">
                                <span class="d-none d-xl-inline-block ms-1" style="">{{ $studentphoto->name}}</span>
                                <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item
                                <a class="dropdown-item" href="{{route('student.profile')}}"><i class="ri-user-line align-middle me-1"></i> Profile</a>

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="{{route('student.logout')}}"><i class="ri-shut-down-line align-middle me-1 text-danger"></i> Logout</a>
                            </div>
                        </div>

                        
                    </div>
                </div>
            </header>


</body>
</html>
