@php

$adminData = App\Models\User::findOrFail(Auth::user()->id);

@endphp

<div class="vertical-menu">

<div data-simplebar class="h-100">

    <!-- User details -->
    <div class="user-profile text-center mt-3">
        <div class="">
            <img src="{{ empty($adminData->photo)? asset('uploads/no_image.png') : asset('uploads/admin_profile/'.$adminData->photo)}}"  alt="" class="avatar-md rounded-circle">
        </div>
        <div class="mt-3">
            <h4 class="font-size-16 mb-1">{{$adminData->name}}</h4>
            <span class="text-muted"><i class="ri-record-circle-line align-middle font-size-14 text-success"></i>{{$adminData->email}}</span>
        </div>
    </div>

    <!--- Sidemenu -->
    <div id="sidebar-menu">
        <!-- Left Menu Start -->
        <ul class="metismenu list-unstyled" id="side-menu">
            <li class="menu-title">MAIN CATEGORY</li>

            <li>
                <a href="{{route('admin.dashboard')}}" class="waves-effect">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>



            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-account-circle-line"></i>
                    <span>Create SalesPerson</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                   <li><a href="{{route('create.salesperson')}}">Add Salesperson</a></li>
                   <li><a href="{{route('manage.salesperson')}}">Manage Salesperson</a></li>
                  
                
                
                </ul>
            </li>


            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-account-circle-line"></i>
                    <span>Product Details</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                   <li><a href="{{route('create.product')}}">Create Product</a></li>
                   <li><a href="{{route('manage.product')}}">Manage Product</a></li>
                   
                  
                
                
                </ul>
            </li>



            
            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-account-circle-line"></i>
                    <span>New Stock</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                   <li><a href="{{route('stocks.create')}}">Add New Stock</a></li>
                   <li><a href="{{route('stocks.manage')}}">Manage Stock</a></li>
                   
                  
                
                
                </ul>
            </li>



            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-account-circle-line"></i>
                    <span>Stock For Sale</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                   <li><a href="{{route('create.sale')}}">Add Stock For Sale</a></li>
                   <li><a href="{{route('manage.stock')}}">Manage Stock For Sale</a></li>
                   
                   
                  
                
                
                </ul>
            </li>


            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-account-circle-line"></i>
                    <span> Settings</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                   <li><a href="{{route('admin.settings')}}">App Settings</a></li>

                   
                  
                
                
                </ul>
            </li>
            
        </ul>
    </div>
    <!-- Sidebar -->
</div>
</div>