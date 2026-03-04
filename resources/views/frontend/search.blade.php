@extends('layouts.frontend')

@section('title', 'نتائج البحث - موسوعة الحديث الصحيح')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    
    <!-- Search Info -->
    <div class="mb-6 animate-up">
        <h1 class="text-2xl md:text-3xl font-tajawal font-bold text-gray-800 mb-2">
            نتائج البحث
            @if(request('q'))
                عن: <span class="text-emerald-600">"{{ request('q') }}"</span>
            @endif
        </h1>
        <p class="text-gray-500">
            <i class="fa-solid fa-search ml-1"></i>
            تم العثور على <span class="font-bold text-emerald-600">{{ $hadiths->total() }}</span> نتيجة
        </p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 mb-6 animate-up" style="animation-delay: 0.1s;">
        <form action="{{ route('search') }}" method="GET" class="flex flex-wrap gap-3">
            <input type="hidden" name="q" value="{{ request('q') }}">
            
            <select name="grade" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all">
                <option value="">كل الدرجات</option>
                <option value="صحيح" {{ request('grade') === 'صحيح' ? 'selected' : '' }}>صحيح</option>
                <option value="حسن" {{ request('grade') === 'حسن' ? 'selected' : '' }}>حسن</option>
                <option value="ضعيف" {{ request('grade') === 'ضعيف' ? 'selected' : '' }}>ضعيف</option>
            </select>

            <select name="book_id" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all">
                <option value="">كل الكتب</option>
                @foreach($books as $book)
                    <option value="{{ $book->id }}" {{ request('book_id') == $book->id ? 'selected' : '' }}>
                        {{ $book->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition-colors shadow-lg shadow-emerald-100">
                <i class="fa-solid fa-filter ml-1"></i> تطبيق الفلاتر
            </button>

            @if(request()->hasAny(['grade', 'book_id']))
                <a href="{{ route('search') }}?q={{ request('q') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-bold transition-colors">
                    <i class="fa-solid fa-times ml-1"></i> إزالة الفلاتر
                </a>
            @endif
        </form>
    </div>

    <!-- Results -->
    @if($hadiths->count() > 0)
        <div class="space-y-6">
            @foreach($hadiths as $index => $hadith)
            <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:border-emerald-300 hover:shadow-lg transition-all animate-up" style="animation-delay: {{ ($index + 2) * 0.05 }}s;">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex flex-wrap gap-3 mb-4">
                        <span class="bg-emerald-50 text-emerald-800 px-3 py-1.5 rounded-full text-xs font-bold border border-emerald-200">
                            <i class="fa-solid fa-hashtag ml-1"></i> {{ $hadith->number_in_book }}
                        </span>
                        @php
                            $gradeColor = match($hadith->grade) {
                                'صحيح' => 'green',
                                'حسن' => 'blue',
                                default => 'yellow'
                            };
                        @endphp
                        <span class="bg-{{ $gradeColor }}-50 text-{{ $gradeColor }}-700 px-3 py-1.5 rounded-full text-xs font-bold border border-{{ $gradeColor }}-200">
                            <i class="fa-solid fa-check-circle ml-1"></i> {{ $hadith->grade }}
                        </span>
                        @if($hadith->narrators->count() > 0)
                            @foreach($hadith->narrators as $nar)
                                <span class="bg-gray-50 text-gray-600 px-3 py-1.5 rounded-full text-xs font-bold border border-gray-200">
                                    <i class="fa-solid fa-user ml-1"></i> {{ $nar->name }}
                                </span>
                            @endforeach
                        @endif
                        @if($hadith->book)
                            <span class="bg-purple-50 text-purple-600 px-3 py-1.5 rounded-full text-xs font-bold border border-purple-200">
                                <i class="fa-solid fa-book ml-1"></i> {{ $hadith->book->name }}
                            </span>
                        @endif
                    </div>

                    <!-- Hadith Text -->
                    <p class="font-scheherazade text-lg leading-loose text-gray-800 mb-4 pr-4">
                        {{ $hadith->content }}
                    </p>

                    <!-- Footer -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            @if($hadith->sources->count() > 0)
                                <i class="fa-solid fa-book-bookmark text-emerald-500"></i>
                                <span class="font-bold">المصادر:</span>
                                @foreach($hadith->sources->take(3) as $source)
                                    <span class="bg-gray-100 px-2 py-0.5 rounded-lg font-bold">{{ $source->name }}</span>
                                @endforeach
                                @if($hadith->sources->count() > 3)
                                    <span class="text-emerald-600 font-bold">+{{ $hadith->sources->count() - 3 }}</span>
                                @endif
                            @endif
                        </div>
                        <a href="{{ route('hadith.show', $hadith->id) }}" class="text-emerald-600 hover:text-emerald-700 font-bold text-sm transition-colors">
                            عرض التفاصيل <i class="fa-solid fa-arrow-left mr-1"></i>
                        </a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $hadiths->links() }}
        </div>
    @else
        <!-- No Results -->
        <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-gray-100 animate-up">
            <i class="fa-solid fa-search text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-800 mb-2">لم يتم العثور على نتائج</h3>
            <p class="text-gray-500 mb-6">جرب استخدام كلمات مفتاحية أخرى أو أزل الفلاتر</p>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-colors shadow-lg shadow-emerald-100">
                <i class="fa-solid fa-house"></i>
                العودة للرئيسية
            </a>
        </div>
    @endif

</div>
@endsection
