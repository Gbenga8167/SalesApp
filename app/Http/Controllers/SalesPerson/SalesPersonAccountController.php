<?php

namespace App\Http\Controllers\SalesPerson;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
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

//SALES PERSON DASHBOARD  GOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOODDDDDDDDDDDDDD

public function dashboardData()
{
    $userId = auth()->id();

    $settings = \DB::table('settings')->first();
    $tz = $settings->timezone ?? 'Africa/Lagos';

    $start = Carbon::now($tz)->startOfDay()->timezone('UTC');
    $end   = Carbon::now($tz)->endOfDay()->timezone('UTC');

    // ✅ TODAY SALES
    $todaySales = \DB::table('sales_transactions')
        ->where('cashier_id', $userId)
        ->whereBetween('created_at', [$start, $end])
        ->sum('total_amount');

    // ✅ TRANSACTIONS
    $totalTransactions = \DB::table('sales_transactions')
        ->where('cashier_id', $userId)
        ->whereBetween('created_at', [$start, $end])
        ->count();

    // ✅ ITEMS SOLD
    $itemsSold = \DB::table('sales_items')
        ->join('sales_transactions', 'sales_items.transaction_id', '=', 'sales_transactions.id')
        ->where('sales_transactions.cashier_id', $userId)
        ->whereBetween('sales_transactions.created_at', [$start, $end])
        ->sum('sales_items.quantity');

    // ✅ SALES TREND
    $salesChartRaw = \DB::table('sales_transactions')
        ->selectRaw("
            HOUR(CONVERT_TZ(created_at, '+00:00', ?)) as hour,
            SUM(total_amount) as total
        ", [$this->getMysqlOffset($tz)])
        ->where('cashier_id', $userId)
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


// PAYMENT CHART DATA (TODAY)
public function paymentChartData()
{
    $settings = \DB::table('settings')->first();
    $tz = $settings->timezone ?? 'Africa/Lagos';

    $start = Carbon::now($tz)->startOfDay()->timezone('UTC');
    $end   = Carbon::now($tz)->endOfDay()->timezone('UTC');

    $data = \DB::table('sales_transactions')
        ->select('payment_method', \DB::raw('COUNT(*) as total'))
        ->where('cashier_id', auth()->id())
        ->whereBetween('created_at', [$start, $end])
        ->groupBy('payment_method')
        ->get();

    return response()->json($data);
}



// DAILY SALES CHART (TODAY → HOURLY TREND)
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
        ->where('cashier_id', auth()->id())
        ->whereBetween('created_at', [$start, $end])
        ->groupBy('date')
        ->orderBy('date', 'ASC')
        ->get();

    return response()->json($data);
}


// TOP PRODUCTS (TODAY)
public function topProductsChart()
{
    $settings = \DB::table('settings')->first();
    $tz = $settings->timezone ?? 'Africa/Lagos';

    $start = Carbon::now($tz)->startOfDay()->timezone('UTC');
    $end   = Carbon::now($tz)->endOfDay()->timezone('UTC');

    $data = \DB::table('sales_items')
        ->join('sales_transactions', 'sales_items.transaction_id', '=', 'sales_transactions.id')
        ->where('sales_transactions.cashier_id', auth()->id())
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


//ADD THIS HELPER TO CONVERT THE TIME UTC FROM DATA BASE TO ADMIN SET TIME ZONE
private function getMysqlOffset($timezone)
{
    $now = new \DateTime("now", new \DateTimeZone($timezone));
    $offset = $now->getOffset() / 3600;

    return ($offset >= 0 ? '+' : '') . $offset . ':00';
}

}
