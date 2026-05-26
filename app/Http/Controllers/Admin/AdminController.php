<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesItem;
use App\Models\SalesTransaction;
use App\Models\Setting;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function AdminLogout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }//end method

    public function AdminProfile(){

        $id = Auth::user()->id;
        $adminData = User::findOrFail($id);
        return view('backend.admin_backend.admin_profile_view', compact('adminData'));
    
    
    }//end method

    
    public function AdminProfileUpdate(Request $request){

        $id = Auth::user()->id;
        $admin = User::findOrFail($id);

        //validate input
        $request->validate([

        'user_name'  => ['required', 'regex:/^[a-zA-Z0-9_]+$/'],
        'email'     => ['required'],
        'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'], 
        ],

        [
        'user_name.regex'  => 'Username can only contain letters, numbers, and underscore.',
        'photo.image' => 'Uploaded file must be an image.',        
        ]);

        $admin->user_name = $request->user_name;
        $admin->email = $request->email;
       
        //checking if admin is also updating his profile photo along with other data
        if( $request->hasFile('photo')){
    
            //save the request photo in a variable
            $file = $request->file('photo');
    
            //update the admin profile image in the image folder directory, to avoid show previous image repeatedly
            @unlink(public_path('uploads/admin_profile/'.$admin->photo));
    
            //generating unique name for the image 
            $imageName = date('YmdHi'). '.' .$file->getClientOriginalName(); // sample-> 20250118.pic_name.png
    
            //move the photo to the uploads directory
            $file->move(public_path('uploads/admin_profile'), $imageName);
    
            //save new admin profile image in the database
            $admin['photo'] = $imageName;
    
        }
    
        //save data
        $admin->save();
    
        $notification = array(
            'message' => 'Admin Profile Updated Successfully!',
            'alert-type' => 'success'
        );
    
        //redirect back to same page
    
        return redirect()->back()->with($notification);
    
    
    
    }//end method

    public function AdminPasswordChange(){

        return view('backend.admin_backend.password_change');
    
    }//end method

    public function AdminPasswordUpdate(Request $request){

        $request->validate([
    
            'old_password' => 'required',
            'new_password' => 'required|confirmed|min:8',
        ]);
    
        if(!Hash::check($request->old_password, Auth::user()->password)){
            $notification = array(
                'message' => 'Old Password Does Not Match!',
                'alert-type' => 'error');
      
            //redirect back to same page
        
            return redirect()->back()->with($notification);
        }

        User::whereId(Auth::user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);
    
        $notification = array(
            'message' => 'Password Updated Succesfully',
            'alert-type' => 'success'
        );
    
        //redirect back to same page
    
        return redirect()->back()->with($notification);
    }




    //ADMIN DASHBOARD AND CHART


