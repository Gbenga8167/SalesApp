@extends('backend.admin_backend.admin_dashboard')
@section('admin')

<style>
.container-fluid{ padding: 15px; }

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

/* CARDS */
.stat-card {
    border-radius: 10px;
    padding: 28px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
    transition: all 0.4s ease;
    color: #fff;

    opacity: 0;
    transform: translateY(20px);
    animation: fadeUp 0.6s ease forwards;
}

.stat-card:nth-child(1){ animation-delay: 0.1s; }
.stat-card:nth-child(2){ animation-delay: 0.3s; }
.stat-card:nth-child(3){ animation-delay: 0.5s; }

@keyframes fadeUp{
    to{ opacity:1; transform:translateY(0); }
}

.stat-title{
    font-size:12px;
    font-weight:900;
    text-transform:uppercase;
}

.stat-value{
    font-size:32px;
    font-weight:900;
}

.card-sales { background: linear-gradient(135deg,#0f766e,#14b8a6); }
.card-transactions { background: linear-gradient(135deg,#1e3a8a,#2563eb); }
.card-items { background: linear-gradient(135deg,#6d28d9,#a855f7); }

.chart-card{
    background:#fff;
    border-radius:14px;
    box-shadow:0 6px 18px rgba(0,0,0,0.08);
    padding:10px;
    height:240px;
    display:flex;
    flex-direction:column;
}

.chart-header{
    font-weight:900;
    font-size:16px;
    margin-bottom:6px;
}

.chart-card canvas{
    flex:1;
    max-height:180px;
}
</style>

<div class="container-fluid">

<!-- CARDS -->
<div class="row-cards">

    <div class="stat-card card-sales">
        <div>
            <div class="stat-title">Total Sales Today</div>
            <div class="stat-value" id="todaySales">₦0</div>
        </div>
        <div>💰</div>
    </div>

    <div class="stat-card card-transactions">
        <div>
            <div class="stat-title">Total Transactions</div>
            <div class="stat-value" id="totalTransactions">0</div>
        </div>
        <div>🧾</div>
    </div>

    <div class="stat-card card-items">
        <div>
            <div class="stat-title">Items Sold</div>
            <div class="stat-value" id="itemsSold">0</div>
        </div>
        <div>📦</div>
    </div>

</div>

<!-- CHARTS -->
<div class="chart-row">

    <div class="chart-card">
        <div class="chart-header">Sales Trend (All - Today)</div>
        <canvas id="salesChart"></canvas>
    </div>

    <div class="chart-card">
        <div class="chart-header">Payment Methods</div>
        <canvas id="paymentChart"></canvas>
    </div>

    <div class="chart-card">
        <div class="chart-header">Last 7 Days Sales</div>
        <canvas id="dailySalesChart"></canvas>
    </div>

    <div class="chart-card">
        <div class="chart-header">Top Products</div>
        <canvas id="topProductsChart"></canvas>
    </div>

    <div class="card mb-3">
    <div class="p-3 bg-success text-white chart-header">
        Profit Chart
    </div>

    <div class="p-3">
        <canvas id="profitChart" height="200"></canvas>
    </div>
</div>
</div>

</div>


<script>

function formatMoney(value){
    return Number(value || 0).toLocaleString('en-NG', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

/* =========================
   GLOBAL CHART INSTANCES
========================= */

let salesChartInstance;
let paymentChartInstance;
let dailyChartInstance;
let topChartInstance;
let profitChartInstance;

/* =========================
   LIVE DASHBOARD
========================= */

function loadDashboard(){

    $.get("{{ route('admin.dashboard.data') }}", function(res){

        // ===== CARDS =====
        $('#todaySales').text("₦" + formatMoney(res.todaySales));
        $('#totalTransactions').text(res.totalTransactions);
        $('#itemsSold').text(res.itemsSold);

        // ===== SALES CHART =====

        let labels = res.salesChart.map(i => i.hour);
        let totals = res.salesChart.map(i => i.total);

        // CREATE ONLY ONCE
        if(!salesChartInstance){

            salesChartInstance = new Chart(document.getElementById('salesChart'), {

                type:'line',

                data:{
                    labels: labels,
                    datasets:[{
                        label:"Total Sales (₦)",
                        data: totals,
                        tension:0.4,
                        borderWidth:2
                    }]
                },

                options:{
                    responsive:true,

                    plugins:{
                        tooltip:{
                            callbacks:{
                                title: function(context){
                                    return "Time: " + context[0].label;
                                },
                                label: function(context){
                                    let value = context.raw || 0;

                                    return "Sales: ₦" + Number(value).toLocaleString('en-NG', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            }
                        }
                    },

                    scales:{
                        y:{
                            beginAtZero:true,
                            ticks:{
                                callback: function(value){
                                    return "₦" + Number(value).toLocaleString('en-NG', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            }
                        }
                    }
                }

            });

        } else {

            // UPDATE EXISTING CHART
            salesChartInstance.data.labels = labels;
            salesChartInstance.data.datasets[0].data = totals;
            salesChartInstance.update();

        }

    });
}

/* =========================
   PAYMENT CHART
========================= */

function loadPayment(){

fetch("{{ route('admin.payment.chart') }}")
.then(r=>r.json())
.then(data=>{

    paymentChartInstance = new Chart(document.getElementById('paymentChart'), {

        type:'pie',

        data:{
            labels:data.map(i=>i.payment_method),
            datasets:[{
                data:data.map(i=>i.total)
            }]
        }

    });

});
}

/* =========================
   DAILY CHART
========================= */

function loadDaily(){

fetch("{{ route('admin.daily.chart') }}")
.then(r=>r.json())
.then(data=>{

    dailyChartInstance = new Chart(document.getElementById('dailySalesChart'), {

        type:'line',

        data:{
            labels:data.map(i=>i.date),

            datasets:[{
                label:"Last 7 Days Sales (₦)",
                data:data.map(i=>i.total),
                fill:true,
                tension:0.4
            }]
        },

        options:{
            responsive:true,

            plugins:{
                tooltip:{
                    callbacks:{
                        title: function(context){
                            return "Date: " + context[0].label;
                        },
                        label: function(context){
                            let value = context.raw || 0;

                            return "Total: ₦" + Number(value).toLocaleString('en-NG', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    }
                }
            },

            scales:{
                y:{
                    beginAtZero:true,

                    ticks:{
                        callback: function(value){
                            return "₦" + Number(value).toLocaleString('en-NG', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    }
                }
            }
        }

    });

});
}

/* =========================
   TOP PRODUCTS
========================= */

function loadTop(){

fetch("{{ route('admin.top.products.chart') }}")
.then(r=>r.json())
.then(data=>{

    topChartInstance = new Chart(document.getElementById('topProductsChart'), {

        type:'bar',

        data:{
            labels:data.map(i=>i.product_label),

            datasets:[{
                label:"Qty Sold",
                data:data.map(i=>i.total_qty)
            }]
        }

    });

});
}

/* =========================
   PROFIT CHART
========================= */

function loadProfitChart(){

    $.get("{{ route('admin.chart.7days') }}", function(res){

        let ctx = document.getElementById('profitChart').getContext('2d');

        profitChartInstance = new Chart(ctx, {

            type: 'bar',

            data: {

                labels: res.labels,

                datasets: [{

                    label: 'Last 7 Days Profit',

                    data: res.profits,

                    borderWidth: 1

                }]
            },

            options:{
                responsive:true,

                scales:{
                    y:{
                        beginAtZero:true,

                        ticks:{
                            callback:function(value){

                                return "₦" + Number(value).toLocaleString('en-NG', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });

                            }
                        }
                    }
                }
            }

        });

    });
}

/* =========================
   INIT
========================= */

$(document).ready(function(){

    // LOAD ONCE
    loadPayment();
    loadDaily();
    loadTop();
    loadProfitChart();

    // LIVE ONLY
    loadDashboard();

    // REFRESH ONLY LIVE DATA
    setInterval(function(){

        loadDashboard();

    }, 15000);

});

</script>

@endsection