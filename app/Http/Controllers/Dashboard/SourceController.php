<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SourceController extends Controller
{
    /**
     * AJAX: بحث عن المصادر.
     */
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (mb_strlen($q) < 1) {
            return response()->json([]);
        }

        $sources = Source::where('name', 'LIKE', "%{$q}%")
            ->orWhere('code', 'LIKE', "%{$q}%")
            ->orWhere('type', 'LIKE', "%{$q}%")
            ->withCount('hadiths')
            ->limit(20)
            ->get()
            ->map(function ($source) {
                return [
                    'id' => $source->id,
                    'name' => $source->name,
                    'code' => $source->code,
                    'type' => $source->type,
                    'hadiths_count' => $source->hadiths_count,
                ];
            });

        return response()->json($sources);
    }

    /**
     * Display a listing of sources.
     */
    public function index(Request $request): View
    {
        $query = Source::withCount('hadiths');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $sources = $query->orderBy('code')->paginate(30);

        return view('dashboard.sources.index', compact('sources'));
    }

    /**
     * Show the form for creating a new source.
     */
    public function create(): View
    {
        return view('dashboard.sources.create');
    }

    /**
     * Store a newly created source in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:sources,code',
            'type' => 'nullable|string|max:255',
        ]);

        $source = Source::create($validated);

        return redirect()
            ->route('dashboard.sources.show', $source)
            ->with('success', 'تم إضافة المصدر بنجاح');
    }

    /**
     * Display the specified source.
     */
    public function show(Source $source): View
    {
        $source->loadCount('hadiths');
        $source->load([
            'hadiths' => function ($query) {
                $query->with(['book', 'narrators'])->latest()->take(10);
            }
        ]);

        return view('dashboard.sources.show', compact('source'));
    }

    /**
     * Show the form for editing the specified source.
     */
    public function edit(Source $source): View
    {
        return view('dashboard.sources.edit', compact('source'));
    }

    /**
     * Update the specified source in storage.
     */
    public function update(Request $request, Source $source): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:sources,code,' . $source->id,
            'type' => 'nullable|string|max:255',
        ]);

        $source->update($validated);

        return redirect()
            ->route('dashboard.sources.show', $source)
            ->with('success', 'تم تحديث بيانات المصدر بنجاح');
    }

    /**
     * Remove the specified source from storage.
     */
    public function destroy(Source $source): RedirectResponse
    {
        // Check if source has hadiths
        if ($source->hadiths()->count() > 0) {
            return redirect()
                ->route('dashboard.sources.index')
                ->with('error', 'لا يمكن حذف المصدر لأنه مرتبط بـ ' . $source->hadiths()->count() . ' حديث');
        }

        $source->delete();

        return redirect()
            ->route('dashboard.sources.index')
            ->with('success', 'تم حذف المصدر بنجاح');
    }
}
