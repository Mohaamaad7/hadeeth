@extends('layouts.frontend')

@section('title', 'عن موسوعة الحديث الصحيح')
@section('meta_description', 'موسوعة الحديث الصحيح - نبعٌ صافٍ من مشكاة النبوة. مرجع موثوق للأحاديث الصحيحة فقط.')

@section('content')
    <!-- Header / Navbar -->
    <nav class="bg-white/90 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
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

                <!-- Navigation -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-emerald-600 transition font-medium">
                        <i class="fas fa-home ml-1"></i> الرئيسية
                    </a>
                    <a href="{{ route('search') }}" class="text-gray-600 hover:text-emerald-600 transition font-medium">
                        <i class="fas fa-search ml-1"></i> البحث
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-900 via-emerald-800 to-emerald-950"></div>
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0"
                style="background-image: url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cpath d=%22M30 0L60 30L30 60L0 30Z%22 fill=%22none%22 stroke=%22%23ffffff%22 stroke-width=%220.5%22/%3E%3C/svg%3E'); background-size: 60px 60px;">
            </div>
        </div>

        <div class="absolute top-20 right-10 w-72 h-72 bg-emerald-400/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 left-10 w-96 h-96 bg-emerald-300/10 rounded-full blur-3xl"></div>

        <div class="relative max-w-5xl mx-auto px-4 py-20 sm:py-28 text-center">
            <div
                class="inline-flex items-center justify-center w-20 h-20 bg-white/10 backdrop-blur-sm rounded-2xl mb-8 border border-white/20">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-10 h-10 text-emerald-200">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                </svg>
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white mb-6 leading-tight">
                موسوعة الحديث الصحيح
            </h1>
            <p class="text-2xl sm:text-3xl font-scheherazade text-emerald-200 mb-8">
                نبعٌ صافٍ من مشكاة النبوة
            </p>
            <div
                class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-6 py-3 rounded-full border border-white/20">
                <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                <span class="text-white font-bold text-lg">الصحيح.. ولا شيء غير الصحيح</span>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 py-16">

        <!-- الرؤية والرسالة -->
        <section class="mb-20">
            <div
                class="bg-gradient-to-br from-emerald-50 to-white rounded-3xl p-8 sm:p-12 shadow-xl shadow-emerald-100/50 border border-emerald-100">
                <div class="flex items-start gap-6">
                    <div
                        class="hidden sm:flex w-16 h-16 bg-emerald-600 rounded-2xl items-center justify-center text-white flex-shrink-0 shadow-lg shadow-emerald-200">
                        <i class="fas fa-bullseye text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-black text-emerald-900 mb-6">الرؤية والرسالة</h2>
                        <p class="text-xl leading-relaxed text-gray-700">
                            في زمنٍ كثرت فيه الروايات واختلط فيه الصحيح بالسقيم، تنطلق <strong
                                class="text-emerald-700">"موسوعة الحديث الصحيح"</strong> لتكون ملاذاً آمناً لطالب العلم،
                            ومرجعاً موثوقاً لكل باحثٍ عن سنة النبي ﷺ بصفائها ونقائها.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- المنهجية -->
        <section class="mb-20">
            <div class="text-center mb-12">
                <span class="inline-block bg-emerald-100 text-emerald-700 px-4 py-2 rounded-full text-sm font-bold mb-4">
                    <i class="fas fa-filter ml-2"></i> منهجيتنا
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-gray-900">التخصص في الثبوت</h2>
            </div>

            <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-xl border border-gray-100 mb-8">
                <p class="text-xl leading-relaxed text-gray-700 text-center">
                    تتميز موسوعتنا بأنها <strong class="text-emerald-600">مصفاةٌ دقيقة</strong>، لا مكان فيها للأحاديث
                    الضعيفة أو الموضوعة.
                    <br>
                    نحن نؤمن بأن الوقت أمانة، لذا وفرنا على الباحث عناء التنقيب والفرز.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- للباحثين عن التحقق -->
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-6 border border-amber-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-amber-200">
                            <i class="fas fa-search-plus text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-amber-900">للباحثين عن التحقق</h3>
                    </div>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        نحيلكم بتقدير إلى موسوعة <strong>"الدرر السنية"</strong> لمن أراد البحث في الأحاديث الضعيفة وعللها.
                    </p>
                    <a href="https://dorar.net" target="_blank"
                        class="inline-flex items-center gap-2 text-amber-700 hover:text-amber-900 font-bold transition">
                        <span>زيارة الدرر السنية</span>
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>

                <!-- للباحثين عن اليقين -->
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-6 border border-emerald-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-12 h-12 bg-emerald-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-200">
                            <i class="fas fa-check-double text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-emerald-900">للباحثين عن اليقين</h3>
                    </div>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        موسوعتنا هي وجهتكم للنقل المباشر، والعمل المطمئن بما صح عن رسول الله ﷺ.
                    </p>
                    <a href="{{ route('search') }}"
                        class="inline-flex items-center gap-2 text-emerald-700 hover:text-emerald-900 font-bold transition">
                        <span>ابدأ البحث الآن</span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </section>

        <!-- المادة العلمية -->
        <section class="mb-20">
            <div
                class="relative bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl p-8 sm:p-12 text-white overflow-hidden">
                <div class="absolute top-0 left-0 w-40 h-40 bg-emerald-500/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 right-0 w-60 h-60 bg-emerald-400/10 rounded-full blur-3xl"></div>

                <div class="relative">
                    <div class="text-center mb-10">
                        <span
                            class="inline-block bg-emerald-500/20 text-emerald-300 px-4 py-2 rounded-full text-sm font-bold mb-4 border border-emerald-500/30">
                            <i class="fas fa-gem ml-2"></i> المادة العلمية
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-black">كنزٌ بين يديك</h2>
                    </div>

                    <p class="text-xl leading-relaxed text-gray-300 text-center mb-10">
                        هذا المشروع هو ثمرة تفريغ رقمي دقيق وعناية فائقة بسفرٍ عظيم يجمع أطراف السنة، وهو كتاب:
                    </p>

                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20 text-center">
                        <h3 class="text-2xl sm:text-3xl font-black text-emerald-300 mb-4">
                            صحيح الجامع الصغير وزيادته
                        </h3>
                        <p class="text-2xl text-white/80">
                            (الفتح الكبير)
                        </p>
                        <div class="mt-6 pt-6 border-t border-white/10">
                            <p class="text-gray-300">
                                وقد تم ترتيبه بعناية فائقة على <strong class="text-white">الأبواب الفقهية
                                    والموضوعية</strong>،
                                ليجمع بين سهولة الوصول وغزارة الفائدة.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- أركان العمل -->
        <section class="mb-20">
            <div class="text-center mb-12">
                <span class="inline-block bg-emerald-100 text-emerald-700 px-4 py-2 rounded-full text-sm font-bold mb-4">
                    <i class="fas fa-users ml-2"></i> أركان هذا العمل
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-gray-900">عمالقة الحديث عبر العصور</h2>
                <p class="text-gray-600 mt-3">يقوم هذا الصرح العلمي على أكتاف هؤلاء الأئمة الأعلام</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <!-- السيوطي -->
                <div
                    class="group bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-emerald-200 text-center">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-full mx-auto mb-6 flex items-center justify-center text-white shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform">
                        <span class="text-3xl font-black">١</span>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-2">الحافظ جلال الدين السيوطي</h3>
                    <span class="inline-block bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-sm font-bold">
                        صاحب الأصل والجامع
                    </span>
                </div>

                <!-- النبهاني -->
                <div
                    class="group bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-emerald-200 text-center">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full mx-auto mb-6 flex items-center justify-center text-white shadow-lg shadow-blue-200 group-hover:scale-110 transition-transform">
                        <span class="text-3xl font-black">٢</span>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-2">الشيخ يوسف النبهاني</h3>
                    <span class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-bold">
                        صاحب الزيادات
                    </span>
                </div>

                <!-- الألباني -->
                <div
                    class="group bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-emerald-200 text-center">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-amber-500 to-amber-700 rounded-full mx-auto mb-6 flex items-center justify-center text-white shadow-lg shadow-amber-200 group-hover:scale-110 transition-transform">
                        <span class="text-3xl font-black">٣</span>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-2">العلامة محمد ناصر الدين الألباني</h3>
                    <span class="inline-block bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-sm font-bold">
                        صاحب التحقيق والتصحيح
                    </span>
                </div>
            </div>
        </section>

        <!-- إعداد وجمع -->
        <section class="mb-16">
            <div
                class="bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-3xl p-8 sm:p-12 text-white text-center relative overflow-hidden">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute inset-0"
                        style="background-image: url('data:image/svg+xml,%3Csvg width=%2240%22 height=%2240%22 viewBox=%220 0 40 40%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Ccircle cx=%2220%22 cy=%2220%22 r=%221%22 fill=%22%23ffffff%22/%3E%3C/svg%3E'); background-size: 40px 40px;">
                    </div>
                </div>

                <div class="relative">
                    <div
                        class="w-16 h-16 bg-white/20 rounded-2xl mx-auto mb-6 flex items-center justify-center backdrop-blur-sm border border-white/30">
                        <i class="fas fa-feather-alt text-3xl"></i>
                    </div>

                    <span
                        class="inline-block bg-white/20 px-4 py-2 rounded-full text-sm font-bold mb-6 border border-white/30">
                        إعداد وجمع
                    </span>

                    <p class="text-xl text-emerald-100 mb-4">قام بإعداد هذا السفر وخدمته ليكون بين أيديكم:</p>

                    <h3 class="text-2xl sm:text-3xl font-black mb-3">
                        فضيلة الشيخ / حسن عبد الوهاب عمر عثمان مدين
                    </h3>
                    <p class="text-emerald-200 text-lg">
                        (بكالوريوس الشريعة الإسلامية)
                    </p>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="text-center">
            <a href="{{ route('home') }}"
                class="inline-flex items-center gap-3 bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-4 rounded-2xl font-bold text-lg shadow-xl shadow-emerald-200 hover:shadow-2xl hover:shadow-emerald-300 transition-all duration-300 transform hover:-translate-y-1">
                <i class="fas fa-search"></i>
                <span>ابدأ رحلتك مع الحديث الصحيح</span>
                <i class="fas fa-arrow-left"></i>
            </a>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 mt-16">
        <div class="max-w-5xl mx-auto px-4 text-center">
            <div class="mb-6">
                <div class="inline-flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold">موسوعة الحديث الصحيح</span>
                </div>
                <p class="text-gray-400">الصحيح.. ولا شيء غير الصحيح</p>
            </div>
            <div class="flex justify-center gap-6 mb-6">
                <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition">الرئيسية</a>
                <a href="{{ route('search') }}" class="text-gray-400 hover:text-white transition">البحث</a>
                <a href="{{ route('about') }}" class="text-emerald-400 hover:text-emerald-300 transition">عن الموسوعة</a>
            </div>
            <p class="text-gray-500 text-sm">
                جميع الحقوق محفوظة © {{ date('Y') }}
            </p>
        </div>
    </footer>
@endsection