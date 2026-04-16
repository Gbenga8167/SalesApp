

@php

$id = Auth::user()->id;
$StudentData = App\Models\User::findOrFail(Auth::user()->id);
$studentphoto = App\Models\Student::where('user_id', $id)->first();


@endphp

<div class="vertical-menu">

<div data-simplebar class="h-100">

    <!-- User details -->
    <div class="user-profile text-center mt-3">
        <div class="">
            <img src="{{ empty($studentphoto->photo)? asset('uploads/no_image.png') : asset('uploads/student_photos/'.$studentphoto->photo)}}"  alt="" class="avatar-md rounded-circle">
        </div>
        <div class="mt-3">
            <h4 class="font-size-16 mb-1">{{ucwords(strtolower($studentphoto->name))}}</h4>
            <span class="text-muted"><i class="ri-record-circle-line align-middle font-size-14 text-success"></i>{{$StudentData->user_name}}</span>
        </div>
    </div>

    <!--- Sidemenu -->
    <div id="sidebar-menu">
        <!-- Left Menu Start -->
        <ul class="metismenu list-unstyled" id="side-menu">
            <li class="menu-title">MAIN CATEGORY</li>

            <li>
                <a href="{{route('student.dashboard')}}" class="waves-effect">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>


            <li class="menu-title">APPERANCE</li>

            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                <i class="ri-dashboard-line"></i>
                    <span>My Subjects</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                    <li><a href="{{route('student.subjects')}}">My Subjects</a></li>
                   
                
                
                </ul>
            </li>


            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                <i class="ri-dashboard-line"></i>
                    <span> CBT Question</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                    <li><a href="{{route('student.index')}}">Attempt CBT</a></li>
                   
                
                
                </ul>
            </li>

             <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                <i class="ri-dashboard-line"></i>
                    <span>Profile</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                    <li><a href="{{route('student.profile')}}">View Profile</a></li>

                
                </ul>
            </li>



             <li>
                 <a class="dropdown-item text-danger" href="{{route('student.logout')}}">
                <i class="ri-shut-down-line align-middle me-1 text-danger"></i>
                    <span>Logout</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                    
              
                </ul>
            </li>



            

        </ul>
    </div>
    <!-- Sidebar -->
</div>
</div> 