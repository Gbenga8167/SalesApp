<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\NewStockArrivalController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\Admin\SalespersonController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\SalesPerson\SalesPersonAccountController;
use App\Http\Controllers\SalesPerson\SalesPersonPOSController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// Show login page if guest
Route::get('/', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

// If logged in, redirect to correct dashboard
Route::get('/', function () {
    $user = auth()->user();

    if ($user->role == 1) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role == 2) {
        return redirect()->route('sales_person.dashboard');
    } 
})->middleware('auth');







//sales_person_backend Route for student dashboard
Route::get('/sales-person/dashboard', function () {
    return view('backend.sales_person_backend.sales_person_index');
})->middleware(['auth', 'verified', 'sales.person'])->name('sales_person.dashboard');


    //Sales Person All Route  
    Route::middleware(['auth', 'sales.person'])->group(function(){
    //Start Student All Route  
    Route::controller(SalesPersonAccountController::class)->group(function(){
    Route::get('sales-person/logout','SalesPersonLogout')->name('sales.person.logout');
    Route::get('sales-person/profile','SalesPersonProfile')->name('sales.person.profile');

    //SALES PERSON DASHBOARD ALL ROUTE
    Route::get('/sales/dashboard-data', 'dashboardData')->name('sales.dashboard.data');
    Route::get('/sales/payment-chart-data', 'paymentChartData')->name('sales.payment.chart');
    Route::get('/sales/daily-chart', 'dailySalesChart')->name('sales.daily.chart');
    Route::get('/sales/top-products-chart', 'topProductsChart')->name('sales.top.products.chart');

});

    Route::controller(SalesPersonPOSController::class)->group(function(){

    Route::get('/pos', 'index')->name('sales.pos');

    Route::post('/cart/add',  'addToCart')->name('cart.add');

    Route::post('/cart/remove/{id}',  'removeFromCart')->name('cart.remove');

    Route::post('/cart/update',  'updateCart')->name('cart.update');

    Route::get('/cart',  'getCart')->name('cart.get');

    Route::post('/cart/clear',  'clearCart')->name('cart.clear');

    Route::get('/sales-person/products/search', 'searchProducts')->name('sales.person.products.search');

    Route::get('/sales-person/product/details', 'getProductDetails')->name('sales.person.product.details');

    // PEND TRANSACTIONS
    Route::post('/cart/pend', 'pendTransaction')->name('cart.pend');
    Route::get('/cart/pending', 'getPending')->name('cart.pending');
    Route::post('/cart/load-pending/{id}', 'loadPending')->name('cart.load.pending');
    //DELETE PENDING TRANSACTIONS
    Route::post('/cart/delete-pending/{id}', 'deletePending')->name('cart.delete.pending');

    //CONFIRM SALES
    Route::post('/cart/confirm', 'confirmSale')->name('cart.confirm');

    //PRINT RECEIPT
    Route::get('/receipt/{id}', 'printReceipt')->name('receipt.print');

    //SALES HISTORY
    Route::get('/sales/person/history', 'salesPersonHistory')->name('sales.person.history');
    Route::get('/sales/history/data', 'salesPersonHistoryData')->name('sales.person.history.data');


    //SALE PERSON'S SALE ITEMS
    Route::get('/sales-items/{id}', 'salesItems')->name('sales.items');
    Route::get('/sales-items-suggestions/{id}', 'itemSuggestions')->name('sales.items.suggestions');
    //sales person item page
    Route::get('/sales-items-page/{id}', 'salesItemsPage')->name('sales.items.page');
});




});








