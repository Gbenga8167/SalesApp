@extends('backend.sales_person_backend.sales_person_dashboard')

@section('salesperson')

<style>
.card {
    border-radius: 10px;
}

.table th {
    font-weight: 600;
}

.search-box {
    display: flex;
    gap: 10px;
}

.search-box input {
    padding: 10px;
}

.pagination button {
    margin: 2px;
}
</style>

<div class="container-fluid">

<div class="card shadow-sm">
    <div class="card-header bg-dark text-white">
        Sales History
    </div>

    <div class="card-body">

        <!-- 🔍 SEARCH + DATE -->
        <div class="search-box mb-3">
            <input type="text" id="search" class="form-control" placeholder="Search anything...">

            <input type="date" id="from" class="form-control">
            <input type="date" id="to" class="form-control">
        </div>

        <!-- TABLE -->
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Receipt_no</th>
                    <th>Total</th>
                    <th>Payment Method</th>
                    <th>Transaction Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="tableBody"></tbody>
        </table>

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

// ========================
// LOAD DATA
// ========================
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
    Sales History
</a>
                        </td>
                    </tr>
                `;
            });
        }

        $('#tableBody').html(html);

        // 🔥 PAGINATION
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

// ========================
// SEARCH EVENTS
// ========================
$('#search, #from, #to').on('keyup change', function(){
    loadData();
});

// INIT
loadData();

</script>

@endsection