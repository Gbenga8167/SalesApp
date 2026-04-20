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


/* ERROR ALERT */
.alert-danger {
    background: #fdecea;
    color: #842029;
    padding: 14px 18px;
    border-radius: 8px;
    margin-bottom: 15px;
    border-left: 6px solid #dc3545;
    font-size: 14px;
}

/* ERROR LIST */
.alert-danger ul {
    margin: 0;
    padding-left: 18px;
}

.alert-danger li {
    margin-bottom: 6px;
}

/* Optional animation */
.alert-success, .alert-danger {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-6px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<div class="form-container">

    <h3 style="margin-bottom:20px;">Create Salesperson</h3>

  @if(session('success'))
    <div class="alert-success" id="successAlert">
        <span>{{ session('success') }}</span>
        <button class="close-btn" onclick="closeAlert()">×</button>
    </div>
@endif

{{-- ERROR MESSAGE --}}
@if ($errors->any())
    <div class="alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>⚠ {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <form method="POST" action="{{route('store.salesperson')}}" enctype="multipart/form-data">
        @csrf

        <!-- Full Name -->
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
            @error('name') <div class="error">{{ $message }}</div> @enderror
        </div>

        <!-- Username -->
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="user_name" class="form-control" value="{{ old('user_name') }}">
            @error('user_name') <div class="error">{{ $message }}</div> @enderror
        </div>

        <!-- Email -->
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>

        <!-- Phone -->
        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number') }}">
            @error('phone_number') <div class="error">{{ $message }}</div> @enderror
        </div>


         <div class="form-group">
             <label>Gender</label>
                      <select name="gender" class="form-control">
                 <option value="">Select Gender</option>
                 <option value="Male" {{ old('gender')=='Male'?'selected':'' }}>Male</option>
                 <option value="Female" {{ old('gender')=='Female'?'selected':'' }}>Female</option>
             </select>
             @error('gender') <div class="error">{{ $message }}</div> @enderror
         </div>


         
        <!-- State -->
        <div class="form-group">
            <label>State of Origin</label>
            <select name="state_of_origin" class="form-control">
                <option value="">Select State</option>
                @foreach([
                    'Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River',
                    'Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano',
                    'Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun',
                    'Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'
                ] as $state)
                    <option value="{{ $state }}" {{ old('state_of_origin') == $state ? 'selected' : '' }}>
                        {{ $state }}
                    </option>
                @endforeach
            </select>
            @error('state_of_origin') <div class="error">{{ $message }}</div> @enderror
        </div>

        <!-- Address -->
        <div class="form-group">
            <label>Contact Address</label>
            <textarea name="contact_address" class="form-control">{{ old('contact_address') }}</textarea>
            @error('contact_address') <div class="error">{{ $message }}</div> @enderror
        </div>

        <!-- Photo -->
        <div class="form-group">
            <label>Profile Photo</label>
            <input type="file" name="photo" class="form-control" id="image">
            @error('photo') <div class="error">{{ $message }}</div> @enderror
        </div>


                  <div class="form-group">
                    <label for="example-email-input" class="col-sm-2 col-form-label"></label>
                    <div class="col-sm-10">
                    <img id="ShowImage" src="{{asset('uploads/no_image.png')}}" alt="avatar-4" class="rounded avatar-md">
                    </div>
                </div>


        <!-- Password -->
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control">
            @error('password') <div class="error">{{ $message }}</div> @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>

        <button type="submit" class="btn-submit">Create Salesperson</button>

    </form>

</div>






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



    //photo show at the bottom of the form

     $(document).ready(function(){
 $('#image').on("change", function(e){
    var reader = new FileReader();
    reader.onload = function(e){
        $('#ShowImage').attr('src', e.target.result);
    }
    reader.readAsDataURL(e.target.files['0']);
 });

    });

</script>



@endsection