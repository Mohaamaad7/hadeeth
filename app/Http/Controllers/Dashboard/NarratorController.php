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
                    ->orWhere('fame_name', 'like', "%{$search}%")
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
            'fame_name' => 'nullable|string|max:255',
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
        $narrator->load('alternatives');
        return view('dashboard.narrators.edit', compact('narrator'));
    }

    /**
     * Update the specified narrator in storage.
     */
    public function update(Request $request, Narrator $narrator): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'fame_name' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'grade_status' => 'nullable|string|max:255',
            'color_code' => 'nullable|string|max:7',
            'alternatives' => 'nullable|array',
            'alternatives.*.alternative_name' => 'required_with:alternatives|string|max:255',
            'alternatives.*.type' => 'required_with:alternatives|string|in:misspelling,variation,title,kunya',
            'alternatives.*.notes' => 'nullable|string|max:255',
        ]);

        // is_companion will be automatically set by NarratorObserver based on grade_status
        $narrator->update(collect($validated)->except('alternatives')->toArray());

        // مزامنة الأسماء البديلة
        $narrator->alternatives()->delete();
        if (!empty($validated['alternatives'])) {
            foreach ($validated['alternatives'] as $alt) {
                if (!empty($alt['alternative_name'])) {
                    $narrator->alternatives()->create($alt);
                }
            }
        }

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

    /**
     * AJAX: بحث ذكي عن الرواة.
     * يبحث في: الاسم الكامل، اسم الشهرة، والأسماء البديلة.
     */
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $narrators = Narrator::where('name', 'LIKE', "%{$q}%")
            ->orWhere('fame_name', 'LIKE', "%{$q}%")
            ->orWhereHas('alternatives', function ($query) use ($q) {
                $query->where('alternative_name', 'LIKE', "%{$q}%");
            })
            ->withCount('hadiths')
            ->limit(15)
            ->get()
            ->map(function ($narrator) use ($q) {
                // تحديد مصدر المطابقة
                $matchType = 'name';
                if ($narrator->fame_name && mb_stripos($narrator->fame_name, $q) !== false) {
                    $matchType = 'fame_name';
                } elseif ($narrator->alternatives->count() > 0) {
                    $alt = $narrator->alternatives->first(function ($a) use ($q) {
                        return mb_stripos($a->alternative_name, $q) !== false;
                    });
                    if ($alt) {
                        $matchType = 'alternative';
                    }
                }

                return [
                    'id' => $narrator->id,
                    'name' => $narrator->name,
                    'fame_name' => $narrator->fame_name,
                    'grade_status' => $narrator->grade_status,
                    'hadiths_count' => $narrator->hadiths_count,
                    'match_type' => $matchType,
                ];
            });

        return response()->json($narrators);
    }

    /**
     * AJAX: إضافة راوي سريع (من modal في صفحة إدخال الحديث).
     */
    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'fame_name' => 'nullable|string|max:255',
            'grade_status' => 'nullable|string|max:255',
        ]);

        $validated['color_code'] = '#22c55e';

        $narrator = Narrator::create($validated);

        return response()->json([
            'success' => true,
            'narrator' => [
                'id' => $narrator->id,
                'name' => $narrator->name,
                'fame_name' => $narrator->fame_name,
            ],
        ]);
    }
}
