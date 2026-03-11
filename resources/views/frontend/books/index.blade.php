@extends('layouts.frontend')

@section('title', 'فهرس الكتب - موسوعة الحديث الصحيح')
@section('meta_description', 'تصفح فهرس كتب موسوعة الحديث الصحيح - 45 كتاباً مرتبة بترتيب المصنف مع الأبواب والأحاديث')
@section('og_title', 'فهرس الكتب - موسوعة الحديث الصحيح')
@section('og_description', 'تصفح فهرس كتب موسوعة الحديث الصحيح - 45 كتاباً مرتبة بترتيب المصنف مع الأبواب والأحاديث')

@section('content')
    <!-- Hero Header -->
    <section class="hero-pattern py-16 text-white">
        <div class="max-w-5xl mx-auto px-4 text-center relative z-10">
            <h1 class="text-3xl lg:text-5xl font-extrabold mb-4 animate-fade-in">
                <i class="fa-solid fa-book-open ml-2"></i> فهرس الكتب
            </h1>
            <p class="text-emerald-100/80 text-lg max-w-2xl mx-auto font-medium">
                تصفح الموسوعة كاملة — {{ $books->count() }} كتاباً مرتبة بترتيب المصنف
            </p>
        </div>
    </section>

    <!-- Books Grid -->
    <section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @php
            $colors = [
                'emerald',
                'blue',
                'purple',
                'amber',
                'rose',
                'cyan',
                'indigo',
                'teal',
                'orange',
                'lime',
                'fuchsia',
                'sky',
                'violet',
                'pink',
                'emerald',
            ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($books as $index => $book)
                @php $color = $colors[$index % count($colors)]; @endphp
                <a href="{{ route('books.show', $book) }}"
                    class="floating-card bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden group animate-up"
                    style="animation-delay: {{ $index * 0.03 }}s;">

                    <!-- Color Bar -->
                    <div class="h-1.5 bg-{{ $color }}-500"></div>

                    <div class="p-6">
                        <!-- Book Number & Name -->
                        <div class="flex items-start gap-4 mb-4">
                            <div
                                class="w-12 h-12 bg-{{ $color }}-50 text-{{ $color }}-600 rounded-2xl flex items-center justify-center flex-shrink-0 font-black text-lg border border-{{ $color }}-100">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-grow">
                                <h3
                                    class="text-lg font-black text-gray-900 group-hover:text-{{ $color }}-600 transition-colors leading-relaxed">
                                    {{ $book->name }}
                                </h3>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="flex items-center gap-4 text-sm text-gray-500">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-scroll text-{{ $color }}-400"></i>
                                <span class="font-bold">{{ $book->total_hadiths }}</span> حديث
                            </span>
                            @if($book->children_count > 0)
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-bookmark text-{{ $color }}-400"></i>
                                    <span class="font-bold">{{ $book->children_count }}</span> باب
                                </span>
                            @else
                                <span class="flex items-center gap-1.5 text-gray-400">
                                    <i class="fa-solid fa-minus"></i>
                                    بدون أبواب
                                </span>
                            @endif
                        </div>

                        <!-- Arrow -->
                        <div
                            class="mt-4 pt-4 border-t border-gray-50 text-{{ $color }}-600 font-bold text-sm flex items-center justify-between">
                            <span>استعراض الكتاب</span>
                            <i class="fa-solid fa-arrow-left group-hover:translate-x-[-6px] transition-transform"></i>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

@endsection