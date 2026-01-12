<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Narrator;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NarratorController extends Controller
{
    /**
     * Display a listing of narrators.
     */
    public function index(Request $request): View
    {
        $query = Narrator::withCount('hadiths');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('bio', 'like', "%{$search}%")
                    ->orWhere('grade_status', 'like', "%{$search}%");
            });
        }

        // Filter by grade status
        if ($request->filled('grade_status')) {
            $query->where('grade_status', $request->grade_status);
        }

        $narrators = $query->orderBy('name')->paginate(20);

        return view('dashboard.narrators.index', compact('narrators'));
    }

    /**
     * Show the form for creating a new narrator.
     */
    public function create(): View
    {
        return view('dashboard.narrators.create');
    }

    /**
     * Store a newly created narrator in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'grade_status' => 'nullable|string|max:255',
            'color_code' => 'nullable|string|max:7',
        ]);

        // Set default color if not provided
        $validated['color_code'] = $validated['color_code'] ?? '#22c55e';

        // is_companion will be automatically set by NarratorObserver based on grade_status
        $narrator = Narrator::create($validated);

        return redirect()
            ->route('dashboard.narrators.show', $narrator)
            ->with('success', 'تم إضافة الراوي بنجاح');
    }

    /**
     * Display the specified narrator.
     */
    public function show(Narrator $narrator): View
    {
        $narrator->loadCount('hadiths');
        $narrator->load([
            'hadiths' => function ($query) {
                $query->with(['book', 'sources'])->latest()->take(10);
            }
        ]);

        return view('dashboard.narrators.show', compact('narrator'));
    }

    /**
     * Show the form for editing the specified narrator.
     */
    public function edit(Narrator $narrator): View
    {
        return view('dashboard.narrators.edit', compact('narrator'));
    }

    /**
     * Update the specified narrator in storage.
     */
    public function update(Request $request, Narrator $narrator): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'grade_status' => 'nullable|string|max:255',
            'color_code' => 'nullable|string|max:7',
        ]);

        // is_companion will be automatically set by NarratorObserver based on grade_status
        $narrator->update($validated);

        return redirect()
            ->route('dashboard.narrators.show', $narrator)
            ->with('success', 'تم تحديث بيانات الراوي بنجاح');
    }

    /**
     * Remove the specified narrator from storage.
     */
    public function destroy(Narrator $narrator): RedirectResponse
    {
        // Check if narrator has hadiths
        if ($narrator->hadiths()->count() > 0) {
            return redirect()
                ->route('dashboard.narrators.index')
                ->with('error', 'لا يمكن حذف الراوي لأنه مرتبط بـ ' . $narrator->hadiths()->count() . ' حديث');
        }

        $narrator->delete();

        return redirect()
            ->route('dashboard.narrators.index')
            ->with('success', 'تم حذف الراوي بنجاح');
    }
}
