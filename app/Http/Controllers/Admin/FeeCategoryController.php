<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RealRashid\SweetAlert\Facades\Alert;

class FeeCategoryController extends Controller
{
    public function index(): View
    {
        $categories = FeeCategory::orderBy('display_order')->get();
        return view('admin.fees.categories', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100|unique:fee_categories,name',
            'description'   => 'nullable|string|max:500',
            'is_compulsory' => 'boolean',
            'display_order' => 'required|integer|min:0|max:999',
            'is_active'     => 'boolean',
        ]);

        $validated['is_compulsory'] = $request->boolean('is_compulsory', true);
        $validated['is_active']     = $request->boolean('is_active', true);

        FeeCategory::create($validated);

        Alert::success('Success', 'Fee category created.');
        return redirect()->route('admin.fees.categories');
    }

    public function edit(FeeCategory $feeCategory): View
    {
        $categories = FeeCategory::orderBy('display_order')->get();
        return view('admin.fees.categories', compact('categories', 'feeCategory'));
    }

    public function update(Request $request, FeeCategory $feeCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100|unique:fee_categories,name,' . $feeCategory->id,
            'description'   => 'nullable|string|max:500',
            'is_compulsory' => 'boolean',
            'display_order' => 'required|integer|min:0|max:999',
            'is_active'     => 'boolean',
        ]);

        $validated['is_compulsory'] = $request->boolean('is_compulsory', true);
        $validated['is_active']     = $request->boolean('is_active', true);

        $feeCategory->update($validated);

        Alert::success('Success', 'Fee category updated.');
        return redirect()->route('admin.fees.categories');
    }

    public function destroy(FeeCategory $feeCategory): RedirectResponse
    {
        if ($feeCategory->feeStructures()->exists()) {
            Alert::error('Error', 'Cannot delete category with fee structures.');
            return redirect()->route('admin.fees.categories');
        }

        $feeCategory->delete();
        Alert::success('Success', 'Fee category deleted.');
        return redirect()->route('admin.fees.categories');
    }
}
