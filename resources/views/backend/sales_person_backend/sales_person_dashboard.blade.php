
<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        <title>Sales Dashboard </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
        <meta content="Themesdesign" name="author" />

        <!-- JQUERY FOR OFFLINE-->
        <script src="{{ asset('BackendTem/assets/libs/jquery/jquery.min.js') }}"></script> 

        <!-- App favicon -->
        <link rel="shortcut icon" href="{{asset('BackendTem/assets/images/favicon.ico')}}">

        <!-- jquery.vectormap css -->
        <link href="{{asset('BackendTem/assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css')}}" rel="stylesheet" type="text/css" />

        <!-- DataTables -->
        <link href="{{asset('BackendTem/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />

        <!-- Responsive datatable examples -->
        <link href="{{asset('BackendTem/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />  

        <!-- Bootstrap Css -->
        <link href="{{asset('BackendTem/assets/css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />

        <!-- chart link -->
         <script src="{{ asset('BackendTem/assets/js/chart.min.js') }}"></script>
 
        <!-- Icons Css -->
        <link href="{{asset('BackendTem/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
        <!-- App Css-->

        <link href="{{asset('BackendTem/assets/css/app.min.css')}}" id="app-style" rel="stylesheet" type="text/css" />
        
       <!--Toaster Massage-->
       <link rel="stylesheet" href="{{ asset('BackendTem/assets/libs/toastr/toastr.min.css') }}"> 


        <style>
                body {
            font-family: 'Poppins', sans-serif;
        }
        </style>

    </head>

    <body data-topbar="dark">
    
    <!-- <body data-layout="horizontal" data-topbar="dark"> -->

        <!-- Begin page -->
        <div id="layout-wrapper">

   
          @include('backend.sales_person_backend.sections.header')

            <!-- ========== Left Sidebar Start ========== -->
           
            @include('backend.sales_person_backend.sections.sidebar')

            <!-- Left Sidebar End -->

            

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">

                <div class="page-content">

                   @yield('salesperson')
                    
                </div>
                <!-- End Page-content -->
               
                @include('backend.sales_person_backend.sections.footer')
                
            </div>
            <!-- end main content-->

        </div>
        <!-- END layout-wrapper -->

        <!-- Right Sidebar -->

        <!-- /Right-bar -->

        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>

        <!-- JAVASCRIPT -->
        <script src="{{asset('BackendTem/assets/libs/jquery/jquery.min.js')}}"></script>
        <script src="{{asset('BackendTem/assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{asset('BackendTem/assets/libs/metismenu/metisMenu.min.js')}}"></script>
        <script src="{{asset('BackendTem/assets/libs/simplebar/simplebar.min.js')}}"></script>
        <script src="{{asset('BackendTem/assets/libs/node-waves/waves.min.js')}}"></script>

        <!-- jquery.vectormap map -->
        <script src="{{asset('BackendTem/assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>
        <script src="{{asset('BackendTem/assets/libs/admin-resources/jquery.vectormap/maps/jquery-jvectormap-us-merc-en.js')}}"></script>

        <!-- Required datatable js -->
        <script src="{{asset('BackendTem/assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('BackendTem/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
        
        <!-- Responsive examples -->
        <script src="{{asset('BackendTem/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
        <script src="{{asset('BackendTem/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>

        <script src="{{asset('BackendTem/assets/js/pages/dashboard.init.js')}}"></script>

        <!-- App js -->
        <script src="{{asset('BackendTem/assets/js/app.js')}}"></script>

        <!-- Toater msg for error or success feed-back -->
        <script src="{{ asset('BackendTem/assets/libs/toastr/toastr.min.js') }}"></script>
    
<script>
 @if(Session::has('message'))
 var type = "{{ Session::get('alert-type','info') }}"
 switch(type){
    case 'info':
    toastr.info(" {{ Session::get('message') }} ");
    break;

    case 'success':
    toastr.success(" {{ Session::get('message') }} ");
    break;

    case 'warning':
    toastr.warning(" {{ Session::get('message') }} ");
    break;

    case 'error':
    toastr.error(" {{ Session::get('message') }} ");
    break; 
 }
 @endif 
</script>

   <!-- Required datatable js -->
   <script src="{{asset('BackendTem/assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
   <script src="{{asset('BackendTem/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
     
   <!-- Datatable init js -->
   <script src="{{asset('BackendTem/assets/js/pages/datatables.init.js')}}"></script>

    <!-- Delete toaster alert POP-UP-->
    <script src="{{ asset('BackendTem/assets/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
  
   <script>
    
    $(function(){
    $(document).on('click','#delete',function(e){
        e.preventDefault();
        var link = $(this).attr("href");

                  Swal.fire({
                    title: 'Are you sure?',
                    text: "You Want To Delete This Data?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                  }).then((result) => {
                    if (result.isConfirmed) {
                      window.location.href = link
                      Swal.fire(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                      )
                    }
                  }) 

    });

  });






   </script>


    </body>

</html>