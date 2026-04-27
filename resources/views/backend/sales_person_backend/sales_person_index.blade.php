@extends('backend.sales_person_backend.sales_person_dashboard')

@section('salesperson')

<style>

/* =========================
   GLOBAL
========================= */
.container-fluid{
    padding: 15px;
}

/* GRID */
.row-cards,
.chart-row{
    display: grid;
    gap: 15px;
}

.row-cards{
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
}

.chart-row{
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    margin-top: 20px;
}

/* =========================
   STAT CARDS
========================= */
.stat-card {
    border-radius: 16px;
    padding: 28px; /* 🔥 taller */
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
    transition: all 0.4s ease;
    color: #fff;

    /* 🔥 animation */
    opacity: 0;
    transform: translateY(20px);
    animation: fadeUp 0.6s ease forwards;
}

/* DELAY EFFECT */
.stat-card:nth-child(1){ animation-delay: 0.1s; }
.stat-card:nth-child(2){ animation-delay: 0.3s; }
.stat-card:nth-child(3){ animation-delay: 0.5s; }

@keyframes fadeUp{
    to{
        opacity: 1;
        transform: translateY(0);
    }
}

.stat-card:hover {
    transform: translateY(-5px);
}

/* TEXT */
.stat-title {
    font-size: 12px;
    font-weight: 900; /* 🔥 bold */
    letter-spacing: 1px;
    text-transform: uppercase;
}

.stat-value {
    font-size: 32px;
    font-weight: 900;
    margin-top: 6px;
}

.icon {
    font-size: 30px;
}

/* COLORS */
.card-sales {
    background: linear-gradient(135deg, #0f766e, #14b8a6);
}
.card-transactions {
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
}
.card-items {
    background: linear-gradient(135deg, #6d28d9, #a855f7);
}

/* =========================
   CHART CARDS
========================= */
.chart-card{
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    padding: 10px;
    height: 240px;
    display: flex;
    flex-direction: column;
}

.chart-header{
    font-weight: 900;
    font-size: 16px;
    margin-bottom: 6px;
    text-transform: uppercase;
}

.chart-card canvas{
    flex: 1;
    max-height: 180px;
}

</style>

<div class="container-fluid">

<div class="row-cards">

    <div class="stat-card card-sales">
        <div>
            <div class="stat-title">Today Sales</div>
            <div class="stat-value" id="todaySales">₦0</div>
        </div>
        <div class="icon">💰</div>
    </div>

    <div class="stat-card card-transactions">
        <div>
            <div class="stat-title">Transactions</div>
            <div class="stat-value" id="totalTransactions">0</div>
        </div>
        <div class="icon">🧾</div>
    </div>

    <div class="stat-card card-items">
        <div>
            <div class="stat-title">Items Sold</div>
            <div class="stat-value" id="itemsSold">0</div>
        </div>
        <div class="icon">📦</div>
    </div>

</div>

<div class="chart-row">

    <div class="chart-card">
        <div class="chart-header">Sales Trend (Today)</div>
        <canvas id="salesChart"></canvas>
    </div>

    <div class="chart-card">
        <div class="chart-header">Payment Methods</div>
        <canvas id="paymentChart"></canvas>
    </div>

    <div class="chart-card">
        <div class="chart-header">Daily Sales</div>
        <canvas id="dailySalesChart"></canvas>
    </div>

    <div class="chart-card">
        <div class="chart-header">Top Products</div>
        <canvas id="topProductsChart"></canvas>
    </div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

function formatMoney(value){
    return parseFloat(value).toLocaleString();
}

$(document).ready(function(){

    $.get("{{ route('sales.dashboard.data') }}", function(res){

        $('#todaySales').text("₦" + formatMoney(res.todaySales));
        $('#totalTransactions').text(res.totalTransactions);
        $('#itemsSold').text(res.itemsSold);

        new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: res.salesChart.map(i => i.hour + ":00"),
                datasets: [{
                    data: res.salesChart.map(i => i.total),
                    borderWidth: 2,
                    fill: false
                }]
            },
            options:{
                plugins:{
                    tooltip:{
                        enabled:true,
                        mode:'index',
                        intersect:false
                    }
                }
            }
        });

    });

});

// PAYMENT
fetch("{{ route('sales.payment.chart') }}")
.then(r=>r.json())
.then(data=>{
    new Chart(document.getElementById('paymentChart'), {
        type:'pie',
        data:{
            labels:data.map(i=>i.payment_method),
            datasets:[{data:data.map(i=>i.total)}]
        },
        options:{
            plugins:{ tooltip:{ enabled:true } }
        }
    });
});

// DAILY
fetch("{{ route('sales.daily.chart') }}")
.then(r=>r.json())
.then(data=>{
    new Chart(document.getElementById('dailySalesChart'), {
        type:'line',
        data:{
            labels:data.map(i=>i.date),
            datasets:[{data:data.map(i=>i.total), fill:true}]
        }
    });
});

// TOP PRODUCTS
fetch("{{ route('sales.top.products.chart') }}")
.then(r=>r.json())
.then(data=>{
    new Chart(document.getElementById('topProductsChart'), {
        type:'bar',
        data:{
            labels:data.map(i=>i.product_label),
            datasets:[{data:data.map(i=>i.total_qty)}]
        },
        options:{
            plugins:{
                tooltip:{
                    callbacks:{
                        label: ctx => "Sold: " + ctx.raw
                    }
                }
            }
        }
    });
});

</script>

@endsection