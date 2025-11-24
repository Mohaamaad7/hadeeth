<x-layouts.app>
    <!-- Hero Section with Search -->
    <section class="bg-gradient-to-b from-[--color-primary] to-[--color-charcoal] text-[--color-paper] py-20">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <!-- Title -->
                <h1 class="text-5xl md:text-6xl font-bold mb-4 font-[family-name:--font-serif]">
                    صحيح الجامع الصغير
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-[--color-accent]">
                    الموسوعة الرقمية للأحاديث النبوية الصحيحة
                </p>

                <!-- Search Bar -->
                <form action="/search" method="GET" class="relative">
                    <div class="flex items-center bg-white rounded-full shadow-2xl overflow-hidden">
                        <input 
                            type="text" 
                            name="q" 
                            placeholder="ابحث في الأحاديث..." 
                            class="flex-1 px-8 py-5 text-lg text-[--color-primary] focus:outline-none font-[family-name:--font-arabic]"
                            required
                        >
                        <button 
                            type="submit" 
                            class="bg-[--color-accent] text-white px-10 py-5 hover:bg-[--color-gold-dark] transition-colors font-medium">
                            بحث
                        </button>
                    </div>
                </form>

                <!-- Quick Stats -->
                <div class="grid grid-cols-3 gap-6 mt-12 max-w-2xl mx-auto">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-[--color-accent]">{{ $totalHadiths ?? 0 }}</div>
                        <div class="text-sm mt-2">حديث</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-[--color-accent]">{{ $totalBooks ?? 0 }}</div>
                        <div class="text-sm mt-2">كتاب</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-[--color-accent]">{{ $totalSources ?? 9 }}</div>
                        <div class="text-sm mt-2">مصدر</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Additions -->
    <section class="container mx-auto px-4 py-16">
        <div class="mb-10">
            <h2 class="text-4xl font-bold text-[--color-primary] mb-2 font-[family-name:--font-serif]">
                آخر الإضافات
            </h2>
            <div class="w-20 h-1 bg-[--color-accent]"></div>
        </div>

        @if($latestHadiths && $latestHadiths->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($latestHadiths as $hadith)
                    <x-hadith-card :hadith="$hadith" />
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-lg shadow">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-gray-600 text-lg">لا توجد أحاديث بعد</p>
                <p class="text-gray-500 text-sm mt-2">ابدأ بإضافة الأحاديث من لوحة التحكم</p>
            </div>
        @endif

        <!-- View All Button -->
        @if($latestHadiths && $latestHadiths->count() > 0)
            <div class="text-center mt-10">
                <a href="/hadiths" class="inline-flex items-center gap-2 bg-[--color-accent] text-white px-8 py-3 rounded-full hover:bg-[--color-gold-dark] transition-colors font-medium">
                    عرض جميع الأحاديث
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
            </div>
        @endif
    </section>

    <!-- Features Section -->
    <section class="bg-white py-16">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-[--color-accent] rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-[--color-primary]">أحاديث صحيحة</h3>
                    <p class="text-gray-600">جميع الأحاديث من المصادر المعتمدة والموثوقة</p>
                </div>

                <!-- Feature 2 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-[--color-accent] rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-[--color-primary]">بحث متقدم</h3>
                    <p class="text-gray-600">بحث سريع ودقيق في جميع الأحاديث</p>
                </div>

                <!-- Feature 3 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-[--color-accent] rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-[--color-primary]">تصنيف منظم</h3>
                    <p class="text-gray-600">تنظيم الأحاديث حسب الكتب والمواضيع</p>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
