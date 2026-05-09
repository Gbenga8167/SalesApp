<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    //SETTING PAGE
    public function SettingsPage(){
    $setting = Setting::first(); // only one record
    return view('backend.admin_backend.settings.index', compact('setting'));
}

public function UpdateSettings(Request $request)
{
    try {

        // =========================================
        // VALIDATE FORM INPUTS
        // =========================================
        $request->validate([

            // COMPANY NAME
            'company_name' => 'nullable|string|max:255',

            // COMPANY ADDRESS
            'address' => 'nullable|string|max:255',

            // COMPANY PHONE NUMBER
            'phone_number' => 'nullable|string|max:40',

            // COMPANY EMAIL
            'email' => 'nullable|email|max:255',

            // COMPANY LOGO
            'logo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',

            // TIMEZONE
            'timezone' => 'nullable|string|max:100',

        ]);



        // =========================================
        // GET FIRST SETTINGS RECORD
        // =========================================
        $setting = Setting::first();



        // =========================================
        // CREATE SETTINGS IF EMPTY
        // =========================================
        if (!$setting) {

            $setting = new Setting();

        }



        // =========================================
        // HANDLE LOGO UPLOAD
        // =========================================
        if ($request->hasFile('logo')) {

            // DELETE OLD LOGO
            if (
                $setting->logo
                &&
                file_exists(
                    public_path('uploads/settings/'.$setting->logo)
                )
            ) {

                unlink(
                    public_path('uploads/settings/'.$setting->logo)
                );

            }



            // GET FILE
            $file = $request->file('logo');



            // CREATE UNIQUE FILENAME
            $filename = time().'.'.$file->getClientOriginalExtension();



            // MOVE FILE
            $file->move(
                public_path('uploads/settings'),
                $filename
            );



            // SAVE LOGO NAME
            $setting->logo = $filename;
        }



        // =========================================
        // SAVE COMPANY DETAILS
        // =========================================
        $setting->company_name = $request->company_name;

        $setting->address = $request->address;

        $setting->phone_number = $request->phone_number;

        $setting->email = $request->email;

        $setting->timezone = $request->timezone;



        // =========================================
        // SAVE SETTINGS
        // =========================================
        $setting->save();



        // =========================================
        // SUCCESS MESSAGE
        // =========================================
        return back()->with(
            'success',
            'Settings updated successfully!'
        );



    } catch (\Exception $e) {

        // =========================================
        // ERROR MESSAGE
        // =========================================
        return back()->with(
            'error',
            'Something went wrong. Please try again!'
        );

    }
}


}
