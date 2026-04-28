
<div id="table-wrapper">
    <div id="table-data">
<table>
<thead>
<tr>
<th>#</th>
<th style="white-space:nowrap;">Product Name</th>
<th>Category</th>
<th>All Stock</th>
<th>Goods in Stock</th>
<th>Goods On Sales</th>
<th>Real Time Stock</th>
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
    <strong style="color:green;">
        {{ $row->real_time_stock }}
    </strong>
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

{{-- 🔥 PAGINATION --}}
<div style="margin-top:15px;">
    {{ $sales->links('pagination::bootstrap-5') }}
</div>

    </div>
</div>




<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

function reloadTable() {
    $.ajax({
        url: window.location.href,
        type: "GET",
        success: function (response) {

            // 🔥 extract ONLY inner table content safely
            let newContent = $(response).find("#table-data").html();

            if (newContent) {
                $("#table-data").html(newContent);
            }
        }
    });
}

// 🔥 wait for DOM fully read REFRESH PAGE EVERY 5 SECONDS
$(document).ready(function () {
    setInterval(reloadTable, 5000);
});

</script>
