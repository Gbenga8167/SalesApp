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
    $request->validate([
        'company_name' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:255',
        'logo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
    ]);

    $setting = Setting::first();

    if (!$setting) {
        $setting = new Setting();
    }

    // IMAGE UPLOAD
    if ($request->hasFile('logo')) {

        // delete old
        if ($setting->logo && file_exists(public_path('uploads/settings/'.$setting->logo))) {
            unlink(public_path('uploads/settings/'.$setting->logo));
        }

        $file = $request->file('logo');
        $filename = time().'.'.$file->getClientOriginalExtension();
        $file->move(public_path('uploads/settings'), $filename);

        $setting->logo = $filename;
    }

    $setting->company_name = $request->company_name;
    $setting->address = $request->address;

    $setting->save();

    return back()->with('success', 'Settings updated successfully!');
}
}
