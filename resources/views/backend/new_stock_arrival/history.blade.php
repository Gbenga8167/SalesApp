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
    width:100%;
    border-collapse:collapse;
    min-width:900px;
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
}

/* ACTION BUTTON RESPONSIVE */
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

<div class="table-wrapper">
<table>
<thead>
<tr>
<th>Product</th>
<th>Category</th>
<th>Qty</th>
<th>Description</th>
<th>Cost Price</th>
<th>Date Purchased</th>
<th>Date Created</th>
<th>Image</th>
<th>Action</th>
</tr>
</thead>

<tbody>
@foreach($records as $row)
<tr>
<td>{{ $row->product_name }}</td>
<td>{{ $row->category }}</td>
<td>{{ $row->quantity }}</td>
<td>{{ $row->description }}</td>
<td>₦{{ number_format($row->cost_price, 2) }}</td>
<td>{{ \Carbon\Carbon::parse($row->purchase_date)->format('d M Y') }}</td>
<td>{{ $row->created_at->format('d M Y') }}</td>

<td>
@if($row->image)
<img src="{{ asset('uploads/NewStockArrival/'.$row->image) }}" width="50">
@endif
</td>

<td>
<a href="{{ route('stocks.edit', $row->id) }}" class="btn btn-edit">Edit</a>

<a href="{{ route('stocks.delete', $row->id) }}" 
   class="btn btn-delete" id="delete">Delete</a>
</td>

</tr>
@endforeach
</tbody>
</table>
</div>

</div>

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