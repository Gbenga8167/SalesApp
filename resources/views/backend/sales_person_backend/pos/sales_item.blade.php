@extends('backend.sales_person_backend.sales_person_dashboard')
@section('salesperson')

<style>
#suggestionsBox {
    z-index: 999;
    max-height: 200px;
    overflow-y: auto;
}

.list-group-item {
    cursor: pointer;
}

.list-group-item:hover {
    background: #f1f1f1;
}
</style>


<div class="container mt-3">

    <h5 class="mb-3">Transaction Items</h5>

    <!-- 🔍 SEARCH BAR -->
    <div class="row mb-3">
        <div class="col-md-6 position-relative">
            <input type="text" id="search" class="form-control" placeholder="Search product/category...">
            <div id="suggestionsBox" class="list-group position-absolute w-100"></div>
        </div>

        <div class="col-md-3">
            <input type="date" id="from" class="form-control">
        </div>

        <div class="col-md-3">
            <input type="date" id="to" class="form-control">
        </div>
    </div>

    <!-- TABLE -->
    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody id="itemsTable"></tbody>
            </table>

            <div id="paginationLinks"></div>

        </div>
    </div>

</div>

<script>

//TRANSACTION TABLE
    let transactionId = "{{ $transaction->id }}"; // pass from blade

function loadItems(page = 1){

    $.get(`/sales-items/${transactionId}?page=${page}`, {
        search: $('#search').val(),
        from: $('#from').val(),
        to: $('#to').val()
    }, function(res){

        let html = '';

        res.data.forEach(item => {
            html += `
                <tr>
                    <td>${item.product_name}</td>
                    <td>${item.category ?? ''}</td>
                    <td>${item.quantity}</td>
                    <td>₦${parseFloat(item.price).toFixed(2)}</td>
                    <td>₦${parseFloat(item.subtotal).toFixed(2)}</td>
                </tr>
            `;
        });

        $('#itemsTable').html(html);
        $('#paginationLinks').html(res.links);
    });
}

// INITIAL LOAD
loadItems();


// LIVE SEARCH
$('#search, #from, #to').on('keyup change', function(){
    loadItems();
});


//PAGINATION
$(document).on('click', '#paginationLinks a', function(e){
    e.preventDefault();

    let url = $(this).attr('href');
    let page = url.split('page=')[1];

    loadItems(page);
});


//SUGGESTION
$('#search').on('keyup', function(){

    let query = $(this).val();

    if(query.length < 1){
        $('#suggestionsBox').html('');
        return;
    }

    $.get(`/sales-items-suggestions/${transactionId}`, { q: query }, function(res){

        let html = '';

        res.forEach(item => {
            html += `<a class="list-group-item suggestion">${item}</a>`;
        });

        $('#suggestionsBox').html(html);
    });
});


//SUGGESTION CLICK
$(document).on('click', '.suggestion', function(){
    $('#search').val($(this).text());
    $('#suggestionsBox').html('');
    loadItems();
});
</script>
@endsection