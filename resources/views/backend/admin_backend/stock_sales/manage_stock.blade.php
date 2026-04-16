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

/* TABLE WRAPPER (SCROLL 🔥) */
.table-wrapper{
    max-height:500px;
    overflow-y:auto;
    overflow-x:auto;
}

/* TOTAL BOX */
.total-box{
    background:#198754;
    color:#fff;
    padding:12px 15px;
    border-radius:8px;
    margin-bottom:15px;
    font-size:16px;
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
    min-width:800px;
}

/* 🔥 STICKY HEADER */
thead th{
    position: sticky;
    top: 0;
    background:#198754;
    color:#fff;
    z-index: 10;
    padding:12px;
    text-align:left;
}

/* BODY */
td{
    padding:12px;
    border-bottom:1px solid #eee;
}

/* BUTTON GROUP RESPONSIVE 🔥 */
td:last-child{
    display:flex;
    flex-wrap:wrap;
    gap:5px;
}

/* BUTTONS */
.action-btn{
    padding:6px 10px;
    border-radius:6px;
    text-decoration:none;
    color:#fff;
    font-size:12px;
    white-space:nowrap;
}

.btn-history{ background: #198754; }
.btn-edit{ background: #0d6efd; }
.btn-add{ background: #dc3545; }

.btn-history:hover,
.btn-edit:hover,
.btn-add:hover{
    color:#000;
}

/* MOBILE FIX */
@media(max-width:768px){
    .action-btn{
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

<h3>Manage Stock For Sale</h3>

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


{{-- 🔥 TOTAL SUMMARY --}}
@php
    $totalStock = $sales->sum('available_stock');
@endphp

<div class="total-box">
    🔥 Total Goods In Stock: <b>{{ number_format($totalStock) }}</b>
</div>


{{-- 🔍 SEARCH --}}
<input type="text" id="globalSearch" class="search-box" placeholder="Search product, category...">


{{-- 🔥 WRAP TABLE --}}
<div class="table-wrapper">

<table>
<thead>
<tr>
<th>#</th>
<th style="white-space:nowrap;">Product Name</th>
<th>Category</th>
<th>All Stock</th>
<th>Goods in Stock</th>
<th>Goods On Sales</th>
<th>Selling Price</th>
<th>Expected Amount (₦)</th>
<th>Action</th>
</tr>
</thead>

<tbody>

@foreach($sales as $key => $row)
<tr>

<td>{{ ($sales->currentPage() - 1) * $sales->perPage() + $loop->iteration }}</td>

<td>{{ $row->product_name }}</td>

<td>{{ $row->category }}</td>

<td>
    <strong style="color:red;">
        {{ $row->available_stock + $row->total_sold }}
    </strong>
</td>

<td>
    <strong>{{ $row->available_stock }}</strong>
</td>

<td>
    {{ $row->total_sold }}
</td>

<td>
    ₦{{ number_format($row->selling_price, 2) }}
</td>

<td>
    ₦{{ number_format($row->total_amount, 2) }}
</td>

<td>

<a href="{{ route('sales.history', [$row->product_name, $row->category]) }}" 
   class="action-btn btn-history">
   History
</a>

<a href="{{ route('sales.history', [$row->product_name, $row->category]) }}" 
   class="action-btn btn-edit">
   Edit
</a>

<a href="{{ route('create.sale') }}" 
   class="action-btn btn-add">
   Add
</a>

</td>

</tr>
@endforeach

</tbody>
</table>

</div>

{{-- 🔥 PAGINATION --}}
<div style="margin-top:15px;">
    {{ $sales->links('pagination::bootstrap-5') }}
</div>

</div>


{{-- 🔍 SEARCH SCRIPT --}}
<script>
document.getElementById('globalSearch').addEventListener('keyup', function () {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll("tbody tr");

    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(value) ? "" : "none";
    });
});
</script>

@endsection