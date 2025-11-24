@props(['hadith'])

<article class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 p-6 border-r-4 border-[--color-accent]">
    <!-- Hadith Number & Grade -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex gap-2">
            @if($hadith->number_in_book)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[--color-charcoal] text-[--color-paper]">
                    رقم {{ $hadith->number_in_book }}
                </span>
            @endif
            
            @if($hadith->grade)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                    {{ $hadith->grade === 'صحيح' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $hadith->grade === 'حسن' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $hadith->grade === 'ضعيف' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                    {{ $hadith->grade }}
                </span>
            @endif
        </div>

        <!-- Source Badges -->
        <div class="flex gap-1">
            @foreach($hadith->sources as $source)
                <span class="w-8 h-8 rounded-full bg-[--color-accent] text-white flex items-center justify-center text-sm font-bold" 
                      title="{{ $source->name }}">
                    {{ $source->code }}
                </span>
            @endforeach
        </div>
    </div>

    <!-- Hadith Content -->
    <div class="mb-4">
        <p class="text-xl leading-loose text-[--color-primary] font-[family-name:--font-arabic]">
            {{ $hadith->content }}
        </p>
    </div>

    <!-- Metadata -->
    <div class="flex items-center justify-between text-sm text-gray-600 border-t pt-4">
        <div class="flex flex-col gap-1">
            @if($hadith->narrator)
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-[--color-accent]" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                    </svg>
                    <span>الراوي: <strong>{{ $hadith->narrator->name }}</strong></span>
                </span>
            @endif
            
            @if($hadith->book)
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-[--color-accent]" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                    </svg>
                    <span>الكتاب: <strong>{{ $hadith->book->name }}</strong></span>
                </span>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-2">
            <button @click="copyToClipboard('{{ addslashes($hadith->content) }}')" 
                    class="p-2 rounded-full hover:bg-gray-100 transition-colors" 
                    title="نسخ النص">
                <svg class="w-5 h-5 text-[--color-accent]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </button>
            
            <button @click="shareHadith({{ $hadith->id }})" 
                    class="p-2 rounded-full hover:bg-gray-100 transition-colors" 
                    title="مشاركة">
                <svg class="w-5 h-5 text-[--color-accent]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Explanation (if exists) -->
    @if($hadith->explanation)
        <div class="mt-4 pt-4 border-t border-gray-200" x-data="{ expanded: false }">
            <button @click="expanded = !expanded" class="flex items-center gap-2 text-[--color-accent] font-medium text-sm">
                <span x-text="expanded ? 'إخفاء الشرح' : 'عرض الشرح'"></span>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': expanded }" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
            <div x-show="expanded" x-collapse class="mt-3 text-gray-700 text-sm leading-relaxed">
                {{ $hadith->explanation }}
            </div>
        </div>
    @endif
</article>

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('تم نسخ النص');
        });
    }

    function shareHadith(id) {
        // Stub for future implementation
        alert('سيتم إضافة ميزة المشاركة قريباً');
    }
</script>
