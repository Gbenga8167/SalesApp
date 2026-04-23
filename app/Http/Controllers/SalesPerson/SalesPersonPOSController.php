<?php

namespace App\Http\Controllers\SalesPerson;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalesPersonPOSController extends Controller
{
    // ==============================
    // 1. SHOW POS PAGE
    // ==============================
   public function index(){

   // session()->forget('current_pending_id'); // optional reset

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

    // =========================
    // 🔥 CALCULATE STOCK (IN)
    // =========================
    $totalStock = Sale::where('product_name', $request->name)
        ->where('category', $request->category)
        ->sum('quantity');

    // =========================
    // 🔥 CALCULATE SOLD (OUT)
    // =========================
    $totalSold = \DB::table('sales_items')
        ->where('product_name', $request->name)
        ->where('category', $request->category)
        ->sum('quantity');

    // =========================
    // 🔥 AVAILABLE STOCK
    // =========================
    $availableStock = $totalStock - $totalSold;

    if ($availableStock < 0) {
        $availableStock = 0;
    }

    // =========================
    // 🔥 EXISTING CART QUANTITY CHECK
    // =========================
    $existingQty = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;

    $newTotalQty = $existingQty + $request->quantity;

    // =========================
    // 🚨 HARD BLOCK (NO BYPASS)
    // =========================
    if ($availableStock <= 0) {
        return response()->json([
            'status' => 'error',
            'message' => 'Product is out of stock'
        ]);
    }

    if ($newTotalQty > $availableStock) {
        return response()->json([
            'status' => 'error',
            'message' => 'Stock exceeded! Available: ' . $availableStock
        ]);
    }

    // =========================
    // 🛒 ADD / UPDATE CART
    // =========================
    if (isset($cart[$id])) {
        $cart[$id]['quantity'] += $request->quantity;
    } else {
        $cart[$id] = [
            'product_id' => $id,
            'name'       => $request->name,
            'category'   => $request->category,
            'price'      => $request->price,
            'quantity'   => $request->quantity,
        ];
    }

    // Recalculate subtotal
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
    session()->forget('current_pending_id');

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
    $productName = $request->product_name;
    $category = $request->category;

    // 🔥 Get latest product (price, etc.)
    $product = Sale::where('product_name', $productName)
        ->where('category', $category)
        ->orderBy('id', 'desc')
        ->first();

    if (!$product) {
        return response()->json(null);
    }

    // =========================
    // 🔥 TOTAL STOCK (IN)
    // =========================
    $totalStock = Sale::where('product_name', $productName)
        ->where('category', $category)
        ->sum('quantity');

    // =========================
    // 🔥 TOTAL SOLD (OUT)
    // =========================
    $totalSold = \DB::table('sales_items')
        ->where('product_name', $productName)
        ->where('category', $category)
        ->sum('quantity');


    // =========================
    // CART COUNTING LOGIC FOR REAL TIME
    //  AVAILABLE STOCK UPDATE AT THE (LEFT SIDE) AFTER ADDING TO CART
    // =========================

        $cart = session()->get('cart', []);

        $cartQty = 0;

       foreach ($cart as $item) {
      if (
        $item['name'] === $productName &&
        $item['category'] === $category
       ) {
        $cartQty += $item['quantity'];
       }
   }
    // =========================
    // 🔥 AVAILABLE STOCK
    // =========================
    $availableStock = $totalStock - $totalSold - $cartQty;

    // prevent negative (just in case)
    if ($availableStock < 0) {
        $availableStock = 0;
    }

    // 🔥 attach to response
    $product->available_stock = $availableStock;
    

    return response()->json($product);
}


//🔵 PEND TRANSACTION
public function pendTransaction()
{
    $cart = session()->get('cart', []);

    if (empty($cart)) {
        return response()->json(['status' => 'empty']);
    }

    $pending = session()->get('pending_transactions', []);

    $currentId = session()->get('current_pending_id');

    if ($currentId) {
        // 🔥 UPDATE existing pending instead of duplicating
        foreach ($pending as $key => $item) {
            if ($item['id'] == $currentId) {
                $pending[$key]['cart'] = $cart;
                $pending[$key]['created_at'] = now();
                break;
            }
        }

    } else {
        // 🔥 CREATE new pending
        $pending[] = [
            'id' => uniqid(),
            'cart' => $cart,
            'created_at' => now()
        ];
    }

    session()->put('pending_transactions', $pending);

    // clear cart + reset tracker
    session()->forget('cart');
    session()->forget('current_pending_id');

    return response()->json([
        'status' => 'pended'
    ]);
}


//🔵 GET ALL PENDING
public function getPending(){

    return response()->json([
        'pending' => session()->get('pending_transactions', [])
    ]);
}


//🔵 LOAD BACK INTO CART
public function loadPending($id)
{
    $pending = session()->get('pending_transactions', []);
    $cart = [];

    foreach ($pending as $item) {
        if ($item['id'] == $id) {
            $cart = $item['cart'];

            // 🔥 Track current pending
            session()->put('current_pending_id', $id);

            break;
        }
    }

    session()->put('cart', $cart);

    return response()->json([
        'status' => 'loaded'
    ]);
}



//DELETE PENDING
public function deletePending($id)
{
    $pending = session()->get('pending_transactions', []);

    foreach ($pending as $key => $item) {
        if ($item['id'] == $id) {
            unset($pending[$key]);
        }
    }

    // 🔥 VERY IMPORTANT: reindex array
    $pending = array_values($pending);

    session()->put('pending_transactions', $pending);

    return response()->json([
        'status' => 'deleted'
    ]);
}



//CONFIRM TRANSACTION, PRINT AND GENERATE RECEIPT
public function confirmSale(Request $request)
{
    $cart = session()->get('cart', []);

    if (empty($cart)) {
        return response()->json(['status' => 'empty']);
    }

    try {

        DB::beginTransaction();

        // =========================
        // 🔥 GENERATE RECEIPT NUMBER (LOCKED)
        // =========================
        $today = Carbon::now()->format('Ymd');

        $lastTransaction = DB::table('sales_transactions')
            ->whereDate('created_at', Carbon::today())
            ->lockForUpdate() // 🔥 prevents duplicate under pressure
            ->orderBy('id', 'desc')
            ->first();

        if ($lastTransaction) {
            // extract last number
            $lastNumber = intval(substr($lastTransaction->receipt_no, -4));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $receiptNo = '#' . $today . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // =========================
        // 🔥 CALCULATE TOTAL
        // =========================
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['subtotal'];
        }

        // =========================
        // 🔥 SAVE TRANSACTION
        // =========================
        $transactionId = DB::table('sales_transactions')->insertGetId([
            'receipt_no'    => $receiptNo,
            'total_amount'  => $total,
            'payment_method'=> $request->payment_method ?? 'cash',
            'cashier_id'    => auth()->id() ?? 1,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // =========================
        // 🔥 SAVE ITEMS
        // =========================
        foreach ($cart as $item) {

            DB::table('sales_items')->insert([
                'transaction_id' => $transactionId,
                'product_name'   => $item['name'],
                'category'       => $item['category'],
                'quantity'       => $item['quantity'],
                'price'          => $item['price'],
                'subtotal'       => $item['subtotal'],
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        // =========================
        // 🔥 CLEAR CART
        // =========================
        session()->forget('cart');
        session()->forget('current_pending_id');

        DB::commit();

        return response()->json([
            'status' => 'success',
            'receipt_no' => $receiptNo,
            'transaction_id' => $transactionId
        ]);

    } catch (\Exception $e) {

        DB::rollback();

        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}


//PRINT RECEIPT
public function printReceipt($id)
{
    // 🔥 GET TRANSACTION
    $transaction = \DB::table('sales_transactions')
        ->where('id', $id)
        ->first();

    if (!$transaction) {
        abort(404);
    }

    // 🔥 GET ITEMS
    $items = \DB::table('sales_items')
        ->where('transaction_id', $id)
        ->get();

    // 🔥 GET SETTINGS (company info)
    $settings = \DB::table('settings')->first();

    // 🔥 GET CASHIER
    $cashier = \App\Models\User::find($transaction->cashier_id);

    return view('backend.sales_person_backend.pos.receipt', compact(
        'transaction',
        'items',
        'settings',
        'cashier'
    ));
}
}
