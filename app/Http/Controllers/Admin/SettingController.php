<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Exception;

class SettingController extends Controller
{
    /**
     * Display the settings form.
     */
    public function index(): View
    {
        $settings = [
            'store_name' => Setting::getValue('store_name', 'Tokoku.id'),
            'store_tagline' => Setting::getValue('store_tagline', 'Premium E-Commerce Platform'),
            'store_logo' => Setting::getValue('store_logo', ''),
            'contact_email' => Setting::getValue('contact_email', 'support@tokoku.id'),
            'contact_phone' => Setting::getValue('contact_phone', '+6281234567890'),
            'contact_address' => Setting::getValue('contact_address', 'Jl. Premium No. 1, Jakarta, Indonesia'),
            'default_shipping_cost' => Setting::getValue('default_shipping_cost', '15000.00'),
            'free_shipping_threshold' => Setting::getValue('free_shipping_threshold', '500000.00'),
            'social_twitter' => Setting::getValue('social_twitter', ''),
            'social_instagram' => Setting::getValue('social_instagram', ''),
            'social_facebook' => Setting::getValue('social_facebook', ''),
            'midtrans_server_key' => '',
            'midtrans_client_key' => '',
            'midtrans_sandbox_mode' => Setting::getValue('midtrans_sandbox_mode', 'true'),
        ];

        // Decrypt Midtrans Keys if present
        $encServerKey = Setting::getValue('midtrans_server_key', '');
        if ($encServerKey) {
            try {
                $settings['midtrans_server_key'] = Crypt::decryptString($encServerKey);
            } catch (Exception $e) {
                // If it fails (e.g. plain text was seeded), fallback to plain
                $settings['midtrans_server_key'] = $encServerKey;
            }
        }

        $encClientKey = Setting::getValue('midtrans_client_key', '');
        if ($encClientKey) {
            try {
                $settings['midtrans_client_key'] = Crypt::decryptString($encClientKey);
            } catch (Exception $e) {
                $settings['midtrans_client_key'] = $encClientKey;
            }
        }

        return view('admin.settings', compact('settings'));
    }

    /**
     * Update settings in the database.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_tagline' => 'nullable|string|max:255',
            'store_logo' => 'nullable|image|max:2048', // Max 2MB
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_address' => 'required|string|max:500',
            'default_shipping_cost' => 'required|numeric|min:0',
            'free_shipping_threshold' => 'required|numeric|min:0',
            'social_twitter' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_facebook' => 'nullable|url|max:255',
            'midtrans_server_key' => 'nullable|string',
            'midtrans_client_key' => 'nullable|string',
            'midtrans_sandbox_mode' => 'nullable|string', // Checkbox is sent if checked, or we toggle
        ]);

        // 1. Save general settings
        Setting::setValue('store_name', $validated['store_name']);
        Setting::setValue('store_tagline', $validated['store_tagline'] ?? '');
        Setting::setValue('contact_email', $validated['contact_email']);
        Setting::setValue('contact_phone', $validated['contact_phone']);
        Setting::setValue('contact_address', $validated['contact_address']);
        Setting::setValue('default_shipping_cost', $validated['default_shipping_cost']);
        Setting::setValue('free_shipping_threshold', $validated['free_shipping_threshold']);
        Setting::setValue('social_twitter', $validated['social_twitter'] ?? '');
        Setting::setValue('social_instagram', $validated['social_instagram'] ?? '');
        Setting::setValue('social_facebook', $validated['social_facebook'] ?? '');

        // 2. Handle logo file upload (local storage)
        if ($request->hasFile('store_logo')) {
            $logoFile = $request->file('store_logo');
            
            // Delete old logo if it exists on public disk
            $oldLogo = Setting::getValue('store_logo', '');
            if ($oldLogo) {
                $filename = basename($oldLogo);
                if (Storage::disk('public')->exists('logo/' . $filename)) {
                    Storage::disk('public')->delete('logo/' . $filename);
                }
            }

            $path = $logoFile->store('logo', 'public');
            $url = Storage::disk('public')->url($path);
            Setting::setValue('store_logo', $url);
        }

        // 3. Encrypt & Save Midtrans Keys
        if (!empty($validated['midtrans_server_key'])) {
            Setting::setValue('midtrans_server_key', Crypt::encryptString($validated['midtrans_server_key']));
        }
        if (!empty($validated['midtrans_client_key'])) {
            Setting::setValue('midtrans_client_key', Crypt::encryptString($validated['midtrans_client_key']));
        }

        // 4. Save Sandbox Mode (Checkbox)
        $sandboxMode = $request->has('midtrans_sandbox_mode') ? 'true' : 'false';
        Setting::setValue('midtrans_sandbox_mode', $sandboxMode);

        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully.');
    }
}
