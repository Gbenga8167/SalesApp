@php
$settings = DB::table('settings')->first();
$company = $settings->company_name ?? 'My Company';
@endphp

<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="{{asset('BackendTem/assets/images/favicon.ico')}}">

    <link href="{{asset('BackendTem/assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('BackendTem/assets/css/icons.min.css')}}" rel="stylesheet">
    <link href="{{asset('BackendTem/assets/css/app.min.css')}}" rel="stylesheet">

    <style>

        body.auth-body-bg{
            background: linear-gradient(135deg, #0f766e, #1e3a8a);
        }

        .bg-overlay{ display:none !important; }

        /* 🔥 SMALLER & CLEANER FORM WIDTH */
        .login-box{
            max-width: 450px;
            margin: auto;
        }

        .card{
            border: none;
            border-radius: 18px;
        }

        .card-body{
            padding: 55px 45px;
        }

        /* 🔥 STRONG COMPANY TITLE (BOLD + SPARKLE + GLOW) */
        .company-title{
            font-size: 34px;
            font-weight: 1000;
            text-align: center;
            margin-bottom: 10px;
            letter-spacing: 1px;

            background: linear-gradient(
                90deg,
                #00f5ff,
                #7c3aed,
                #22c55e,
                #f97316,
                #00f5ff
            );
            background-size: 400% 400%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;

            animation: shine 3s ease-in-out infinite;

            /* glow effect */
            text-shadow: 0 0 12px rgba(0,245,255,0.25);
        }

        @keyframes shine {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .login-title{
            text-align: center;
            font-size: 15px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 28px;
        }

        /* LABELS */
        .form-label{
            font-weight: 700;
            font-size: 15px;
            color: #334155;
        }

        /* INPUTS */
        .form-control{
            height: 52px;
            font-size: 15px;
            font-weight: 500;
            border-radius: 10px;
        }

        .form-control:focus{
            border-color: #0f766e;
            box-shadow: none;
        }

        /* BUTTON */
        .btn-info{
            height: 52px;
            font-weight: 800;
            border-radius: 10px;
            background: #0f766e;
            border: none;
        }

        .btn-info:hover{
            background: #115e59;
        }

    </style>

</head>

<body class="auth-body-bg d-flex justify-content-center align-items-center min-vh-100">

<div class="container">

    <div class="row justify-content-center">

        <div class="col-md-7 col-lg-5">

            <div class="card shadow-lg login-box">

                <div class="card-body">

                    <!-- 🔥 COMPANY NAME -->
                    <div class="company-title">
                        {{ $company }}
                    </div>

                    <div class="login-title">
                         Welcome Back — Please Login
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- LOGIN -->
                        <div class="mb-3">
                            <label class="form-label">Email / Username</label>
                            <input class="form-control @error('login') is-invalid @enderror"
                                   type="text"
                                   name="login"
                                   value="{{ old('login') }}"
                                   placeholder="Enter email or username">
                            @error('login')
                                <span style="font-size:14px" class="text-danger ">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- PASSWORD -->
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input class="form-control @error('password') is-invalid @enderror"
                                   type="password"
                                   name="password"
                                   placeholder="Enter password">
                            @error('password')
                                <span style="font-size:14px" class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- BUTTON -->
                        <button class="btn btn-info w-100 mt-4" type="submit">
                            Log In
                        </button>

                    </form>

                </div>

            </div>

        </div>
    </div>

</div>

</body>
</html>