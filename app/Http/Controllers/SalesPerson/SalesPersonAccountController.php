<?php

namespace App\Http\Controllers\SalesPerson;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesPersonAccountController extends Controller
{
    // LOGGED OUT STUDENT
    public function SalesPersonLogout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }//end method


    public function SalesPersonProfile(){

        //GET AUTHENTICATED LOGGED IN USERS(SALES PERSON)
        $id = Auth::user()->id;
        $sales_person_data = User::findOrFail($id);
        return view('backend.sales_person_backend.sales_person_profile_view', compact('sales_person_data'));
    
    
    }//end method




    //SALES PERSON DASHBOARD

//SALES PERSON DASHBOARD 
public function dashboardData()
{
    $userId = auth()->id();

    // ✅ TODAY SALES (REVENUE)
    $todaySales = \DB::table('sales_transactions')
        ->where('cashier_id', $userId)
        ->whereDate('created_at', today())
        ->sum('total_amount');

    // ✅ TODAY TRANSACTIONS
    $totalTransactions = \DB::table('sales_transactions')
        ->where('cashier_id', $userId)
        ->whereDate('created_at', today())
        ->count();

    // ✅ ITEMS SOLD (REAL QUANTITY)
    $itemsSold = \DB::table('sales_items')
        ->join('sales_transactions', 'sales_items.transaction_id', '=', 'sales_transactions.id')
        ->where('sales_transactions.cashier_id', $userId)
        ->whereDate('sales_transactions.created_at', today())
        ->sum('sales_items.quantity');

    // ✅ SALES TREND (TODAY ONLY 🔥)
    $salesChart = \DB::table('sales_transactions')
        ->selectRaw('HOUR(created_at) as hour, SUM(total_amount) as total')
        ->where('cashier_id', $userId)
        ->whereDate('created_at', today())
        ->groupBy('hour')
        ->orderBy('hour', 'ASC')
        ->get();

    return response()->json([
        'todaySales' => $todaySales,
        'totalTransactions' => $totalTransactions,
        'itemsSold' => $itemsSold,
        'salesChart' => $salesChart,
    ]);
}



//PAYMENT CHART DATA
public function paymentChartData()
{
    $data = \DB::table('sales_transactions')
        ->select('payment_method', \DB::raw('COUNT(*) as total'))
        ->where('cashier_id', auth()->id())
        ->whereDate('created_at', today()) // ✅ TODAY FILTER
        ->groupBy('payment_method')
        ->get();

    return response()->json($data);
}


//DAILY SALES CHART
public function dailySalesChart()
{
    $data = \DB::table('sales_transactions')
        ->selectRaw('HOUR(created_at) as hour, SUM(total_amount) as total')
        ->where('cashier_id', auth()->id())
        ->whereDate('created_at', today()) // ✅ TODAY ONLY
        ->groupBy('hour')
        ->orderBy('hour', 'ASC')
        ->get();

    return response()->json($data);
}


//TOP PRODUCTS CHART
public function topProductsChart()
{
    $data = \DB::table('sales_items')
        ->join('sales_transactions', 'sales_items.transaction_id', '=', 'sales_transactions.id')

        ->where('sales_transactions.cashier_id', auth()->id())
        ->whereDate('sales_transactions.created_at', today()) // ✅ TODAY FILTER

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
}
