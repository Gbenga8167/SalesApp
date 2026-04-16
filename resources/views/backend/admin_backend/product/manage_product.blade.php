@extends('backend.admin_backend.admin_dashboard')

@section('admin')

<style>
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
}

.close-btn {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
}

.fade-out {
    opacity: 0;
    transform: translateY(-10px);
}
</style>

<div class="container-fluid">

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Manage Products</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Manage</a></li>
                    <li class="breadcrumb-item active">Products</li>
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

<h4 class="card-title">All Products</h4>

<table id="datatable" class="table table-bordered dt-responsive nowrap" style="width:100%;">
<thead>
<tr>
<th>#</th>
<th>Product ID</th>
<th>Name</th>
<th>Category</th>
<th>Action</th>
</tr>
</thead>

<tbody>
@foreach($products as $key => $product)
<tr>
<td>{{ $key+1 }}</td>
<td>{{ $product->product_code }}</td>
<td>{{ $product->product_name }}</td>
<td>{{ $product->category }}</td>

<td>
<a href="{{ route('edit.product', $product->id) }}">
<button class="btn btn-primary">Edit</button>
</a>

<a href="{{ route('delete.product', $product->id) }}" id="delete">
<button class="btn btn-danger">Delete</button>
</a>
</td>

</tr>
@endforeach
</tbody>
</table>

</div>
</div>
</div>
</div>

</div>

<script>
function closeAlert() {
    let alert = document.getElementById('successAlert');
    alert.classList.add('fade-out');
    setTimeout(() => alert.remove(), 500);
}

setTimeout(() => {
    let alert = document.getElementById('successAlert');
    if (alert) {
        alert.classList.add('fade-out');
        setTimeout(() => alert.remove(), 500);
    }
}, 4000);
</script>

@endsection