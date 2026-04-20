<?php

namespace App\Http\Controllers\SalesPerson;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;

class SalesPersonPOSController extends Controller
{
    // ==============================
    // 1. SHOW POS PAGE
    // ==============================
    public function index()
    {
        $cart = session()->get('cart', []);

        return view('backend.sales_person_backend.pos.index', compact('cart'));
    }


    // ==============================
    // 2. ADD TO CART
    // ==============================
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'name' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);

        $id = $request->product_id;

        // If product already exists → merge quantity
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $request->quantity;
        } else {
            $cart[$id] = [
                'product_id' => $id,
                'name'       => $request->name,
                'price'      => $request->price,
                'quantity'   => $request->quantity,
            ];
        }

        // Recalculate subtotal per item
        foreach ($cart as $key => $item) {
            $cart[$key]['subtotal'] = $item['price'] * $item['quantity'];
        }

        session()->put('cart', $cart);

        return response()->json([
            'status' => 'success',
            'cart'   => $cart
        ]);
    }


    // ==============================
    // 3. REMOVE ITEM FROM CART
    // ==============================
    public function removeFromCart($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
        }

        session()->put('cart', $cart);

        return response()->json([
            'status' => 'removed',
            'cart'   => $cart
        ]);
    }


    // ==============================
    // 4. UPDATE QUANTITY
    // ==============================
    public function updateCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity'   => 'required|integer|min:1'
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            $cart[$request->product_id]['quantity'] = $request->quantity;
            $cart[$request->product_id]['subtotal'] =
            $cart[$request->product_id]['price'] * $request->quantity;
        }

        session()->put('cart', $cart);

        return response()->json([
            'status' => 'updated',
            'cart'   => $cart
        ]);
    }


    // ==============================
    // 5. CLEAR CART
    // ==============================
    public function clearCart()
    {
        session()->forget('cart');

        return response()->json([
            'status' => 'cleared'
        ]);
    }


    // ==============================
    // 6. GET CART (FOR UI REFRESH)
    // ==============================
    public function getCart()
    {
        return response()->json([
            'cart' => session()->get('cart', [])
        ]);
    }



//SEARCH PRODUCT UI
public function searchProducts(Request $request)
{
    $query = $request->query('query');

    if (!$query) {
        return response()->json([
            'products' => [],
            'categories' => []
        ]);
    }

    // 🔥 LIKE search for suggestions
    $products = Sale::where('product_name', 'LIKE', "%{$query}%")
        ->select('product_name')
        ->distinct()
        ->pluck('product_name');

    // 🔥 categories only when exact match
    $categories = Sale::where('product_name', $query)
    ->select('category')
    ->distinct()
    ->pluck('category');

    return response()->json([
        'products' => $products,
        'categories' => $categories
    ]);
}

//GET PRODUCT DISPLAY FOR THE SALESPERSON AFTER 
// CLICKING SELECTING PRODUCT NAME ANDNCATEGORY


public function getProductDetails(Request $request)
{
    // 🔥 Get latest product (KEEP THIS)
    $product = Sale::where('product_name', $request->product_name)
        ->where('category', $request->category)
        ->orderBy('id', 'desc')
        ->first();

    if (!$product) {
        return response()->json(null);
    }

    // 🔥 ADD THIS (sum total quantity)
    $totalQuantity = Sale::where('product_name', $request->product_name)
        ->where('category', $request->category)
        ->sum('quantity');

    // 🔥 Replace only quantity
    $product->quantity = $totalQuantity;

    return response()->json($product);
}



}
