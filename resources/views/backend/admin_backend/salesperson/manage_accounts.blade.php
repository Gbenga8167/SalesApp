@extends('backend.admin_backend.admin_dashboard')

@section('admin')

<div class="container-fluid">

<div class="card shadow-sm">

<div class="p-3 text-white" style="background:#198754;font-size:20px;">
    Manage Accounts
</div>

<div class="p-3">

<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">

<thead style="background:#198754;color:white;">
<tr>
    <th>#</th>
    <th>Name</th>
    <th>Username</th>
    <th>Status</th>
</tr>
</thead>

<tbody>

@foreach($users as $key => $user)
<tr>
    <td>{{ $key+1 }}</td>
    <td>{{ $user->name }}</td>
    <td>{{ $user->user_name }}</td>

    <td>
        <button 
            class="btn toggleStatus"
            data-id="{{ $user->id }}"
            style="background: {{ $user->status ? 'green' : 'red' }}; color:white;"
        >
            {{ $user->status ? 'Active' : 'Deactivated' }}
        </button>
    </td>
</tr>
@endforeach

</tbody>

</table>
</div>

</div>
</div>

</div>

<script>
$(document).on('click', '.toggleStatus', function(e){

    e.preventDefault();

    let btn = $(this);
    let userId = btn.data('id');

    // 🔥 Determine action text dynamically
    let isActive = btn.text().trim() === 'Active';

    let actionText = isActive 
        ? "Deactivate this account?" 
        : "Activate this account?";

    let confirmBtnText = isActive 
        ? "Yes, deactivate!" 
        : "Yes, activate!";

    let successText = isActive 
        ? "Account has been deactivated." 
        : "Account has been activated.";

    let confirmColor = isActive ? '#d33' : '#3085d6';

    Swal.fire({
        title: 'Are you sure?',
        text: actionText,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: confirmColor,
        cancelButtonColor: '#6c757d',
        confirmButtonText: confirmBtnText
    }).then((result) => {

        if (result.isConfirmed) {

            $.post("{{ route('admin.toggle.user.status') }}", {
                _token: "{{ csrf_token() }}",
                user_id: userId
            }, function(res){

                if(res.error){
                    Swal.fire('Error!', res.error, 'error');
                    return;
                }

                if(res.status == 1){
                    btn.text('Active').css('background','green');
                } else {
                    btn.text('Deactivated').css('background','red');
                }

                Swal.fire(
                    'Success!',
                    successText,
                    'success'
                );

            });

        }

    });

});
</script>

@endsection