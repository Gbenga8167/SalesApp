<header id="page-topbar">

<style>

/* 🔥 IMPORT NICE FONT */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

/* 🔥 HEADER BACKGROUND */
#page-topbar{
    background: linear-gradient(90deg, #198754, #157347);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

/* 🔥 APPLY FONT */
#page-topbar *{
    font-family: 'Poppins', sans-serif;
}

/* 🔥 CENTER COMPANY NAME */
.company-title{
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    top: 12px;
    font-size: 20px;
    font-weight: 700;
    color: #f8f9fa;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

/* 🔥 HIDE ON MOBILE */
@media (max-width: 768px){
    .company-title{
        display: none;
    }
}

/* 🔥 LOGO STYLE */
.logo-img{
    height: 42px;
    width: 42px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

/* 🔥 BUTTONS */
.header-item{
    color: #f8f9fa !important;
}

/* 🔥 USER NAME */
.user-dropdown span{
    color: #f8f9fa;
    font-weight: 500;
}

/* 🔥 ICON HOVER */
.header-item:hover{
    background: rgba(255,255,255,0.1);
    border-radius: 6px;
}

/* 🔥 DROPDOWN */
.dropdown-menu{
    border-radius: 8px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

</style>

<div class="navbar-header position-relative">

    {{-- 🔥 FETCH SETTINGS --}}
    @php
        $settings = App\Models\Setting::first();
        $sales_person_data = App\Models\User::findOrFail(Auth::user()->id);
    @endphp

    {{-- 🔥 CENTER COMPANY NAME --}}
<div class="company-title">
    {{ ucwords($settings->company_name ?? 'your company name') }}
</div>

    <div class="d-flex">

        <!-- LOGO -->
        <div class="navbar-brand-box">
            <a href="{{ route('admin.dashboard') }}" class="logo">

                @if(!empty($settings->logo))
                    <img src="{{ asset('uploads/settings/'.$settings->logo) }}" 
                         class="logo-img">
                @else
                    <img src="{{ asset('uploads/no_image.png') }}" 
                         class="logo-img">
                @endif

            </a>
        </div>

        <!-- MENU BUTTON -->
        <button type="button" 
            class="btn btn-sm px-3 font-size-24 header-item waves-effect" 
            id="vertical-menu-btn">
            <i class="ri-menu-2-line align-middle"></i>
        </button>

    </div>

    <div class="d-flex">

        <!-- FULLSCREEN -->
        <div class="dropdown d-none d-lg-inline-block ms-1">
            <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                <i class="ri-fullscreen-line"></i>
            </button>
        </div>

        <!-- USER DROPDOWN -->
        <div class="dropdown d-inline-block user-dropdown">
            <button type="button" class="btn header-item waves-effect"
                data-bs-toggle="dropdown">

                <img class="rounded-circle header-profile-user"
                    src="{{ empty($sales_person_data->photo) 
                        ? asset('uploads/no_image.png') 
                        : asset('uploads/salesperson_photos/'.$sales_person_data->photo) }}">

                <span class="d-none d-xl-inline-block ms-1">
                    {{ $sales_person_data->name }}
                </span>

                <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
            </button>

            <div class="dropdown-menu dropdown-menu-end">

                <a class="dropdown-item" href="{{ route('sales.person.profile') }}">
                    <i class="ri-user-line align-middle me-1"></i> Profile
                </a>

                <a class="dropdown-item" href="{{ route('admin.password.change') }}">
                    <i class="ri-lock-line align-middle me-1"></i> Change Password
                </a>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item text-danger" href="{{ route('sales.person.logout') }}">
                    <i class="ri-shut-down-line align-middle me-1 text-danger"></i> Logout
                </a>

            </div>
        </div>

    </div>

</div>

</header> 