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


    //MANAGE STOCK METHOD
    public function manageStocks(){
    $stocks = NewStockArrival::select(
            'product_name',
            'category',
            DB::raw('SUM(quantity) as total_quantity')
        )
        ->groupBy('product_name', 'category')
        ->orderBy('product_name')
        ->get();

    return view('backend.new_stock_arrival.manage', compact('stocks'));
}


//MANAGE STOCK HISTORY
public function stockHistory(Request $request){

    $records = NewStockArrival::where('product_name', $request->product_name)
        ->where('category', $request->category)
        ->latest()
        ->get();

    $total = $records->sum('quantity');

    return view('backend.new_stock_arrival.history', compact('records', 'total'));
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

    return redirect()->route('stocks.history', [
        'product_name' => $request->product_name,
        'category' => $request->category
    ])->with('success', 'Stock updated successfully!');
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
