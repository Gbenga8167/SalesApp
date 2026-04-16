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
    background:#0d6efd;
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

<h3>New Stock Entry</h3>

@if(session('success'))
<div class="alert-success">
    {{ session('success') }}
</div>
@endif

@if ($errors->any())
<div class="alert-error">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif


{{-- SEARCH MODE --}}
<div class="mode-box">
    <input type="text" id="searchProduct" class="form-control" placeholder="Search product...">
    
    <div id="suggestions" class="suggestion-box"></div>

    <br>
    <div id="searchResult"></div>
</div>

<hr>

<form method="POST" action="{{ route('stocks.store') }}" enctype="multipart/form-data">
@csrf

{{-- MANUAL --}}
<div id="manualSection">

<div class="form-group">
<label>Product Name</label>
<select name="product_name" id="productSelect" class="form-control">
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
<select name="category" id="manualCategory" class="form-control">
<option value="">Select category</option>
</select>
</div>
</div>

</div>

{{-- FINAL VALUES (IMPORTANT) --}}
<input type="hidden" name="product_name" id="selectedProduct">
<input type="hidden" name="category" id="selectedCategory">

<div class="form-group">
<label>Quantity</label>
<input type="number" name="quantity" class="form-control">
</div>

<div class="form-group">
<label>Cost Price</label>
<input type="text" name="cost_price" class="form-control">
</div>

<div class="form-group">
<label>Purchase Date</label>
<input type="date" name="purchase_date" class="form-control">
</div>

<div class="form-group">
<label>Description</label>
<textarea name="description" class="form-control"></textarea>
</div>

<div class="form-group">
<label>Profile Photo</label>
<input type="file" name="image" class="form-control" id="image">
</div>

<div class="form-group">
<img id="ShowImage" src="{{asset('uploads/no_image.png')}}" style="width:80px;height:80px;border-radius:6px;">
</div>

<button type="submit" class="btn-submit">Save Stock</button>

</form>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

$(document).ready(function(){

// =======================
// MANUAL MODE
// =======================
$('#productSelect').on('change', function(){

    let name = $(this).val();

    if(!name) return;

    // reset search
    $('#searchProduct').val('');
    $('#suggestions').html('');
    $('#searchResult').html('');

    $('#manualSection').show(); // ensure visible

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


// capture manual category
$(document).on('change', '#manualCategory', function(){
    $('#selectedCategory').val($(this).val());
});


// =======================
// SEARCH MODE
// =======================
$('#searchProduct').on('keyup', function(){

    let value = $(this).val();

    // reset everything
    $('#suggestions').html('');
    $('#searchResult').html('');
    $('#selectedProduct').val('');
    $('#selectedCategory').val('');

    if(value.length === 0){
        $('#manualSection').show();
        return;
    }

    $('#manualSection').hide();

    $.get("{{ route('stocks.categories') }}", {product_name:value}, function(res){

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

    // ✅ FORCE HIDE MANUAL (FIXED BUG)
    $('#manualSection').hide();

    $('#searchProduct').val(productName);
    $('#selectedProduct').val(productName);

    $('#suggestions').html('');
    $('#searchResult').html('');

    $.get("{{ route('stocks.categories') }}", {product_name:productName}, function(res){

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
// AUTO SELECT CATEGORY (NO BUTTON AGAIN 🔥)
// =======================
$(document).on('change', 'input[name="category_radio"]', function(){

    let selected = $(this).val();

    $('#selectedCategory').val(selected);
});


// IMAGE PREVIEW
$('#image').on("change", function(e){
    var reader = new FileReader();
    reader.onload = function(e){
        $('#ShowImage').attr('src', e.target.result);
    }
    reader.readAsDataURL(e.target.files['0']);
});

});
</script>

@endsection