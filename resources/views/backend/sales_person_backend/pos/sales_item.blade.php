@extends('backend.sales_person_backend.sales_person_dashboard')
@section('salesperson')

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

/* Desktop default */
.search-box {
    display: flex;
    gap: 10px;
}

/* make search take available space */
.search-input {
    min-width: 250px;
}

/* date inputs */
.date-input {
    max-width: 180px;
}

/* 🔥 MOBILE STACK */
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
        Transaction Items
    </div>

    <!-- SEARCH -->

<div class="search-box d-flex flex-wrap gap-2 align-items-center">

    <!-- SEARCH -->
    <div class="position-relative flex-grow-1 search-input">
        <b>Search product/category...</b>
        <input type="text" id="search" class="form-control" placeholder="Search product/category...">
        <div id="suggestionsBox" class="list-group position-absolute w-100"></div>
    </div>

    <!-- FROM -->
    <div>
        <b>From</b>
        <input type="date" id="from" class="form-control date-input">
        
    </div>

    <!-- TO -->
    <div>
        <b>To</b>
        <input type="date" id="to" class="form-control date-input">
    </div>

</div>



    <div class="card-body">

        <div class="d-flex justify-content-between mb-3">
            <a href="{{ route('sales.person.history') }}" class="btn btn-secondary">← Back</a>

            <button id="printBtn" class="btn btn-success">🖨 Print Receipt</button>
        </div>

        <div class="table-responsive">
    <table class="table table-sm ">
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
        <div class="mt-3 text-end">
            <h2><strong>Total: ₦<span id="grandTotal">0.00</span></strong></h2>
        </div>

        <!-- SIMPLE PAGINATION -->
        <div id="paginationLinks" class="d-flex justify-content-between align-items-center mt-3"></div>

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

    $.get(`/sales-items/${transactionId}`, {
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

        // TOTAL
        $('#grandTotal').text(formatMoney(res.total_amount));
    });
}


/* INIT */
loadItems();

/* SEARCH */
$('#search, #from, #to').on('keyup change', function(){
    loadItems();
});

/* SUGGESTIONS */
$('#search').on('keyup', function () {

    let query = $(this).val().trim();

    // 🔥 CLEAR if empty
    if (query.length < 1) {
        $('#suggestionsBox').html('').hide();
        return;
    }

    $.get(`/sales-items-suggestions/${transactionId}`, { q: query }, function (res) {

        let html = '';

        // 🔥 ensure backend result is filtered BEFORE showing
        res.forEach(item => {

            // only show matches (extra safety)
            if (item.toLowerCase().includes(query.toLowerCase())) {

                html += `<a class="list-group-item suggestion">${item}</a>`;
            }
        });

        // 🔥 if nothing matches
        if (html === '') {
            html = `<div class="list-group-item text-muted">No match found</div>`;
        }

        $('#suggestionsBox').html(html).show();
    });
});


// 🔥 CLICK SUGGESTION (CLEAN + RESET)
$(document).on('click', '.suggestion', function () {

    let value = $(this).text();

    $('#search').val(value);
    $('#suggestionsBox').html('').hide(); // 🔥 CLEAR IMMEDIATELY

    loadItems();
});


// 🔥 CLICK OUTSIDE = CLEAR BOX
$(document).on('click', function (e) {
    if (!$(e.target).closest('#search').length) {
        $('#suggestionsBox').html('').hide();
    }
});



/* PRINT */
$('#printBtn').click(function(){
    window.open('/receipt/' + transactionId, '_blank');
});

</script>

@endsection