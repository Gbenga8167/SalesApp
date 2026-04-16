<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SalespersonController extends Controller
{
    /**
     * Show create form
     */
    public function create()
    {
        return view('backend.admin_backend.salesperson.create');
    }

    /**
     * Store new salesperson
     */
    public function store(Request $request)
    {
         //dd($request->all());
        // ✅ VALIDATION
        $request->validate([
            'name' => 'required|string|max:255',
            'user_name' => 'required|string|max:100|unique:users,user_name',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|max:20',
            'state_of_origin' => 'required|string|max:255',
            'contact_address' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);


        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            //update the admin profile image in the image folder directory, to avoid show previous image repeatedly
            @unlink(public_path('uploads/salesperson_photos'.$userPhoto->photo));
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/salesperson_photos'), $filename);
            $photoPath =  $filename;
        }

        
        // ✅ CREATE USER (Salesperson role = 2)
        User::create([
            'name' => $request->name,
            'user_name' => $request->user_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'state_of_origin' => $request->state_of_origin,
            'contact_address' => $request->contact_address,
            'photo' => $photoPath,
            'role' => 2,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Salesperson created successfully!');
    }

    
    public function ManageSalesPerson(){
    $salespersons = User::where('role', 2)->latest()->get();
    return view('backend.admin_backend.salesperson.manage_salesperson', compact('salespersons'));
}


public function EditSalesPerson($id)
{
    $salesperson = User::findOrFail($id);

    $states = [
        'Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno',
        'Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','Gombe','Imo',
        'Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos',
        'Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers',
        'Sokoto','Taraba','Yobe','Zamfara','FCT'
    ];

    return view('backend.admin_backend.salesperson.edit_salesperson', compact('salesperson','states'));
}


    public function UpdateSalesPerson(Request $request, $id){
    $salesperson = User::findOrFail($id);

    // ✅ VALIDATION
    $request->validate([
        'name' => 'required|string|max:255',
        'user_name' => 'required|string|max:100|unique:users,user_name,'.$id,
        'email' => 'required|email|unique:users,email,'.$id,
        'phone_number' => 'required',
        'state_of_origin' => 'required',
        'contact_address' => 'required',
        'password' => 'nullable|min:6|confirmed', // 👈 KEY PART
    ]);


    if ($request->hasFile('photo')) {

    // delete old
    if ($salesperson->photo) {
        $oldPath = public_path('uploads/salesperson_photos/'.$salesperson->photo);
        if (file_exists($oldPath)) {
            unlink($oldPath);
        }
    }

    // upload new
    $file = $request->file('photo');
    $filename = time().'.'.$file->getClientOriginalExtension();
    $file->move(public_path('uploads/salesperson_photos'), $filename);

    $salesperson->photo = $filename;
}


    // ✅ UPDATE BASIC INFO
    $salesperson->name = $request->name;
    $salesperson->user_name = $request->user_name;
    $salesperson->email = $request->email;
    $salesperson->phone_number = $request->phone_number;
    $salesperson->state_of_origin = $request->state_of_origin;
    $salesperson->contact_address = $request->contact_address;

    // ✅ PASSWORD UPDATE (ONLY IF FILLED)
    if ($request->filled('password')) {
        $salesperson->password = Hash::make($request->password);
    }

    $salesperson->save();

    return redirect()->route('manage.salesperson')->with('success', 'Salesperson updated successfully!');
}



public function DeleteSalesPerson($id)
{
    $salesperson = User::findOrFail($id);

    if ($salesperson->photo) {
        $filePath = public_path('uploads/salesperson_photos/' . $salesperson->photo);

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $salesperson->delete();

    return redirect()->back()->with('success', 'Salesperson deleted successfully!');
}


}