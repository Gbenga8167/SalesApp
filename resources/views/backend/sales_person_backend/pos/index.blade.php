@extends('backend.sales_person_backend.sales_person_dashboard')

@section('salesperson')


<style>
body {
    font-family: 'Poppins', sans-serif;
}

/* Bigger inputs */
.form-control {
    font-size: 16px;
    padding: 12px;
}

/* Suggestion styling */
.suggestion-item {
    padding: 10px;
    cursor: pointer;
    font-weight: 500;
}

.suggestion-item:hover {
    background: #f1f1f1;
}

/* Category radio style */
.category-option {
    display: block;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
    margin-bottom: 8px;
    cursor: pointer;
}

.category-option:hover {
    background: #f8f9fa;
}

.category-option input {
    margin-right: 8px;
}

/* Cart table */
.table td {
    vertical-align: middle;
    font-size: 15px;
}

/* Buttons */
.btn {
    font-size: 15px;
    padding: 10px;
}
</style>



<div class="container-fluid">

<div class="row g-3">

    {{-- ================= LEFT PANEL ================= --}}
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                Product Search
            </div>

            <div class="card-body">

                <input type="text" id="searchProduct" class="form-control mb-3" placeholder="Search product...">

                <div id="productList" class="row g-2"></div>

            </div>
        </div>
    </div>


    {{-- ================= RIGHT PANEL (CART) ================= --}}
    <div class="col-md-8">
        <div class="card shadow-sm">

            <div class="card-header bg-dark text-white">
                Cart
            </div>

            <div class="card-body">

                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Amount</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="cartTable">
                        {{-- CART LOADS HERE --}}
                    </tbody>
                </table>

                <hr>

                <div class="mb-2">
                    <h1><strong>Total: ₦<span id="cartTotal">0</span></strong></h1>
                </div>


                {{-- PAYMENT METHOD --}}
                <div class="mb-2">
                    <label><input type="radio" name="payment" value="cash"> Cash</label><br>
                    <label><input type="radio" name="payment" value="transfer"> Bank Transfer</label><br>
                    <label><input type="radio" name="payment" value="pos"> POS</label>
                </div>


                {{-- ACTION BUTTONS --}}
                <button class="btn btn-warning w-100 mb-2">Pend Transaction</button>
                <button class="btn btn-success w-100">Confirm & Print</button>

            </div>
        </div>
    </div>

</div>

</div>


{{-- ================= JAVASCRIPT ================= --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

// ===============================
// LOAD CART
// ===============================
function loadCart(){
    $.get("{{ route('cart.get') }}", function(res){

        let cart = res.cart;
        let html = "";
        let total = 0;

$.each(cart, function(id, item){

    total += item.subtotal;

   html += `
             <tr>
                 <td>
                     <strong>${item.name}</strong><br>
                     <small class="text-muted">${item.category ?? ''}</small>
                 </td>
             
                 <td>${item.quantity}</td>
             
                 <td>
                     ₦${item.price.toLocaleString()} <br>
                    
                 </td>
             
                 <td> ₦${item.subtotal.toLocaleString()}</td>
             
                 <td>
                     <button class='btn btn-sm btn-danger removeItem' data-id='${id}'>x</button>
                 </td>
             </tr>
        `;
        });

        $('#cartTable').html(html);
        $('#cartTotal').text(total.toLocaleString());
    });
}

loadCart();


// ===============================
// PRODUCT SEARCH (STEP 4)
// ===============================
$('#searchProduct').on('keyup', function () {

    let query = $(this).val();

    if (query.length < 1) {
        $('#productList').html('');
        return;
    }

    $.ajax({
        url: "{{ route('sales.person.products.search') }}",
        type: "GET",
        data: { query: query },
        success: function (res) {

            let html = "";

            // 🔥 SUGGESTIONS
            if (res.products.length === 0) {
                html = `<div class="text-muted p-2">No product found</div>`;
            } else {

                res.products.forEach(productName => {

                    html += `
                        <div class="suggestion-item p-2 border-bottom"
                             style="cursor:pointer;">
                             ${productName}
                        </div>
                    `;
                });
            }

            $('#productList').html(html);
        }
    }); 
});




// ===============================
// CATEGORY SELECT → LOAD PRODUCT DETAILS
// ===============================
$(document).on('change', 'input[name="category"]', function () {

    let productName = $('#searchProduct').val();
    let category = $(this).val();

    $.get("{{ route('sales.person.product.details') }}", {
        product_name: productName,
        category: category
    }, function (product) {

        if (!product) {
            $('#productList').html('<div class="text-danger">Product not found</div>');
            return;
        }

        let html = `
            <div class="card p-3 shadow-sm">

                <h5>${product.product_name}</h5>
                <p><label><strong>Category:</strong> ${product.category}</label</p>
                <p><strong>Price:</strong> ₦${product.selling_price}</p>
                <p><strong>Stock:</strong> ${product.quantity}</p>

                <input type="number" id="qty" class="form-control mb-2" placeholder="Enter quantity">

              <button class="btn btn-success addToCart"
                     data-id="${product.id}"
                     data-name="${product.product_name}"
                     data-price="${product.selling_price}"
                     data-category="${product.category}">
                     Add To Cart
                </button>

            </div>
        `;

        $('#productList').html(html);
    });

});


// ===============================
// ADD TO CART
// ===============================
$(document).on('click', '.addToCart', function () {

    let qty = $('#qty').val();

    if (!qty || qty <= 0) {
        alert('Enter valid quantity');
        return;
    }

    $.post("{{ route('cart.add') }}", {
        _token: "{{ csrf_token() }}",
        product_id: $(this).data('id'),
        name: $(this).data('name'),
        category: $(this).data('category'),
        price: $(this).data('price'),
        quantity: qty
    }, function () {

        loadCart();

        // reset UI
        $('#productList').html('');
        $('#searchProduct').val('');
    });

});



// ===============================
// REMOVE ITEM
// ===============================
$(document).on('click', '.removeItem', function(){

    let id = $(this).data('id');

    $.ajax({
        url: "/cart/remove/" + id,
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}"
        },
        success: function(){
            loadCart();
        }
    });

});


//SEARCH PRODUCT
$(document).on('click', '.suggestion-item', function () {

    let productName = $(this).text().trim();;

    $('#searchProduct').val(productName.trim());;

    // 🔥 Fetch categories
    $.get("{{ route('sales.person.products.search') }}", {
        query: productName
    }, function (res) {

        let html = `<strong>Select Category:</strong><br>`;

        res.categories.forEach(cat => {
            html += `
                      <label class="category-option">
                          <input type="radio" name="category" value="${cat}">
                          <span>${cat}</span>
                      </label>
                      `;
               });

        $('#productList').html(html);
    });

});
</script>

@endsection
