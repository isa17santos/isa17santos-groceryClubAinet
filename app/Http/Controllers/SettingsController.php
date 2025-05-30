<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Settings;

class SettingsController extends Controller
{
    public function edit()
    {
        $setting = Settings::first();
        return view('settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate(['membership_fee' => 'required|numeric|min:0']);

        $setting = Settings::first();
        $setting->update(['membership_fee' => $request->membership_fee]);

        return redirect()->route('settings.edit')->with('success', 'Taxa de adesão atualizada.');
    }
}
