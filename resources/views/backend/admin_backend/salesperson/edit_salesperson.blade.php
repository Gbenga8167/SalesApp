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

    .img-preview {
        margin-top: 10px;
    }

    .img-preview img {
        width: 100px;
        height: 100px;
        border-radius: 6px;
        object-fit: cover;
        border: 1px solid #ddd;
    }
</style>

<div class="form-container">

<h3 style="margin-bottom:20px;">Edit Salesperson</h3>

{{-- SUCCESS --}}
@if(session('success'))
<div class="alert-success" id="successAlert">
    <span>{{ session('success') }}</span>
    <button class="close-btn" onclick="closeAlert()">×</button>
</div>
@endif

{{-- ERRORS --}}
@if ($errors->any())
<div class="alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>⚠ {{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('update.salesperson', $salesperson->id) }}" enctype="multipart/form-data">
@csrf

<!-- Name -->
<div class="form-group">
<label>Full Name</label>
<input type="text" name="name" value="{{ $salesperson->name }}" class="form-control">
</div>

<!-- Username -->
<div class="form-group">
<label>Username</label>
<input type="text" name="user_name" value="{{ $salesperson->user_name }}" class="form-control">
</div>

<!-- Email -->
<div class="form-group">
<label>Email</label>
<input type="email" name="email" value="{{ $salesperson->email }}" class="form-control">
</div>

<!-- Phone -->
<div class="form-group">
<label>Phone Number</label>
<input type="text" name="phone_number" value="{{ $salesperson->phone_number }}" class="form-control">
</div>

<!-- State -->
<div class="form-group">
<label>State of Origin</label>
<select name="state_of_origin" class="form-control">
<option value="">-- Select State --</option>
@foreach($states as $state)
<option value="{{ $state }}" {{ $salesperson->state_of_origin == $state ? 'selected' : '' }}>
    {{ $state }}
</option>
@endforeach
</select>
</div>

<!-- Address -->
<div class="form-group">
<label>Contact Address</label>
<textarea name="contact_address" class="form-control">{{ $salesperson->contact_address }}</textarea>
</div>

<!-- PHOTO -->
<div class="form-group">
<label>Profile Photo</label>
<input type="file" name="photo" class="form-control" id="image">
</div>

<!-- CURRENT PHOTO -->
<div class="img-preview">
<img id="ShowImage"
src="{{ $salesperson->photo ? asset('uploads/salesperson_photos/'.$salesperson->photo) : asset('uploads/no_image.png') }}">
</div>

<!-- PASSWORD -->
<div class="form-group">
<label>New Password (Optional)</label>
<small class="text-muted">Leave blank if you don't want to change the password.</small>
<input type="password" name="password" class="form-control" placeholder="Enter new password (leave blank to keep current)">
@error('password') <div class="error">{{ $message }}</div> @enderror
</div>

<!-- CONFIRM -->
<div class="form-group">
<label>Confirm Password</label>
<input type="password" name="password_confirmation" class="form-control">
</div>

<button type="submit" class="btn-submit">Update Salesperson</button>

</form>

</div>

<script>
// Close alert
function closeAlert() {
    let alert = document.getElementById('successAlert');
    alert.classList.add('fade-out');
    setTimeout(() => alert.remove(), 500);
}

// Auto fade
setTimeout(() => {
    let alert = document.getElementById('successAlert');
    if (alert) {
        alert.classList.add('fade-out');
        setTimeout(() => alert.remove(), 500);
    }
}, 4000);

// IMAGE PREVIEW
document.getElementById('image').addEventListener('change', function(e){
    let reader = new FileReader();
    reader.onload = function(e){
        document.getElementById('ShowImage').src = e.target.result;
    }
    reader.readAsDataURL(e.target.files[0]);
});
</script>

@endsection