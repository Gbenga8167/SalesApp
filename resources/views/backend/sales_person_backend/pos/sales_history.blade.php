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

/* HEADER (MATCH YOUR OTHER PAGE STYLE) */
.page-title {
    background: linear-gradient(45deg, #343a40, #212529);
    color: #fff;
    padding: 12px 15px;
    font-weight: 600;
    font-size: 18px;
}

/* SEARCH AREA */
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

/* BUTTON STYLE (KEEP YOUR COLOR) */
.btn-primary {
    font-weight: 500;
}

/* PAGINATION */
#pagination {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
    margin-top: 10px;
}

#pagination button {
    border-radius: 6px;
    padding: 6px 12px;
}

/* MOBILE */
@media (max-width: 768px) {
    .search-box {
        flex-direction: column;
    }
}
</style>

<div class="container-fluid">

<div class="card shadow-sm">

    <!-- HEADER -->
    <div class="page-title">
        Sales History
    </div>

    <div class="card-body">

        <!-- SEARCH -->
        <div class="search-box">
            <input type="text" id="search" class="form-control" placeholder="Search receipt, payment, etc...">
            <input type="date" id="from" class="form-control">
            <input type="date" id="to" class="form-control">
        </div>

        <!-- TABLE -->
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Receipt No</th>
                        <th>Total</th>
                        <th>Payment Method</th>
                        <th>Transaction Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody id="tableBody"></tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div id="pagination"></div>

    </div>
</div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

function formatMoney(value){
    return parseFloat(value).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// LOAD DATA
function loadData(page = 1){

    $.get("{{ route('sales.person.history.data') }}", {
        page: page,
        search: $('#search').val(),
        from: $('#from').val(),
        to: $('#to').val()
    }, function(res){

        let html = "";

        if(res.data.length === 0){
            html = `<tr><td colspan="5" class="text-center text-muted">No records found</td></tr>`;
        } else {

            res.data.forEach(item => {

                html += `
                    <tr>
                        <td>${item.receipt_no}</td>
                        <td>₦${formatMoney(item.total_amount)}</td>
                        <td>${item.payment_method}</td>
                        <td>${item.created_at}</td>
                        <td>
                            <a href="/sales-items-page/${item.id}" class="btn btn-sm btn-primary">
                                View Items
                            </a>
                        </td>
                    </tr>
                `;
            });
        }

        $('#tableBody').html(html);

        // SIMPLE PAGINATION
        let pag = "";

        for(let i = 1; i <= res.last_page; i++){
            pag += `
                <button class="btn btn-sm ${i === res.current_page ? 'btn-dark' : 'btn-light'}"
                        onclick="loadData(${i})">
                    ${i}
                </button>
            `;
        }

        $('#pagination').html(pag);
    });
}

// EVENTS
$('#search, #from, #to').on('keyup change', function(){
    loadData();
});

// INIT
loadData();

</script>

@endsection