<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Hadith;
use App\Models\HadithChain;
use App\Models\Narrator;
use App\Models\Source;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CleanupController extends Controller
{
    /**
     * عرض صفحة تنظيف قاعدة البيانات مع الإحصائيات.
     */
    public function index(): View
    {
        $stats = [
            'hadiths' => [
                'total' => Hadith::count(),
                'with_narrator' => Hadith::whereNotNull('narrator_id')->count(),
                'without_narrator' => Hadith::whereNull('narrator_id')->count(),
                'with_sources' => Hadith::whereHas('sources')->count(),
                'without_sources' => Hadith::doesntHave('sources')->count(),
                'with_raw_text' => Hadith::whereNotNull('raw_text')->where('raw_text', '!=', '')->count(),
            ],
            'books' => [
                'total' => Book::count(),
                'main' => Book::mainBooks()->count(),
                'chapters' => Book::whereNotNull('parent_id')->count(),
                'empty_main' => Book::mainBooks()->doesntHave('children')->doesntHave('hadiths')->count(),
                'empty_chapters' => Book::whereNotNull('parent_id')->doesntHave('hadiths')->count(),
            ],
            'narrators' => [
                'total' => Narrator::count(),
                'with_hadiths' => Narrator::whereHas('hadiths')->count(),
                'orphan' => Narrator::doesntHave('hadiths')->count(),
            ],
            'sources' => [
                'total' => Source::count(),
                'with_hadiths' => Source::whereHas('hadiths')->count(),
                'orphan' => Source::doesntHave('hadiths')->count(),
            ],
            'chains' => [
                'total' => HadithChain::count(),
            ],
            'pivot' => [
                'hadith_source' => DB::table('hadith_source')->count(),
            ],
        ];

        return view('dashboard.cleanup.index', compact('stats'));
    }

    /**
     * حذف جميع الأحاديث (مع العلاقات).
     */
    public function deleteHadiths(Request $request): RedirectResponse
    {
        $request->validate([
            'confirm' => 'required|in:DELETE',
        ]);

        $count = Hadith::count();

        // حذف العلاقات أولاً — داخل transaction لضمان الاتساق
        DB::transaction(function () {
            DB::table('hadith_source')->delete();
            DB::table('hadith_narrator')->delete();
            DB::table('chain_narrators')->delete();
            HadithChain::query()->delete();
            Hadith::query()->delete();
        });

        return redirect()->route('dashboard.cleanup.index')
            ->with('success', "✅ تم حذف {$count} حديث وجميع العلاقات المرتبطة");
    }

    /**
     * حذف الرواة الأيتام فقط (بدون أحاديث).
     */
    public function deleteOrphanNarrators(Request $request): RedirectResponse
    {
        $request->validate([
            'confirm' => 'required|in:DELETE',
        ]);

        $orphans = Narrator::doesntHave('hadiths')->doesntHave('chains');
        $count = $orphans->count();
        $orphans->delete();

        return redirect()->route('dashboard.cleanup.index')
            ->with('success', "✅ تم حذف {$count} راوي بلا أحاديث");
    }

    /**
     * حذف جميع الرواة (يتطلب حذف الأحاديث أولاً).
     */
    public function deleteAllNarrators(Request $request): RedirectResponse
    {
        $request->validate([
            'confirm' => 'required|in:DELETE',
        ]);

        // فحص: هل هناك أحاديث مرتبطة؟
        $linkedCount = Narrator::whereHas('hadiths')->count();
        if ($linkedCount > 0) {
            return redirect()->route('dashboard.cleanup.index')
                ->with('error', "⛔ لا يمكن حذف الرواة! يوجد {$linkedCount} راوي مرتبط بأحاديث. احذف الأحاديث أولاً.");
        }

        $count = Narrator::count();

        DB::transaction(function () {
            DB::table('chain_narrators')->delete();
            Narrator::query()->delete();
        });

        return redirect()->route('dashboard.cleanup.index')
            ->with('success', "✅ تم حذف {$count} راوي");
    }

    /**
     * حذف الأبواب الفارغة (بدون أحاديث).
     */
    public function deleteEmptyChapters(Request $request): RedirectResponse
    {
        $request->validate([
            'confirm' => 'required|in:DELETE',
        ]);

        $empty = Book::whereNotNull('parent_id')->doesntHave('hadiths');
        $count = $empty->count();
        $empty->delete();

        return redirect()->route('dashboard.cleanup.index')
            ->with('success', "✅ تم حذف {$count} باب فارغ");
    }

    /**
     * حذف جميع الكتب والأبواب (يتطلب حذف الأحاديث أولاً).
     */
    public function deleteAllBooks(Request $request): RedirectResponse
    {
        $request->validate([
            'confirm' => 'required|in:DELETE',
        ]);

        $linkedCount = Hadith::count();
        if ($linkedCount > 0) {
            return redirect()->route('dashboard.cleanup.index')
                ->with('error', "⛔ لا يمكن حذف الكتب! يوجد {$linkedCount} حديث مرتبط. احذف الأحاديث أولاً.");
        }

        // حذف الأبواب (children) أولاً ثم الكتب الرئيسية
        $count = Book::count();

        DB::transaction(function () {
            Book::whereNotNull('parent_id')->delete();
            Book::query()->delete();
        });

        return redirect()->route('dashboard.cleanup.index')
            ->with('success', "✅ تم حذف {$count} كتاب/باب");
    }

    /**
     * حذف المصادر الأيتام (بدون أحاديث).
     */
    public function deleteOrphanSources(Request $request): RedirectResponse
    {
        $request->validate([
            'confirm' => 'required|in:DELETE',
        ]);

        $orphans = Source::doesntHave('hadiths');
        $count = $orphans->count();
        $orphans->delete();

        return redirect()->route('dashboard.cleanup.index')
            ->with('success', "✅ تم حذف {$count} مصدر بلا أحاديث");
    }

    /**
     * حذف سلاسل الإسناد فقط.
     */
    public function deleteChains(Request $request): RedirectResponse
    {
        $request->validate([
            'confirm' => 'required|in:DELETE',
        ]);

        $count = HadithChain::count();

        DB::transaction(function () {
            DB::table('chain_narrators')->delete();
            HadithChain::query()->delete();
        });

        return redirect()->route('dashboard.cleanup.index')
            ->with('success', "✅ تم حذف {$count} سلسلة إسناد");
    }

    /**
     * تنظيف شامل — حذف كل شيء بالترتيب الصحيح.
     */
    public function nukeAll(Request $request): RedirectResponse
    {
        $request->validate([
            'confirm' => 'required|in:NUKE',
        ]);

        // الترتيب مهم: من الأكثر تبعية إلى الأقل — داخل transaction لضمان الاتساق
        DB::transaction(function () {
            DB::table('hadith_source')->delete();
            DB::table('hadith_narrator')->delete();
            DB::table('chain_narrators')->delete();
            HadithChain::query()->delete();
            Hadith::query()->delete();
            Book::whereNotNull('parent_id')->delete();
            Book::query()->delete();
            Narrator::query()->delete();
            // المصادر لا نحذفها — هي القاموس الأساسي
        });

        return redirect()->route('dashboard.cleanup.index')
            ->with('success', '✅ تم تنظيف قاعدة البيانات بالكامل (ما عدا المصادر والمستخدمين)');
    }
}
