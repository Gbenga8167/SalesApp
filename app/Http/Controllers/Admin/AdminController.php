<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesItem;
use App\Models\SalesTransaction;
use App\Models\Setting;
use App\Models\User;
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
        'email'     => ['required', 'email:rfc,dns'],
        'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'], 
        ],

        [
        'email.email' => 'Please enter a valid email address (example: name@example.com).',
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
        $query->whereBetween('sales_transactions.created_at', [
            $request->from . ' 00:00:00',
            $request->to . ' 23:59:59'
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


























//PROFIT ANALYSIS

public function adminSalesReport(Request $request)
{
    $query = \DB::table('sales_items')
        ->join('sales_transactions', 'sales_items.transaction_id', '=', 'sales_transactions.id')
        ->join('users', 'sales_transactions.cashier_id', '=', 'users.id') // salesperson

        ->select(
            'users.name as salesperson',
            'sales_items.product_name',
            'sales_items.category',

            \DB::raw('SUM(sales_items.quantity) as total_qty'),
            \DB::raw('SUM(sales_items.subtotal) as total_sales'),

            \DB::raw('AVG(sales_items.cost_price) as cost_price'),
            \DB::raw('AVG(sales_items.price) as selling_price'),

            \DB::raw('SUM(sales_items.quantity * sales_items.cost_price) as total_cost')
        )

        ->groupBy(
            'users.name',
            'sales_items.product_name',
            'sales_items.category'
        );

    // 🔍 SEARCH (salesperson / product / category)
    if ($request->search) {
        $search = $request->search;

        $query->where(function($q) use ($search){
            $q->where('users.name', 'LIKE', "%{$search}%")
              ->orWhere('sales_items.product_name', 'LIKE', "%{$search}%")
              ->orWhere('sales_items.category', 'LIKE', "%{$search}%");
        });
    }

    // 📅 DATE FILTER
    if ($request->from && $request->to) {
        $query->whereBetween('sales_transactions.created_at', [
            $request->from . ' 00:00:00',
            $request->to . ' 23:59:59'
        ]);
    }

    $data = $query->orderByDesc('total_sales')->get();

    // 🔥 CALCULATE PROFIT PER ROW
    $data->transform(function ($row) {
        $row->profit = $row->total_sales - $row->total_cost;
        return $row;
    });

    // 🔥 TOTALS
    $totalSales = $data->sum('total_sales');
    $totalCost  = $data->sum('total_cost');
    $totalProfit = $totalSales - $totalCost;

    return response()->json([
        'data' => $data,
        'total_sales' => $totalSales,
        'total_cost' => $totalCost,
        'total_profit' => $totalProfit
    ]);
}


//ADMIN SALES TRANSACTIONS PAGE
public function adminSalesTransactions()
{
    return view('backend.admin_backend.admin_transactions');
}


//ADMIN SALES TRANSACTIONS DATA (WITH PAGINATION)
public function adminSalesTransactionsData(Request $request)
{
    try {
        // First get the base query for transactions
        $transactionQuery = \DB::table('sales_transactions')
            ->join('users', 'sales_transactions.cashier_id', '=', 'users.id')
            ->select(
                'sales_transactions.id',
                'sales_transactions.receipt_no',
                'sales_transactions.total_amount',
                'sales_transactions.payment_method',
                'sales_transactions.created_at',
                'users.name as salesperson_name',
                'users.user_name as salesperson_username'
            );

        // 🔍 SEARCH (product/category/salesperson name/username)
        if ($request->search_value) {
            $search = $request->search_value;

            $transactionQuery->where(function($q) use ($search){
                $q->where('users.name', 'LIKE', "%{$search}%")
                  ->orWhere('users.user_name', 'LIKE', "%{$search}%")
                  ->orWhere('sales_transactions.receipt_no', 'LIKE', "%{$search}%")
                  ->orWhere('sales_transactions.payment_method', 'LIKE', "%{$search}%");
            });
        }

        // 📅 DATE FILTER (FROM - TO)
        if ($request->from && $request->to) {
            $transactionQuery->whereBetween('sales_transactions.created_at', [
                $request->from . ' 00:00:00',
                $request->to . ' 23:59:59'
            ]);
        }

        $settings = Setting::first();

        // TOTAL COUNT
        $total = $transactionQuery->count();

        // GET ALL TRANSACTIONS (for profit calculation)
        $allTransactions = $transactionQuery->clone()->get();
        
        // Calculate profit for each transaction
        $transactionIds = $allTransactions->pluck('id')->toArray();
        
        $profitData = [];
        $costData = [];
        
        if (!empty($transactionIds)) {
            $profitData = \DB::table('sales_items')
                ->whereIn('transaction_id', $transactionIds)
                ->select(
                    'transaction_id',
                    \DB::raw('SUM(subtotal) as total_sales'),
                    \DB::raw('SUM(COALESCE(cost_price, 0) * quantity) as total_cost')
                )
                ->groupBy('transaction_id')
                ->pluck('total_sales', 'transaction_id')
                ->toArray();
            
            $costData = \DB::table('sales_items')
                ->whereIn('transaction_id', $transactionIds)
                ->select(
                    'transaction_id',
                    \DB::raw('SUM(COALESCE(cost_price, 0) * quantity) as total_cost')
                )
                ->groupBy('transaction_id')
                ->pluck('total_cost', 'transaction_id')
                ->toArray();
        }

        // Add profit to each transaction
        $totalProfit = 0;
        foreach ($allTransactions as $row) {
            $sales = isset($profitData[$row->id]) ? $profitData[$row->id] : $row->total_amount;
            $cost = isset($costData[$row->id]) ? $costData[$row->id] : 0;
            $row->profit = $sales - $cost;
            $totalProfit += $row->profit;
        }

        // PAGINATION
        $data = $transactionQuery
            ->orderBy('sales_transactions.id', 'desc')
            ->offset($request->start ?? 0)
            ->limit($request->length ?? 10)
            ->get();

        // Add profit to paginated data
        foreach ($data as $row) {
            $sales = isset($profitData[$row->id]) ? $profitData[$row->id] : $row->total_amount;
            $cost = isset($costData[$row->id]) ? $costData[$row->id] : 0;
            $row->profit = $sales - $cost;
            
            // FORMAT DATE
            $row->created_at = Carbon::parse($row->created_at)
                ->timezone($settings->timezone ?? 'Africa/Lagos')
                ->format('d M Y h:i A');
        }

        return response()->json([
            "draw" => intval($request->draw ?? 1),
            "recordsTotal" => $total,
            "recordsFiltered" => $total,
            "data" => $data,
            "total_profit" => $totalProfit
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            "error" => $e->getMessage(),
            "draw" => intval($request->draw ?? 1),
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => [],
            "total_profit" => 0
        ], 500);
    }
}


//ADMIN VIEW SALES ITEMS PAGE
/*public function adminSalesItemsPage($id)
{
    $transaction = SalesTransaction::findOrFail($id);
    
    return view('backend.admin_backend.admin_sales_item', compact('transaction'));
}


//ADMIN SALES ITEMS DATA
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

    // 🔥 GET ALL (NO PAGINATION)
    $items = $query->orderBy('id', 'desc')->get();

    // TOTAL (FILTERED)
    $totalAmount = $items->sum('subtotal');
    
    // CALCULATE PROFIT (subtotal - cost_price * quantity)
    $totalCost = $items->sum(function($item) {
        return ($item->cost_price ?? 0) * $item->quantity;
    });
    $totalProfit = $totalAmount - $totalCost;

    return response()->json([
        'data' => $items,
        'total_amount' => $totalAmount,
        'total_profit' => $totalProfit
    ]);
}


//ADMIN ITEM SUGGESTIONS
public function adminItemSuggestions(Request $request, $id)
{
    $query = $request->q;

    return SalesItem::where('transaction_id', $id)
        ->where(function ($q) use ($query) {
            $q->where('product_name', 'LIKE', "%{$query}%")
              ->orWhere('category', 'LIKE', "%{$query}%");
        })
        ->limit(10)
        ->pluck('product_name');
}
*/
}
