@extends('layouts.frontend')

@section('title', $narrator->name . ' - موسوعة الحديث الصحيح')
@section('meta_description', 'أحاديث ' . $narrator->name . ' — ' . $narrator->hadiths_count . ' حديث في موسوعة الحديث الصحيح')
@section('og_title', $narrator->name . ' - موسوعة الحديث الصحيح')
@section('og_description', 'تصفح ' . $narrator->hadiths_count . ' حديث رواها ' . $narrator->name . ' في موسوعة الحديث الصحيح')

@section('content')
    <div class="container mx-auto px-4 py-8 max-w-5xl">

        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-6 font-medium animate-up">
            <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">الرئيسية</a>
            <i class="fa-solid fa-chevron-left text-xs text-emerald-300"></i>
            <span class="text-emerald-700 font-bold">الراوي: {{ $narrator->name }}</span>
        </div>

        <!-- Narrator Profile Card -->
        <section class="bg-white rounded-3xl shadow-xl overflow-hidden mb-8 animate-up border border-gray-100">
            <div class="h-2 bg-gradient-to-r from-emerald-400 via-emerald-600 to-blue-500"></div>

            <div class="p-8 md:p-12">
                <!-- Header -->
                <div class="flex flex-col md:flex-row items-start md:items-center gap-6 mb-8">
                    <!-- Avatar -->
                    <div
                        class="flex-shrink-0 w-24 h-24 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white shadow-xl shadow-emerald-100">
                        <i class="fa-solid fa-user text-4xl"></i>
                    </div>

                    <!-- Info -->
                    <div class="flex-grow">
                        <h1 class="text-3xl md:text-4xl font-tajawal font-bold text-gray-800 mb-3">
                            {{ $narrator->name }}
                        </h1>

                        <div class="flex flex-wrap gap-3 items-center">
                            @if($narrator->grade_status)
                                <span class="px-4 py-2 rounded-full text-sm font-bold shadow-sm"
                                    style="background-color: {{ $narrator->color_code }}; color: white;">
                                    <i class="fa-solid fa-star ml-1"></i>
                                    {{ $narrator->grade_status }}
                                </span>
                            @endif

                            <span class="bg-gray-100 text-gray-700 px-4 py-2 rounded-full text-sm font-bold">
                                <i class="fa-solid fa-book ml-1"></i>
                                {{ $narrator->hadiths_count }} حديث
                            </span>

                            <span class="bg-purple-100 text-purple-700 px-4 py-2 rounded-full text-sm font-bold">
                                <i class="fa-solid fa-diagram-project ml-1"></i>
                                {{ $narrator->chains_count }} سلسلة
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Bio / Jarh wa Ta'deel -->
                @if($narrator->bio)
                    <div class="bg-emerald-50/30 p-6 md:p-8 rounded-2xl border border-emerald-100">
                        <h2 class="text-xl font-tajawal font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-scroll text-emerald-600"></i>
                            الترجمة والجرح والتعديل
                        </h2>
                        <div class="font-scheherazade text-lg leading-relaxed text-gray-700 whitespace-pre-wrap">
                            {{ $narrator->bio }}
                        </div>
                    </div>
                @else
                    <div class="bg-gray-50 p-6 rounded-2xl text-center text-gray-500">
                        <i class="fa-solid fa-circle-info text-3xl mb-3"></i>
                        <p>لا توجد معلومات متاحة حالياً عن هذا الراوي</p>
                    </div>
                @endif
            </div>
        </section>

        <!-- Hadiths List -->
        @if($narrator->hadiths->count() > 0)
            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 mb-8 animate-up"
                style="animation-delay: 0.1s;">
                <h2 class="text-2xl font-tajawal font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-book-open text-emerald-500"></i>
                    الأحاديث المروية
                    <span class="text-sm font-normal text-gray-500">({{ $narrator->hadiths_count }} حديث)</span>
                </h2>

                <div class="space-y-4">
                    @foreach($narrator->hadiths->take(10) as $hadith)
                        <a href="{{ route('hadith.show', $hadith->id) }}"
                            class="block p-5 border border-gray-100 rounded-xl hover:border-emerald-300 hover:shadow-md transition-all group">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-xs bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full font-bold">
                                    #{{ $hadith->number_in_book }}
                                </span>
                                @php
                                    $gradeColor = match ($hadith->grade) {
                                        'صحيح' => 'green',
                                        'حسن' => 'blue',
                                        default => 'yellow'
                                    };
                                @endphp
                                <span
                                    class="text-xs bg-{{ $gradeColor }}-50 text-{{ $gradeColor }}-700 px-3 py-1 rounded-full font-bold">
                                    {{ $hadith->grade }}
                                </span>
                                @if($hadith->book)
                                    <span class="text-xs bg-purple-50 text-purple-600 px-3 py-1 rounded-full font-bold">
                                        {{ $hadith->book->name }}
                                    </span>
                                @endif
                            </div>
                            <p class="font-scheherazade text-base text-gray-700 line-clamp-2 group-hover:text-gray-900">
                                {{ $hadith->content }}
                            </p>
                        </a>
                    @endforeach
                </div>

                @if($narrator->hadiths_count > 10)
                    <div class="mt-6 text-center">
                        <a href="{{ route('search') }}?narrator_id={{ $narrator->id }}"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-colors shadow-lg shadow-emerald-100">
                            <i class="fa-solid fa-list"></i>
                            عرض جميع الأحاديث ({{ $narrator->hadiths_count }})
                        </a>
                    </div>
                @endif
            </section>
        @endif

        <!-- Chains Appearances -->
        @if($narratorChains->count() > 0)
            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 animate-up"
                style="animation-delay: 0.2s;">
                <h2 class="text-2xl font-tajawal font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-diagram-project text-emerald-500"></i>
                    السلاسل التي يظهر فيها الراوي
                    <span class="text-sm font-normal text-gray-500">({{ $narratorChains->count() }} سلسلة)</span>
                </h2>

                <div class="space-y-4">
                    @foreach($narratorChains as $chain)
                        <div class="p-5 border border-gray-100 rounded-xl bg-gray-50/50 hover:border-emerald-200 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-gray-700">
                                        <i class="fa-solid fa-book text-emerald-500 ml-1"></i>
                                        {{ $chain->source->name }}
                                    </span>
                                    <span class="text-xs bg-purple-50 text-purple-600 px-2 py-1 rounded-full font-bold">
                                        {{ $chain->source->code }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-500">
                                    موضع: {{ $chain->pivot->position }} من {{ $chain->narrators->count() }}
                                </span>
                            </div>

                            @if($chain->pivot->role)
                                <div class="text-sm text-gray-600 mb-2">
                                    <span class="font-bold">الدور:</span> {{ $chain->pivot->role }}
                                </div>
                            @endif

                            <a href="{{ route('hadith.show', $chain->hadith_id) }}"
                                class="text-sm text-emerald-600 hover:text-emerald-700 font-bold transition-colors">
                                عرض الحديث والسلسلة الكاملة <i class="fa-solid fa-arrow-left mr-1"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

    </div>
@endsection