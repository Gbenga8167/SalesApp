@extends('backend.admin_backend.admin_dashboard')

@section('admin')

<div class="container-fluid">

<div class="card">

    <div class="p-3 bg-dark text-white" style="font-size:25px">
        Profit Report
    </div>
<div class="p-3">

<!-- 🔍 SEARCH -->
<div class="mb-2">
    <b>Search (Salesperson / Product / Category)</b>
    <input type="text" id="search" class="form-control" placeholder="Search...">
</div>

<!-- 📅 DATE FILTER -->
<div class="d-flex gap-2 mb-3">

    <div style="flex:1;">
        <b>From</b>
        <input type="date" id="from" class="form-control">
    </div>

    <div style="flex:1;">
        <b>To</b>
        <input type="date" id="to" class="form-control">
    </div>

</div>


<!-- =========================================
EXPORT BUTTONS
========================================= -->
<div class="mb-3 d-flex gap-2">

    <!-- CSV BUTTON -->
    <button class="btn btn-success" id="exportCSV">
        Export CSV
    </button>

    <!-- PDF BUTTON -->
    <button class="btn btn-danger" id="exportPDF">
        Export PDF
    </button>

</div>

    <h5>Total Sales: ₦<span id="totalSales">0</span></h5>
    <h5 style="color:red;">Total Cost: ₦<span id="totalCost">0</span></h5>
    <h3 style="color:green;">Profit: ₦<span id="totalProfit">0</span></h3>

    <!-- ✅ ADD THIS WRAPPER -->
    <div class="table-responsive">
        <table class="table table-bordered" id="profitTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Salesperson</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Qty</th>
                    <th>Selling</th>
                    <th>Cost</th>
                    <th>Profit</th>
                    <th>Date</th>
                </tr>
            </thead>
        </table>
    </div>

</div>

</div>


<div class="card mb-3">
    <div class="p-3 bg-success text-white" style="font-size:25px;">
        Profit Chart
    </div>

    <div class="p-3">
        <canvas id="profitChart" height="100"></canvas>
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

$(document).ready(function(){

let table = $('#profitTable').DataTable({
    processing: true,
    serverSide: true,
    //responsive: true,
    //autoWidth: false,

    ajax: {
        url: "{{ route('admin.profit.report.data') }}",
        data: function(d){
            d.from = $('#from').val();
            d.to = $('#to').val();
        },
        dataSrc: function(json){

            $('#totalSales').text(formatMoney(json.totalSales));
            $('#totalCost').text(formatMoney(json.totalCost));
            $('#totalProfit').text(formatMoney(json.totalProfit));

            return json.data;
        }
    },

    columns: [
    { data: null },
    { data: 'salesperson_name' }, 
    { data: 'product_name' },
    { data: 'category' },
    { data: 'quantity' },
    {
        // data: 'price'
        data: 'subtotal',
        render: function(data){
            return "₦" + formatMoney(data);
        }
    },
    {
        //data: 'cost_price',
        data: 'total_cost',
        render: function(data){
            return "₦" + formatMoney(data);
        }
    },
    {
        data: 'profit',
        render: function(data){
            return "₦" + formatMoney(data);
        }
    },
    { data: 'created_at' },
],

    columnDefs: [{
        targets: 0,
        render: function(data, type, row, meta){
            return meta.row + 1;
        }
    }]
});

// 🔍 SEARCH
$('#search').keyup(function(){
    table.search(this.value).draw();
});

// 📅 DATE FILTER
$('#from, #to').change(function(){
    table.draw();
});

});




/* =========================
   PROFIT CHART
========================= */
let chart;

// LOAD CHART
function loadChart(){

    let search = $('#search').val();
    let from = $('#from').val();
    let to = $('#to').val();

    $.get("{{ route('admin.profit.chart.data') }}", {
        search: search,
        from: from,
        to: to
    }, function(res){

        if(chart){
            chart.destroy();
        }

        let ctx = document.getElementById('profitChart').getContext('2d');

        chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: res.labels,
                datasets: [{
                    label: 'Profit (₦)',
                    data: res.profits,
                   
                }]
            }
        });

    });
}

// 🔥 TRIGGERS
$('#search').keyup(function(){
    loadChart();
});

$('#from, #to').change(function(){
    loadChart();
});

// INITIAL LOAD
loadChart();



// =========================================
// EXPORT CSV
// =========================================
$('#exportCSV').click(function(){

    // Get filters
    let search = $('#search').val();
    let from = $('#from').val();
    let to = $('#to').val();

    // Redirect to CSV export route
    window.location.href =
        "{{ route('export.profit.csv') }}"
        + "?search=" + search
        + "&from=" + from
        + "&to=" + to;
});



// =========================================
// EXPORT PDF
// =========================================
$('#exportPDF').click(function(){

    // Get filters
    let search = $('#search').val();
    let from = $('#from').val();
    let to = $('#to').val();

    // Redirect to PDF export route
    window.location.href =
        "{{ route('export.profit.pdf') }}"
        + "?search=" + search
        + "&from=" + from
        + "&to=" + to;
});

</script>

@endsection