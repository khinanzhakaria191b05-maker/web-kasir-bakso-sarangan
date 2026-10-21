<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings.edit');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'shop_name'      => 'required|string|max:100',
            'shop_address'   => 'nullable|string|max:200',
            'shop_phone'     => 'nullable|string|max:30',
            'receipt_footer' => 'nullable|string|max:200',
            'qris_image'     => 'nullable|image|max:3072',
        ]);

        foreach (['shop_name', 'shop_address', 'shop_phone', 'receipt_footer'] as $key) {
            Setting::put($key, $data[$key] ?? null);
        }

        if ($request->hasFile('qris_image')) {
            if ($old = Setting::get('qris_image')) {
                Storage::disk('public')->delete($old);
            }
            Setting::put('qris_image', $request->file('qris_image')->store('settings', 'public'));
        }

        return back()->with('success', 'Pengaturan disimpan.');
    }
}