// Start Admin All Route
    //Admin Dashbord Login Route
    Route::get('/admin/dashboard', function () {
    return view('backend.admin_backend.admin_index');
})->middleware(['auth', 'verified', 'admin'])->name('admin.dashboard');


 //Admin All Route
     Route::middleware(['auth', 'admin'])->group(function(){

    Route::controller(AdminController::class)->group(function(){
    Route::get('admin/logout','AdminLogout')->name('admin.logout');
    Route::get('admin/profile','AdminProfile')->name('admin.profile');
    Route::post('admin/profile/update','AdminProfileUpdate')->name('admin.profile.update');
    Route::get('admin/password/change','AdminPasswordChange')->name('admin.password.change');
    Route::post('admin/password/update','AdminPasswordUpdate')->name('admin.password.update');

});



    Route::controller(SalespersonController::class)->group(function(){

    //ADMIN CREATE SALES PERSON ACCTOUNT
    Route::get('/admin/create-salesperson', 'create')->name('create.salesperson');
    Route::post('/admin/store-salesperson', 'store')->name('store.salesperson');


    //MANAGE SALES PERSON ACCOUNT (EDIT UPDATE AND DELETE)
    Route::get('/admin/manage-salesperson', 'ManageSalesPerson')->name('manage.salesperson');
    Route::get('/admin/edit-salesperson/{id}', 'EditSalesPerson')->name('edit.salesperson');
    Route::post('/admin/update-salesperson/{id}', 'UpdateSalesPerson')->name('update.salesperson');
    Route::get('/admin/delete-salesperson/{id}', 'DeleteSalesPerson')->name('delete.salesperson');


 });


 //PRODUCT DETAILS ROUTE

    Route::controller(ProductController::class)->group(function(){

    //CREATE ROUTE
    Route::get('/admin/create-product', 'create')->name('create.product');

    //STORE ROUTE
    Route::post('/admin/store-product', 'store')->name('store.product');

    //MANGE ROTE
    Route::get('/admin/manage-product', 'index')->name('manage.product');

     //EDIT ROUTE
    Route::get('/admin/edit-product/{id}', 'edit')->name('edit.product');

    //UPDATE ROUTE
    Route::post('/admin/update-product/{id}', 'update')->name('update.product');

    Route::get('/admin/delete-product/{id}', 'delete')->name('delete.product');

    //UPLOAD CSV
   Route::post('/product/upload-csv', 'previewCsv')->name('product.upload.csv');

   //CONFIRM
   Route::post('/product/confirm-csv',  'confirmCsv')->name('product.confirm.csv');
});




    Route::controller(NewStockArrivalController::class)->group(function () {

    Route::get('/stocks/create', 'create')->name('stocks.create');
    Route::post('/stocks/store', 'store')->name('stocks.store');

    // AJAX
    Route::get('/get-product-categories', 'getCategories')->name('stocks.categories');

    //MANAGE STOCK
    Route::get('/manage-stocks', 'manageStocks')->name('stocks.manage');
   // Route::get('/stock-history', 'stockHistory')->name('stocks.history');
    Route::get('/stock-history/{product}/{category}', 'stockHistory')->name('stocks.history');

    //DELETE 

    Route::get('/stock/edit/{id}', 'editStock')->name('stocks.edit');
    Route::post('/stock/update/{id}', 'updateStock')->name('stocks.update');

    Route::get('/stock/delete/{id}', 'deleteStock')->name('stocks.delete');
});



    //STOCK FOR SALES ROUTE
    Route::controller(SalesController::class)->group(function (){

    Route::get('/create/sale', 'CreateSale')->name('create.sale');
    

    // AJAX
    Route::get('/get-sale-categories', 'getSaleCategories')->name('create.sale.categories');

    Route::post('/stock-for-sale/store', 'storeStockForSale')->name('sales.store');

    //GET COST PRICE
    Route::get('/get-cost-price', 'getCostPrice')->name('stocks.cost.price');

    //MANAGE SALES
    Route::get('/manage-stock', 'ManageStock')->name('manage.stock');

    //SALES HISTORY
    Route::get('/sales-history/{product}/{category}', 'SalesHistory')->name('sales.history');

    //EDIT AND DELETE 
    Route::get('/sales/edit/{id}','EditSale')->name('sales.edit');
    //UPDATE
    Route::post('/sales/update/{id}', 'UpdateSale')->name('sales.update');

    //DELETE 
    Route::get('/sales/delete/{id}', 'DeleteSale')->name('sales.delete');

    //GET AVAILABLESTOCK
    Route::get('/stocks/available', 'getAvailableStock')->name('stocks.available');
});


//SETTINGS ROUTE
    Route::controller(SettingController::class)->group(function (){
    Route::get('/admin/settings',  'SettingsPage')->name('admin.settings');
    Route::post('/admin/settings/update', 'UpdateSettings')->name('admin.settings.update');
     });
     
}); //end admin route










require __DIR__.'/auth.php';
