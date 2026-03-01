@extends('layouts.frontend')

@php
    // استخراج أول 60 حرف من الحديث للعنوان
    $hadithSnippet = Str::limit(strip_tags($hadith->content), 60, '...');
    $appName = config('app.name', 'موسوعة الحديث الصحيح');
    $pageTitle = $appName . ' | ' . $hadithSnippet;
    
    // Meta Description أطول للوصف
    $metaDescription = Str::limit(strip_tags($hadith->content), 155) . ' - حديث ' . $hadith->grade . ' من رواية ' . ($hadith->narrator?->name ?? 'غير محدد') . ' في ' . ($hadith->book?->name ?? 'كتب الحديث');
    
    $ogImage = asset('images/og-hadith.png');
@endphp

@section('title', $pageTitle)

@section('meta_description', $metaDescription)

@section('meta_keywords', 'حديث رقم ' . $hadith->number_in_book . ', ' . ($hadith->narrator?->name ?? '') . ', ' . ($hadith->book?->name ?? '') . ', حديث ' . $hadith->grade . ', الأحاديث النبوية')

@section('og_type', 'article')
@section('og_title', $pageTitle)
@section('og_description', $metaDescription)
@section('og_image', $ogImage)

@section('twitter_title', $pageTitle)
@section('twitter_description', $metaDescription)
@section('twitter_image', $ogImage)

