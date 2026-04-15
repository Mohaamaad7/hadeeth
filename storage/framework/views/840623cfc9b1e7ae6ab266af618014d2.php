

<?php $__env->startSection('title', 'فهرس الكتب - موسوعة الحديث الصحيح'); ?>
<?php $__env->startSection('meta_description', 'تصفح فهرس كتب موسوعة الحديث الصحيح - 45 كتاباً مرتبة بترتيب المصنف مع الأبواب والأحاديث'); ?>
<?php $__env->startSection('og_title', 'فهرس الكتب - موسوعة الحديث الصحيح'); ?>
<?php $__env->startSection('og_description', 'تصفح فهرس كتب موسوعة الحديث الصحيح - 45 كتاباً مرتبة بترتيب المصنف مع الأبواب والأحاديث'); ?>

<?php $__env->startSection('content'); ?>
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

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\hadeeth\resources\views/frontend/books/index.blade.php ENDPATH**/ ?>