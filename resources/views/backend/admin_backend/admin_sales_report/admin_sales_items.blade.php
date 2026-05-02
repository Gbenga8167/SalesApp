@extends('backend.admin_backend.admin_dashboard')

@section('admin')

<style>
body {
    font-family: 'Poppins', sans-serif;
}

/* HEADER */
.page-title {
    background: #198754;
    color: #fff;
    padding: 12px 15px;
    border-radius: 8px 8px 0 0;
    font-weight: 600;
    font-size: 18px;
}

/* CARD */
.card {
    border-radius: 10px;
    overflow: hidden;
}

/* SEARCH */
.search-box {
    display: flex;
    gap: 10px;
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.form-control {
    font-size: 15px;
    font-weight: 500;
    padding: 12px;
}

/* TABLE */
.table th {
    font-weight: 600;
    font-size: 15px;
}

.table td {
    font-size: 15px;
    font-weight: 500;
}

/* TOTAL */
#grandTotal {
    color: #28a745;
    font-size: 32px;
    font-weight: bold;
}

/* RESPONSIVE */
.search-input {
    min-width: 250px;
}

.date-input {
    max-width: 180px;
}

@media (max-width: 768px) {
    .search-box {
        flex-direction: column;
        align-items: stretch;
    }

    .search-input,
    .date-input {
        width: 100% !important;
        max-width: 100% !important;
    }
}
</style>

<div class="container mt-3">

<div class="card shadow-sm">

    <div class="page-title">
        Admin Transaction Items
    </div>

    <!-- SEARCH -->
    <div class="search-box d-flex flex-wrap gap-2 align-items-center">

        <div class="position-relative flex-grow-1 search-input">
            <b>Search product/category...</b>
            <input type="text" id="search" class="form-control" placeholder="Search product/category...">
        </div>

        <div>
            <b>From</b>
            <input type="date" id="from" class="form-control date-input">
        </div>

        <div>
            <b>To</b>
            <input type="date" id="to" class="form-control date-input">
        </div>

    </div>

    <div class="card-body">

        <!-- ACTIONS -->
        <div class="d-flex justify-content-between mb-3">
            <a href="{{ route('admin.sales.history') }}" class="btn btn-secondary">← Back</a>

            <!-- 🔥 UPDATED BUTTON -->
            <a href="{{ url('/admin/receipt/' . $transaction->id) }}" class="btn btn-success">
                👁 VIEW & PRINT RECEIPT
            </a>
        </div>

        <!-- TABLE -->
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody id="itemsTable"></tbody>
            </table>
        </div>

        <!-- TOTAL -->
        <div class="mt-3 text-end">
            <h2><strong>Total: ₦<span id="grandTotal">0.00</span></strong></h2>
        </div>

    </div>
</div>

</div>

<script>

let transactionId = "{{ $transaction->id }}";

/* FORMAT MONEY */
function formatMoney(value){
    return parseFloat(value).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

/* LOAD ITEMS */
function loadItems(){

    $.get(`/admin/sales-items/${transactionId}`, {
        search: $('#search').val(),
        from: $('#from').val(),
        to: $('#to').val()
    }, function(res){

        let html = '';

        res.data.forEach((item, index) => {
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.product_name}</td>
                    <td>${item.category ?? ''}</td>
                    <td>${item.quantity}</td>
                    <td>₦${formatMoney(item.price)}</td>
                    <td>₦${formatMoney(item.subtotal)}</td>
                </tr>
            `;
        });

        $('#itemsTable').html(html);

        $('#grandTotal').text(formatMoney(res.total_amount));
    });
}

/* INIT */
loadItems();

/* SEARCH + FILTER */
$('#search, #from, #to').on('keyup change', function(){
    loadItems();
});

</script>

@endsection