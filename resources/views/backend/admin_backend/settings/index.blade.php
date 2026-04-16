@extends('backend.admin_backend.admin_dashboard')

@section('admin')

<style>
.form-container{
    max-width:1200px;
    margin:30px auto;
    background:#fff;
    padding:25px;
    border-radius:10px;
    box-shadow:0 4px 20px rgba(0,0,0,0.1);
}
.form-group{margin-bottom:15px;}
.form-control{width:100%;padding:10px;border:1px solid #ccc;border-radius:6px;}
.btn-submit{background:#198754;color:#fff;padding:10px 20px;border:none;border-radius:6px;}
.img-preview img{width:100px;height:100px;object-fit:cover;border-radius:6px;}
</style>

<div class="form-container">

<h3>App Settings</h3>

@if(session('success'))
<div style="color:green">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
@csrf

<div class="form-group">
<label>Company Name</label>
<input type="text" name="company_name" class="form-control" value="{{ $setting->company_name ?? '' }}">
</div>

<div class="form-group">
<label>Address</label>
<input type="text" name="address" class="form-control" value="{{ $setting->address ?? '' }}">
</div>

<div class="form-group">
<label>Logo</label>
<input type="file" name="logo" class="form-control" id="logo">
</div>

<div class="img-preview">
<img id="showLogo"
src="{{ $setting && $setting->logo ? asset('uploads/settings/'.$setting->logo) : asset('uploads/no_image.png') }}">
</div>

<br>
<button class="btn-submit">Save Settings</button>

</form>

</div>

<script>
document.getElementById('logo').onchange = function(e){
    let reader = new FileReader();
    reader.onload = function(e){
        document.getElementById('showLogo').src = e.target.result;
    }
    reader.readAsDataURL(e.target.files[0]);
}
</script>

@endsection