@push('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Article",
    "headline": "حديث رقم {{ $hadith->number_in_book }}",
    "description": "{{ Str::limit($hadith->content, 200) }}",
    "author": {
        "@@type": "Person",
        "name": "{{ $hadith->narrator?->name ?? 'غير محدد' }}"
    },
    "publisher": {
        "@@type": "Organization",
        "name": "موسوعة الحديث الصحيح",
        "url": "{{ url('/') }}"
    },
    "mainEntityOfPage": {
        "@@type": "WebPage",
        "@@id": "{{ url()->current() }}"
    },
    "datePublished": "{{ $hadith->created_at?->toIso8601String() ?? now()->toIso8601String() }}",
    "dateModified": "{{ $hadith->updated_at?->toIso8601String() ?? now()->toIso8601String() }}",
    "articleBody": "{{ $hadith->content }}",
    "keywords": ["حديث", "{{ $hadith->grade }}", "{{ $hadith->narrator?->name ?? '' }}", "{{ $hadith->book?->name ?? '' }}"]
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@@type": "ListItem",
            "position": 1,
            "name": "الرئيسية",
            "item": "{{ url('/') }}"
        },
        @if($hadith->book)
        {
            "@@type": "ListItem",
            "position": 2,
            "name": "{{ $hadith->book->name }}",
            "item": "{{ url('/') }}"
        },
        {
            "@@type": "ListItem",
            "position": 3,
            "name": "حديث رقم {{ $hadith->number_in_book }}",
            "item": "{{ url()->current() }}"
        }
        @else
        {
            "@@type": "ListItem",
            "position": 2,
            "name": "حديث رقم {{ $hadith->number_in_book }}",
            "item": "{{ url()->current() }}"
        }
        @endif
    ]
}
</script>
@endpush

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
                        <input type="text" name="q"
                            class="w-full py-2.5 pr-10 pl-4 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all"
                            placeholder="بحث جديد...">
                        <i class="fa-solid fa-magnifying-glass absolute right-3 top-3 text-gray-400 text-sm"></i>
                    </form>
                </div>

                <!-- Auth & Mobile Menu -->
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

        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-6 font-medium animate-up">
            <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">الرئيسية</a>
            <i class="fa-solid fa-chevron-left text-xs text-emerald-300"></i>
            @if($hadith->book)
                <a href="{{ route('search') }}?book_id={{ $hadith->book_id }}"
                    class="hover:text-emerald-600 transition-colors">{{ $hadith->book->name }}</a>
                <i class="fa-solid fa-chevron-left text-xs text-emerald-300"></i>
            @endif
            <span class="text-emerald-700 font-bold">حديث رقم {{ $hadith->number_in_book }}</span>
        </div>

        <!-- Main Hadith Card -->
        <section class="bg-white rounded-3xl shadow-xl overflow-hidden mb-8 animate-up border border-gray-100 relative">
            <!-- Header Stripe -->
            <div class="h-2 bg-gradient-to-r from-emerald-400 via-emerald-600 to-blue-500"></div>

            <div class="p-8 md:p-12 relative">
                <!-- Decorative Corner -->
                <div class="absolute top-4 left-4 opacity-10">
                    <i class="fa-solid fa-mosque text-6xl text-emerald-500"></i>
                </div>

                <!-- Metadata Badges -->
                <div class="flex flex-wrap gap-3 mb-6 relative z-10">
                    <span class="bg-emerald-50 text-emerald-800 px-4 py-1.5 rounded-full text-sm font-bold border border-emerald-200">
                        <i class="fa-solid fa-hashtag ml-1 text-emerald-500"></i> حديث رقم: {{ $hadith->number_in_book }}
                    </span>
                    @php
                        $gradeColor = match($hadith->grade) {
                            'صحيح' => 'green',
                            'حسن' => 'blue',
                            default => 'yellow'
                        };
                    @endphp
                    <span class="bg-{{ $gradeColor }}-50 text-{{ $gradeColor }}-700 px-4 py-1.5 rounded-full text-sm font-bold border border-{{ $gradeColor }}-200">
                        <i class="fa-solid fa-check-circle ml-1"></i> {{ $hadith->grade }}
                    </span>
                    @if($hadith->narrator)
                        <span class="bg-gray-50 text-gray-600 px-4 py-1.5 rounded-full text-sm font-bold border border-gray-200">
                            <i class="fa-solid fa-user ml-1 text-gray-400"></i> الصحابي: {{ $hadith->narrator->name }}
                        </span>
                    @endif
                    @if($hadith->book)
                        <span class="bg-purple-50 text-purple-600 px-4 py-1.5 rounded-full text-sm font-bold border border-purple-200">
                            <i class="fa-solid fa-book ml-1"></i> {{ $hadith->book->name }}
                        </span>
                    @endif
                </div>

                <!-- The Hadith Text -->
                <div class="hadith-frame bg-emerald-50/30 p-8 md:p-10 rounded-2xl text-center my-6">
                    <div class="ornament-corner top-left"></div>
                    <div class="ornament-corner top-right"></div>
                    <div class="ornament-corner bottom-left"></div>
                    <div class="ornament-corner bottom-right"></div>

                    <p class="font-scheherazade text-2xl md:text-3xl leading-[3.5] text-gray-800 text-justify md:text-center relative z-10">
                        « {{ $hadith->content }} »
                    </p>
                </div>

                <!-- Additional Texts (الزيادات) -->
                @if(!empty($hadith->additions) && is_array($hadith->additions))
                    <div class="mt-4 mb-6 space-y-3 px-2 md:px-6">
                        @foreach($hadith->additions as $addition)
                            <div class="bg-gray-50/80 rounded-xl p-4 md:p-5 border border-gray-100 relative overflow-hidden group hover:border-emerald-200 transition-colors">
                                <div class="absolute right-0 top-0 bottom-0 w-1.5 bg-emerald-400/70 group-hover:bg-emerald-500 transition-colors"></div>
                                <div class="flex flex-col gap-3 pr-3">
                                    <div class="flex items-center gap-2">
                                        <span class="bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-md text-xs font-bold inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-plus-circle"></i>
                                            زيادة من: {{ $addition['source_name'] }}
                                        </span>
                                    </div>
                                    <p class="font-scheherazade text-xl md:text-2xl leading-[2.5] text-gray-700 text-justify">
                                        « {{ $addition['text'] }} »
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- النص الأصلي كما ورد في المصدر (الأمانة العلمية) -->
                @if($hadith->raw_text)
                    <div class="mt-8 mb-4">
                        <button onclick="toggleRawText()" id="rawTextToggle"
                            class="w-full flex items-center justify-between bg-amber-50/30 hover:bg-amber-50 border border-amber-200/60 rounded-xl px-4 py-3 text-right transition-all duration-300 group shadow-sm hover:shadow-md">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-700 group-hover:bg-amber-200 transition-colors">
                                    <i class="fa-solid fa-scroll text-sm"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-gray-800 text-sm">النص كما ورد في المصدر</span>
                                    <span class="block text-[10px] text-amber-600 font-medium">الأمانة العلمية — نص الكتاب الأصلي</span>
                                </div>
                            </div>
                            <i id="rawTextArrow" class="fa-solid fa-chevron-down text-amber-400 transition-transform duration-300 text-sm" style="transform: rotate(180deg);"></i>
                        </button>

                        <div id="rawTextContent" class="overflow-hidden transition-all duration-500 ease-in-out" style="opacity: 1;">
                            <div class="mt-1 rounded-b-xl border border-t-0 border-amber-200/60 overflow-hidden shadow-inner">
                                {{-- الشريط العلوي الزخرفي --}}
                                <div class="h-0.5 bg-gradient-to-r from-amber-300 via-amber-500 to-amber-300"></div>
                                
                                <div class="p-4 md:p-6 relative" style="background: linear-gradient(135deg, #fffdf5 0%, #fef9e7 50%, #fffdf5 100%);">
                                    {{-- زخارف الأركان --}}
                                    <div class="absolute top-2 right-2 w-4 h-4 border-t border-r border-amber-400/50 rounded-tr-sm"></div>
                                    <div class="absolute top-2 left-2 w-4 h-4 border-t border-l border-amber-400/50 rounded-tl-sm"></div>
                                    <div class="absolute bottom-2 right-2 w-4 h-4 border-b border-r border-amber-400/50 rounded-br-sm"></div>
                                    <div class="absolute bottom-2 left-2 w-4 h-4 border-b border-l border-amber-400/50 rounded-bl-sm"></div>

                                    {{-- النص الأصلي --}}
                                    <p class="font-scheherazade text-lg md:text-xl leading-[2.5] text-amber-950 text-center relative z-10 px-2 md:px-6" dir="rtl">
                                        {{ $hadith->raw_text }}
                                    </p>

                                    {{-- ملاحظة أسفل النص --}}
                                    <div class="mt-4 pt-3 border-t border-dashed border-amber-300/50 text-center">
                                        <p class="text-[10px] text-amber-600/80 font-medium flex items-center justify-center gap-1.5">
                                            <i class="fa-solid fa-book-open text-amber-400"></i>
                                            هذا النص الأصلي كما ورد في كتاب "صحيح الجامع الصغير وزيادته" للإمام الألباني رحمه الله
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Takhrij / Sources -->
                @if($hadith->sources->count() > 0)
                    <div class="mt-8 pt-6 border-t border-dashed border-gray-200 text-sm text-gray-600 flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
                        <div class="flex items-center gap-2 flex-wrap">
                            <i class="fa-solid fa-book-bookmark text-emerald-500 text-lg"></i>
                            <span class="font-bold">التخريج:</span>
                            @foreach($hadith->sources as $source)
                                <span class="bg-gray-100 px-3 py-1 rounded-lg text-xs font-bold">{{ $source->name }} ({{ $source->code }})</span>
                            @endforeach
                        </div>
                        <div class="flex gap-3">
                            <button onclick="copyHadith()" class="text-gray-400 hover:text-emerald-600 transition-colors" title="نسخ الحديث">
                                <i class="fa-regular fa-copy text-xl"></i>
                            </button>
                            <button onclick="openShareModal()" class="text-gray-400 hover:text-emerald-600 transition-colors" title="مشاركة">
                                <i class="fa-solid fa-share-nodes text-xl"></i>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <!-- Sanad Chains (سلاسل الإسناد) - Dynamic -->
        @if($hadith->chains->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 animate-up" style="animation-delay: 0.1s;">
                @foreach($hadith->chains as $index => $chain)
                    <section class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-emerald-200 transition-colors">
                        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-100">
                            <span class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 font-black text-sm">{{ $index + 1 }}</span>
                            <h3 class="font-tajawal font-bold text-lg text-gray-800">طريق {{ $chain->source->name }}</h3>
                        </div>
                        <div class="pr-2">
                            @foreach($chain->narrators as $narrator)
                                <div class="timeline-node {{ $loop->last ? '' : 'pb-4' }}">
                                    @if($narrator->pivot->role)
                                        <span class="text-sm text-gray-500 block mb-1">{{ $narrator->pivot->role }}</span>
                                    @endif
                                    @if($loop->last)
                                        <span class="font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-lg">{{ $narrator->name }}</span>
                                    @else
                                        <a href="{{ route('narrator.show', $narrator->id) }}" target="_blank"
                                            class="font-scheherazade text-lg text-emerald-700 hover:text-emerald-500 hover:underline transition-colors">
                                            {{ $narrator->name }}
                                        </a>
                                    @endif
                                    @if(!$loop->first && !$loop->last)
                                        @php
                                            $appearsInOtherChains = false;
                                            foreach ($hadith->chains as $otherChain) {
                                                if ($otherChain->id !== $chain->id) {
                                                    if ($otherChain->narrators->contains('id', $narrator->id)) {
                                                        $appearsInOtherChains = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        @endphp
                                        @if($appearsInOtherChains)
                                            <span class="text-xs text-gray-400 mr-2 bg-gray-50 px-2 py-0.5 rounded">(ملتقى الطريقين)</span>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        @endif

        <!-- Simple Explanation (الشرح البسيط) -->
        @if($hadith->explanation)
            <section class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden animate-up mb-8"
                style="animation-delay: 0.15s;">
                <div class="bg-emerald-50/50 p-5 border-b border-emerald-100 flex items-center gap-3">
                    <i class="fa-solid fa-lightbulb text-emerald-600 text-xl"></i>
                    <h2 class="font-tajawal font-bold text-xl text-gray-800">الشرح والتفسير</h2>
                </div>
                <div class="p-6 md:p-8">
                    <div class="text-gray-700 leading-loose text-justify prose prose-lg max-w-none explanation-content">
                        {!! $hadith->explanation !!}
                    </div>
                </div>
            </section>
        @endif

        <!-- Narrator Bio (if available) -->
        @if($hadith->narrator && $hadith->narrator->bio)
            <section class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100 mb-8 animate-up"
                style="animation-delay: 0.1s;">
                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-100">
                    <i class="fa-solid fa-user-tie text-emerald-500 text-xl"></i>
                    <h3 class="font-tajawal font-bold text-xl text-gray-800">نبذة عن الصحابي</h3>
                </div>
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600 text-2xl">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div class="flex-grow">
                        <h4 class="font-bold text-lg text-gray-800 mb-2">{{ $hadith->narrator->name }}</h4>
                        @if($hadith->narrator->grade_status)
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold mb-3"
                                style="background-color: {{ $hadith->narrator->color_code }}20; color: {{ $hadith->narrator->color_code }}; border: 1px solid {{ $hadith->narrator->color_code }};">
                                {{ $hadith->narrator->grade_status }}
                            </span>
                        @endif
                        <p class="text-gray-600 leading-relaxed whitespace-pre-wrap">{{ $hadith->narrator->bio }}</p>
                    </div>
                </div>
            </section>
        @endif

        <!-- Related Hadiths -->
        @if($relatedHadiths->count() > 0)
            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 animate-up"
                style="animation-delay: 0.2s;">
                <h3 class="font-tajawal font-bold text-xl text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-layer-group text-emerald-500"></i>
                    أحاديث ذات صلة
                </h3>
                <div class="grid md:grid-cols-2 gap-4">
                    @foreach($relatedHadiths as $related)
                        <a href="{{ route('hadith.show', $related->id) }}"
                            class="block p-5 border border-gray-100 rounded-xl hover:border-emerald-300 hover:shadow-md transition-all group">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-xs bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full font-bold">#{{ $related->number_in_book }}</span>
                                <span class="text-xs bg-green-50 text-green-700 px-3 py-1 rounded-full font-bold">{{ $related->grade }}</span>
                            </div>
                            <p class="font-scheherazade text-sm text-gray-700 line-clamp-2 group-hover:text-gray-900">
                                {{ Str::limit($related->content, 100) }}
                            </p>
                        </a>
                    @endforeach
                </div>
            </section>
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

    <!-- Share Modal -->
    <div id="shareModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeShareModal()"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-3xl shadow-2xl p-6 md:p-8 w-full max-w-md mx-4 transform scale-95 opacity-0 transition-all duration-300" id="shareModalContent">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-black text-gray-900">
                    <i class="fa-solid fa-share-nodes text-emerald-500 ml-2"></i>
                    مشاركة الحديث
                </h3>
                <button onclick="closeShareModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Share Buttons Grid -->
            <div class="grid grid-cols-2 gap-3 mb-6">
                <!-- WhatsApp -->
                <button onclick="shareToWhatsApp()" class="flex items-center justify-center gap-3 bg-[#25D366] hover:bg-[#20bd5a] text-white py-4 px-4 rounded-2xl font-bold transition-all hover:scale-105 shadow-lg shadow-green-200">
                    <i class="fa-brands fa-whatsapp text-2xl"></i>
                    <span>واتساب</span>
                </button>
                
                <!-- Twitter/X -->
                <button onclick="shareToTwitter()" class="flex items-center justify-center gap-3 bg-black hover:bg-gray-800 text-white py-4 px-4 rounded-2xl font-bold transition-all hover:scale-105 shadow-lg">
                    <i class="fa-brands fa-x-twitter text-2xl"></i>
                    <span>تويتر</span>
                </button>
                
                <!-- Telegram -->
                <button onclick="shareToTelegram()" class="flex items-center justify-center gap-3 bg-[#0088cc] hover:bg-[#0077b5] text-white py-4 px-4 rounded-2xl font-bold transition-all hover:scale-105 shadow-lg shadow-blue-200">
                    <i class="fa-brands fa-telegram text-2xl"></i>
                    <span>تيليجرام</span>
                </button>
                
                <!-- Facebook -->
                <button onclick="shareToFacebook()" class="flex items-center justify-center gap-3 bg-[#1877F2] hover:bg-[#166fe5] text-white py-4 px-4 rounded-2xl font-bold transition-all hover:scale-105 shadow-lg shadow-blue-200">
                    <i class="fa-brands fa-facebook-f text-2xl"></i>
                    <span>فيسبوك</span>
                </button>
            </div>
            
            <!-- Divider -->
            <div class="flex items-center gap-3 mb-4">
                <div class="flex-grow h-px bg-gray-200"></div>
                <span class="text-gray-400 text-sm">أو</span>
                <div class="flex-grow h-px bg-gray-200"></div>
            </div>
            
            <!-- Copy Link -->
            <button onclick="copyLinkOnly()" class="w-full flex items-center justify-center gap-3 bg-gray-100 hover:bg-gray-200 text-gray-700 py-4 px-4 rounded-2xl font-bold transition-all">
                <i class="fa-solid fa-link text-xl text-emerald-500"></i>
                <span>نسخ الرابط فقط</span>
            </button>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Toggle النص الأصلي (الأمانة العلمية)
        function toggleRawText() {
            const content = document.getElementById('rawTextContent');
            const arrow = document.getElementById('rawTextArrow');
            
            if (content.style.maxHeight === '0px') {
                // مغلق ← نفتحه
                content.style.maxHeight = content.scrollHeight + 'px';
                content.style.opacity = '1';
                arrow.style.transform = 'rotate(180deg)';
                
                // بعد انتهاء الحركة، نجعله none ليتمدد بحرية مع الشاشات
                setTimeout(() => {
                    if (content.style.maxHeight !== '0px') {
                        content.style.maxHeight = 'none';
                    }
                }, 500);
            } else {
                // مفتوح ← نغلقه
                // إذا لم يكن له ارتفاع محدد، نحدد ارتفاعه ليسهل عمل الـ animation
                if (!content.style.maxHeight || content.style.maxHeight === 'none') {
                    content.style.maxHeight = content.scrollHeight + 'px';
                    // Force DOM reflow
                    content.offsetHeight;
                }
                
                // إغلاق القسم
                content.style.maxHeight = '0px';
                content.style.opacity = '0';
                arrow.style.transform = 'rotate(0deg)';
            }
        }

        // Helper function to copy text with fallback for HTTP
        function copyToClipboard(text) {
            return new Promise((resolve, reject) => {
                // Try modern clipboard API first (requires HTTPS)
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text)
                        .then(resolve)
                        .catch(() => fallbackCopy(text, resolve, reject));
                } else {
                    // Fallback for HTTP or older browsers
                    fallbackCopy(text, resolve, reject);
                }
            });
        }

        function fallbackCopy(text, resolve, reject) {
            try {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.left = '-9999px';
                textarea.style.top = '-9999px';
                document.body.appendChild(textarea);
                textarea.focus();
                textarea.select();
                
                const successful = document.execCommand('copy');
                document.body.removeChild(textarea);
                
                if (successful) {
                    resolve();
                } else {
                    reject(new Error('Copy failed'));
                }
            } catch (err) {
                reject(err);
            }
        }

        function showToast(message, isError = false) {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-6 left-1/2 transform -translate-x-1/2 ${isError ? 'bg-red-600' : 'bg-emerald-600'} text-white px-6 py-3 rounded-xl shadow-lg z-50 font-bold flex items-center gap-2`;
            toast.innerHTML = `<i class="fa-solid ${isError ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i> ${message}`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2500);
        }

        function copyHadith() {
            // Build formatted hadith text
            const hadithContent = `{{ $hadith->content }}`;
            const narrator = `{{ $hadith->narrator?->name ?? 'غير محدد' }}`;
            const grade = `{{ $hadith->grade }}`;
            const book = `{{ $hadith->book?->name ?? '' }}`;
            const hadithNumber = `{{ $hadith->number_in_book }}`;
            const sources = `{{ $hadith->sources->pluck('name')->join('، ') }}`;
            const url = window.location.href;
            
            // Format: Hadith first, then source, link, then metadata
            let formattedText = `« ${hadithContent} »\n\n`;
            formattedText += `📚 المصدر: موسوعة الحديث الصحيح\n`;
            formattedText += `🔗 الرابط: ${url}\n\n`;
            formattedText += `📜 الراوي: ${narrator}\n`;
            formattedText += `✅ الدرجة: ${grade}\n`;
            if (book) {
                formattedText += `📖 الكتاب: ${book}\n`;
            }
            formattedText += `🔢 رقم الحديث: ${hadithNumber}\n`;
            if (sources) {
                formattedText += `📑 التخريج: ${sources}\n`;
            }
            
            copyToClipboard(formattedText)
                .then(() => showToast('تم نسخ الحديث مع المعلومات'))
                .catch(() => showToast('فشل النسخ - جرب التحديد اليدوي', true));
        }

        // Share Modal Functions
        function openShareModal() {
            const modal = document.getElementById('shareModal');
            const content = document.getElementById('shareModalContent');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeShareModal() {
            const modal = document.getElementById('shareModal');
            const content = document.getElementById('shareModalContent');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }

        // Share Text - Same format as copy but without emojis (URL encoding issues)
        const shareUrl = window.location.href;
        const narrator = `{{ $hadith->narrator?->name ?? 'غير محدد' }}`;
        const grade = `{{ $hadith->grade }}`;
        const book = `{{ $hadith->book?->name ?? '' }}`;
        const hadithNumber = `{{ $hadith->number_in_book }}`;
        const sources = `{{ $hadith->sources->pluck('name')->join('، ') }}`;
        
        let shareTextRaw = `« {{ $hadith->content }} »\n\n`;
        shareTextRaw += `المصدر: موسوعة الحديث الصحيح\n`;
        shareTextRaw += `الرابط: ${shareUrl}\n\n`;
        shareTextRaw += `الراوي: ${narrator}\n`;
        shareTextRaw += `الدرجة: ${grade}\n`;
        if (book) {
            shareTextRaw += `الكتاب: ${book}\n`;
        }
        shareTextRaw += `رقم الحديث: ${hadithNumber}\n`;
        if (sources) {
            shareTextRaw += `التخريج: ${sources}\n`;
        }
        
        const shareText = encodeURIComponent(shareTextRaw);

        function shareToWhatsApp() {
            window.open(`https://wa.me/?text=${shareText}`, '_blank');
            closeShareModal();
        }

        function shareToTwitter() {
            // Twitter has character limit, so use shorter version
            const twitterText = encodeURIComponent(`« {{ Str::limit($hadith->content, 200) }} »\n\n• {{ $hadith->narrator?->name ?? '' }} | {{ $hadith->grade }}\n\nموسوعة الحديث الصحيح`);
            window.open(`https://twitter.com/intent/tweet?text=${twitterText}&url=${encodeURIComponent(shareUrl)}`, '_blank');
            closeShareModal();
        }

        function shareToTelegram() {
            window.open(`https://t.me/share/url?url=${encodeURIComponent(shareUrl)}&text=${shareText}`, '_blank');
            closeShareModal();
        }

        function shareToFacebook() {
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`, '_blank');
            closeShareModal();
        }

        function copyLinkOnly() {
            copyToClipboard(window.location.href)
                .then(() => {
                    closeShareModal();
                    showToast('تم نسخ الرابط');
                })
                .catch(() => showToast('فشل النسخ', true));
        }
    </script>
@endpush