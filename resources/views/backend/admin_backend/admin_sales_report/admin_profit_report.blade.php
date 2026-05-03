@extends('backend.admin_backend.admin_dashboard')

@section('admin')

<div class="container-fluid">

<div class="card">

    <div class="p-3 bg-dark text-white">
        Profit Report
    </div>

    <div class="p-3">
 
        <input type="text" id="search" class="form-control mb-2" placeholder="Search...">

        <div class="d-flex gap-2 mb-3">
            <input type="date" id="from" class="form-control">
            <input type="date" id="to" class="form-control">
        </div>

        <h5>Total Sales: ₦<span id="totalSales">0</span></h5>
        <h5>Total Cost: ₦<span id="totalCost">0</span></h5>
        <h3 style="color:green;">Profit: ₦<span id="totalProfit">0</span></h3>

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
</script>

@endsection