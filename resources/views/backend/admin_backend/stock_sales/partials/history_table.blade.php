<table>
<thead>
<tr>
<th>#</th>
<th>Product</th>
<th>Category</th>
<th>Quantity</th>
<th>Selling Price</th>
<th>Amount (₦)</th>
<th>Date</th>
<th>Action</th>
</tr>
</thead>

<tbody>

@foreach($sales as $key => $row)
<tr>

<td>{{ ($sales->currentPage() - 1) * $sales->perPage() + $loop->iteration }}</td>

<td>{{ $row->product_name }}</td>

<td>{{ $row->category }}</td>

<td>{{ $row->quantity }}</td>

<td>₦{{ number_format($row->selling_price, 2) }}</td>

<td>₦{{ number_format($row->amount, 2) }}</td>

<td>{{ $row->created_at->format('d M Y') }}</td>

<td>

{{-- EDIT --}}
<a href="{{ route('sales.edit', $row->id) }}" class="btn btn-edit">
Edit
</a>

{{-- DELETE --}}
<a href="{{ route('sales.delete', $row->id) }}" 
   class="btn btn-delete"   id="delete">
Delete
</a>

</td>

</tr>
@endforeach

</tbody>
</table>

{{-- 🔥 PAGINATION --}}
<div style="margin-top:15px;">
    {{ $sales->links('pagination::bootstrap-5') }}
</div>
