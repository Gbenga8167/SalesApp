<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewStockArrival;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;



class SalesController extends Controller
{


public function CreateSale(){
        $products = Product::select('product_name')->distinct()->get();
        return view('backend.admin_backend.stock_sales.create', compact('products'));
    }

    // ✅ SEARCH + SUGGESTION + CATEGORY
    public function getSaleCategories(Request $request)
    {
        $query = $request->product_name;

        // 🔥 Suggest product names (LIKE search)
        $products = Product::where('product_name', 'LIKE', "%$query%")
            ->select('product_name')
            ->distinct()
            ->pluck('product_name');

        // 🔥 Get categories if exact match
        $categories = Product::where('product_name', $query)
            ->pluck('category');

        return response()->json([
            'products' => $products,
            'categories' => $categories
        ]);
    }


    
//STOCK SALES     
public function storeStockForSale(Request $request){

    $request->validate([
        'product_name' => 'required|string',
        'category' => 'required|string',
        'quantity' => 'required|numeric|min:1',
        'selling_price' => 'required|numeric|min:0',
    ]);

    // 🔥 CHECK AVAILABLE STOCK
    $totalPurchased = \DB::table('new_stock_arrivals')
        ->where('product_name', $request->product_name)
        ->where('category', $request->category)
        ->sum('quantity');

    $totalSold = \DB::table('sales')
        ->where('product_name', $request->product_name)
        ->where('category', $request->category)
        ->sum('quantity');

    $available = $totalPurchased - $totalSold;

    if($request->quantity > $available){
        return back()->withErrors(['quantity' => 'Quantity exceeds available stock!']);
    }

    $amount = $request->quantity * $request->selling_price;

    Sale::create([
        'product_name' => $request->product_name,
        'category' => $request->category,
        'quantity' => $request->quantity,
        'selling_price' => $request->selling_price,
        'amount' => $amount,
    ]);

    return back()->with('success', 'Stock for sale added successfully!');
}


//POPULATE COST PRICE
public function getCostPrice(Request $request)
{
    $costPrice = NewStockArrival::where('product_name', $request->product_name)
        ->where('category', $request->category)
        ->orderBy('id', 'desc')
        ->value('cost_price');

    return response()->json([
        'cost_price' => $costPrice ?? 0
    ]);
}


//MANAGE SALES  
public function ManageStock(Request $request){

    $query = \DB::table('sales')
        ->select(
            'product_name',
            'category',
            \DB::raw('SUM(quantity) as total_sold'),
            \DB::raw('SUM(amount) as total_amount')
        )
        ->groupBy('product_name', 'category');

    // 🔍 SEARCH
    if($request->search){
        $query->where(function($q) use ($request){
            $q->where('product_name', 'like', '%'.$request->search.'%')
              ->orWhere('category', 'like', '%'.$request->search.'%');
        });
    }

    $sales = $query->paginate(3)->withQueryString();

    // 🔥 PROCESS AFTER PAGINATION
    $sales->getCollection()->transform(function ($row) {

        $totalPurchased = \DB::table('new_stock_arrivals')
            ->where('product_name', $row->product_name)
            ->where('category', $row->category)
            ->sum('quantity');

        $latestPrice = \DB::table('sales')
            ->where('product_name', $row->product_name)
            ->where('category', $row->category)
            ->latest()
            ->value('selling_price');

        $row->total_purchased = $totalPurchased;
        $row->available_stock = $totalPurchased - $row->total_sold;
        $row->selling_price = $latestPrice ?? 0;

        return $row;
    });

    // AJAX RESPONSE
    if($request->ajax()){
        return view('backend.admin_backend.stock_sales.partials.manage_table', compact('sales'))->render();
    }

    return view('backend.admin_backend.stock_sales.manage_stock', compact('sales'));
}





//SALES HISTORY
public function SalesHistory(Request $request, $product, $category){

    $query = Sale::where('product_name', $product)
        ->where('category', $category);

    // 🔍 SEARCH (ALL FIELDS 🔥)
    if($request->search){
        $search = $request->search;

        $query->where(function($q) use ($search){
            $q->where('product_name', 'like', "%$search%")
              ->orWhere('category', 'like', "%$search%")
              ->orWhere('selling_price', 'like', "%$search%")
              ->orWhere('amount', 'like', "%$search%")
              ->orWhere('quantity', 'like', "%$search%")
              ->orWhere('created_at', 'like', "%$search%");
        });
    }

    $sales = $query->latest()
        ->paginate(5)
        ->withQueryString();

    if($request->ajax()){
        return view('backend.admin_backend.stock_sales.partials.history_table', compact('sales'))->render();
    }

    return view('backend.admin_backend.stock_sales.sales_history', compact('sales','product','category'));
}





//SALES EDIT
public function EditSale($id)
{
    $sale = Sale::findOrFail($id);
    return view('backend.admin_backend.stock_sales.edit_sale', compact('sale'));
}

//Update sales
public function UpdateSale(Request $request, $id)
{
    $request->validate([
        'quantity' => 'required|numeric|min:1',
        'selling_price' => 'required|numeric|min:0',
    ]);

    $sale = Sale::findOrFail($id);

    $amount = $request->quantity * $request->selling_price;

    $sale->update([
        'quantity' => $request->quantity,
        'selling_price' => $request->selling_price,
        'amount' => $amount,
    ]);

    return redirect()->route('sales.history', [$sale->product_name, $sale->category])
        ->with('success', 'Sale updated successfully!');
}

//DELETE SALES HISTORY
public function DeleteSale($id)
{
    $sale = Sale::findOrFail($id);

    // keep product + category before deleting
    $product = $sale->product_name;
    $category = $sale->category;

    $sale->delete();

    return redirect()->route('sales.history', [$product, $category])
        ->with('success', 'Sale deleted successfully!');
}


//GET GOOD AVAILABLE IN NEWSALESARRIVAL
public function getAvailableStock(Request $request){
    
    $totalPurchased = \DB::table('new_stock_arrivals')
        ->where('product_name', $request->product_name)
        ->where('category', $request->category)
        ->sum('quantity');

    $totalSold = \DB::table('sales')
        ->where('product_name', $request->product_name)
        ->where('category', $request->category)
        ->sum('quantity');

    $available = $totalPurchased - $totalSold;

    return response()->json([
        'available_stock' => $available
    ]);
}
}
