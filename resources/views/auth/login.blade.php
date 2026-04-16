<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        <title>Login </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
        <meta content="Themesdesign" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{asset('BackendTem/assets/images/favicon.ico')}}">

        <!-- Bootstrap Css -->
        
        <link href="{{asset('BackendTem/assets/css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{asset('BackendTem/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{asset('BackendTem/assets/css/app.min.css')}}" id="app-style" rel="stylesheet" type="text/css" />

    </head>

    <body class="auth-body-bg d-flex justify-content-center align-items-center min-vh-100">
    <div class="bg-overlay"></div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <h1 class="text-muted text-center font-size-20">Log In</h1>
                        <div class="p-3">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="form-group mb-3 row">
                                    <div class="col-12">
                                        <input class="form-control @error('login') is-invalid @enderror" 
                                               type="text" name="login" 
                                               value="{{ old('login') }}" 
                                               placeholder="Username">
                                        @error('login')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group mb-3 row">
                                    <div class="col-12">
                                        <input class="form-control @error('password') is-invalid @enderror" 
                                               type="password" name="password" 
                                               placeholder="Password">
                                        @error('password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group mb-3 row">
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="customCheck1">
                                            <label class="form-check-label" for="customCheck1">Remember me</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3 text-center row mt-3 pt-1">
                                    <div class="col-12">
                                        <button class="btn btn-info w-100 waves-effect waves-light" type="submit">Log In</button>
                                    </div>
                                </div>

                                <div class="form-group mb-0 row mt-2">
                                    <div class="col-sm-7 mt-3">
                                        <a href="auth-recoverpw.html" class="text-muted"><i class="mdi mdi-lock"></i> Forgot your password?</a>
                                    </div>
                                    <div class="col-sm-5 mt-3 text-end">
                                        <a href="auth-register.html" class="text-muted"><i class="mdi mdi-account-circle"></i> Create an account</a>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div>
        </div>
    </div>



        <!-- JAVASCRIPT -->
        <script src="{{asset('BackendTem/assets/libs/jquery/jquery.min.js')}}"></script>
        <script src="{{asset('BackendTem/assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{asset('BackendTem/assets/libs/metismenu/metisMenu.min.js')}}"></script>
        <script src="{{asset('BackendTem/assets/libs/simplebar/simplebar.min.js')}}"></script>
        <script src="{{asset('BackendTem/assets/libs/node-waves/waves.min.js')}}"></script>

        <script src="{{asset('BackendTem/assets/js/app.js')}}"></script>

    </body>
</html>







