

<?php $__env->startSection('title'); ?><?php echo e($book->name); ?> - موسوعة الحديث الصحيح<?php $__env->stopSection(); ?>
<?php $__env->startSection('meta_description', 'تصفح ' . $book->name . ' في موسوعة الحديث الصحيح'); ?>

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

    <!-- Breadcrumb -->
    <div class="bg-gray-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <nav class="flex items-center gap-2 text-sm text-gray-500 font-medium flex-wrap">
                <a href="<?php echo e(route('home')); ?>" class="hover:text-emerald-600 transition-colors">
                    <i class="fa-solid fa-house"></i>
                </a>
                <i class="fa-solid fa-chevron-left text-[10px] text-gray-300"></i>
                <a href="<?php echo e(route('books.index')); ?>" class="hover:text-emerald-600 transition-colors">الكتب</a>
                <?php if($parentBook): ?>
                    <i class="fa-solid fa-chevron-left text-[10px] text-gray-300"></i>
                    <a href="<?php echo e(route('books.show', $parentBook)); ?>"
                        class="hover:text-emerald-600 transition-colors"><?php echo e($parentBook->name); ?></a>
                <?php endif; ?>
                <i class="fa-solid fa-chevron-left text-[10px] text-gray-300"></i>
                <span class="text-emerald-700 font-bold"><?php echo e($book->name); ?></span>
            </nav>
        </div>
    </div>

    <!-- Book Header -->
    <section class="hero-pattern py-12 text-white">
        <div class="max-w-5xl mx-auto px-4 text-center relative z-10">
            <?php if($parentBook): ?>
                <p class="text-emerald-300 font-bold mb-2">
                    <i class="fa-solid fa-book ml-1"></i> <?php echo e($parentBook->name); ?>

                </p>
            <?php endif; ?>
            <h1 class="text-2xl lg:text-4xl font-extrabold mb-3 animate-fade-in">
                <?php echo e($book->name); ?>

            </h1>
            <?php if($chapters): ?>
                <p class="text-emerald-100/80 font-medium">
                    <?php echo e($chapters->count()); ?> باب
                </p>
            <?php elseif($hadiths): ?>
                <p class="text-emerald-100/80 font-medium">
                    <?php echo e($hadiths->total()); ?> حديث
                </p>
            <?php endif; ?>

            <!-- PDF Download Buttons -->
            <?php
                $pdfBook = $parentBook ?? $book;
                $isChapter = $parentBook !== null;
            ?>
            <div class="flex flex-wrap items-center justify-center gap-3 mt-5">
                
                <a href="<?php echo e(route('books.pdf', $pdfBook)); ?>" target="_blank"
                    class="inline-flex items-center gap-2 px-5 py-2 bg-white/15 backdrop-blur-sm border border-white/30 rounded-xl text-white font-bold text-xs hover:bg-white/25 transition-all hover:scale-105">
                    <i class="fa-solid fa-file-pdf text-red-300"></i>
                    PDF المستخرج
                    <span class="text-emerald-200 text-[10px]">(<?php echo e($pdfBook->name); ?>)</span>
                </a>
                <a href="<?php echo e(route('books.pdf', [$pdfBook, 'type' => 'original'])); ?>" target="_blank"
                    class="inline-flex items-center gap-2 px-5 py-2 bg-amber-900/30 backdrop-blur-sm border border-amber-300/30 rounded-xl text-white font-bold text-xs hover:bg-amber-900/50 transition-all hover:scale-105">
                    <i class="fa-solid fa-scroll text-amber-300"></i>
                    PDF الأصل
                    <span class="text-amber-200 text-[10px]">(<?php echo e($pdfBook->name); ?>)</span>
                </a>

                
                <?php if($isChapter): ?>
                    <a href="<?php echo e(route('books.pdf', $book)); ?>" target="_blank"
                        class="inline-flex items-center gap-2 px-5 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white/80 font-bold text-xs hover:bg-white/20 transition-all hover:scale-105">
                        <i class="fa-solid fa-file-pdf text-red-200"></i>
                        PDF هذا الباب
                    </a>
                    <a href="<?php echo e(route('books.pdf', [$book, 'type' => 'original'])); ?>" target="_blank"
                        class="inline-flex items-center gap-2 px-5 py-2 bg-amber-900/20 backdrop-blur-sm border border-amber-300/20 rounded-xl text-white/80 font-bold text-xs hover:bg-amber-900/40 transition-all hover:scale-105">
                        <i class="fa-solid fa-scroll text-amber-200"></i>
                        PDF هذا الباب (الأصل)
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        
        <?php if($chapters): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php $__currentLoopData = $chapters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $chapter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('books.show', $chapter)); ?>"
                        class="floating-card bg-white rounded-2xl border border-gray-100 shadow-sm p-5 group animate-up"
                        style="animation-delay: <?php echo e($index * 0.03); ?>s;">
                        <div class="flex items-start gap-3">
                            <div
                                class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0 font-black text-sm border border-emerald-100">
                                <?php echo e($chapter->sort_order); ?>

                            </div>
                            <div class="flex-grow">
                                <h3
                                    class="font-bold text-gray-800 group-hover:text-emerald-600 transition-colors leading-relaxed mb-2">
                                    <?php echo e($chapter->name); ?>

                                </h3>
                                <div class="flex items-center gap-2 text-xs text-gray-400">
                                    <i class="fa-solid fa-scroll"></i>
                                    <span class="font-bold"><?php echo e($chapter->hadiths_count); ?></span> حديث
                                </div>
                            </div>
                            <i
                                class="fa-solid fa-arrow-left text-gray-300 group-hover:text-emerald-500 group-hover:translate-x-[-4px] transition-all mt-2"></i>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
        <?php elseif($hadiths && $hadiths->count() > 0): ?>
            <div class="space-y-5">
                <?php $__currentLoopData = $hadiths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $hadith): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article
                        class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:border-emerald-300 hover:shadow-lg transition-all animate-up"
                        style="animation-delay: <?php echo e($index * 0.04); ?>s;">
                        <div class="p-6">
                            <!-- Header -->
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span
                                    class="bg-emerald-50 text-emerald-800 px-3 py-1 rounded-full text-xs font-bold border border-emerald-200">
                                    <i class="fa-solid fa-hashtag ml-1"></i> <?php echo e($hadith->number_in_book); ?>

                                </span>
                                <?php
                                    $gradeColor = match ($hadith->grade) {
                                        'صحيح' => 'green',
                                        'حسن' => 'blue',
                                        default => 'yellow'
                                    };
                                ?>
                                <span
                                    class="bg-<?php echo e($gradeColor); ?>-50 text-<?php echo e($gradeColor); ?>-700 px-3 py-1 rounded-full text-xs font-bold border border-<?php echo e($gradeColor); ?>-200">
                                    <i class="fa-solid fa-check-circle ml-1"></i> <?php echo e($hadith->grade); ?>

                                </span>
                                <?php if($hadith->narrator): ?>
                                    <span
                                        class="bg-gray-50 text-gray-600 px-3 py-1 rounded-full text-xs font-bold border border-gray-200">
                                        <i class="fa-solid fa-user ml-1"></i> <?php echo e($hadith->narrator->name); ?>

                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Hadith Text -->
                            <p class="font-scheherazade text-lg leading-loose text-gray-800 mb-4">
                                <?php echo e($hadith->content); ?>

                            </p>

                            <!-- Footer -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <div class="flex items-center gap-2 text-xs text-gray-500 flex-wrap">
                                    <?php if($hadith->sources->count() > 0): ?>
                                        <i class="fa-solid fa-book-bookmark text-emerald-500"></i>
                                        <span class="font-bold">التخريج:</span>
                                        <?php $__currentLoopData = $hadith->sources->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $source): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="bg-gray-100 px-2 py-0.5 rounded-lg font-bold"><?php echo e($source->name); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($hadith->sources->count() > 4): ?>
                                            <span class="text-emerald-600 font-bold">+<?php echo e($hadith->sources->count() - 4); ?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <a href="<?php echo e(route('hadith.show', $hadith->id)); ?>"
                                    class="text-emerald-600 hover:text-emerald-700 font-bold text-sm transition-colors">
                                    التفاصيل <i class="fa-solid fa-arrow-left mr-1"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                <?php echo e($hadiths->links()); ?>

            </div>
        <?php else: ?>
            <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-gray-100 animate-up">
                <i class="fa-solid fa-inbox text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-800 mb-2">لا توجد أحاديث بعد</h3>
                <p class="text-gray-500">هذا الكتاب لم يتم إضافة أحاديث إليه حتى الآن</p>
            </div>
        <?php endif; ?>

        
        <?php if($parentBook): ?>
            <?php
                $allChapters = $parentBook->children()->orderBy('sort_order')->get();
                $currentIndex = $allChapters->search(fn($c) => $c->id === $book->id);
                $prevChapter = $currentIndex > 0 ? $allChapters[$currentIndex - 1] : null;
                $nextChapter = $currentIndex < $allChapters->count() - 1 ? $allChapters[$currentIndex + 1] : null;
            ?>
            <div class="flex items-center justify-between mt-10 pt-6 border-t border-gray-200">
                <?php if($prevChapter): ?>
                    <a href="<?php echo e(route('books.show', $prevChapter)); ?>"
                        class="flex items-center gap-3 text-gray-600 hover:text-emerald-600 transition-colors group">
                        <i class="fa-solid fa-arrow-right group-hover:translate-x-[4px] transition-transform text-lg"></i>
                        <div>
                            <div class="text-xs text-gray-400 font-bold">الباب السابق</div>
                            <div class="font-bold"><?php echo e(Str::limit($prevChapter->name, 40)); ?></div>
                        </div>
                    </a>
                <?php else: ?>
                    <div></div>
                <?php endif; ?>

                <a href="<?php echo e(route('books.show', $parentBook)); ?>" class="text-gray-400 hover:text-emerald-600 transition-colors"
                    title="العودة لفهرس الأبواب">
                    <i class="fa-solid fa-th-large text-xl"></i>
                </a>

                <?php if($nextChapter): ?>
                    <a href="<?php echo e(route('books.show', $nextChapter)); ?>"
                        class="flex items-center gap-3 text-gray-600 hover:text-emerald-600 transition-colors group text-left">
                        <div>
                            <div class="text-xs text-gray-400 font-bold">الباب التالي</div>
                            <div class="font-bold"><?php echo e(Str::limit($nextChapter->name, 40)); ?></div>
                        </div>
                        <i class="fa-solid fa-arrow-left group-hover:translate-x-[-4px] transition-transform text-lg"></i>
                    </a>
                <?php else: ?>
                    <div></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-10 mt-6">
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
<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\hadeeth\resources\views/frontend/books/show.blade.php ENDPATH**/ ?>