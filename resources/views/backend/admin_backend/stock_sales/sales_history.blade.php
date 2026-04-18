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

/* 🔥 TABLE WRAPPER (SCROLL ENABLED) */
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
    width:100%;
    border-collapse:collapse;
    min-width:700px;
}

/* 🔥 STICKY HEADER */
thead th{
    position: sticky;
    top: 0;
    background:#198754;
    color:#fff;
    z-index:10;
    padding:12px;
    text-align:left;
}

/* BODY */
td{
    padding:12px;
    border-bottom:1px solid #eee;
}

/* 🔥 ACTION BUTTON GROUP */
td:last-child{
    display:flex;
    flex-wrap:wrap;
    gap:5px;
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

.btn-edit{background:#0d6efd;}
.btn-delete{background:#dc3545;}

.btn-edit:hover,
.btn-delete:hover{
    color:#000;
}

/* 🔥 MOBILE RESPONSIVE */
@media(max-width:768px){
    .btn{
        flex:1 1 100%;
        text-align:center;
    }
}

/* TOTAL BOX */
.total-box{
    background:#198754;
    color:#fff;
    padding:12px;
    border-radius:6px;
    margin-bottom:15px;
    font-size:16px;
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

<h3>Sales History: {{ $product }} ({{ $category }})</h3>

@if(session('success'))
<div class="alert-success">
    {{ session('success') }}
</div>
@endif

@if ($errors->any())
<div class="alert-error">
    @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
    @endforeach
</div>
@endif


{{-- 🔥 TOTAL QUANTITY --}}
@php
    $totalQty = $sales->sum('quantity');
@endphp

<div class="total-box">
    🔥 Total Quantity Available For Sales: <b>{{ number_format($totalQty) }}</b>
</div>


{{-- 🔍 SEARCH --}}
<input type="text" id="search" class="search-box" placeholder="Search date, price...">


<div  id="table-data" class="table-wrapper">

 @include('backend.admin_backend.stock_sales.partials.history_table')

</div>



{{-- 🔍 SEARCH SCRIPT --}}

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function(){

    // 🔍 SEARCH
    $('#search').on('keyup', function(){
        let value = $(this).val();
        fetchData(1, value);
    });

    // 🔄 PAGINATION
    $(document).on('click', '.pagination a', function(e){
        e.preventDefault();

        let page = $(this).attr('href').split('page=')[1];
        let search = $('#search').val();

        fetchData(page, search);
    });

    function fetchData(page, search = ''){
        $.ajax({
            url: "?page=" + page + "&search=" + search,
            success: function(data){
                $('#table-data').html(data);
            }
        });
    }

});
</script>


@endsection