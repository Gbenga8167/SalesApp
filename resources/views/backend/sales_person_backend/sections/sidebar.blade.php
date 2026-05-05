

@php

$id = Auth::user()->id;
$salesPerson = App\Models\User::findOrFail(Auth::user()->id);


@endphp

<div class="vertical-menu">

<div data-simplebar class="h-100">

    <!-- User details -->
    <div class="user-profile text-center mt-3">
        <div class="">
            <img src="{{ empty($salesPerson->photo)? asset('uploads/no_image.png') : asset('uploads/salesperson_photos/'.$salesPerson->photo)}}"  alt="" class="avatar-md rounded-circle">
        </div>
        <div class="mt-3">
            <h4 class="font-size-16 mb-1">{{ucwords(strtolower($salesPerson->name))}}</h4>
            <span class="text-muted"><i class="ri-record-circle-line align-middle font-size-14 text-success"></i>{{$salesPerson->user_name}}</span>
        </div>
    </div>

    <!--- Sidemenu -->
    <div id="sidebar-menu">
        <!-- Left Menu Start -->
<ul class="metismenu list-unstyled" id="side-menu">
    <li class="menu-title">MAIN CATEGORY</li>

    <li>
        <a href="{{route('sales_person.dashboard')}}" class="waves-effect">
            <i class="ri-dashboard-line"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="menu-title">OPERATIONS</li>

    <!-- MAKE SALES -->
    <li>
        <a href="javascript: void(0);" class="has-arrow waves-effect">
            <i class="ri-shopping-cart-2-line"></i>
            <span>Make Sales</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{route('sales.pos')}}">
                    <i class="ri-store-2-line"></i> POS System
                </a>
            </li>
        </ul>
    </li>

    <!-- SALES HISTORY -->
    <li>
        <a href="javascript: void(0);" class="has-arrow waves-effect">
            <i class="ri-file-list-3-line"></i>
            <span>Manage Sales</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{route('sales.person.history')}}">
                    <i class="ri-time-line"></i> Sales History
                </a>
            </li>
        </ul>
    </li>

    <!-- REPORTS (OPTIONAL FUTURE EXPANSION) 
    <li>
        <a href="javascript: void(0);" class="has-arrow waves-effect">
            <i class="ri-bar-chart-line"></i>
            <span>Reports</span>
        </a>
        <ul class="sub-menu">
            <li>
                <a href="#">
                    <i class="ri-line-chart-line"></i> Performance Report
                </a>
            </li>
        </ul>
    </li>
-->
</ul>
    </div>
    <!-- Sidebar -->
</div>
</div> 