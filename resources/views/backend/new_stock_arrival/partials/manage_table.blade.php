<table>
<thead>
<tr>
<th>#</th>
<th>Product</th>
<th>Category</th>
<th>All Stock</th>
<th>Action</th>
</tr>
</thead>

<tbody>
@foreach($stocks as $key => $stock)
<tr>
<td>{{ ($stocks->currentPage() - 1) * $stocks->perPage() + $loop->iteration }}</td>
<td>{{ $stock->product_name }}</td>
<td>{{ $stock->category }}</td>
<td><strong>{{ $stock->total_quantity }}</strong></td>
<td>

<a href="{{ route('stocks.history', [$stock->product_name, $stock->category])}}" 
   class="action-btn btn-history">History</a>

<a href="{{ route('stocks.history', [$stock->product_name, $stock->category]) }}" 
   class="action-btn btn-edit">Edit</a>

<a href="{{ route('stocks.create') }}" 
   class="action-btn btn-add">Add</a>

</td>
</tr>
@endforeach
</tbody>
</table>

{{-- 🔥 PAGINATION --}}
<div style="margin-top:15px;">
    {{ $stocks->links('pagination::bootstrap-5') }}
</div>
