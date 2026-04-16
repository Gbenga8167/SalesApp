@extends('backend.admin_backend.admin_dashboard')

@section('admin')

<style>
.form-container{
    max-width:1200px;
    margin:30px auto;
    background:#fff;
    padding:25px;
    border-radius:10px;
    box-shadow:0 4px 20px rgba(0,0,0,0.1);
}

.form-group{margin-bottom:15px;}

label{font-weight:600;}

.form-control{
    width:100%;
    padding:10px;
    border:1px solid #ccc;
    border-radius:6px;
}

.btn-submit{
    background:#198754;
    color:#fff;
    padding:10px 20px;
    border:none;
    border-radius:6px;
}

.mode-box{
    background:#f8f9fa;
    padding:15px;
    border-radius:6px;
    margin-bottom:15px;
    position:relative;
}

.suggestion-box{
    background:#fff;
    border:1px solid #ddd;
    position:absolute;
    width:100%;
    z-index:999;
    max-height:150px;
    overflow-y:auto;
}

.suggestion-item{
    padding:8px;
    cursor:pointer;
}

.suggestion-item:hover{
    background:#f1f1f1;
}

/* ALERTS */
.alert-success{
    background:#e6f9f0;
    color:#0f5132;
    padding:12px 15px;
    border-left:5px solid #198754;
    border-radius:6px;
    margin-bottom:15px;
}

.alert-error{
    background:#fdecea;
    color:#842029;
    padding:12px 15px;
    border-left:5px solid #dc3545;
    border-radius:6px;
    margin-bottom:15px;
}
</style>

<div class="form-container">

<h3>Stock For Sale</h3>

@if(session('success'))
<div class="alert-success">
    {{ session('success') }}
</div>
@endif

@if ($errors->any())
<div class="alert-error">
    @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
    @endforeach
</div>
@endif

{{-- SEARCH --}}
<div class="mode-box">
    <input type="text" id="searchProduct" class="form-control" placeholder="Search product...">
    <div id="suggestions" class="suggestion-box"></div>
    <br>
    <div id="searchResult"></div>
</div>

<hr>

<form method="POST" action="{{ route('sales.store') }}">
@csrf

{{-- ✅ MANUAL SECTION (FIXED 🔥) --}}
<div id="manualSection">

<div class="form-group">
<label>Product Name</label>
<select id="productSelect" class="form-control">
<option value="">Select Product</option>
@foreach($products as $product)
<option value="{{ $product->product_name }}">
{{ $product->product_name }}
</option>
@endforeach
</select>
</div>

<div class="form-group">
<label>Category</label>
<div id="categoryBox">
<select id="manualCategory" class="form-control">
<option value="">Select category</option>
</select>
</div>
</div>

</div>

{{-- FINAL VALUES --}}
<input type="hidden" name="product_name" id="selectedProduct">
<input type="hidden" name="category" id="selectedCategory">



<div class="form-group">
    <label>Quantity Available In Stock</label>
    <input type="text" id="available_stock" class="form-control" readonly>
</div>

<div class="form-group">
<label>Quantity To Be Sold</label>
<input type="number" name="quantity" id="quantity" class="form-control">

<div id="stockInfo" style="margin-top:5px; font-weight:600;"></div>
</div>


<div class="form-group">
    <label>Cost Price</label>
    <input type="text" id="cost_price" class="form-control" readonly>
</div>

<div class="form-group">
<label>Selling Price</label>
<input type="number" name="selling_price" id="price" class="form-control">
</div>

<div class="form-group">
<label>Expected Amount</label>
<input type="text" id="amount" class="form-control" readonly>
</div>

<button type="submit" class="btn-submit">Add Sale</button>

</form>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

