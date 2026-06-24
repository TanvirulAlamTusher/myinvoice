<?php
namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessSettingController extends Controller
{
    /**
     * Display the business settings
     */
    public function index()
    {
        $settings = BusinessSetting::first();
        return view('settings.business_settings.index', compact('settings'));
    }

    /**
     * Save or update business settings
     */
    public function save(Request $request)
    {
        $validated = $request->validate([
            // Company
            'business_name'    => 'nullable|string|max:255',
            'owner_name'       => 'nullable|string|max:255',

            // Header
            'top_tagline'      => 'nullable|string|max:255',
            'tagline'          => 'nullable|string|max:255',

            // Contact
            'phone_1'          => 'nullable|string|max:20',
            'phone_2'          => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:255',
            'website'          => 'nullable|url|max:255',
            'address'          => 'nullable|string',

            // Files
            'logo'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'signature'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon'          => 'nullable|image|mimes:jpeg,png,jpg,ico,svg|max:1024',

            // Footer
            'terms_conditions' => 'nullable|string',
        ]);

        // Get existing record
        $existing = BusinessSetting::first();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($existing && $existing->logo) {
                Storage::disk('public')->delete($existing->logo);
            }
            $validated['logo'] = $request->file('logo')->store('business-settings', 'public');
        } elseif ($existing && $existing->logo) {
            // Keep existing logo if no new file uploaded
            $validated['logo'] = $existing->logo;
        }

        // Handle signature upload
        if ($request->hasFile('signature')) {
            // Delete old signature if exists
            if ($existing && $existing->signature) {
                Storage::disk('public')->delete($existing->signature);
            }
            $validated['signature'] = $request->file('signature')->store('business-settings', 'public');
        } elseif ($existing && $existing->signature) {
            // Keep existing signature if no new file uploaded
            $validated['signature'] = $existing->signature;
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {

            // Delete old favicon if exists
            if ($existing && $existing->favicon) {
                Storage::disk('public')->delete($existing->favicon);
            }

            $validated['favicon'] = $request->file('favicon')
                ->store('business-settings', 'public');

        } elseif ($existing && $existing->favicon) {

            // Keep existing favicon if no new upload
            $validated['favicon'] = $existing->favicon;
        }

        // Update or create using updateOrCreate
        BusinessSetting::updateOrCreate(
            ['id' => $existing->id ?? 0],
            $validated
        );

        return redirect()->route('business-settings.index')
            ->with('success', 'Business settings saved successfully!');
    }

    /**
     * Delete business settings
     */
    /**
     * Delete business settings
     */
    public function destroy()
    {
        $settings = BusinessSetting::first();

        if ($settings) {

            // Delete logo
            if ($settings->logo) {
                Storage::disk('public')->delete($settings->logo);
            }

            // Delete signature
            if ($settings->signature) {
                Storage::disk('public')->delete($settings->signature);
            }

            // Delete favicon
            if ($settings->favicon) {
                Storage::disk('public')->delete($settings->favicon);
            }

            // Delete record
            $settings->delete();

            return redirect()->route('business-settings.index')
                ->with('success', 'Business settings deleted successfully!');
        }

        return redirect()->route('business-settings.index')
            ->with('error', 'No settings found to delete.');
    }
}
