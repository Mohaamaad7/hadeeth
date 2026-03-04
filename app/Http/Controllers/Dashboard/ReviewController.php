<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Hadith;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:reviewer,admin');
    }

    /**
     * عرض قائمة الأحاديث المعلقة للمراجعة.
     */
    public function index(Request $request): View
    {
        $query = Hadith::with(['book', 'narrators', 'sources', 'enteredBy']);

        // فلتر الحالة
        $status = $request->get('status', 'pending');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // بحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('content', 'LIKE', "%{$search}%")
                    ->orWhere('raw_text', 'LIKE', "%{$search}%")
                    ->orWhere('number_in_book', $search);
            });
        }

        $hadiths = $query->orderBy('created_at', 'desc')->paginate(20);

        // إحصائيات
        $counts = [
            'pending' => Hadith::pending()->count(),
            'approved' => Hadith::approved()->count(),
            'rejected' => Hadith::rejected()->count(),
        ];

        return view('dashboard.review.index', compact('hadiths', 'counts', 'status'));
    }

    /**
     * عرض حديث للمراجعة التفصيلية.
     */
    public function show(Hadith $hadith): View
    {
        $hadith->load(['book', 'narrators', 'sources', 'enteredBy', 'reviewer']);

        // الحديث السابق والتالي في نفس الحالة
        $previousPending = Hadith::pending()
            ->where('id', '<', $hadith->id)
            ->orderBy('id', 'desc')
            ->first();
        $nextPending = Hadith::pending()
            ->where('id', '>', $hadith->id)
            ->orderBy('id', 'asc')
            ->first();

        return view('dashboard.review.show', compact('hadith', 'previousPending', 'nextPending'));
    }

    /**
     * اعتماد حديث.
     */
    public function approve(Request $request, Hadith $hadith): RedirectResponse
    {
        $hadith->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $request->input('notes'),
        ]);

        // الانتقال للحديث التالي المعلّق
        $next = Hadith::pending()->where('id', '>', $hadith->id)->orderBy('id')->first();
        if ($next) {
            return redirect()->route('dashboard.review.show', $next)
                ->with('success', "✅ تم اعتماد الحديث رقم {$hadith->number_in_book}");
        }

        return redirect()->route('dashboard.review.index')
            ->with('success', "✅ تم اعتماد الحديث رقم {$hadith->number_in_book} — لا توجد أحاديث معلقة أخرى");
    }

    /**
     * رفض حديث مع سبب.
     */
    public function reject(Request $request, Hadith $hadith): RedirectResponse
    {
        $request->validate([
            'review_notes' => 'required|string|min:5',
        ], [
            'review_notes.required' => 'يجب كتابة سبب الرفض',
            'review_notes.min' => 'سبب الرفض قصير جداً (5 أحرف على الأقل)',
        ]);

        $hadith->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $request->input('review_notes'),
        ]);

        $next = Hadith::pending()->where('id', '>', $hadith->id)->orderBy('id')->first();
        if ($next) {
            return redirect()->route('dashboard.review.show', $next)
                ->with('success', "❌ تم رفض الحديث رقم {$hadith->number_in_book}");
        }

        return redirect()->route('dashboard.review.index')
            ->with('success', "❌ تم رفض الحديث رقم {$hadith->number_in_book}");
    }

    /**
     * اعتماد جماعي (للأدمن فقط).
     */
    public function bulkApprove(Request $request): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'الاعتماد الجماعي متاح للأدمن فقط');
        }

        $request->validate([
            'hadith_ids' => 'required|array',
            'hadith_ids.*' => 'exists:hadiths,id',
        ]);

        $count = Hadith::whereIn('id', $request->hadith_ids)
            ->where('status', 'pending')
            ->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_notes' => 'اعتماد جماعي',
            ]);

        return redirect()->route('dashboard.review.index')
            ->with('success', "✅ تم اعتماد {$count} حديث");
    }

    /**
     * اعتماد الكل (للأدمن فقط).
     */
    public function approveAll(Request $request): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'الاعتماد الجماعي متاح للأدمن فقط');
        }

        $count = Hadith::pending()->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => 'اعتماد جماعي شامل',
        ]);

        return redirect()->route('dashboard.review.index')
            ->with('success', "✅ تم اعتماد {$count} حديث معلّق");
    }
}
