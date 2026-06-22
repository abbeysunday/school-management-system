<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class SchoolSetupController extends Controller
{
    public function index(): View
    {
        $school = SchoolProfile::firstOrFail();
        return view('admin.setup.school', compact('school'));
    }

    public function update(Request $request): RedirectResponse
    {
        $school = SchoolProfile::firstOrFail();

        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'short_name'            => 'nullable|string|max:100',
            'address'               => 'nullable|string',
            'motto'                 => 'nullable|string|max:255',
            'phone'                 => 'nullable|string|max:20',
            'email'                 => 'nullable|email|max:150',
            'website'               => 'nullable|url|max:150',
            'principal_name'        => 'nullable|string|max:150',
            'waec_centre_number'    => 'nullable|string|max:30',
            'neco_centre_number'    => 'nullable|string|max:30',
            'rc_number'             => 'nullable|string|max:50',
            'state'                 => 'nullable|string|max:50',
            'lga'                   => 'nullable|string|max:50',
            'city'                  => 'nullable|string|max:100',
            'ca_weight'             => 'nullable|integer|min:0|max:100',
            'exam_weight'           => 'nullable|integer|min:0|max:100',
            'currency_symbol'       => 'nullable|string|max:5',
            'timezone'              => 'nullable|string|max:100',
            'paystack_public_key'   => 'nullable|string',
            'paystack_secret_key'   => 'nullable|string',
            'termii_api_key'        => 'nullable|string',
            'termii_sender_id'      => 'nullable|string|max:20',
            'mail_from_address'     => 'nullable|email|max:255',
            'mail_from_name'        => 'nullable|string|max:255',
            'sms_on_absence'        => 'boolean',
            'sms_on_payment'        => 'boolean',
            'sms_on_result_publish' => 'boolean',
            'logo'                  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'stamp'                 => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
        ]);

        // ── Logo upload (resize max 300x300) ──
        if ($request->hasFile('logo')) {
            if ($school->logo && Storage::disk('public')->exists($school->logo)) {
                Storage::disk('public')->delete($school->logo);
            }

            $file     = $request->file('logo');
            $filename = 'logo_' . time() . '.png';
            $path     = 'logos/' . $filename;

            Storage::disk('public')->makeDirectory('logos');

            $manager = new ImageManager(new Driver());
            $manager->read($file->getRealPath())
                    ->scaleDown(300, 300)
                    ->save(storage_path('app/public/' . $path));

            $validated['logo'] = $path;
        }

        // ── Stamp upload (resize max 300x300) ──
        if ($request->hasFile('stamp')) {
            if ($school->stamp && Storage::disk('public')->exists($school->stamp)) {
                Storage::disk('public')->delete($school->stamp);
            }

            $file     = $request->file('stamp');
            $filename = 'stamp_' . time() . '.png';
            $path     = 'stamps/' . $filename;

            Storage::disk('public')->makeDirectory('stamps');

            $manager = new ImageManager(new Driver());
            $manager->read($file->getRealPath())
                    ->scaleDown(300, 300)
                    ->save(storage_path('app/public/' . $path));

            $validated['stamp'] = $path;
        }

        $school->update($validated);

        return redirect()
            ->route('admin.setup.school')
            ->with('success', 'School profile updated successfully.');
    }
}
