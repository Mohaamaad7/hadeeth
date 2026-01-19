<?php $__env->startSection('title', 'موسوعة الحديث الصحيح | الرئيسية'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Header / Navbar -->
    <nav class="bg-white/90 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <!-- Logo -->
                <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-emerald-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-emerald-100 transform rotate-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-7 h-7 -rotate-3">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-emerald-950 tracking-tight leading-none">موسوعة الحديث الصحيح
                        </h1>
                        <span class="text-[11px] font-bold text-blue-600 tracking-[0.2em] uppercase">The Authentic Hadith
                            Encyclopedia</span>
                    </div>
                </a>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-reverse space-x-10">
                    <a href="<?php echo e(route('home')); ?>"
                        class="text-emerald-700 font-bold hover:text-emerald-500 transition-colors">الرئيسية</a>
                    <a href="#" class="text-gray-500 font-semibold hover:text-emerald-600 transition-colors">الكتب</a>
                    <a href="#" class="text-gray-500 font-semibold hover:text-emerald-600 transition-colors">الرواة</a>
                    <a href="<?php echo e(route('about')); ?>"
                        class="text-gray-500 font-semibold hover:text-emerald-600 transition-colors">عن المشروع</a>
                </div>

                <!-- Auth & Mobile Menu -->
                <div class="flex items-center gap-3">
                    <a href="<?php echo e(route('hadith.random')); ?>"
                        class="hidden md:block text-gray-600 font-bold px-4 py-2 hover:text-emerald-600 transition-colors">
                        <i class="fa-solid fa-shuffle ml-1"></i> حديث عشوائي
                    </a>
                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-btn" class="md:hidden text-gray-600 hover:text-emerald-600 text-2xl">
                        <i class="fa-solid fa-bars-staggered"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu"
        class="fixed inset-0 bg-white/95 backdrop-blur-sm z-50 transform translate-x-full transition-transform duration-300 flex flex-col items-center justify-center md:hidden">
        <button id="close-menu" class="absolute top-6 left-6 text-3xl text-gray-500 hover:text-red-500">
            <i class="fa-solid fa-times"></i>
        </button>
        <nav class="flex flex-col items-center gap-6 text-xl font-bold text-gray-700">
            <a href="<?php echo e(route('home')); ?>" class="hover:text-emerald-600">الرئيسية</a>
            <a href="#" class="hover:text-emerald-600">الكتب</a>
            <a href="#" class="hover:text-emerald-600">الرواة</a>
            <a href="<?php echo e(route('about')); ?>" class="hover:text-emerald-600">عن المشروع</a>
            <a href="<?php echo e(route('hadith.random')); ?>" class="hover:text-emerald-600">حديث عشوائي</a>
        </nav>
    </div>

    <!-- Hero Section -->
    <section class="hero-pattern py-24 lg:py-40 text-white border-b-4 border-emerald-500/20">
        <div class="max-w-5xl mx-auto px-4 text-center relative z-10">
            <h2 class="text-4xl lg:text-6xl font-extrabold mb-8 leading-tight tracking-tight animate-fade-in">
                مرجعك الرقمي الأول لعلوم <br />
                <span class="text-emerald-400">الحديث الشريف</span>
            </h2>
            <p class="text-emerald-50/80 text-lg lg:text-xl mb-12 max-w-2xl mx-auto font-medium leading-relaxed">
                قاعدة بيانات ذكية تضم آلاف الأحاديث الصحيحة مع إحصائيات دقيقة وتراجم مفصلة للرواة.
            </p>

            <!-- Search Bar -->
            <form action="<?php echo e(route('search')); ?>" method="GET" class="max-w-3xl mx-auto">
                <div
                    class="search-container flex items-center p-2 rounded-2xl shadow-2xl transition-all focus-within:ring-4 focus-within:ring-emerald-500/20 border border-white/20">
                    <div class="flex-grow flex items-center px-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                            stroke="currentColor" class="w-6 h-6 text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <input type="text" name="q" placeholder="ابحث عن حديث، راوٍ، أو كتاب..."
                            class="w-full py-4 px-4 text-lg lg:text-xl text-gray-900 focus:outline-none bg-transparent font-medium"
                            value="<?php echo e(request('q')); ?>" required>
                    </div>
                    <button type="submit"
                        class="bg-emerald-600 text-white px-8 lg:px-10 py-4 rounded-xl font-black text-lg hover:bg-emerald-500 transition-all shadow-lg active:scale-95">
                        بحث
                    </button>
                </div>

                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <span class="text-emerald-100/60 font-medium">الأكثر بحثاً:</span>
                    <a href="<?php echo e(route('search')); ?>?q=حديث+جبريل"
                        class="px-4 py-1 rounded-full bg-white/10 hover:bg-white/20 border border-white/10 transition-all text-sm">حديث
                        جبريل</a>
                    <a href="<?php echo e(route('search')); ?>?q=فضل+الصيام"
                        class="px-4 py-1 rounded-full bg-white/10 hover:bg-white/20 border border-white/10 transition-all text-sm">فضل
                        الصيام</a>
                    <a href="<?php echo e(route('search')); ?>?q=أركان+الإسلام"
                        class="px-4 py-1 rounded-full bg-white/10 hover:bg-white/20 border border-white/10 transition-all text-sm">أركان
                        الإسلام</a>
                </div>
            </form>
        </div>
    </section>

    <!-- Quick Stats -->
    <section class="py-12 bg-white border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-4">
            <div class="flex flex-wrap justify-center gap-12 text-center">
                <div class="group cursor-pointer">
                    <div class="text-4xl font-black text-emerald-600 group-hover:text-emerald-700 transition-colors">
                        <?php echo e(number_format($totalHadiths)); ?>

                    </div>
                    <div class="text-sm text-gray-500 mt-1 font-semibold">حديث</div>
                </div>
                <div class="w-px h-16 bg-gray-200"></div>
                <div class="group cursor-pointer">
                    <div class="text-4xl font-black text-emerald-600 group-hover:text-emerald-700 transition-colors">
                        <?php echo e(number_format($totalBooks)); ?>

                    </div>
                    <div class="text-sm text-gray-500 mt-1 font-semibold">كتاب</div>
                </div>
                <div class="w-px h-16 bg-gray-200"></div>
                <div class="group cursor-pointer">
                    <div class="text-4xl font-black text-emerald-600 group-hover:text-emerald-700 transition-colors">
                        <?php echo e(number_format($totalSources)); ?>

                    </div>
                    <div class="text-sm text-gray-500 mt-1 font-semibold">مصدر</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Features -->
    <section class="py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Books -->
            <div class="floating-card bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-8 h-8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-4">المكتبة الحديثية</h3>
                <p class="text-gray-500 leading-relaxed font-medium">تصفح الكتب السبعة ومسانيد الأئمة مع شروح مفصلة لكل باب
                    فقهي وموضوع.</p>
                <div class="mt-8 pt-8 border-t border-gray-50">
                    <a href="<?php echo e(route('search')); ?>?source=خ" class="text-blue-600 font-black cursor-pointer group">استعرض
                        الكتب <span class="group-hover:mr-2 transition-all">←</span></a>
                </div>
            </div>

            <!-- Narrators -->
            <div class="floating-card bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-8 h-8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-4">شجرة الرواة</h3>
                <p class="text-gray-500 leading-relaxed font-medium">قاعدة بيانات تفاعلية لرجال الحديث والجرح والتعديل توضح
                    الاتصال بين السند والمتن.</p>
                <div class="mt-8 pt-8 border-t border-gray-50">
                    <span class="text-emerald-600 font-black cursor-pointer group">بحث في الرواة <span
                            class="group-hover:mr-2 transition-all">←</span></span>
                </div>
            </div>

            <!-- Analysis -->
            <div class="floating-card bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <div class="w-16 h-16 bg-gray-50 text-gray-800 rounded-2xl flex items-center justify-center mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-8 h-8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-4">إحصائيات متقدمة</h3>
                <p class="text-gray-500 leading-relaxed font-medium">رسوم بيانية توضح توزيع الأحاديث حسب الصحة والضعف،
                    وتكرارها في المصادر المختلفة.</p>
                <div class="mt-8 pt-8 border-t border-gray-50">
                    <span class="text-gray-800 font-black cursor-pointer group">شاهد الأرقام <span
                            class="group-hover:mr-2 transition-all">←</span></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Categories -->
    <section class="py-12 bg-gray-50 border-t border-gray-100">
        <div class="max-w-4xl mx-auto px-4">
            <h3 class="text-center text-xl font-bold text-gray-800 mb-8">تصفح حسب المصدر</h3>
            <div class="flex flex-wrap justify-center gap-6 text-sm text-gray-600">
                <a href="<?php echo e(route('search')); ?>?source=خ"
                    class="flex items-center gap-2 hover:text-emerald-600 transition-colors group bg-white px-5 py-3 rounded-xl shadow-sm border border-gray-100">
                    <i class="fa-solid fa-book-open text-emerald-400 group-hover:text-emerald-600"></i>
                    <span class="font-bold">صحيح البخاري</span>
                </a>
                <a href="<?php echo e(route('search')); ?>?source=م"
                    class="flex items-center gap-2 hover:text-emerald-600 transition-colors group bg-white px-5 py-3 rounded-xl shadow-sm border border-gray-100">
                    <i class="fa-solid fa-book-open text-emerald-400 group-hover:text-emerald-600"></i>
                    <span class="font-bold">صحيح مسلم</span>
                </a>
                <a href="<?php echo e(route('search')); ?>?source=ت"
                    class="flex items-center gap-2 hover:text-emerald-600 transition-colors group bg-white px-5 py-3 rounded-xl shadow-sm border border-gray-100">
                    <i class="fa-solid fa-book-open text-emerald-400 group-hover:text-emerald-600"></i>
                    <span class="font-bold">سنن الترمذي</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Latest Hadiths -->
    <?php if($latestHadiths->count() > 0): ?>
        <section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-black text-gray-800 mb-10 text-center">
                <i class="fa-solid fa-star text-emerald-500 ml-2"></i>
                أحاديث مميزة
            </h2>
            <div class="grid md:grid-cols-3 gap-6">
                <?php $__currentLoopData = $latestHadiths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hadith): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('hadith.show', $hadith->id)); ?>"
                        class="floating-card bg-white p-6 rounded-2xl border border-gray-100 shadow-sm group">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-xs bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full font-bold">
                                #<?php echo e($hadith->number_in_book); ?>

                            </span>
                            <span class="text-xs bg-green-50 text-green-700 px-3 py-1 rounded-full font-bold">
                                <?php echo e($hadith->grade); ?>

                            </span>
                        </div>
                        <p class="font-scheherazade text-lg text-gray-700 leading-relaxed line-clamp-3 group-hover:text-gray-900">
                            <?php echo e(Str::limit($hadith->content, 120)); ?>

                        </p>
                        <div class="mt-4 pt-4 border-t border-gray-50 text-sm text-gray-500 flex items-center justify-between">
                            <span>
                                <i class="fa-solid fa-user ml-1 text-emerald-400"></i> <?php echo e($hadith->narrator?->name ?? 'غير محدد'); ?>

                            </span>
                            <span class="text-emerald-600 font-bold group-hover:translate-x-[-4px] transition-transform">
                                اقرأ المزيد ←
                            </span>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-16">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex flex-col items-center mb-8">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <h4 class="text-xl font-black text-gray-900">موسوعة الحديث الصحيح</h4>
            </div>
            <p class="text-gray-400 text-sm font-medium mb-8">منصة رقمية متطورة لخدمة التراث النبوي</p>
            <div class="flex justify-center gap-10 text-gray-500 font-bold mb-10">
                <a href="#" class="hover:text-emerald-600 transition-colors">عن الموسوعة</a>
                <a href="#" class="hover:text-emerald-600 transition-colors">المنهجية العلمية</a>
                <a href="#" class="hover:text-emerald-600 transition-colors">اتصل بنا</a>
            </div>
            <p class="text-gray-300 text-[12px]">© <?php echo e(date('Y')); ?> جميع الحقوق محفوظة لفريق عمل الموسوعة</p>
        </div>
    </footer>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\server\www\hadeeth\resources\views/frontend/home.blade.php ENDPATH**/ ?>