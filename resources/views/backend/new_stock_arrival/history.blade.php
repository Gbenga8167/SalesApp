@extends('backend.admin_backend.admin_dashboard')

@section('admin')

<style>

/* CONTAINER */
.table-container{
    max-width:1200px;
    margin:30px auto;
    background:#fff;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 20px rgba(0,0,0,0.08);
}

/* TABLE WRAPPER */
.table-wrapper{
    max-height:500px;
    overflow-y:auto;
    overflow-x:auto;
}

/* SEARCH */
.search-box{
    width:100%;
    padding:10px 14px;
    border:1px solid #ccc;
    border-radius:8px;
    margin-bottom:15px;
}

/* TABLE */
table{
    border-collapse:collapse;
    width:100%;
}

/* STICKY HEADER */
thead th{
    position: sticky;
    top: 0;
    background:#198754;
    color:#fff;
    padding:12px;
    text-align:left;
    z-index:10;
    white-space:nowrap;
}

/* BODY */
td{
    padding:12px;
    border-bottom:1px solid #eee;
    white-space:nowrap;
    vertical-align: middle; /* ✅ FIX */
}

/* ACTION BUTTON RESPONSIVE */
td:last-child a{
    display:inline-block;
    margin:2px;
}
/* BUTTONS */
.btn{
    padding:6px 10px;
    border-radius:6px;
    text-decoration:none;
    color:#fff;
    font-size:12px;
    white-space:nowrap;
}

.btn-edit{ background:#0d6efd; }
.btn-delete{ background:#dc3545; }

.btn-edit:hover,
.btn-delete:hover{
    color:#000;
}

/* MOBILE */
@media(max-width:768px){
    .btn{
        flex:1 1 100%;
        text-align:center;
    }
}

/* ALERTS */
.alert-success{
    background:#e6f9f0;
    color:#0f5132;
    padding:12px;
    border-left:5px solid #198754;
    border-radius:6px;
    margin-bottom:15px;
}

.alert-error{
    background:#fdecea;
    color:#842029;
    padding:12px;
    border-left:5px solid #dc3545;
    border-radius:6px;
    margin-bottom:15px;
}

</style>

<div class="table-container">

<h3>Stock History</h3>

@if(session('success'))
<div class="alert-success">{{ session('success') }}</div>
@endif

@if ($errors->any())
<div class="alert-error">
    @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
    @endforeach
</div>
@endif

<div class="total-box" style="background:#198754;color:#fff;padding:10px;border-radius:5px;margin-bottom:15px;">
   <b>Total Quantity: {{ $total }}</b>
</div>

<input type="text" id="globalSearch" class="search-box" placeholder="Search product, category, date...">

<div id="table-data" class="table-wrapper">
    @include('backend.new_stock_arrival.partials.history_table')
</div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

// 🔍 LIVE SEARCH
$('#globalSearch').on('keyup', function(){
    fetchData(1);
});

// 🔥 PAGINATION CLICK (NO RELOAD)
$(document).on('click', '.pagination a', function(e){
    e.preventDefault();

    let page = $(this).attr('href').split('page=')[1];
    fetchData(page);
});

// 🔥 FETCH DATA FUNCTION
function fetchData(page = 1){

    let query = $('#globalSearch').val();

    $.ajax({
        url: window.location.pathname + "?page=" + page + "&search=" + query,
        success: function(data){
            $('#table-data').html(data);
        }
    });
}

</script>

@endsection