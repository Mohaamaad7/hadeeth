<x-layouts.app>
    <!-- Search Results Header -->
    <section class="bg-gradient-to-b from-[--color-primary] to-[--color-charcoal] text-[--color-paper] py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <!-- Back Button -->
                <a href="/" class="inline-flex items-center gap-2 text-[--color-accent] hover:text-[--color-gold-dark] mb-4 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    العودة للرئيسية
                </a>

                <!-- Search Title -->
                <h1 class="text-4xl md:text-5xl font-bold mb-4 font-[family-name:--font-serif]">
                    نتائج البحث
                </h1>
                <p class="text-xl text-[--color-accent] mb-6">
                    نتائج البحث عن: <span class="font-bold">"{{ $query }}"</span>
                </p>

                <!-- Search Bar (Repeat) -->
                <form action="{{ route('search') }}" method="GET" class="relative">
                    <div class="flex items-center bg-white rounded-full shadow-2xl overflow-hidden">
                        <input 
                            type="text" 
                            name="q" 
                            value="{{ $query }}"
                            placeholder="ابحث في الأحاديث..." 
                            class="flex-1 px-8 py-4 text-lg text-[--color-primary] focus:outline-none font-[family-name:--font-arabic]"
                            required
                        >
                        <button 
                            type="submit" 
                            class="bg-[--color-accent] text-white px-8 py-4 hover:bg-[--color-gold-dark] transition-colors font-medium">
                            بحث
                        </button>
                    </div>
                </form>

                <!-- Results Count -->
                @if($results->total() > 0)
                    <p class="text-sm mt-4 text-gray-300">
                        تم العثور على <strong class="text-[--color-accent]">{{ $results->total() }}</strong> حديث
                    </p>
                @endif
            </div>
        </div>
    </section>

    <!-- Search Results Content -->
    <section class="container mx-auto px-4 py-12">
        @if($results->total() > 0)
            <!-- Results Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                @foreach($results as $hadith)
                    <x-hadith-card :hadith="$hadith" />
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $results->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="max-w-2xl mx-auto text-center py-16">
                <div class="bg-white rounded-2xl shadow-lg p-12">
                    <!-- Icon -->
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>

                    <!-- Message -->
                    <h2 class="text-2xl font-bold text-[--color-primary] mb-3 font-[family-name:--font-serif]">
                        لم يتم العثور على نتائج
                    </h2>
                    <p class="text-gray-600 mb-6 text-lg">
                        لم نعثر على أحاديث تطابق بحثك عن "<strong>{{ $query }}</strong>"
                    </p>

                    <!-- Suggestions -->
                    <div class="bg-[--color-paper] rounded-lg p-6 text-right">
                        <h3 class="font-bold text-[--color-primary] mb-3 text-lg">اقتراحات للبحث:</h3>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-[--color-accent] mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>تأكد من كتابة الكلمات بشكل صحيح</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-[--color-accent] mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>جرب استخدام كلمات مفتاحية مختلفة أو أقل</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-[--color-accent] mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>استخدم كلمات عامة أكثر</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-[--color-accent] mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>التشكيل (الحركات) غير مطلوب في البحث</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Return Home -->
                    <a href="/" class="inline-flex items-center gap-2 mt-8 bg-[--color-accent] text-white px-8 py-3 rounded-full hover:bg-[--color-gold-dark] transition-colors font-medium">
                        العودة للصفحة الرئيسية
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </a>
                </div>
            </div>
        @endif
    </section>
</x-layouts.app>
