<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewStockArrival;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewStockArrivalController extends Controller
{
    public function create(){
        $products = Product::select('product_name')->distinct()->get();
        return view('backend.new_stock_arrival.create', compact('products'));
    }

    // ✅ SEARCH + SUGGESTION + CATEGORY
    public function getCategories(Request $request)
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

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required',
            'category' => 'required',
            'quantity' => 'required|numeric',
            'cost_price' => 'required|numeric',
            'purchase_date' => 'required|date',
        ]);

        $imageName = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/NewStockArrival'), $imageName);
        }

        NewStockArrival::create([
            'product_name' => $request->product_name,
            'category' => $request->category,
            'quantity' => $request->quantity,
            'cost_price' => $request->cost_price,
            'purchase_date' => $request->purchase_date,
            'description' => $request->description,
            'image' => $imageName,
        ]);

        return back()->with('success', 'Stock added successfully!');
    }


// ================== MANAGE STOCK ==================
public function manageStocks(Request $request){

    $query = DB::table('new_stock_arrivals')
        ->select(
            'product_name',
            'category',
            DB::raw('SUM(quantity) as total_quantity')
        )
        ->groupBy('product_name', 'category');

    // 🔍 SEARCH
    if($request->search){
        $query->where(function($q) use ($request){
            $q->where('product_name', 'like', '%'.$request->search.'%')
              ->orWhere('category', 'like', '%'.$request->search.'%');
        });
    }

    $stocks = $query->orderBy('product_name')
        ->paginate(5)
        ->withQueryString();

    // 🔥 AJAX RESPONSE
    if($request->ajax()){
        return view('backend.new_stock_arrival.partials.manage_table', compact('stocks'))->render();
    }

    return view('backend.new_stock_arrival.manage', compact('stocks'));
}



// ================== STOCK HISTORY ==================
public function stockHistory(Request $request, $product, $category){

    $query = NewStockArrival::where('product_name', $product)
        ->where('category', $category);

    // 🔍 SEARCH (ALL FIELDS 🔥)
    if($request->search){
        $search = $request->search;

        $query->where(function($q) use ($search){
            $q->where('product_name', 'like', "%$search%")
              ->orWhere('category', 'like', "%$search%")
              ->orWhere('description', 'like', "%$search%")
              ->orWhere('cost_price', 'like', "%$search%")
              ->orWhere('quantity', 'like', "%$search%")
              ->orWhere('purchase_date', 'like', "%$search%")
              ->orWhere('created_at', 'like', "%$search%");
        });
    }

    $records = $query->latest()
        ->paginate(10)
        ->withQueryString();

    $total = NewStockArrival::where('product_name', $product)
        ->where('category', $category)
        ->sum('quantity');

    if($request->ajax()){
        return view('backend.new_stock_arrival.partials.history_table', compact('records'))->render();
    }

    return view('backend.new_stock_arrival.history', compact(
        'records','total','product','category'
    ));
}



//EDIT STOCK
public function editStock($id)
{
    $stock = NewStockArrival::findOrFail($id);

    // ✅ ADD THIS
    $products = Product::select('product_name')->distinct()->get();

    return view('backend.new_stock_arrival.edit', compact('stock', 'products'));
}//END EDIT STOCK

public function updateStock(Request $request, $id)
{
    $request->validate([
        'product_name' => 'required',
        'category' => 'required',
        'quantity' => 'required|numeric',
        'cost_price' => 'required|numeric',
        'purchase_date' => 'required|date',
    ]);

    $stock = NewStockArrival::findOrFail($id);

    // ✅ IMAGE FIX (was photo before, now image)
    if ($request->hasFile('image')) {

        if ($stock->image) {
            $oldPath = public_path('uploads/NewStockArrival/'.$stock->image);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $file = $request->file('image');
        $filename = time().'_'.$file->getClientOriginalName();
        $file->move(public_path('uploads/NewStockArrival'), $filename);

        $stock->image = $filename;
    }

    $stock->update([
        'product_name' => $request->product_name,
        'category' => $request->category,
        'quantity' => $request->quantity,
        'cost_price' => $request->cost_price,
        'purchase_date' => $request->purchase_date,
        'description' => $request->description,
        'image' => $stock->image ?? null,
    ]);

       $product = $request->product_name;
       $category = $request->category;

      return redirect()->route('stocks.history', [$product, $category])
       ->with('success', 'Stock updated successfully!');
}//END UPDATE STOCK



//DELETE STOCK
public function deleteStock($id)
{
    $stock = NewStockArrival::findOrFail($id);
     if ($stock->image) {
        $filePath = public_path('uploads/NewStockArrival/' . $stock->image);

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $stock->delete();

    return back()->with('success', 'Stock deleted successfully!');
}

}
