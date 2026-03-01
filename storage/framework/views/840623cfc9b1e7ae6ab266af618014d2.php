

<?php $__env->startSection('title', 'فهرس الكتب - موسوعة الحديث الصحيح'); ?>
<?php $__env->startSection('meta_description', 'تصفح فهرس كتب موسوعة الحديث الصحيح - 45 كتاباً مرتبة بترتيب المصنف مع الأبواب والأحاديث'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Navbar -->
    <nav class="bg-white/90 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-3">
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
                    <a href="<?php echo e(route('home')); ?>"
                        class="text-gray-500 font-semibold hover:text-emerald-600 transition-colors">الرئيسية</a>
                    <a href="<?php echo e(route('books.index')); ?>"
                        class="text-emerald-700 font-bold hover:text-emerald-500 transition-colors">الكتب</a>
                    <a href="<?php echo e(route('about')); ?>"
                        class="text-gray-500 font-semibold hover:text-emerald-600 transition-colors">عن المشروع</a>
                </div>
                <div class="flex items-center gap-3">
                    <a href="<?php echo e(route('hadith.random')); ?>"
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
            <a href="<?php echo e(route('home')); ?>" class="hover:text-emerald-600">الرئيسية</a>
            <a href="<?php echo e(route('books.index')); ?>" class="text-emerald-600">الكتب</a>
            <a href="<?php echo e(route('about')); ?>" class="hover:text-emerald-600">عن المشروع</a>
            <a href="<?php echo e(route('hadith.random')); ?>" class="hover:text-emerald-600">حديث عشوائي</a>
        </nav>
    </div>

    <!-- Hero Header -->
    <section class="hero-pattern py-16 text-white">
        <div class="max-w-5xl mx-auto px-4 text-center relative z-10">
            <h1 class="text-3xl lg:text-5xl font-extrabold mb-4 animate-fade-in">
                <i class="fa-solid fa-book-open ml-2"></i> فهرس الكتب
            </h1>
            <p class="text-emerald-100/80 text-lg max-w-2xl mx-auto font-medium">
                تصفح الموسوعة كاملة — <?php echo e($books->count()); ?> كتاباً مرتبة بترتيب المصنف
            </p>
        </div>
    </section>

    <!-- Books Grid -->
    <section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php
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
        ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $color = $colors[$index % count($colors)]; ?>
                <a href="<?php echo e(route('books.show', $book)); ?>"
                    class="floating-card bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden group animate-up"
                    style="animation-delay: <?php echo e($index * 0.03); ?>s;">

                    <!-- Color Bar -->
                    <div class="h-1.5 bg-<?php echo e($color); ?>-500"></div>

                    <div class="p-6">
                        <!-- Book Number & Name -->
                        <div class="flex items-start gap-4 mb-4">
                            <div
                                class="w-12 h-12 bg-<?php echo e($color); ?>-50 text-<?php echo e($color); ?>-600 rounded-2xl flex items-center justify-center flex-shrink-0 font-black text-lg border border-<?php echo e($color); ?>-100">
                                <?php echo e($index + 1); ?>

                            </div>
                            <div class="flex-grow">
                                <h3
                                    class="text-lg font-black text-gray-900 group-hover:text-<?php echo e($color); ?>-600 transition-colors leading-relaxed">
                                    <?php echo e($book->name); ?>

                                </h3>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="flex items-center gap-4 text-sm text-gray-500">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-scroll text-<?php echo e($color); ?>-400"></i>
                                <span class="font-bold"><?php echo e($book->total_hadiths); ?></span> حديث
                            </span>
                            <?php if($book->children_count > 0): ?>
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-bookmark text-<?php echo e($color); ?>-400"></i>
                                    <span class="font-bold"><?php echo e($book->children_count); ?></span> باب
                                </span>
                            <?php else: ?>
                                <span class="flex items-center gap-1.5 text-gray-400">
                                    <i class="fa-solid fa-minus"></i>
                                    بدون أبواب
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Arrow -->
                        <div
                            class="mt-4 pt-4 border-t border-gray-50 text-<?php echo e($color); ?>-600 font-bold text-sm flex items-center justify-between">
                            <span>استعراض الكتاب</span>
                            <i class="fa-solid fa-arrow-left group-hover:translate-x-[-6px] transition-transform"></i>
                        </div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-10">
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
            <p class="text-gray-400 text-sm">© <?php echo e(date('Y')); ?> جميع الحقوق محفوظة</p>
        </div>
    </footer>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\hadeeth\resources\views/frontend/books/index.blade.php ENDPATH**/ ?>