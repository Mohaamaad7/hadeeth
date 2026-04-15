@extends('layouts.frontend')

@section('title', 'أحاديث صحيحة عن ' . $topic->name . ' | موسوعة الحديث الصحيح')
@section('meta_description', 'مجموعة من الأحاديث النبوية الصحيحة الواردة في موضوع ' . $topic->name . ' مع التخريج والشرح وتراجم الرواة.')

@section('content')
<section class="page-header py-16 bg-white border-b border-gray-100">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <span class="inline-block px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-sm mb-4 border border-emerald-100">
            فهرس المواضيع
        </span>
        <h1 class="text-4xl lg:text-5xl font-black text-gray-900 mb-6 tracking-tight leading-tight">
            أحاديث عن <span class="text-emerald-600">{{ $topic->name }}</span>
        </h1>
        <p class="text-xl text-gray-500 font-medium">تم العثور على {{ $hadiths->total() }} حديث صحيح</p>
    </div>
</section>

<section class="py-12 bg-gray-50 flex-grow">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($hadiths->isEmpty())
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-12 text-center text-gray-500 mb-8">
                <i class="fa-solid fa-file-circle-xmark text-5xl mb-4 text-gray-300"></i>
                <p class="text-xl font-bold">عذراً، لا توجد أحاديث معتمدة في هذا الموضوع حالياً.</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach($hadiths as $hadith)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 font-bold text-sm border border-emerald-100">
                                #{{ $hadith->number_in_book }}
                            </span>
                            @if($hadith->book)
                                <a href="{{ route('books.show', $hadith->book->id) }}" class="text-gray-500 hover:text-emerald-600 text-sm font-medium transition-colors">
                                    <i class="fa-solid fa-book-open mr-1"></i> {{ $hadith->book->name }}
                                </a>
                            @endif
                            <span class="px-3 py-1 rounded-full bg-gray-50 text-gray-700 font-bold text-sm border border-gray-200 mr-auto">
                                {{ $hadith->grade }}
                            </span>
                        </div>

                        <div class="font-scheherazade text-2xl lg:text-3xl leading-relaxed text-gray-900 mb-6 relative z-10">
                            {!! $hadith->content !!}
                        </div>

                        <div class="pt-6 border-t border-gray-100 flex flex-wrap items-center justify-between gap-4">
                            <div class="text-sm text-gray-600 font-medium">
                                <i class="fa-solid fa-user-pen ml-1 text-emerald-500"></i>
                                الرواة: {{ $hadith->narrators->pluck('name')->join('، ') ?: 'غير محدد' }}
                            </div>
                            
                            <a href="{{ route('hadith.show', [$hadith->id, $hadith->slug]) }}" 
                               class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition-colors">
                                التفاصيل والشرح <i class="fa-solid fa-arrow-left text-sm"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $hadiths->links() }}
            </div>
        @endif
        
        <div class="mt-12 text-center">
            <a href="{{ route('topics.index') }}" class="text-emerald-600 hover:text-emerald-700 font-bold">
                <i class="fa-solid fa-arrow-right ml-1"></i> العودة لفهرس المواضيع
            </a>
        </div>
    </div>
</section>
@endsection
