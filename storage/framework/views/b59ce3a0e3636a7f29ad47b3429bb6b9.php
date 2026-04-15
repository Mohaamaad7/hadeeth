




<?php
    $currentRoute = Route::currentRouteName();
    $isHome = $currentRoute === 'home';
?>

<nav id="main-navbar"
    class="bg-white/80 backdrop-blur-xl border-b border-emerald-100/50 sticky top-0 z-50 transition-all duration-500">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between <?php echo e($isHome ? 'h-20' : 'h-16'); ?> items-center transition-all duration-300">
            
            <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-3 group">
                <div
                    class="w-11 h-11 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-200/50 transform rotate-3 group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-6 h-6 -rotate-3">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <div class="hidden sm:block">
                    <h1
                        class="text-lg font-black text-emerald-950 tracking-tight leading-none group-hover:text-emerald-700 transition-colors">
                        موسوعة الحديث الصحيح
                    </h1>
                    <span class="text-[10px] font-bold text-emerald-600/60 tracking-[0.15em] uppercase">The Authentic
                        Hadith Encyclopedia</span>
                </div>
            </a>

            
            <div class="hidden lg:flex items-center gap-1">
                <?php
                    $navItems = [
                        ['route' => 'home', 'label' => 'الرئيسية', 'icon' => 'fa-house'],
                        ['route' => 'books.index', 'label' => 'الكتب', 'icon' => 'fa-book-open'],
                        ['route' => 'search', 'label' => 'البحث', 'icon' => 'fa-magnifying-glass'],
                        ['route' => 'about', 'label' => 'عن المشروع', 'icon' => 'fa-circle-info'],
                    ];
                ?>

                <?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $isActive = $currentRoute === $item['route']
                                    || ($item['route'] === 'books.index' && str_starts_with($currentRoute, 'books.'));
                            ?>
                            <a href="<?php echo e(route($item['route'])); ?>" class="relative px-4 py-2 rounded-xl text-sm font-bold transition-all duration-300 group
                                          <?php echo e($isActive
                    ? 'text-emerald-700 bg-emerald-50'
                    : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50/50'); ?>">
                                <i
                                    class="fa-solid <?php echo e($item['icon']); ?> ml-1.5 text-xs <?php echo e($isActive ? 'text-emerald-500' : 'text-gray-400 group-hover:text-emerald-400'); ?> transition-colors"></i>
                                <?php echo e($item['label']); ?>

                                <?php if($isActive): ?>
                                    <span
                                        class="absolute bottom-0 left-1/2 -translate-x-1/2 w-6 h-0.5 bg-emerald-500 rounded-full"></span>
                                <?php endif; ?>
                            </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <div class="flex items-center gap-2">
                
                <?php if (! ($isHome)): ?>
                    <div class="hidden md:block lg:block">
                        <form action="<?php echo e(route('search')); ?>" method="GET" class="relative group">
                            <input type="text" name="q" value="<?php echo e(request('q')); ?>"
                                class="w-48 lg:w-56 py-2 pr-9 pl-4 bg-gray-50/80 border border-gray-200/80 rounded-xl text-sm focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20 focus:w-72 transition-all duration-300 placeholder:text-gray-400"
                                placeholder="ابحث في الأحاديث...">
                            <i
                                class="fa-solid fa-magnifying-glass absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 text-xs transition-colors"></i>
                        </form>
                    </div>
                <?php endif; ?>

                
                <a href="<?php echo e(route('hadith.random')); ?>"
                    class="hidden md:inline-flex items-center gap-2 text-gray-500 font-bold px-4 py-2 rounded-xl hover:text-emerald-600 hover:bg-emerald-50/50 transition-all duration-300 text-sm">
                    <i class="fa-solid fa-shuffle text-xs"></i>
                    <span>حديث عشوائي</span>
                </a>

                
                <button id="mobile-menu-btn"
                    class="lg:hidden flex items-center justify-center w-10 h-10 rounded-xl text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 transition-all duration-300"
                    aria-label="القائمة">
                    <i class="fa-solid fa-bars-staggered text-lg"></i>
                </button>
            </div>
        </div>
    </div>

    
    <div class="h-[2px] bg-gray-100/50 relative overflow-hidden">
        <div id="scroll-progress"
            class="h-full bg-gradient-to-r from-emerald-400 to-emerald-600 transition-all duration-150 ease-out"
            style="width: 0%"></div>
    </div>
</nav><?php /**PATH C:\laragon\www\hadeeth\resources\views/frontend/partials/header.blade.php ENDPATH**/ ?>