$(document).ready(function(){

let availableStock = 0; // 🔥 GLOBAL VARIABLE

// =======================
// FETCH COST PRICE
// =======================
function fetchCostPrice(){
    let product = $('#selectedProduct').val();
    let category = $('#selectedCategory').val();

    if(product && category){
        $.get("{{ route('stocks.cost.price') }}", {
            product_name: product,
            category: category
        }, function(res){

            let cost = res.cost_price;

            if(cost && cost !== 0){
                let formatted = new Intl.NumberFormat('en-NG', {
                    style: 'currency',
                    currency: 'NGN'
                }).format(cost);

                $('#cost_price').val(formatted);
            } else {
                $('#cost_price').val('₦0.00');
            }
        });
    }
}


// =======================
// FETCH AVAILABLE STOCK 🔥
// =======================
function fetchAvailableStock(){
    let product = $('#selectedProduct').val();
    let category = $('#selectedCategory').val();

    if(product && category){
        $.get("{{ route('stocks.available') }}", {
            product_name: product,
            category: category
        }, function(res){

            availableStock = res.available_stock ?? 0;

            $('#available_stock').val(availableStock.toLocaleString());

            // 🔥 SHOW LIVE MESSAGE
            if(availableStock > 10){
                $('#stockInfo')
                    .text(`✔ Only ${availableStock} items left in stock`)
                    .css('color', 'green');
            } 
            else if(availableStock > 0){
                $('#stockInfo')
                    .text(`⚠ Only ${availableStock} items left (Low stock!)`)
                    .css('color', 'orange');
            } 
            else{
                $('#stockInfo')
                    .text(`❌ Out of stock`)
                    .css('color', 'red');
            }
        });
    }
    
} 

// =======================
// MANUAL MODE
// =======================
$('#productSelect').on('change', function(){

    $('#stockInfo').text('');
    let name = $(this).val();
    if(!name) return;

    $('#searchProduct').val('');
    $('#suggestions').html('');
    $('#searchResult').html('');

    $('#manualSection').show();
    $('#selectedProduct').val(name);

    $.get("{{ route('stocks.categories') }}", {product_name:name}, function(res){

        let html = `<select id="manualCategory" class="form-control">`;
        html += `<option value="">Select category</option>`;

        res.categories.forEach(cat=>{
            html += `<option value="${cat}">${cat}</option>`;
        });

        html += `</select>`;
        $('#categoryBox').html(html);
    });
});


// =======================
// CATEGORY SELECT
// =======================
$(document).on('change', '#manualCategory', function(){

    $('#selectedCategory').val($(this).val());

    fetchCostPrice();
    fetchAvailableStock(); // 🔥 FIXED
});


// =======================
// SEARCH MODE
// =======================
$('#searchProduct').on('keyup', function(){

    let value = $(this).val();

    $('#suggestions').html('');
    $('#searchResult').html('');
    $('#selectedProduct').val('');
    $('#selectedCategory').val('');
    $('#cost_price').val('');
    $('#available_stock').val('');
    $('#stockInfo').text('');

    if(value.length === 0){
        $('#manualSection').show();
        return;
    }

    $('#manualSection').hide();

    $.get("{{ route('create.sale.categories') }}", {product_name:value}, function(res){

        let html = '';

        if(res.products.length === 0){
            html = `<div class="suggestion-item">No product found</div>`;
        }else{
            res.products.forEach(p=>{
                html += `<div class="suggestion-item">${p}</div>`;
            });
        }

        $('#suggestions').html(html);
    });
});


// =======================
// CLICK SUGGESTION
// =======================
$(document).on('click', '.suggestion-item', function(){

    let productName = $(this).text();

    $('#manualSection').hide();

    $('#searchProduct').val(productName);
    $('#selectedProduct').val(productName);

    $('#suggestions').html('');
    $('#searchResult').html('');
    $('#cost_price').val('');
    $('#available_stock').val('');
    $('#stockInfo').text('');

    $.get("{{ route('create.sale.categories') }}", {product_name:productName}, function(res){

        let html = `<label><strong>Select Category</strong></label><br>`;

        res.categories.forEach(cat=>{
            html += `
            <div>
                <input type="radio" name="category_radio" value="${cat}">
                ${cat}
            </div>`;
        });

        $('#searchResult').html(html);
    });
});


// =======================
// SEARCH CATEGORY SELECT
// =======================
$(document).on('change', 'input[name="category_radio"]', function(){

    $('#selectedCategory').val($(this).val());

    fetchCostPrice();
    fetchAvailableStock(); // 🔥 FIXED
});


// =======================
// VALIDATE QUANTITY 🔥 FIXED
// =======================
$('#quantity').on('keyup change', function(){

    let qty = parseInt($(this).val()) || 0;

 if(availableStock <= 0){
    alert('❌ This product is out of stock!');
    $(this).val('');
    return;
}

    if(qty > availableStock){
        alert('❌ Quantity exceeds available stock!');

        $(this).val('');
    }
});


// =======================
// AUTO CALCULATE
// =======================
$('#quantity, #price').on('keyup change', function(){

    let qty = $('#quantity').val();
    let price = $('#price').val();

    let total = (parseFloat(qty) || 0) * (parseFloat(price) || 0);

    $('#amount').val(total ? total.toLocaleString() : '');
});

});


</script>

@endsection