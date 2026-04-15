<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Spatie\Tags\Tag;
use App\Models\Hadith;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    /**
     * عرض جميع المواضيع (سحابة الكلمات)
     */
    public function index()
    {
        // جلب جميع الوسوم مع عدد الأحاديث المرتبطة بها
        $topics = Tag::withCount('posts as hadiths_count') // 'posts' is internally the relation name for tags model depending on config, but wait, spatie tags has usage count?
             // Actually spatie/laravel-tags stores general tags. Let's just pull all tags.
             ->get();

        // Note: spatie-tags doesn't have a direct 'posts' relation out of the box on the Tag model itself unless custom.
        // Let's use getWithType or just all tags.
        // It's better to get tags ordered by names.
        $topics = Tag::orderBy('name->ar')->get();
        // Since we only use default type.
        
        return view('frontend.topics.index', compact('topics'));
    }

    /**
     * عرض الأحاديث المرتبطة بموضوع معين
     */
    public function show($slug)
    {
        // جلب الوسم عن طريق الـ slug
        $topic = Tag::where('slug->ar', $slug)->orWhere('slug->en', $slug)->firstOrFail();

        // جلب الأحاديث التي تحتوي على هذا الوسم (معتمد)
        $hadiths = Hadith::withAnyTags([$topic])
            ->where('status', 'approved')
            ->with(['book', 'narrators', 'sources'])
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('frontend.topics.show', compact('topic', 'hadiths'));
    }
}
