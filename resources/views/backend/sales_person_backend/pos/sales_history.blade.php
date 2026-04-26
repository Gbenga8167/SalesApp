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
    background: linear-gradient(45deg, #343a40, #212529);
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
}

.table td {
    font-size: 15px;
    font-weight: 500;
    vertical-align: middle;
}

/* BUTTON */
.btn-primary {
    font-weight: 500;
}

/* 🔥 FIX DATATABLE "+" BUTTON ALIGNMENT */
table.dataTable.dtr-inline.collapsed > tbody > tr > td:first-child:before,
table.dataTable.dtr-inline.collapsed > tbody > tr > th:first-child:before {
    top: 50%;
    left: 8px;
    transform: translateY(-50%);
    margin-top: 0;
}

/* Give space so it doesn't overlap S/N */
table.dataTable.dtr-inline.collapsed > tbody > tr > td:first-child,
table.dataTable.dtr-inline.collapsed > tbody > tr > th:first-child {
    padding-left: 30px !important;
}

/* 🔥 SMALLER PAGINATION ON MOBILE ONLY */
@media (max-width: 768px) {

    .dataTables_paginate {
        text-align: center !important;
    }

    .dataTables_paginate .paginate_button {
        padding: 3px 8px !important;
        font-size: 12px !important;
        margin: 2px !important;
    }

    /* spacing */
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 10px;
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

        <!-- 🔍 SEARCH -->
        <div class="search-box d-flex flex-wrap gap-2 align-items-center">

            <!-- SEARCH -->
            <div class="flex-grow-1 search-input">
                <b>Search receipt/payment...</b>
                <input type="text" id="search" class="form-control" placeholder="Search receipt, payment, etc...">
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
        

        <!-- TABLE -->
    <div class="table-responsive">
        <table class="table table-sm table-bordered  dt-responsive nowrap"  id="historyTable">
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

        responsive: false, // ❌ REMOVE "+" BUTTON
        scrollX: true,     // ✅ ENABLE SCROLL

        pagingType: "simple", // ✅ ONLY PREV / NEXT

        ajax: {
            url: "{{ route('sales.person.history.data') }}",
            data: function(d){
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

        pageLength: 2
    });

    // ✅ USE DATATABLES BUILT-IN SEARCH
    $('#search').on('keyup', function () {
        table.search(this.value).draw();
    });

    // 📅 DATE FILTER
    $('#from, #to').on('change', function () {
        table.draw();
    });

});
</script>

@endsection