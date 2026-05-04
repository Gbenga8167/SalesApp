@extends('backend.admin_backend.admin_dashboard')

@section('admin')
<style>
    th{
        color: white;
    }

thead{
    background: #198754;;
}



</style>

<div class="container-fluid">

<div class="card shadow-sm">

    <div class="p-3 text-white" style="background-color: #198754; font-size:25px">
         Salesperson Leaderboard
    </div>

    <div class="p-3">

        <!-- 🔍 SEARCH -->
        <div class="mb-2">
            <b>Search (Salesperson / Username)</b>
            <input type="text" id="search" class="form-control" placeholder="Search...">
        </div>

        <!-- 📅 DATE -->
        <div class="d-flex gap-2 mb-3">

            <div style="flex:1;">
                <b>From</b>
                <input type="date" id="from" class="form-control">
            </div>

            <div style="flex:1;">
                <b>To</b>
                <input type="date" id="to" class="form-control">
            </div>

        </div>

        <!-- TABLE -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle" id="leaderboardTable">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Salesperson</th>
                        <th>Username</th>
                        <th>Total Sales (₦)</th>
                        <th>Transactions</th>
                        <th>Last Sale</th>
                    </tr>
                </thead>

            </table>
        </div>

    </div>

</div>

</div>


<script>
$(document).ready(function(){

let table = $('#leaderboardTable').DataTable({

    processing: true,
    serverSide: false,
    responsive: true,
    autoWidth: false,

    ajax: {
        url: "{{ route('admin.leaderboard.data') }}",
        data: function(d){
            d.from = $('#from').val();
            d.to = $('#to').val();
        },
        dataSrc: 'data'
    },

    columns: [
    { data: 'rank' },
    { data: 'name' },
    { data: 'user_name' },
    {
        data: 'total_sales',
        render: function(data){
            return '₦' + data;
        }
    },
    { data: 'total_transactions' },
    { data: 'last_sale' },
  ]

});


// 🔍 SEARCH
$('#search').keyup(function(){
    table.search(this.value).draw();
});

// 📅 DATE FILTER
$('#from, #to').change(function(){
    table.ajax.reload();
});

});
</script>

@endsection