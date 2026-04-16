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

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    font-weight: 600;
    display: block;
    margin-bottom: 5px;
}

.form-control {
    width: 100%;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    outline: none;
}

.form-control:focus {
    border-color: #0d6efd;
}

.error {
    color: red;
    font-size: 13px;
}

.btn-submit {
    background: #0d6efd;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

.btn-submit:hover {
    background: #0b5ed7;
}

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
}

/* ERROR ALERT */
.alert-danger {
    background: #fdecea;
    color: #842029;
    padding: 14px 18px;
    border-radius: 8px;
    margin-bottom: 15px;
    border-left: 6px solid #dc3545;
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
    transition: 0.5s;
}
</style>

<div class="form-container">

<!-- CSV UPLOAD FOR PRODUCTS -->
<div class="card mb-3 p-3 shadow-sm">

    <h5 class="mb-3 text-primary fw-bold">Upload Products via CSV</h5>

    <small class="text-muted mb-3" style="font-size:14px;">
        ⚠️ Save file as <b>CSV UTF-8 (Comma delimited)</b><br>
        Format: product_name, category
    </small>

    @if(session('message'))
        <div class="alert {{ session('alert-type') == 'success' ? 'alert-success' : 'alert-danger' }}">
            {{ session('message') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>⚠ {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    

    <form action="{{ route('product.upload.csv') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <input type="file" name="csv_file" class="form-control" accept=".csv" required>
        </div>

        <button type="submit" class="btn-submit">
            Upload & Preview
        </button>
    </form>

</div>

<form method="POST" action="{{ route('store.product') }}">
@csrf

@if(session('success'))
<div class="alert-success">{{ session('success') }}</div>
@endif

@if ($errors->any())
<div class="alert-danger">
<ul>
@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif

<div class="form-group">
<label>Product Name</label>
<input type="text" name="product_name" class="form-control" value="{{ old('product_name') }}">
</div>

<div class="form-group">
<label>Category</label>
<input type="text" name="category" class="form-control" value="{{ old('category') }}">
</div>

<button type="submit" class="btn-submit">Save Product</button>

</form>

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