// ================= ADMIN DASHBOARD =================
public function dashboardData()
{
    $settings = \DB::table('settings')->first();
    $tz = $settings->timezone ?? 'Africa/Lagos';

    // ✅ Convert "today" to UTC range (CRITICAL FIX)
    $start = Carbon::now($tz)->startOfDay()->timezone('UTC');
    $end   = Carbon::now($tz)->endOfDay()->timezone('UTC');

    // ================= CARDS =================

    $todaySales = \DB::table('sales_transactions')
        ->whereBetween('created_at', [$start, $end])
        ->sum('total_amount');

    $totalTransactions = \DB::table('sales_transactions')
        ->whereBetween('created_at', [$start, $end])
        ->count();

    $itemsSold = \DB::table('sales_items')
        ->join('sales_transactions', 'sales_items.transaction_id', '=', 'sales_transactions.id')
        ->whereBetween('sales_transactions.created_at', [$start, $end])
        ->sum('sales_items.quantity');

    // ================= SALES TREND (HOURLY) =================

    $salesChartRaw = \DB::table('sales_transactions')
        ->selectRaw("
            HOUR(CONVERT_TZ(created_at, '+00:00', ?)) as hour,
            SUM(total_amount) as total
        ", [$this->getMysqlOffset($tz)])
        ->whereBetween('created_at', [$start, $end])
        ->groupBy('hour')
        ->orderBy('hour')
        ->pluck('total', 'hour');

    $salesChart = [];

    for ($i = 0; $i < 24; $i++) {
        $salesChart[] = [
            'hour' => Carbon::createFromTime($i, 0, 0)->format('g A'),
            'total' => $salesChartRaw[$i] ?? 0
        ];
    }

    return response()->json([
        'todaySales' => $todaySales,
        'totalTransactions' => $totalTransactions,
        'itemsSold' => $itemsSold,
        'salesChart' => $salesChart,
    ]);
}


// ================= PAYMENT =================
public function paymentChartData()
{
    $settings = \DB::table('settings')->first();
    $tz = $settings->timezone ?? 'Africa/Lagos';

    $start = Carbon::now($tz)->startOfDay()->timezone('UTC');
    $end   = Carbon::now($tz)->endOfDay()->timezone('UTC');

    $data = \DB::table('sales_transactions')
        ->select('payment_method', \DB::raw('COUNT(*) as total'))
        ->whereBetween('created_at', [$start, $end])
        ->groupBy('payment_method')
        ->get();

    return response()->json($data);
}


// ================= LAST 7 DAYS =================
public function dailySalesChart()
{
    $settings = \DB::table('settings')->first();
    $tz = $settings->timezone ?? 'Africa/Lagos';

    $start = Carbon::now($tz)->subDays(6)->startOfDay()->timezone('UTC');
    $end   = Carbon::now($tz)->endOfDay()->timezone('UTC');

    $data = \DB::table('sales_transactions')
        ->selectRaw("
            DATE(CONVERT_TZ(created_at, '+00:00', ?)) as date,
            SUM(total_amount) as total
        ", [$this->getMysqlOffset($tz)])
        ->whereBetween('created_at', [$start, $end])
        ->groupBy('date')
        ->orderBy('date', 'ASC')
        ->get();

    return response()->json($data);
}


// ================= TOP PRODUCTS =================
public function topProductsChart()
{
    $settings = \DB::table('settings')->first();
    $tz = $settings->timezone ?? 'Africa/Lagos';

    $start = Carbon::now($tz)->startOfDay()->timezone('UTC');
    $end   = Carbon::now($tz)->endOfDay()->timezone('UTC');

    $data = \DB::table('sales_items')
        ->join('sales_transactions', 'sales_items.transaction_id', '=', 'sales_transactions.id')
        ->whereBetween('sales_transactions.created_at', [$start, $end])
        ->select(
            \DB::raw("CONCAT(sales_items.product_name, ' - ', sales_items.category) as product_label"),
            \DB::raw('SUM(sales_items.quantity) as total_qty')
        )
        ->groupBy('sales_items.product_name', 'sales_items.category')
        ->orderByDesc('total_qty')
        ->limit(10)
        ->get();

    return response()->json($data);
}


// ================= ADD THIS HELPER TO CONVERT THE TIME UTC FROM DATA BASE TO ADMIN SET TIME ZONE =================
private function getMysqlOffset($timezone)
{
    $now = new \DateTime("now", new \DateTimeZone($timezone));
    $offset = $now->getOffset() / 3600;

    return ($offset >= 0 ? '+' : '') . $offset . ':00';
}



// ================= CONVERT LOCAL DATE RANGE TO UTC =================
private function convertDateRangeToUTC($from, $to)
{
    $settings = Setting::first();

    $timezone = $settings->timezone ?? 'Africa/Lagos';

    $start = Carbon::parse($from, $timezone)
        ->startOfDay()
        ->timezone('UTC');

    $end = Carbon::parse($to, $timezone)
        ->endOfDay()
        ->timezone('UTC');

    return [$start, $end];
}




//ADMIN SALES REPORT 
public function adminSalesHistory()
{
    return view('backend.admin_backend.admin_sales_report.sales_history');
}

public function adminSalesHistoryData(Request $request)
{
    $query = \DB::table('sales_transactions')
        ->join('users', 'sales_transactions.cashier_id', '=', 'users.id')
        ->select(
            'sales_transactions.*',
            'users.name as salesperson_name',
            'users.user_name as username'
        );

    // 🔍 SEARCH
    if ($request->search['value'] ?? null) {
        $search = $request->search['value'];

        $query->where(function ($q) use ($search) {
            $q->where('sales_transactions.receipt_no', 'like', "%{$search}%")
              ->orWhere('sales_transactions.payment_method', 'like', "%{$search}%")
              ->orWhere('sales_transactions.total_amount', 'like', "%{$search}%")
              ->orWhere('users.name', 'like', "%{$search}%")
              ->orWhere('users.user_name', 'like', "%{$search}%")
              ->orWhereExists(function($sub) use ($search){
                  $sub->select(\DB::raw(1))
                      ->from('sales_items')
                      ->whereColumn('sales_items.transaction_id', 'sales_transactions.id')
                      ->where(function($q2) use ($search){
                          $q2->where('product_name', 'like', "%{$search}%")
                             ->orWhere('category', 'like', "%{$search}%");
                      });
              });
        });
    }

    // 📅 DATE RANGE
    if ($request->from && $request->to) {
[$start, $end] = $this->convertDateRangeToUTC(
    $request->from,
    $request->to
);

$query->whereBetween('sales_transactions.created_at', [
    $start,
    $end
]);
    }

    $settings = Setting::first();

    $total = $query->count();

    $data = $query
        ->orderBy('sales_transactions.id', 'desc')
        ->offset($request->start)
        ->limit($request->length)
        ->get();

    foreach ($data as $row) {
        $row->created_at = Carbon::parse($row->created_at)
            ->timezone($settings->timezone ?? 'Africa/Lagos')
            ->format('d M Y h:i A');
    }

// 🔥 CLONE QUERY FOR TOTAL (VERY IMPORTANT)
$totalSales = (clone $query)->sum('sales_transactions.total_amount');

return response()->json([
    "draw" => intval($request->draw),
    "recordsTotal" => $total,
    "recordsFiltered" => $total,
    "data" => $data,
    "totalSales" => $totalSales // ✅ NEW
]);

}




// GET FILTERED SALES FOR CSV/PDF
private function getFilteredSales($request)
{
    $query = \DB::table('sales_transactions')
        ->join('users', 'sales_transactions.cashier_id', '=', 'users.id')
        ->select(
            'sales_transactions.*',
            'users.name as salesperson_name',
            'users.user_name as username'
        );

    // 🔍 SEARCH
    if ($request->search_value) {
        $search = $request->search_value;

        $query->where(function ($q) use ($search) {
            $q->where('sales_transactions.receipt_no', 'like', "%{$search}%")
              ->orWhere('sales_transactions.payment_method', 'like', "%{$search}%")
              ->orWhere('sales_transactions.total_amount', 'like', "%{$search}%")
              ->orWhere('users.name', 'like', "%{$search}%")
              ->orWhere('users.user_name', 'like', "%{$search}%")
              ->orWhereExists(function($sub) use ($search){
                  $sub->select(\DB::raw(1))
                      ->from('sales_items')
                      ->whereColumn('sales_items.transaction_id', 'sales_transactions.id')
                      ->where(function($q2) use ($search){
                          $q2->where('product_name', 'like', "%{$search}%")
                             ->orWhere('category', 'like', "%{$search}%");
                      });
              });
        });
    }

    // 📅 DATE FILTER
    if ($request->from && $request->to) {
[$start, $end] = $this->convertDateRangeToUTC(
    $request->from,
    $request->to
);

$query->whereBetween('sales_transactions.created_at', [
    $start,
    $end
]);
    }

    return $query->orderBy('sales_transactions.id', 'desc')->get();
}



// EXPORT SALES CSV
public function exportSalesCSV(Request $request)
{
    $data = $this->getFilteredSales($request);

    $filename = "sales_report.csv";

    $headers = [
        "Content-Type" => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
    ];

    $callback = function () use ($data) {

        $file = fopen('php://output', 'w');

        // HEADERS
        fputcsv($file, [
            'S/N',
            'Salesperson',
            'Receipt No',
            'Total',
            'Payment Method',
            'Date'
        ]);

        $i = 1;

        foreach ($data as $row) {

            fputcsv($file, [
                $i++,
                $row->salesperson_name,
                $row->receipt_no,
                $row->total_amount,
                $row->payment_method,
                $row->created_at = Carbon::parse($row->created_at)
            ->timezone($settings->timezone ?? 'Africa/Lagos')
            ->format('d M Y h:i A'),
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}



 // EXPORT SALES PDF
public function exportSalesPDF(Request $request)
{
    $settings = Setting::first();
    $data = $this->getFilteredSales($request);

    $total = $data->sum('total_amount');

    $pdf = Pdf::loadView('backend.admin_backend.admin_sales_report.sales_pdf', [
        'data' => $data,
        'total' => $total,
        'settings' => $settings,
        'from' => $request->from,
        'to' => $request->to,
    ]);

    return $pdf->download('sales_report.pdf');
}




//ADMIN SALES ITEM (SALES REPORT)
public function adminSalesItemsPage($id)
{
    $transaction = SalesTransaction::findOrFail($id);

    return view('backend.admin_backend.admin_sales_report.admin_sales_items', compact('transaction'));
}


public function adminSalesItems(Request $request, $id)
{
    $query = SalesItem::where('transaction_id', $id);

    // 🔍 SEARCH
    if ($request->search) {
        $search = $request->search;

        $query->where(function($q) use ($search){
            $q->where('product_name', 'LIKE', "%{$search}%")
              ->orWhere('category', 'LIKE', "%{$search}%");
        });
    }

    // 📅 DATE FILTER
    if ($request->from) {
        $query->whereDate('created_at', '>=', $request->from);
    }

    if ($request->to) {
        $query->whereDate('created_at', '<=', $request->to);
    }

    $items = $query->orderBy('id', 'desc')->get();

    $totalAmount = $items->sum('subtotal');

    return response()->json([
        'data' => $items,
        'total_amount' => $totalAmount,
    ]);
}


//ADMIN SALES RECEIPT PRINT
public function adminReceipt($id)
{
    $transaction = SalesTransaction::findOrFail($id);

    $items = SalesItem::where('transaction_id', $id)->get();

    $settings = Setting::first();

    $cashier = User::find($transaction->cashier_id);

    return view('backend.admin_backend.admin_sales_report.admin_receipt', compact(
        'transaction',
        'items',
        'settings',
        'cashier'
    ));
}


    //ADMIN PROFIT REPORT 
    // PROFIT REPORT
public function adminProfitReport()
{
    return view('backend.admin_backend.admin_sales_report.admin_profit_report');
}

 // PROFIT REPORT DATA
public function adminProfitReportData(Request $request)
{
    $query = \DB::table('sales_items')
        ->join('sales_transactions', 'sales_items.transaction_id', '=', 'sales_transactions.id')
        ->join('users', 'sales_transactions.cashier_id', '=', 'users.id')
        ->select(
            'sales_items.*',
            'sales_transactions.created_at',
            'users.name as salesperson_name',
            'users.user_name'
        );

    // 🔍 SEARCH
    if ($request->search['value'] ?? null) {
        $search = $request->search['value'];

        $query->where(function ($q) use ($search) {
            $q->where('sales_items.product_name', 'like', "%{$search}%")
              ->orWhere('sales_items.category', 'like', "%{$search}%")
              ->orWhere('users.name', 'like', "%{$search}%")
              ->orWhere('users.user_name', 'like', "%{$search}%");
        });
    }

    // 📅 DATE FILTER
    if (!empty($request->from) && !empty($request->to)) {
[$start, $end] = $this->convertDateRangeToUTC(
    $request->from,
    $request->to
);

$query->whereBetween('sales_transactions.created_at', [
    $start,
    $end
]);
    }

    $total = $query->count();

    $data = $query
        ->orderBy('sales_items.id', 'desc')
        ->offset($request->start)
        ->limit($request->length)
        ->get();

    // 🔥 CALCULATE PROFIT PER ROW
    foreach ($data as $row) {

        $row->total_cost = $row->cost_price * $row->quantity;

        $row->profit = $row->subtotal - $row->total_cost;

        $row->created_at = Carbon::parse($row->created_at)
    ->timezone($settings->timezone ?? 'Africa/Lagos')
    ->format('d M Y h:i A');
    }

    // 🔥 TOTALS (SAFE VERSION)
        $totalSales = (clone $query)->get()->sum('subtotal');

        $totalCost = (clone $query)->get()->sum(function($row){
            return $row->cost_price * $row->quantity;
        });

        $totalProfit = $totalSales - $totalCost;

    return response()->json([
        "draw" => intval($request->draw),
        "recordsTotal" => $total,
        "recordsFiltered" => $total,
        "data" => $data,

        // 🔥 SUMMARY
        "totalSales" => $totalSales,
        "totalCost" => $totalCost,
        "totalProfit" => $totalProfit
    ]);
}




// ======================================================
// REUSABLE FILTER METHOD
// ======================================================
// This method handles:
// - search
// - date filtering
// - joins
// We will reuse it for:
// 1. DataTable
// 2. CSV export
// 3. PDF export
// ======================================================

private function getFilteredProfit(Request $request)
{

    // ==========================================
    // START MAIN QUERY
    // ==========================================
    $query = \DB::table('sales_items')

        // Join sales transactions table
        ->join(
            'sales_transactions',
            'sales_items.transaction_id',
            '=',
            'sales_transactions.id'
        )

        // Join users table
        ->join(
            'users',
            'sales_transactions.cashier_id',
            '=',
            'users.id'
        )

        // Select needed columns
        ->select(
            'sales_items.*',
            'sales_transactions.created_at',
            'users.name as salesperson_name',
            'users.user_name'
        );



    // ==========================================
    // SEARCH FILTER
    // ==========================================
    if ($request->search) {

        // Store search value
        $search = $request->search;

        // Apply search conditions
        $query->where(function ($q) use ($search) {

            // Search product name
            $q->where('sales_items.product_name', 'like', "%{$search}%")

                // Search category
                ->orWhere('sales_items.category', 'like', "%{$search}%")

                // Search salesperson name
                ->orWhere('users.name', 'like', "%{$search}%")

                // Search username
                ->orWhere('users.user_name', 'like', "%{$search}%");
        });
    }



    // ==========================================
    // DATE FILTER
    // ==========================================
    if ($request->from && $request->to) {

        // Filter by transaction date
[$start, $end] = $this->convertDateRangeToUTC(
    $request->from,
    $request->to
);

$query->whereBetween('sales_transactions.created_at', [
    $start,
    $end
]);
    }



    // ==========================================
    // GET DATA
    // ==========================================
    $data = $query
        ->orderBy('sales_items.id', 'desc')
        ->get();



    // ==========================================
    // CALCULATE PROFIT
    // ==========================================
    foreach ($data as $row) {

        // Calculate total cost
        $row->total_cost =
            $row->cost_price * $row->quantity;

        // Calculate profit
        $row->profit =
            $row->subtotal - $row->total_cost;
    }



    // Return final data
    return $data;
}





// ======================================================
// EXPORT PROFIT REPORT CSV
// ======================================================

public function exportProfitCSV(Request $request)
{

    // Get filtered data
    $data = $this->getFilteredProfit($request);



    // ==========================================
    // CSV HEADERS
    // ==========================================
    $headers = [

        // Tell browser this is CSV
        "Content-type" => "text/csv",

        // CSV file name
        "Content-Disposition" =>
            "attachment; filename=profit_report.csv",
    ];



    // ==========================================
    // CREATE CSV CALLBACK
    // ==========================================
    $callback = function() use ($data) {

        // Open output stream
        $file = fopen('php://output', 'w');



        // ======================================
        // CSV COLUMN HEADINGS
        // ======================================
        fputcsv($file, [

            'S/N',
            'Salesperson',
            'Product',
            'Category',
            'Quantity',
            'Sales Amount',
            'Total Cost',
            'Profit',
            'Date'

        ]);



        // ======================================
        // LOOP THROUGH DATA
        // ======================================
        foreach ($data as $index => $row) {

            // Add row into CSV
            fputcsv($file, [

                // Serial number
                $index + 1,

                // Salesperson
                $row->salesperson_name,

                // Product
                $row->product_name,

                // Category
                $row->category,

                // Quantity
                $row->quantity,

                // Sales amount
                number_format($row->subtotal, 2),

                // Total cost
                number_format($row->total_cost, 2),

                // Profit
                number_format($row->profit, 2),

                // Date
                 Carbon::parse($row->created_at)
                 ->timezone($settings->timezone ?? 'Africa/Lagos')
                 ->format('d M Y h:i A'),
            ]);
        }



        // Close CSV stream
        fclose($file);
    };



    // Return CSV download
    return response()->stream($callback, 200, $headers);
}







// ======================================================
// EXPORT PROFIT REPORT PDF
// ======================================================

public function exportProfitPDF(Request $request)
{

    // Get filtered data
    $data = $this->getFilteredProfit($request);



    // ==========================================
    // CALCULATE TOTALS
    // ==========================================
    $totalSales = $data->sum('subtotal');

    $totalCost = $data->sum('total_cost');

    $totalProfit = $totalSales - $totalCost;



    // Get company settings
    $settings = Setting::first();



    // ==========================================
    // LOAD PDF VIEW
    // ==========================================
    $pdf = Pdf::loadView(
        'backend.admin_backend.admin_sales_report.profit_pdf',

        [

            // Send data to Blade
            'data' => $data,

            // Totals
            'totalSales' => $totalSales,
            'totalCost' => $totalCost,
            'totalProfit' => $totalProfit,

            // Date filters
            'from' => $request->from,
            'to' => $request->to,

            // Settings
            'settings' => $settings,
        ]
    );



    // Download PDF
    return $pdf->download('profit_report.pdf');
}





 //ADMIN PROFIT DATA CHART
public function profitChartData(Request $request)
{
    $query = \DB::table('sales_items')
        ->join('sales_transactions', 'sales_items.transaction_id', '=', 'sales_transactions.id')
        ->join('users', 'sales_transactions.cashier_id', '=', 'users.id');

    // 🔍 SEARCH (same as table)
    if ($request->search) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('sales_items.product_name', 'like', "%{$search}%")
              ->orWhere('sales_items.category', 'like', "%{$search}%")
              ->orWhere('users.name', 'like', "%{$search}%")
              ->orWhere('users.user_name', 'like', "%{$search}%");
        });
    }

    // 📅 DATE FILTER
    if (!empty($request->from) && !empty($request->to)) {
[$start, $end] = $this->convertDateRangeToUTC(
    $request->from,
    $request->to
);

$query->whereBetween('sales_transactions.created_at', [
    $start,
    $end
]);
    }

    // 🔥 GROUP BY DATE
    $data = $query
        ->selectRaw("
            DATE(sales_transactions.created_at) as date,
            SUM(subtotal) as total_sales,
            SUM(cost_price * quantity) as total_cost
        ")
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();

    $labels = [];
    $profits = [];

    foreach ($data as $row) {
        $profit = $row->total_sales - $row->total_cost;

        $labels[] = Carbon::parse($row->date)->format('d M');
        $profits[] = $profit;
    }

    return response()->json([
        'labels' => $labels,
        'profits' => $profits
    ]);
}



//ADMIN LAST 7-DAYS CHART
public function profitChartLast7Days()
{
    $data = \DB::table('sales_items')
        ->join('sales_transactions', 'sales_items.transaction_id', '=', 'sales_transactions.id')
        ->whereBetween('sales_transactions.created_at', [
            now()->subDays(6)->startOfDay(),
            now()->endOfDay()
        ])
        ->selectRaw("
            DATE(sales_transactions.created_at) as date,
            SUM(subtotal) as total_sales,
            SUM(cost_price * quantity) as total_cost
        ")
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();

    $labels = [];
    $profits = [];

    foreach ($data as $row) {
        $profit = $row->total_sales - $row->total_cost;

        $labels[] = \Carbon\Carbon::parse($row->date)->format('d M');
        $profits[] = $profit;
    }

    return response()->json([
        'labels' => $labels,
        'profits' => $profits
    ]);
}




//aDMIN LEADERBOARD
public function leaderboardData(Request $request)
{
    $query = \DB::table('sales_transactions')
        ->join('users', 'sales_transactions.cashier_id', '=', 'users.id')
        ->select(
            'users.name',
            'users.user_name',
            \DB::raw('SUM(sales_transactions.total_amount) as total_sales'),
            \DB::raw('COUNT(sales_transactions.id) as total_transactions'),
            \DB::raw('MAX(sales_transactions.created_at) as last_sale')
        )
        ->groupBy('users.id', 'users.name', 'users.user_name');

    // 🔍 SEARCH
    if ($request->search['value'] ?? null) {
        $search = $request->search['value'];

        $query->where(function ($q) use ($search) {
            $q->where('users.name', 'like', "%{$search}%")
              ->orWhere('users.user_name', 'like', "%{$search}%");
        });
    }

    // 📅 DATE FILTER
    if (!empty($request->from) && !empty($request->to)) {
[$start, $end] = $this->convertDateRangeToUTC(
    $request->from,
    $request->to
);

$query->whereBetween('sales_transactions.created_at', [
    $start,
    $end
]);
    }

    $data = $query
        ->orderByDesc('total_sales')
        ->get();

    // 🏆 ADD RANKING + FORMAT
    $rank = 1;

    foreach ($data as $row) {

        $row->rank = $rank++;

        $row->total_sales = number_format($row->total_sales, 2);
        $row->total_transactions = number_format($row->total_transactions);

        $row->last_sale = \Carbon\Carbon::parse($row->last_sale)
            ->timezone(optional(\App\Models\Setting::first())->timezone ?? 'Africa/Lagos')
            ->format('d M Y h:i A');
    }

    return response()->json([
        "data" => $data
    ]);
}




 //SALES PERSON ACCOUNT MANAGER(ACTIVATE/DEACTIVATE)
public function manageAccounts()
{
    $users = User::where('role', 2)->get();
    return view('backend.admin_backend.salesperson.manage_accounts', compact('users'));
}


//SALES PERSON ACCOUNT MANAGER TOGGLE USER STATUS(ACTIVATE/DEACTIVATE)
public function toggleUserStatus(Request $request)
{
    $user = User::findOrFail($request->user_id);

    // ❌ Prevent admin from deactivating themselves
    if ($user->id == auth()->id()) {
        return response()->json([
            'error' => 'You cannot deactivate your own account.'
        ], 403);
    }

    $user->status = !$user->status;
    $user->save();

    return response()->json([
        'status' => $user->status
    ]);
}
}
