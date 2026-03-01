@extends('layouts.frontend')

@section('title'){{ $book->name }} - موسوعة الحديث الصحيح@endsection
@section('meta_description', 'تصفح ' . $book->name . ' في موسوعة الحديث الصحيح')

@section('content')
    <!-- Navbar -->
    <nav class="bg-white/90 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-100 transform rotate-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5 -rotate-3">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <span class="text-lg font-black text-emerald-950 tracking-tight hidden md:block">موسوعة الحديث
                        الصحيح</span>
                </a>
                <div class="hidden md:flex items-center space-x-reverse space-x-10">
                    <a href="{{ route('home') }}"
                        class="text-gray-500 font-semibold hover:text-emerald-600 transition-colors">الرئيسية</a>
                    <a href="{{ route('books.index') }}"
                        class="text-emerald-700 font-bold hover:text-emerald-500 transition-colors">الكتب</a>
                    <a href="{{ route('about') }}"
                        class="text-gray-500 font-semibold hover:text-emerald-600 transition-colors">عن المشروع</a>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('hadith.random') }}"
                        class="hidden md:flex items-center gap-2 text-gray-600 font-bold px-4 py-2 hover:text-emerald-600 transition-colors">
                        <i class="fa-solid fa-shuffle"></i> حديث عشوائي
                    </a>
                    <button id="mobile-menu-btn" class="md:hidden text-gray-600 hover:text-emerald-600 text-xl">
                        <i class="fa-solid fa-bars-staggered"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobile-menu"
        class="fixed inset-0 bg-white/95 backdrop-blur-sm z-50 transform translate-x-full transition-transform duration-300 flex flex-col items-center justify-center md:hidden">
        <button id="close-menu" class="absolute top-6 left-6 text-3xl text-gray-500 hover:text-red-500">
            <i class="fa-solid fa-times"></i>
        </button>
        <nav class="flex flex-col items-center gap-6 text-xl font-bold text-gray-700">
            <a href="{{ route('home') }}" class="hover:text-emerald-600">الرئيسية</a>
            <a href="{{ route('books.index') }}" class="text-emerald-600">الكتب</a>
            <a href="{{ route('about') }}" class="hover:text-emerald-600">عن المشروع</a>
            <a href="{{ route('hadith.random') }}" class="hover:text-emerald-600">حديث عشوائي</a>
        </nav>
    </div>

    <!-- Breadcrumb -->
    <div class="bg-gray-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <nav class="flex items-center gap-2 text-sm text-gray-500 font-medium flex-wrap">
                <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">
                    <i class="fa-solid fa-house"></i>
                </a>
                <i class="fa-solid fa-chevron-left text-[10px] text-gray-300"></i>
                <a href="{{ route('books.index') }}" class="hover:text-emerald-600 transition-colors">الكتب</a>
                @if($parentBook)
                    <i class="fa-solid fa-chevron-left text-[10px] text-gray-300"></i>
                    <a href="{{ route('books.show', $parentBook) }}"
                        class="hover:text-emerald-600 transition-colors">{{ $parentBook->name }}</a>
                @endif
                <i class="fa-solid fa-chevron-left text-[10px] text-gray-300"></i>
                <span class="text-emerald-700 font-bold">{{ $book->name }}</span>
            </nav>
        </div>
    </div>

    <!-- Book Header -->
    <section class="hero-pattern py-12 text-white">
        <div class="max-w-5xl mx-auto px-4 text-center relative z-10">
            @if($parentBook)
                <p class="text-emerald-300 font-bold mb-2">
                    <i class="fa-solid fa-book ml-1"></i> {{ $parentBook->name }}
                </p>
            @endif
            <h1 class="text-2xl lg:text-4xl font-extrabold mb-3 animate-fade-in">
                {{ $book->name }}
            </h1>
            @if($chapters)
                <p class="text-emerald-100/80 font-medium">
                    {{ $chapters->count() }} باب
                </p>
            @elseif($hadiths)
                <p class="text-emerald-100/80 font-medium">
                    {{ $hadiths->total() }} حديث
                </p>
            @endif

            <!-- PDF Download Buttons -->
            @php
                $pdfBook = $parentBook ?? $book;
                $isChapter = $parentBook !== null;
            @endphp
            <div class="flex flex-wrap items-center justify-center gap-3 mt-5">
                {{-- Full Book PDF --}}
                <a href="{{ route('books.pdf', $pdfBook) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-5 py-2 bg-white/15 backdrop-blur-sm border border-white/30 rounded-xl text-white font-bold text-xs hover:bg-white/25 transition-all hover:scale-105">
                    <i class="fa-solid fa-file-pdf text-red-300"></i>
                    PDF المستخرج
                    <span class="text-emerald-200 text-[10px]">({{ $pdfBook->name }})</span>
                </a>
                <a href="{{ route('books.pdf', [$pdfBook, 'type' => 'original']) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-5 py-2 bg-amber-900/30 backdrop-blur-sm border border-amber-300/30 rounded-xl text-white font-bold text-xs hover:bg-amber-900/50 transition-all hover:scale-105">
                    <i class="fa-solid fa-scroll text-amber-300"></i>
                    PDF الأصل
                    <span class="text-amber-200 text-[10px]">({{ $pdfBook->name }})</span>
                </a>

                {{-- Per-Chapter PDF (only when viewing a chapter) --}}
                @if($isChapter)
                    <a href="{{ route('books.pdf', $book) }}" target="_blank"
                        class="inline-flex items-center gap-2 px-5 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white/80 font-bold text-xs hover:bg-white/20 transition-all hover:scale-105">
                        <i class="fa-solid fa-file-pdf text-red-200"></i>
                        PDF هذا الباب
                    </a>
                    <a href="{{ route('books.pdf', [$book, 'type' => 'original']) }}" target="_blank"
                        class="inline-flex items-center gap-2 px-5 py-2 bg-amber-900/20 backdrop-blur-sm border border-amber-300/20 rounded-xl text-white/80 font-bold text-xs hover:bg-amber-900/40 transition-all hover:scale-105">
                        <i class="fa-solid fa-scroll text-amber-200"></i>
                        PDF هذا الباب (الأصل)
                    </a>
                @endif
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- ========== Scenario A: Show Chapters ========== --}}
        @if($chapters)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($chapters as $index => $chapter)
                    <a href="{{ route('books.show', $chapter) }}"
                        class="floating-card bg-white rounded-2xl border border-gray-100 shadow-sm p-5 group animate-up"
                        style="animation-delay: {{ $index * 0.03 }}s;">
                        <div class="flex items-start gap-3">
                            <div
                                class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0 font-black text-sm border border-emerald-100">
                                {{ $chapter->sort_order }}
                            </div>
                            <div class="flex-grow">
                                <h3
                                    class="font-bold text-gray-800 group-hover:text-emerald-600 transition-colors leading-relaxed mb-2">
                                    {{ $chapter->name }}
                                </h3>
                                <div class="flex items-center gap-2 text-xs text-gray-400">
                                    <i class="fa-solid fa-scroll"></i>
                                    <span class="font-bold">{{ $chapter->hadiths_count }}</span> حديث
                                </div>
                            </div>
                            <i
                                class="fa-solid fa-arrow-left text-gray-300 group-hover:text-emerald-500 group-hover:translate-x-[-4px] transition-all mt-2"></i>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- ========== Scenario B: Show Hadiths ========== --}}
        @elseif($hadiths && $hadiths->count() > 0)
            <div class="space-y-5">
                @foreach($hadiths as $index => $hadith)
                    <article
                        class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:border-emerald-300 hover:shadow-lg transition-all animate-up"
                        style="animation-delay: {{ $index * 0.04 }}s;">
                        <div class="p-6">
                            <!-- Header -->
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span
                                    class="bg-emerald-50 text-emerald-800 px-3 py-1 rounded-full text-xs font-bold border border-emerald-200">
                                    <i class="fa-solid fa-hashtag ml-1"></i> {{ $hadith->number_in_book }}
                                </span>
                                @php
                                    $gradeColor = match ($hadith->grade) {
                                        'صحيح' => 'green',
                                        'حسن' => 'blue',
                                        default => 'yellow'
                                    };
                                @endphp
                                <span
                                    class="bg-{{ $gradeColor }}-50 text-{{ $gradeColor }}-700 px-3 py-1 rounded-full text-xs font-bold border border-{{ $gradeColor }}-200">
                                    <i class="fa-solid fa-check-circle ml-1"></i> {{ $hadith->grade }}
                                </span>
                                @if($hadith->narrator)
                                    <span
                                        class="bg-gray-50 text-gray-600 px-3 py-1 rounded-full text-xs font-bold border border-gray-200">
                                        <i class="fa-solid fa-user ml-1"></i> {{ $hadith->narrator->name }}
                                    </span>
                                @endif
                            </div>

                            <!-- Hadith Text -->
                            <p class="font-scheherazade text-lg leading-loose text-gray-800 mb-4">
                                {{ $hadith->content }}
                            </p>

                            <!-- Footer -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <div class="flex items-center gap-2 text-xs text-gray-500 flex-wrap">
                                    @if($hadith->sources->count() > 0)
                                        <i class="fa-solid fa-book-bookmark text-emerald-500"></i>
                                        <span class="font-bold">التخريج:</span>
                                        @foreach($hadith->sources->take(4) as $source)
                                            <span class="bg-gray-100 px-2 py-0.5 rounded-lg font-bold">{{ $source->name }}</span>
                                        @endforeach
                                        @if($hadith->sources->count() > 4)
                                            <span class="text-emerald-600 font-bold">+{{ $hadith->sources->count() - 4 }}</span>
                                        @endif
                                    @endif
                                </div>
                                <a href="{{ route('hadith.show', $hadith->id) }}"
                                    class="text-emerald-600 hover:text-emerald-700 font-bold text-sm transition-colors">
                                    التفاصيل <i class="fa-solid fa-arrow-left mr-1"></i>
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
            <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-gray-100 animate-up">
                <i class="fa-solid fa-inbox text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-800 mb-2">لا توجد أحاديث بعد</h3>
                <p class="text-gray-500">هذا الكتاب لم يتم إضافة أحاديث إليه حتى الآن</p>
            </div>
        @endif

        {{-- Navigation between chapters --}}
        @if($parentBook)
            @php
                $allChapters = $parentBook->children()->orderBy('sort_order')->get();
                $currentIndex = $allChapters->search(fn($c) => $c->id === $book->id);
                $prevChapter = $currentIndex > 0 ? $allChapters[$currentIndex - 1] : null;
                $nextChapter = $currentIndex < $allChapters->count() - 1 ? $allChapters[$currentIndex + 1] : null;
            @endphp
            <div class="flex items-center justify-between mt-10 pt-6 border-t border-gray-200">
                @if($prevChapter)
                    <a href="{{ route('books.show', $prevChapter) }}"
                        class="flex items-center gap-3 text-gray-600 hover:text-emerald-600 transition-colors group">
                        <i class="fa-solid fa-arrow-right group-hover:translate-x-[4px] transition-transform text-lg"></i>
                        <div>
                            <div class="text-xs text-gray-400 font-bold">الباب السابق</div>
                            <div class="font-bold">{{ Str::limit($prevChapter->name, 40) }}</div>
                        </div>
                    </a>
                @else
                    <div></div>
                @endif

                <a href="{{ route('books.show', $parentBook) }}" class="text-gray-400 hover:text-emerald-600 transition-colors"
                    title="العودة لفهرس الأبواب">
                    <i class="fa-solid fa-th-large text-xl"></i>
                </a>

                @if($nextChapter)
                    <a href="{{ route('books.show', $nextChapter) }}"
                        class="flex items-center gap-3 text-gray-600 hover:text-emerald-600 transition-colors group text-left">
                        <div>
                            <div class="text-xs text-gray-400 font-bold">الباب التالي</div>
                            <div class="font-bold">{{ Str::limit($nextChapter->name, 40) }}</div>
                        </div>
                        <i class="fa-solid fa-arrow-left group-hover:translate-x-[-4px] transition-transform text-lg"></i>
                    </a>
                @else
                    <div></div>
                @endif
            </div>
        @endif
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-10 mt-6">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex items-center justify-center gap-3 mb-4">
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <h4 class="text-lg font-black text-gray-900">موسوعة الحديث الصحيح</h4>
            </div>
            <p class="text-gray-400 text-sm">© {{ date('Y') }} جميع الحقوق محفوظة</p>
        </div>
    </footer>
@endsection