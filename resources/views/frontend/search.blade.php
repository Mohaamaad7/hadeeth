@extends('layouts.frontend')

@section('title', 'نتائج البحث - موسوعة الحديث الصحيح')

@section('content')
    <!-- Header / Navbar -->
    <nav class="bg-white/90 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-100 transform rotate-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 -rotate-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <span class="text-lg font-black text-emerald-950 tracking-tight hidden md:block">موسوعة الحديث الصحيح</span>
                </a>

                <!-- Search Bar -->
                <div class="flex-grow max-w-xl mx-4 hidden md:block">
                    <form action="{{ route('search') }}" method="GET" class="relative">
                        <input type="text" name="q" value="{{ request('q') }}"
                            class="w-full py-2.5 pr-10 pl-4 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all"
                            placeholder="بحث جديد...">
                        <i class="fa-solid fa-magnifying-glass absolute right-3 top-3 text-emerald-400 text-sm"></i>
                    </form>
                </div>

                <!-- Quick Links -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('hadith.random') }}" class="hidden md:flex items-center gap-2 text-gray-600 font-bold px-4 py-2 hover:text-emerald-600 transition-colors">
                        <i class="fa-solid fa-shuffle"></i> عشوائي
                    </a>
                    <button id="mobile-menu-btn" class="md:hidden text-gray-600 hover:text-emerald-600 text-xl">
                        <i class="fa-solid fa-bars-staggered"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu" class="fixed inset-0 bg-white/95 backdrop-blur-sm z-50 transform translate-x-full transition-transform duration-300 flex flex-col items-center justify-center md:hidden">
        <button id="close-menu" class="absolute top-6 left-6 text-3xl text-gray-500 hover:text-red-500">
            <i class="fa-solid fa-times"></i>
        </button>
        <nav class="flex flex-col items-center gap-6 text-xl font-bold text-gray-700">
            <a href="{{ route('home') }}" class="hover:text-emerald-600">الرئيسية</a>
            <a href="#" class="hover:text-emerald-600">الكتب</a>
            <a href="#" class="hover:text-emerald-600">الرواة</a>
            <a href="{{ route('hadith.random') }}" class="hover:text-emerald-600">حديث عشوائي</a>
        </nav>
    </div>

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
                        @if($hadith->narrator)
                            <span class="bg-gray-50 text-gray-600 px-3 py-1.5 rounded-full text-xs font-bold border border-gray-200">
                                <i class="fa-solid fa-user ml-1"></i> {{ $hadith->narrator->name }}
                            </span>
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
                                    <span class="bg-gray-100 px-2 py-0.5 rounded-lg font-bold">{{ $source->code }}</span>
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

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-10 mt-10">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex items-center justify-center gap-3 mb-4">
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <h4 class="text-lg font-black text-gray-900">موسوعة الحديث الصحيح</h4>
            </div>
            <p class="text-gray-400 text-sm">© {{ date('Y') }} جميع الحقوق محفوظة</p>
        </div>
    </footer>
@endsection
