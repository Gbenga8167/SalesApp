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
            <span class="text-muted">
                <i class="ri-record-circle-line align-middle font-size-14 text-success"></i>
                {{$adminData->email}}
            </span>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <div id="sidebar-menu">
        <ul class="metismenu list-unstyled" id="side-menu">

            <li class="menu-title">MAIN CATEGORY</li>

            <!-- DASHBOARD -->
            <li>
                <a href="{{route('admin.dashboard')}}" class="waves-effect">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- SALESPERSON -->
            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-team-line"></i>
                    <span>Create SalesPerson</span>
                </a>
                <ul class="sub-menu">
                    <li><a href="{{route('create.salesperson')}}">Add Salesperson</a></li>
                    <li><a href="{{route('manage.salesperson')}}">Manage Salesperson</a></li>
                </ul>
            </li>

            <!-- PRODUCTS -->
            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-archive-line"></i>
                    <span>Product Details</span>
                </a>
                <ul class="sub-menu">
                    <li><a href="{{route('create.product')}}">Create Product</a></li>
                    <li><a href="{{route('manage.product')}}">Manage Product</a></li>
                </ul>
            </li>

            <!-- NEW STOCK -->
            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-stack-line"></i>
                    <span>New Stock</span>
                </a>
                <ul class="sub-menu">
                    <li><a href="{{route('stocks.create')}}">Add New Stock</a></li>
                    <li><a href="{{route('stocks.manage')}}">Manage Stock</a></li>
                </ul>
            </li>

            <!-- STOCK FOR SALE -->
            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-store-2-line"></i>
                    <span>Stock For Sale</span>
                </a>
                <ul class="sub-menu">
                    <li><a href="{{route('create.sale')}}">Add Stock For Sale</a></li>
                    <li><a href="{{route('manage.stock')}}">Manage Stock For Sale</a></li>
                </ul>
            </li>

            <!-- SALES REPORT -->
            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-bar-chart-2-line"></i>
                    <span>Sales Report</span>
                </a>
                <ul class="sub-menu">
                    <li><a href="{{route('admin.sales.history')}}">Sales Report</a></li>
                </ul>
            </li>

            <!-- SETTINGS -->
            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-settings-3-line"></i>
                    <span>Settings</span>
                </a>
                <ul class="sub-menu">
                    <li><a href="{{route('admin.settings')}}">App Settings</a></li>
                </ul>
            </li>

        </ul>
    </div>

</div>
</div>