<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Term;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $parent = auth()->user();
        $children = $parent->children()
            ->with(['user', 'currentEnrollment.classArm.classLevel'])
            ->get();

        $currentTerm = Term::getCurrent();

        return view('parent.dashboard', compact('parent', 'children', 'currentTerm'));
    }
}
