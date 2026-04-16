@extends('backend.admin_backend.admin_dashboard')

@section('admin')

<style>
.form-container {
    max-width: 1200px;
    margin: 30px auto;
    background: #fff;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 15px;
}

label {
    font-weight: 600;
    display: block;
    margin-bottom: 5px;
}

.form-control {
    width: 100%;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

.btn-submit {
    background: #0d6efd;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
}
</style>


<div class="form-container">

<h3>Edit Sale</h3>

<form method="POST" action="{{ route('sales.update', $sale->id) }}">
@csrf

<!-- PRODUCT (READONLY) -->
<div class="form-group">
<label>Product Name</label>
<input type="text" class="form-control" value="{{ $sale->product_name }}" readonly>
</div>

<!-- CATEGORY (READONLY) -->
<div class="form-group">
<label>Category</label>
<input type="text" class="form-control" value="{{ $sale->category }}" readonly>
</div>

<!-- QUANTITY -->
<div class="form-group">
<label>Quantity</label>
<input type="number" name="quantity" id="quantity" value="{{ $sale->quantity }}" class="form-control">

<div id="stockInfo" style="margin-top:5px; font-weight:600;"></div>
</div>

<!-- SELLING PRICE -->
<div class="form-group">
<label>Selling Price</label>
<input type="number" name="selling_price" id="price" value="{{ $sale->selling_price }}" class="form-control">
</div>

<!-- AMOUNT -->
<div class="form-group">
<label>Amount</label>
<input type="text" id="amount" class="form-control" value="₦{{ number_format($sale->amount, 2) }}" readonly>
</div>

<button type="submit" class="btn-submit">Update Sale</button>

</form>

</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

$(document).ready(function(){

let availableStock = 0;

// =======================
// FETCH AVAILABLE STOCK (EDIT MODE)
// =======================
function fetchAvailableStock(){

    $.get("{{ route('stocks.available') }}", {
        product_name: "{{ $sale->product_name }}",
        category: "{{ $sale->category }}"
    }, function(res){

        let currentSaleQty = {{ $sale->quantity }}; // 🔥 IMPORTANT

        // ✅ add back current sale quantity
        availableStock = (res.available_stock ?? 0) + currentSaleQty;

        // DISPLAY MESSAGE
        if(availableStock > 10){
            $('#stockInfo')
                .text(`✔ Available: ${availableStock} items`)
                .css('color', 'green');
        } 
        else if(availableStock > 0){
            $('#stockInfo')
                .text(`⚠ Only ${availableStock} items left`)
                .css('color', 'orange');
        } 
        else{
            $('#stockInfo')
                .text(`❌ Out of stock`)
                .css('color', 'red');
        }
    });
}

// CALL ON LOAD
fetchAvailableStock();


// =======================
// VALIDATE QUANTITY
// =======================
$('#quantity').on('keyup change', function(){

    let qty = parseInt($(this).val()) || 0;

    if(qty > availableStock){
        alert('❌ Quantity exceeds available stock!');

        $(this).val('');
    }
});


// =======================
// AUTO CALCULATE AMOUNT
// =======================
$('#quantity, #price').on('keyup change', function(){

    let qty = parseFloat($('#quantity').val()) || 0;
    let price = parseFloat($('#price').val()) || 0;

    let total = qty * price;

    $('#amount').val(total ? '₦' + total.toLocaleString() : '');
});

});

</script>

@endsection