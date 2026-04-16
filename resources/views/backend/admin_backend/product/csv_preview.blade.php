@extends('backend.admin_backend.admin_dashboard')

@section('admin')

<style>
.form-container {
    max-width: 1200px;
    margin: 30px auto;
    background: #fff;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

/* TITLE */
.page-title {
    text-align: center;
    font-weight: 600;
    margin-bottom: 20px;
}

/* ALERTS */
.alert-danger {
    background: #fdecea;
    color: #842029;
    padding: 14px 18px;
    border-radius: 8px;
    margin-bottom: 15px;
    border-left: 6px solid #dc3545;
}

.alert-warning {
    background: #fff3cd;
    color: #664d03;
    padding: 12px;
    border-radius: 6px;
    margin-top: 15px;
}

/* TABLE */
.table-container {
    overflow-x: auto;
}

.custom-table {
    width: 100%;
    border-collapse: collapse;
}

.custom-table thead {
    background: #0d6efd;
    color: #fff;
}

.custom-table th,
.custom-table td {
    padding: 12px;
    border: 1px solid #eee;
    text-align: left;
}

/* ZEBRA STRIPE */
.custom-table tbody tr:nth-child(even) {
    background: #f9f9f9;
}

/* HOVER */
.custom-table tbody tr:hover {
    background: #eef5ff;
}

/* LINE BREAK */
.break-text {
    word-wrap: break-word;
    white-space: normal;
}

/* BUTTON */
.btn-submit {
    background: #0d6efd;
    color: #fff;
    padding: 12px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    width: 100%;
}

.btn-submit:hover {
    background: #0b5ed7;
}
</style>

<div class="form-container">

<h4 class="page-title">📦 Product CSV Preview</h4>

@if(count($errors))
<div class="alert-danger">
    <ul>
        @foreach($errors as $error)
            <li>⚠ {{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="table-container">
<table class="custom-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Product Name</th>
            <th>Category</th>
        </tr>
    </thead>

    <tbody>
        @foreach($validatedRows as $row)
        <tr>
            <td>{{ $loop->iteration }}</td>

            <td class="break-text">
                {{ $row['product_name'] }}
            </td>

            <td class="break-text">
                {{ $row['category'] }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>

@if(count($errors) === 0)
<form method="POST" action="{{ route('product.confirm.csv') }}">
    @csrf
    <button class="btn-submit mt-3">
        ✅ Confirm & Save Products
    </button>
</form>
@else
<div class="alert-warning">
    ⚠ Fix CSV errors before saving.
</div>
@endif

</div>

@endsection