<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $logoPath = Setting::get('logo_path');
        $appName  = Setting::get('app_name', config('app.name'));
        return view('settings.index', compact('logoPath', 'appName'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'nullable|string|max:100',
            'logo'     => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        if ($request->filled('app_name')) {
            Setting::set('app_name', $request->app_name);
        }

        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            $oldLogo = Setting::get('logo_path');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            // Simpan logo baru
            $path = $request->file('logo')->store('settings', 'public');
            Setting::set('logo_path', $path);
        }

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function deleteLogo()
    {
        $oldLogo = Setting::get('logo_path');
        if ($oldLogo) {
            Storage::disk('public')->delete($oldLogo);
            Setting::set('logo_path', null);
        }

        return back()->with('success', 'Logo berhasil dihapus.');
    }
}
