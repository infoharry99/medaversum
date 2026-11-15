<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\ActivityLog;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        $activityLogs = ActivityLog::with('user')->latest()->take(30)->get();

        return view('settings.index', compact('settings', 'activityLogs'));
    }

    public function update(Request $request)
    {
        $fields = [
            'institute_name' => 'general',
            'institute_tagline' => 'general',
            'contact_email' => 'general',
            'contact_phone' => 'general',
            'address' => 'general',
            'student_id_prefix' => 'general',
            'currency_symbol' => 'fees',
            'currency_code' => 'fees',
            'gst_number' => 'fees',
            'receipt_prefix' => 'fees',
        ];

        foreach ($fields as $key => $group) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key), $group);
            }
        }

        ActivityLog::record(
            'Update Settings',
            'System',
            1,
            "System configuration settings updated by " . auth()->user()->name . "."
        );

        return back()->with('success', 'System configurations updated successfully.');
    }
}
