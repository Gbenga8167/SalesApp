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

.alert-box {
    font-weight: 600;
    font-size: 15px;
    border-radius: 8px;
    padding: 12px 15px;
    margin-bottom: 15px;
    animation: fadeSlide 0.4s ease-in-out;
}

.alert-success {
    background: #e8f5e9;
    color: #1b5e20;
    border-left: 5px solid #28a745;
}

.alert-danger {
    background: #fdecea;
    color: #b71c1c;
    border-left: 5px solid #dc3545;
}

@keyframes fadeSlide {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

</style>

<div class="form-container">

<h3>App Settings</h3>

@if(session('success'))
    <div class="alert alert-success alert-box">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-box">
        {{ session('error') }}
    </div>
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
<label>Timezone</label>

<select name="timezone" class="form-control">
    <option value="Africa/Lagos" {{ $setting->timezone == 'Africa/Lagos' ? 'selected' : '' }}>Africa/Lagos</option>
    <option value="Europe/London" {{ $setting->timezone == 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
    <option value="America/New_York" {{ $setting->timezone == 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
    <option value="Asia/Dubai" {{ $setting->timezone == 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai</option>
</select>
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
    //LOGO PREVIEW
document.getElementById('logo').onchange = function(e){
    let reader = new FileReader();
    reader.onload = function(e){
        document.getElementById('showLogo').src = e.target.result;
    }
    reader.readAsDataURL(e.target.files[0]);
}


//ALERT FADE
setTimeout(() => {
    document.querySelectorAll('.alert-box').forEach(el => {
        el.style.display = 'none';
    });
}, 5000);
</script>

@endsection