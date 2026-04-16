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

.img-preview img {
    width: 100px;
    height: 100px;
    border-radius: 6px;
    object-fit: cover;
    border: 1px solid #ddd;
}


</style>

<div class="form-container">

<h3>Edit Stock</h3>

<form method="POST" action="{{ route('stocks.update', $stock->id) }}" enctype="multipart/form-data">
@csrf

<!-- PRODUCT (DB DROPDOWN) -->
<div class="form-group">
<label>Product Name</label>
<select name="product_name" id="productSelect" class="form-control">
<option value="">Select Product</option>
@foreach($products as $product)
<option value="{{ $product->product_name }}" 
    {{ $stock->product_name == $product->product_name ? 'selected' : '' }}>
    {{ $product->product_name }}
</option>
@endforeach
</select>
</div>

<!-- CATEGORY (AUTO CHANGE) -->
<div class="form-group">
<label>Category</label>
<div id="categoryBox">
<select name="category" id="categorySelect" class="form-control">
<option value="{{ $stock->category }}">{{ $stock->category }}</option>
</select>
</div>
</div>

<!-- QUANTITY -->
<div class="form-group">
<label>Quantity</label>
<input type="number" name="quantity" value="{{ $stock->quantity }}" class="form-control">
</div>

<!-- COST -->
<div class="form-group">
<label>Cost Price</label>
<input type="text" name="cost_price" value="{{ $stock->cost_price }}" class="form-control">
</div>

<!-- DATE -->
<div class="form-group">
<label>Purchase Date</label>
<input type="date" name="purchase_date" value="{{ $stock->purchase_date }}" class="form-control">
</div>

<!-- DESCRIPTION -->
<div class="form-group">
<label>Description</label>
<textarea name="description" class="form-control">{{ $stock->description }}</textarea>
</div>

<!-- IMAGE -->
<div class="form-group">
<label>Stock Image</label>
<input type="file" name="image" class="form-control" id="image">
</div>

<!-- CURRENT IMAGE -->
<div class="img-preview">
<img id="ShowImage"
src="{{ $stock->image ? asset('uploads/NewStockArrival/'.$stock->image) : asset('uploads/no_image.png') }}">
</div>
<br>
<button type="submit" class="btn-submit">Update Stock</button>

</form>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

// IMAGE PREVIEW
$('#image').on('change', function(e){
    let reader = new FileReader();
    reader.onload = function(e){
        $('#ShowImage').attr('src', e.target.result);
    }
    reader.readAsDataURL(e.target.files[0]);
});


// CATEGORY AUTO LOAD (LIKE ADD STOCK)
$('#productSelect').on('change', function(){

    let name = $(this).val();

    if(!name) return;

    $.get("{{ route('stocks.categories') }}", {product_name:name}, function(res){

        let html = `<select name="category" class="form-control">`;
        html += `<option value="">Select category</option>`;

        res.categories.forEach(cat=>{
            html += `<option value="${cat}">${cat}</option>`;
        });

        html += `</select>`;

        $('#categoryBox').html(html);
    });
});

</script>

@endsection