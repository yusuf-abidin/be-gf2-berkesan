<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Get all app settings.
     */
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');

        $data = [
            'ketua_rt' => $settings->get('ketua_rt', ''),
            'bendahara' => $settings->get('bendahara', []),
            'kontak_admin' => $settings->get('kontak_admin', ''),
        ];

        return $this->successResponse($data, "Berhasil mendapatkan pengaturan");
    }

    /**
     * Update app settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'ketua_rt' => 'required|string|max:100',
            'bendahara' => 'required|array',
            'bendahara.*' => 'required|string|max:100',
            'kontak_admin' => 'required|string|max:20',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $settings = Setting::all()->pluck('value', 'key');
        
        $data = [
            'ketua_rt' => $settings->get('ketua_rt', ''),
            'bendahara' => $settings->get('bendahara', []),
            'kontak_admin' => $settings->get('kontak_admin', ''),
        ];

        return $this->successResponse($data, "Berhasil memperbarui pengaturan");
    }
}
