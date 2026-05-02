@extends('backend.admin_backend.admin_dashboard')

@section('admin')

<style>
body {
    font-family: 'Poppins', sans-serif;
}

.card {
    border-radius: 10px;
    overflow: hidden;
}

.page-title {
    background: #198754;
    color: #fff;
    padding: 12px 15px;
    font-weight: 600;
    font-size: 18px;
}

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

.table th {
    font-weight: 600;
    font-size: 15px;
    white-space: nowrap;
}

td{
    padding:12px;
    border-bottom:1px solid #eee;
}

.dataTables_wrapper {
    width: 100% !important;
}

.dataTables_scrollBody {
    max-height: 400px;
}

table.dataTable thead th {
    position: sticky;
    top: 0;
    background: #198754;
    color: white;
    z-index: 10;
}

.btn-primary {
    font-weight: 500;
}

.dataTables_paginate {
    margin-top: 10px;
    font-size: 13px;
}

.dataTables_paginate .paginate_button {
    padding: 4px 10px !important;
    font-size: 12px !important;
}

@media (max-width: 768px) {
    .search-box {
        flex-direction: column;
    }
}
</style>

<div class="container-fluid">

<div class="card shadow-sm">

<!-- TOP TOTAL -->
<div class="px-3 py-2 d-flex justify-content-end">
    <div style="
        background:#e8f5e9;
        padding:10px 18px;
        border-radius:8px;
        font-weight:700;
        font-size:16px;
        color:#198754;
    ">
        TOTAL SALES: ₦<span id="totalSalesTop">0.00</span>
    </div>
</div>

    <div class="page-title">
        Admin Sales History
    </div>

    <div class="card-body">

        <!-- SEARCH -->
        <div class="search-box d-flex flex-wrap gap-2 align-items-center">

            <div class="flex-grow-1 search-input">
                <b>Search product/category/salesperson...</b>
                <input type="text" id="search" class="form-control" placeholder="Search anything...">
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
                        <th>Salesperson</th>
                        <th>Receipt No</th>
                        <th>Total</th>
                        <th>Payment Method</th>
                        <th>Transaction Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>

        <!-- BOTTOM TOTAL -->
        <div class="mt-3 text-end">
            <h3>
                <strong>
                    TOTAL SALES: ₦<span id="totalSalesBottom">0.00</span>
                </strong>
            </h3>
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
        pagingType: "simple",
        scrollX: true,
        scrollCollapse: true,

ajax: {
    url: "{{ route('admin.sales.history.data') }}",
    data: function(d){
        d.search_value = $('#search').val();
        d.from = $('#from').val();
        d.to = $('#to').val();
    },

    dataSrc: function(json){

        $('#totalSalesTop').text(formatMoney(json.totalSales));
        $('#totalSalesBottom').text(formatMoney(json.totalSales));

        return json.data;
    }
},

        columns: [
            { data: null },
            { data: 'salesperson_name' },
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
                    return `<a href="/admin/sales-items-page/${data}" class="btn btn-sm btn-primary">Sales History</a>`;
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