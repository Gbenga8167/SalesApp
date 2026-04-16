<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    // SHOW FORM
    public function create()
    {
        return view('backend.admin_backend.product.create_product');
    }

    // STORE PRODUCT

public function store(Request $request)
{
    $request->validate([
        'product_name' => [
            'required',
            Rule::unique('products')->where(function ($query) use ($request) {
                return $query->where('category', $request->category);
            }),
        ],
        'category' => 'required',
    ]);

    // 🔥 SAFE PRODUCT CODE GENERATOR (NO DUPLICATES GUARANTEE)
    do {
        $productCode = 'PROD-' . strtoupper(uniqid(time().rand(100,999)));
    } while (Product::where('product_code', $productCode)->exists());

    Product::create([
        'product_code' => $productCode,
        'product_name' => $request->product_name,
        'category' => $request->category,
    ]);

    return redirect()->back()->with('success', 'Product created successfully!');
}


    // MANAGE PRODUCTS
    public function index()
    {
        $products = Product::latest()->get();
        return view('backend.admin_backend.product.manage_product', compact('products'));
    }


    // EDIT FORM
public function edit($id)
{
    $product = Product::findOrFail($id);
    return view('backend.admin_backend.product.edit_product', compact('product'));
}


// UPDATE PRODUCT
public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    $request->validate([
        'product_name' => 'required|string',
        'category' => 'required|string',
    ]);

    // ✅ CHECK DUPLICATE (EXCLUDE CURRENT ID)
    $AlreadyExist = Product::where('product_name', $request->product_name)
        ->where('category', $request->category)
        ->where('id', '!=', $id)
        ->first();

    if ($AlreadyExist) {
        return redirect()->back()->withErrors(['error' => 'Product already exists!']);
    }

    $product->update([
        'product_name' => $request->product_name,
        'category' => $request->category,
    ]);

    return redirect()->route('manage.product')->with('success', 'Product updated successfully!');
}

    
    // DELETE
    public function delete($id)
    {
        Product::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Product deleted!');
    }


public function previewCsv(Request $request)
{
    $request->validate([
        'csv_file' => 'required|file|mimes:csv,txt',
    ]);

    $file = $request->file('csv_file');
    $content = file_get_contents($file->getRealPath());
    $rows = array_map('str_getcsv', explode("\n", trim($content)));

    array_shift($rows); // remove header

    $validatedRows = [];
    $errors = [];

    // 👇 this will track duplicates inside SAME CSV file
    $seen = [];

    foreach ($rows as $index => $row) {

        if (count($row) < 2) {
            $errors[] = "Row " . ($index + 2) . " is incomplete.";
            continue;
        }

        $productName = trim($row[0]);
        $category    = trim($row[1]);

        $key = strtolower($productName . '_' . $category);

        // ✅ 1. CHECK DUPLICATE INSIDE CSV FILE
        if (isset($seen[$key])) {
            $errors[] = "Row " . ($index + 2) . " is duplicate inside CSV file.";
            continue;
        }

        $seen[$key] = true;

        $data = [
            'product_name' => $productName,
            'category'     => $category,
            'row_number'   => $index + 2
        ];

        $validator = Validator::make($data, [
            'product_name' => 'required|string',
            'category' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors[] = "Row {$data['row_number']} has invalid data.";
            continue;
        }

        // ✅ 2. CHECK DATABASE DUPLICATE
        $exists = Product::where('product_name', $productName)
            ->where('category', $category)
            ->exists();

        if ($exists) {
            $errors[] = "Row {$data['row_number']} product name and category already exists.";
            continue;
        }

        $validatedRows[] = $data;
    }

    Session::put('product_csv', $validatedRows);

    return view('backend.admin_backend.product.csv_preview', compact(
        'validatedRows',
        'errors'
    ));
}

public function confirmCsv()
{
    $rows = Session::get('product_csv');

    if (!$rows || count($rows) === 0) {
        return redirect()->back()->with([
            'message' => 'No valid data to save.',
            'alert-type' => 'error'
        ]);
    }

    foreach ($rows as $row) {

        // ✅ SKIP DUPLICATES (product_name + category)
        $exists = Product::where('product_name', $row['product_name'])
            ->where('category', $row['category'])
            ->exists();

        if ($exists) {
            continue;
        }

        // 🔥 SAFE PRODUCT CODE GENERATOR (NO DUPLICATES GUARANTEE)
        do {
            $productCode = 'PROD-' . strtoupper(uniqid(time().rand(100,999)));
        } while (Product::where('product_code', $productCode)->exists());

        Product::create([
            'product_name' => $row['product_name'],
            'category' => $row['category'],
            'product_code' => $productCode,
        ]);
    }

    Session::forget('product_csv');

    return redirect()->route('manage.product')->with([
        'message' => count($rows) . ' products uploaded successfully!',
        'alert-type' => 'success'
    ]);
}

}
    // AUTO CODE this checks the database to form order e.g PRD-001, PRD-002 ETC.....
   /* $lastProduct = Product::latest()->first();

    if ($lastProduct) {
        $lastNumber = (int) substr($lastProduct->product_code, 4);
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1;
    }

    $productCode = 'PRD-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
*/
