<table>
<thead>
<tr>
<th>#</th>
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
@foreach($records as $key => $row)
<tr>
<td>{{ $records->firstItem() + $key }}</td>
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

{{-- 🔥 PAGINATION --}}
<div style="margin-top:15px;">
    {{ $records->links('pagination::bootstrap-5') }}
</div>
