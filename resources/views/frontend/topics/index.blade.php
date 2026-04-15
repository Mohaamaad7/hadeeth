@extends('layouts.frontend')

@section('title', 'مواضيع الأحاديث | موسوعة الحديث الصحيح')
@section('meta_description', 'تصفح الأحاديث النبوية الصحيحة مقسمة حسب المواضيع والفهارس لتسهيل البحث والوصول للمعلومة.')

@section('content')
<section class="py-16 bg-gray-50 flex-grow">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-black text-gray-900 mb-4 tracking-tight">
                <i class="fa-solid fa-tags text-emerald-500 mr-2"></i> مواضيع الأحاديث
            </h1>
            <p class="text-xl text-gray-600 font-medium">تصفح الأحاديث الشريفة حسب الفهرس الموضوعي</p>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 md:p-12">
            @if($topics->isEmpty())
                <div class="text-center text-gray-500 py-12">
                    <i class="fa-solid fa-folder-open text-4xl mb-4 text-gray-300"></i>
                    <p class="text-xl">لا توجد مواضيع متاحة حالياً.</p>
                </div>
            @else
                <div class="flex flex-wrap gap-4 justify-center">
                    @foreach($topics as $topic)
                        <a href="{{ route('topics.show', $topic->slug) }}" 
                           class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white transition-all duration-300 font-bold border border-emerald-100 hover:border-emerald-600 shadow-sm hover:shadow-md group">
                            <i class="fa-solid fa-hashtag text-emerald-400 group-hover:text-emerald-200"></i>
                            {{ $topic->name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
