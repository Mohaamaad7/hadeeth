




<?php
    $currentRoute = Route::currentRouteName();
?>


<div id="mobile-menu-overlay"
    class="fixed inset-0 bg-emerald-950/60 backdrop-blur-sm z-[60] opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden"
    onclick="closeMobileMenu()"></div>


<div id="mobile-menu"
    class="fixed top-0 right-0 bottom-0 w-80 max-w-[85vw] bg-white z-[70] transform translate-x-full transition-transform duration-500 ease-[cubic-bezier(0.4,0,0.2,1)] lg:hidden shadow-2xl shadow-emerald-900/20 flex flex-col">

    
    <div class="flex items-center justify-between p-5 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div
                class="w-9 h-9 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center text-white shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                </svg>
            </div>
            <span class="font-black text-emerald-950 text-sm">الموسوعة</span>
        </div>
        <button id="close-menu" onclick="closeMobileMenu()"
            class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-red-50 flex items-center justify-center text-gray-400 hover:text-red-500 transition-all">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    
    <div class="px-5 py-4 border-b border-gray-50">
        <form action="<?php echo e(route('search')); ?>" method="GET" class="relative">
            <input type="text" name="q" value="<?php echo e(request('q')); ?>"
                class="w-full py-3 pr-10 pl-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20 transition-all placeholder:text-gray-400"
                placeholder="ابحث عن حديث...">
            <i
                class="fa-solid fa-magnifying-glass absolute right-3.5 top-1/2 -translate-y-1/2 text-emerald-400 text-sm"></i>
        </form>
    </div>

    
    <nav class="flex-1 overflow-y-auto py-3 px-3">
        <?php
            $mobileNavItems = [
                ['route' => 'home', 'label' => 'الرئيسية', 'icon' => 'fa-house', 'desc' => 'الصفحة الرئيسية'],
                ['route' => 'books.index', 'label' => 'فهرس الكتب', 'icon' => 'fa-book-open', 'desc' => 'تصفح الكتب والأبواب'],
                ['route' => 'search', 'label' => 'البحث المتقدم', 'icon' => 'fa-magnifying-glass', 'desc' => 'بحث في الأحاديث'],
                ['route' => 'about', 'label' => 'عن الموسوعة', 'icon' => 'fa-circle-info', 'desc' => 'المنهجية والهدف'],
                ['route' => 'hadith.random', 'label' => 'حديث عشوائي', 'icon' => 'fa-shuffle', 'desc' => 'اكتشف حديثاً جديداً'],
            ];
        ?>

        <?php $__currentLoopData = $mobileNavItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $isActive = $currentRoute === $item['route']
                    || ($item['route'] === 'books.index' && str_starts_with($currentRoute ?? '', 'books.'));
            ?>
            <a href="<?php echo e(route($item['route'])); ?>" class="flex items-center gap-4 p-3.5 rounded-2xl mb-1 transition-all duration-300 group
                          <?php echo e($isActive
            ? 'bg-emerald-50 text-emerald-700'
            : 'text-gray-600 hover:bg-gray-50'); ?>"
                style="animation: slideInRight 0.4s ease-out <?php echo e(($index * 0.06)); ?>s both;">
                <div
                    class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 transition-all duration-300
                                <?php echo e($isActive
            ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-200'
            : 'bg-gray-100 text-gray-500 group-hover:bg-emerald-100 group-hover:text-emerald-600'); ?>">
                    <i class="fa-solid <?php echo e($item['icon']); ?> text-sm"></i>
                </div>
                <div class="flex-grow">
                    <div class="font-bold text-sm <?php echo e($isActive ? 'text-emerald-800' : ''); ?>"><?php echo e($item['label']); ?></div>
                    <div class="text-[11px] <?php echo e($isActive ? 'text-emerald-600/70' : 'text-gray-400'); ?>"><?php echo e($item['desc']); ?>

                    </div>
                </div>
                <?php if($isActive): ?>
                    <div class="w-1.5 h-8 bg-emerald-500 rounded-full"></div>
                <?php endif; ?>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>

    
    <div class="p-4 border-t border-gray-100 bg-gray-50/50">
        <div class="text-center">
            <p class="text-[10px] text-gray-400 font-medium">الصحيح.. ولا شيء غير الصحيح</p>
        </div>
    </div>
</div><?php /**PATH C:\laragon\www\hadeeth\resources\views/frontend/partials/mobile-menu.blade.php ENDPATH**/ ?>