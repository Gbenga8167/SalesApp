@extends('backend.sales_person_backend.sales_person_dashboard')

@section('salesperson')

<style>
body {
    font-family: 'Poppins', sans-serif;
}

/* CARD */
.card {
    border-radius: 10px;
    overflow: hidden;
}

/* HEADER */
.page-title {
    background: #198754;
    color: #fff;
    padding: 12px 15px;
    font-weight: 600;
    font-size: 18px;
}

/* SEARCH */
.search-box {
    display: flex;
    gap: 10px;
    padding: 15px;
    border-bottom: 1px solid #eee;
}

/* INPUTS */
.form-control {
    font-size: 15px;
    font-weight: 500;
    padding: 12px;
}

/* TABLE */
.table th {
    font-weight: 600;
    font-size: 15px;
    white-space: nowrap;
}

td{
    padding:12px;
    border-bottom:1px solid #eee;
}

/* FULL WIDTH FIX */
.dataTables_wrapper {
    width: 100% !important;
}

/* SCROLL TABLE AREA */
.dataTables_scrollBody {
    max-height: 400px;
}

/* STICKY HEADER */
table.dataTable thead th {
    position: sticky;
    top: 0;
    background: #198754;
    color: white;
    z-index: 10;
}

/* BUTTON */
.btn-primary {
    font-weight: 500;
}

/* ❌ REMOVE + RESPONSIVE ICON COMPLETELY */
table.dataTable.dtr-inline.collapsed > tbody > tr > td:first-child:before,
table.dataTable.dtr-inline.collapsed > tbody > tr > th:first-child:before {
    display: none !important;
}

/* PREV / NEXT SMALLER */
.dataTables_paginate {
    margin-top: 10px;
    font-size: 13px;
}

.dataTables_paginate .paginate_button {
    padding: 4px 10px !important;
    font-size: 12px !important;
}

/* MOBILE STACK */
@media (max-width: 768px) {
    .search-box {
        flex-direction: column;
    }
}
</style>

<div class="container-fluid">

<div class="card shadow-sm">

    <div class="page-title">
        Sales History
    </div>

    <div class="card-body">

        <!-- SEARCH -->
        <div class="search-box d-flex flex-wrap gap-2 align-items-center">

            <div class="flex-grow-1 search-input">
                <b>Search receipt/payment...</b>
                <input type="text" id="search" class="form-control" placeholder="Search receipt, payment, etc...">
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

        <!-- TABLE -->
        <div class="table-responsive">
            <table class="table table-sm table-bordered nowrap" id="historyTable" style="width:100%">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Receipt No</th>
                        <th>Total</th>
                        <th>Payment Method</th>
                        <th>Transaction Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>

    </div>
</div>

</div>


<script>

function formatMoney(value){
    return parseFloat(value).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

$(document).ready(function () {

    let table = $('#historyTable').DataTable({
        processing: true,
        serverSide: true,

        pagingType: "simple", // Prev / Next only

        scrollX: true,       // horizontal scroll
        scrollCollapse: true,

        ajax: {
            url: "{{ route('sales.person.history.data') }}",
            data: function(d){
                d.search_value = $('#search').val();
                d.from = $('#from').val();
                d.to = $('#to').val();
            }
        },

        columns: [
            { data: null },
            { data: 'receipt_no' },
            { 
                data: 'total_amount',
                render: function(data){
                    return "₦" + formatMoney(data);
                }
            },
            { data: 'payment_method' },
            { data: 'created_at' },
            { 
                data: 'id',
                render: function(data){
                    return `<a href="/sales-items-page/${data}" class="btn btn-sm btn-primary">View Items</a>`;
                }
            }
        ],

        columnDefs: [{
            targets: 0,
            render: function (data, type, row, meta) {
                return meta.row + 1 + meta.settings._iDisplayStart;
            }
        }],

        pageLength: 10
    });

    $('#search').on('keyup', function () {
        table.search(this.value).draw();
    });

    $('#from, #to').on('change', function () {
        table.draw();
    });

});

</script>

@endsection