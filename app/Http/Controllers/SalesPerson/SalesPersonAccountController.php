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
}
