@extends('backend.admin_backend.admin_dashboard')

@section('admin')

<style>
        /* SUCCESS ALERT */
.alert-success {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #e6f9f0;
    color: #0f5132;
    padding: 14px 18px;
    border-radius: 8px;
    margin-bottom: 15px;
    border-left: 6px solid #198754;
    font-size: 14px;
    transition: opacity 0.5s ease, transform 0.5s ease;
}

/* Close button */
.close-btn {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: #0f5132;
}

.close-btn:hover {
    color: #000;
}

/* Fade out class */
.fade-out {
    opacity: 0;
    transform: translateY(-10px);
}

</style>

<div class="container-fluid">

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Manage Salespersons</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Manage</a></li>
                    <li class="breadcrumb-item active"> SalesPerson </li>
                </ol>
            </div>

        </div>
    </div>
</div>



           <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
        
                            

                              @if(session('success'))
                                <div class="alert-success" id="successAlert">
                                    <span>{{ session('success') }}</span>
                                    <button class="close-btn" onclick="closeAlert()">×</button>
                                </div>
                            @endif

        
             <h4 class="card-title">View Class Info</h4>

            <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach($salespersons as $key => $user)
            <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->user_name }}</td>
                <td>{{ $user->email }}</td>

                  <td > <a href="{{ route('edit.salesperson', $user->id)}}">
                     <button type="submit" class="btn btn-primary waves-effect waves-light">Edit</button>
                     </a> 
                                                
                                                
                     <a href="{{  route('delete.salesperson', $user->id)}}" id="delete">
                     <button type="submit"  class="btn btn-danger waves-effect waves-light">Delete</button>
                     </a>

                </td>
                                               
                </tr>
                                            

                    @endforeach

                </tr>
                </tbody>
            </table>
        
        </div>
    </div>
   </div> <!-- end col -->
</div> <!-- end row -->



<script>

     //close msg alert
    function closeAlert() {
        let alert = document.getElementById('successAlert');
        alert.classList.add('fade-out');
        setTimeout(() => alert.remove(), 500);
    }

    // Auto fade after 4 seconds
    setTimeout(() => {
        let alert = document.getElementById('successAlert');
        if (alert) {
            alert.classList.add('fade-out');
            setTimeout(() => alert.remove(), 500);
        }
    }, 4000);

</script>
@endsection