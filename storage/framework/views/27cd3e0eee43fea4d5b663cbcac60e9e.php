<?php
    // استخراج أول 60 حرف من الحديث للعنوان
    $hadithSnippet = Str::limit(strip_tags($hadith->content), 60, '...');
    $appName = config('app.name', 'موسوعة الحديث الصحيح');
    $pageTitle = $appName . ' | ' . $hadithSnippet;

    // Meta Description أطول للوصف
    $metaDescription = Str::limit(strip_tags($hadith->content), 155) . ' - حديث ' . $hadith->grade . ' من رواية ' . ($hadith->narrators->pluck('name')->join('، ') ?: 'غير محدد') . ' في ' . ($hadith->book?->name ?? 'كتب الحديث');

    $ogImage = asset('images/og-hadith.png');
?>

<?php $__env->startSection('title', $pageTitle); ?>

<?php $__env->startSection('meta_description', $metaDescription); ?>

<?php $__env->startSection('meta_keywords', 'حديث رقم ' . $hadith->number_in_book . ', ' . ($hadith->narrators->pluck('name')->join('، ') ?? '') . ', ' . ($hadith->book?->name ?? '') . ', حديث ' . $hadith->grade . ', الأحاديث النبوية'); ?>

<?php $__env->startSection('og_type', 'article'); ?>
<?php $__env->startSection('og_title', $pageTitle); ?>
<?php $__env->startSection('og_description', $metaDescription); ?>
<?php $__env->startSection('og_image', $ogImage); ?>

<?php $__env->startSection('twitter_title', $pageTitle); ?>
<?php $__env->startSection('twitter_description', $metaDescription); ?>
<?php $__env->startSection('twitter_image', $ogImage); ?>

<?php $__env->startPush('structured_data'); ?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "حديث رقم <?php echo e($hadith->number_in_book); ?>",
    "description": "<?php echo e(Str::limit($hadith->content, 200)); ?>",
    "author": {
        "@type": "Person",
        "name": "<?php echo e($hadith->narrators->pluck('name')->join('، ') ?: 'غير محدد'); ?>"
    },
    "publisher": {
        "@type": "Organization",
        "name": "موسوعة الحديث الصحيح",
        "url": "<?php echo e(url('/')); ?>"
    },
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "<?php echo e(url()->current()); ?>"
    },
    "datePublished": "<?php echo e($hadith->created_at?->toIso8601String() ?? now()->toIso8601String()); ?>",
    "dateModified": "<?php echo e($hadith->updated_at?->toIso8601String() ?? now()->toIso8601String()); ?>",
    "articleBody": "<?php echo e($hadith->content); ?>",
    "keywords": ["حديث", "<?php echo e($hadith->grade); ?>", "<?php echo e($hadith->narrators->pluck('name')->join('، ')); ?>", "<?php echo e($hadith->book?->name ?? ''); ?>"]
}
</script>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@type": "ListItem",
            "position": 1,
            "name": "الرئيسية",
            "item": "<?php echo e(url('/')); ?>"
        },
        <?php if($hadith->book): ?>
        {
            "@type": "ListItem",
            "position": 2,
            "name": "<?php echo e($hadith->book->name); ?>",
            "item": "<?php echo e(url('/')); ?>"
        },
        {
            "@type": "ListItem",
            "position": 3,
            "name": "حديث رقم <?php echo e($hadith->number_in_book); ?>",
            "item": "<?php echo e(url()->current()); ?>"
        }
        <?php else: ?>
        {
            "@type": "ListItem",
            "position": 2,
            "name": "حديث رقم <?php echo e($hadith->number_in_book); ?>",
            "item": "<?php echo e(url()->current()); ?>"
        }
        <?php endif; ?>
    ]
}
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container mx-auto px-4 py-8 max-w-5xl">

        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-6 font-medium animate-up">
            <a href="<?php echo e(route('home')); ?>" class="hover:text-emerald-600 transition-colors">الرئيسية</a>
            <i class="fa-solid fa-chevron-left text-xs text-emerald-300"></i>
            <?php if($hadith->book): ?>
                <a href="<?php echo e(route('search')); ?>?book_id=<?php echo e($hadith->book_id); ?>"
                    class="hover:text-emerald-600 transition-colors"><?php echo e($hadith->book->name); ?></a>
                <i class="fa-solid fa-chevron-left text-xs text-emerald-300"></i>
            <?php endif; ?>
            <span class="text-emerald-700 font-bold">حديث رقم <?php echo e($hadith->number_in_book); ?></span>
        </div>

        <!-- Main Hadith Card -->
        <section class="bg-white rounded-3xl shadow-xl overflow-hidden mb-8 animate-up border border-gray-100 relative">
            <!-- Header Stripe -->
            <div class="h-2 bg-gradient-to-r from-emerald-400 via-emerald-600 to-blue-500"></div>

            <div class="p-8 md:p-12 relative">
                <!-- Decorative Corner -->
                <div class="absolute top-4 left-4 opacity-10">
                    <i class="fa-solid fa-mosque text-6xl text-emerald-500"></i>
                </div>

                <!-- Metadata Badges -->
                <div class="flex flex-wrap gap-3 mb-6 relative z-10">
                    <span class="bg-emerald-50 text-emerald-800 px-4 py-1.5 rounded-full text-sm font-bold border border-emerald-200">
                        <i class="fa-solid fa-hashtag ml-1 text-emerald-500"></i> حديث رقم: <?php echo e($hadith->number_in_book); ?>

                    </span>
                    <?php
                        $gradeColor = match($hadith->grade) {
                            'صحيح' => 'green',
                            'حسن' => 'blue',
                            default => 'yellow'
                        };
                    ?>
                    <span class="bg-<?php echo e($gradeColor); ?>-50 text-<?php echo e($gradeColor); ?>-700 px-4 py-1.5 rounded-full text-sm font-bold border border-<?php echo e($gradeColor); ?>-200">
                        <i class="fa-solid fa-check-circle ml-1"></i> <?php echo e($hadith->grade); ?>

                    </span>
                    <?php if($hadith->narrators->count() > 0): ?>
                        <?php $__currentLoopData = $hadith->narrators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="bg-gray-50 text-gray-600 px-4 py-1.5 rounded-full text-sm font-bold border border-gray-200">
                                <i class="fa-solid fa-user ml-1 text-gray-400"></i> الصحابي: <?php echo e($nar->name); ?>

                            </span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                    <?php if($hadith->book): ?>
                        <span class="bg-purple-50 text-purple-600 px-4 py-1.5 rounded-full text-sm font-bold border border-purple-200">
                            <i class="fa-solid fa-book ml-1"></i> <?php echo e($hadith->book->name); ?>

                        </span>
                    <?php endif; ?>
                </div>

                <!-- The Hadith Text -->
                <div class="hadith-frame bg-emerald-50/30 p-8 md:p-10 rounded-2xl text-center my-6">
                    <div class="ornament-corner top-left"></div>
                    <div class="ornament-corner top-right"></div>
                    <div class="ornament-corner bottom-left"></div>
                    <div class="ornament-corner bottom-right"></div>

                    <p class="font-scheherazade text-2xl md:text-3xl leading-[3.5] text-gray-800 text-justify md:text-center relative z-10">
                        « <?php echo e($hadith->content); ?> »
                    </p>
                </div>

                <!-- Additional Texts (الزيادات) -->
                <?php if(!empty($hadith->additions) && is_array($hadith->additions)): ?>
                    <div class="mt-4 mb-6 space-y-3 px-2 md:px-6">
                        <?php $__currentLoopData = $hadith->additions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $addition): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="bg-gray-50/80 rounded-xl p-4 md:p-5 border border-gray-100 relative overflow-hidden group hover:border-emerald-200 transition-colors">
                                <div class="absolute right-0 top-0 bottom-0 w-1.5 bg-emerald-400/70 group-hover:bg-emerald-500 transition-colors"></div>
                                <div class="flex flex-col gap-3 pr-3">
                                    <div class="flex items-center gap-2">
                                        <span class="bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-md text-xs font-bold inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-plus-circle"></i>
                                            زيادة من: <?php echo e($addition['source_name']); ?>

                                        </span>
                                    </div>
                                    <p class="font-scheherazade text-xl md:text-2xl leading-[2.5] text-gray-700 text-justify">
                                        « <?php echo e($addition['text']); ?> »
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                <!-- النص الأصلي كما ورد في المصدر (الأمانة العلمية) -->
                <?php if($hadith->raw_text): ?>
                    <div class="mt-8 mb-4">
                        <button onclick="toggleRawText()" id="rawTextToggle"
                            class="w-full flex items-center justify-between bg-amber-50/30 hover:bg-amber-50 border border-amber-200/60 rounded-xl px-4 py-3 text-right transition-all duration-300 group shadow-sm hover:shadow-md">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-700 group-hover:bg-amber-200 transition-colors">
                                    <i class="fa-solid fa-scroll text-sm"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-gray-800 text-sm">النص كما ورد في المصدر</span>
                                    <span class="block text-[10px] text-amber-600 font-medium">الأمانة العلمية — نص الكتاب الأصلي</span>
                                </div>
                            </div>
                            <i id="rawTextArrow" class="fa-solid fa-chevron-down text-amber-400 transition-transform duration-300 text-sm" style="transform: rotate(180deg);"></i>
                        </button>

                        <div id="rawTextContent" class="overflow-hidden transition-all duration-500 ease-in-out" style="opacity: 1;">
                            <div class="mt-1 rounded-b-xl border border-t-0 border-amber-200/60 overflow-hidden shadow-inner">
                                
                                <div class="h-0.5 bg-gradient-to-r from-amber-300 via-amber-500 to-amber-300"></div>

                                <div class="p-4 md:p-6 relative" style="background: linear-gradient(135deg, #fffdf5 0%, #fef9e7 50%, #fffdf5 100%);">
                                    
                                    <div class="absolute top-2 right-2 w-4 h-4 border-t border-r border-amber-400/50 rounded-tr-sm"></div>
                                    <div class="absolute top-2 left-2 w-4 h-4 border-t border-l border-amber-400/50 rounded-tl-sm"></div>
                                    <div class="absolute bottom-2 right-2 w-4 h-4 border-b border-r border-amber-400/50 rounded-br-sm"></div>
                                    <div class="absolute bottom-2 left-2 w-4 h-4 border-b border-l border-amber-400/50 rounded-bl-sm"></div>

                                    
                                    <p class="font-scheherazade text-lg md:text-xl leading-[2.5] text-amber-950 text-center relative z-10 px-2 md:px-6" dir="rtl">
                                        <?php echo e($hadith->raw_text); ?>

                                    </p>

                                    
                                    <div class="mt-4 pt-3 border-t border-dashed border-amber-300/50 text-center">
                                        <p class="text-[10px] text-amber-600/80 font-medium flex items-center justify-center gap-1.5">
                                            <i class="fa-solid fa-book-open text-amber-400"></i>
                                            هذا النص الأصلي كما ورد في كتاب "صحيح الجامع الصغير وزيادته"
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Takhrij / Sources -->
                <?php if($hadith->sources->count() > 0): ?>
                    <div class="mt-8 pt-6 border-t border-dashed border-gray-200 text-sm text-gray-600 flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
                        <div class="flex items-center gap-2 flex-wrap">
                            <i class="fa-solid fa-book-bookmark text-emerald-500 text-lg"></i>
                            <span class="font-bold">التخريج:</span>
                            <?php $__currentLoopData = $hadith->sources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $source): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="bg-gray-100 px-3 py-1 rounded-lg text-xs font-bold"><?php echo e($source->name); ?> (<?php echo e($source->code); ?>)</span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="flex gap-3">
                            <button onclick="copyHadith()" class="text-gray-400 hover:text-emerald-600 transition-colors" title="نسخ الحديث">
                                <i class="fa-regular fa-copy text-xl"></i>
                            </button>
                            <button onclick="openShareModal()" class="text-gray-400 hover:text-emerald-600 transition-colors" title="مشاركة">
                                <i class="fa-solid fa-share-nodes text-xl"></i>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Sanad Chains (سلاسل الإسناد) - Dynamic -->
        <?php if($hadith->chains->count() > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 animate-up" style="animation-delay: 0.1s;">
                <?php $__currentLoopData = $hadith->chains; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $chain): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <section class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-emerald-200 transition-colors">
                        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-100">
                            <span class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 font-black text-sm"><?php echo e($index + 1); ?></span>
                            <h3 class="font-tajawal font-bold text-lg text-gray-800">طريق <?php echo e($chain->source->name); ?></h3>
                        </div>
                        <div class="pr-2">
                            <?php $__currentLoopData = $chain->narrators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $narrator): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="timeline-node <?php echo e($loop->last ? '' : 'pb-4'); ?>">
                                    <?php if($narrator->pivot->role): ?>
                                        <span class="text-sm text-gray-500 block mb-1"><?php echo e($narrator->pivot->role); ?></span>
                                    <?php endif; ?>
                                    <?php if($loop->last): ?>
                                        <span class="font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-lg"><?php echo e($narrator->name); ?></span>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('narrator.show', $narrator->id)); ?>" target="_blank"
                                            class="font-scheherazade text-lg text-emerald-700 hover:text-emerald-500 hover:underline transition-colors">
                                            <?php echo e($narrator->name); ?>

                                        </a>
                                    <?php endif; ?>
                                    <?php if(!$loop->first && !$loop->last): ?>
                                        <?php
                                            $appearsInOtherChains = false;
                                            foreach ($hadith->chains as $otherChain) {
                                                if ($otherChain->id !== $chain->id) {
                                                    if ($otherChain->narrators->contains('id', $narrator->id)) {
                                                        $appearsInOtherChains = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        ?>
                                        <?php if($appearsInOtherChains): ?>
                                            <span class="text-xs text-gray-400 mr-2 bg-gray-50 px-2 py-0.5 rounded">(ملتقى الطريقين)</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </section>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <!-- Simple Explanation (الشرح البسيط) -->
        <?php if($hadith->explanation): ?>
            <section class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden animate-up mb-8"
                style="animation-delay: 0.15s;">
                <div class="bg-emerald-50/50 p-5 border-b border-emerald-100 flex items-center gap-3">
                    <i class="fa-solid fa-lightbulb text-emerald-600 text-xl"></i>
                    <h2 class="font-tajawal font-bold text-xl text-gray-800">الشرح والتفسير</h2>
                </div>
                <div class="p-6 md:p-8">
                    <div class="text-gray-700 leading-loose text-justify prose prose-lg max-w-none explanation-content">
                        <?php echo $hadith->explanation; ?>

                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- Narrator Bio (if available) -->
        <?php if($hadith->narrators->filter(fn($n) => $n->bio)->count() > 0): ?>
            <?php $__currentLoopData = $hadith->narrators->filter(fn($n) => $n->bio); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <section class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100 mb-8 animate-up"
                style="animation-delay: 0.1s;">
                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-100">
                    <i class="fa-solid fa-user-tie text-emerald-500 text-xl"></i>
                    <h3 class="font-tajawal font-bold text-xl text-gray-800">نبذة عن الصحابي</h3>
                </div>
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600 text-2xl">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div class="flex-grow">
                        <h4 class="font-bold text-lg text-gray-800 mb-2"><?php echo e($nar->name); ?></h4>
                        <?php if($nar->grade_status): ?>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold mb-3"
                                style="background-color: <?php echo e($nar->color_code); ?>20; color: <?php echo e($nar->color_code); ?>; border: 1px solid <?php echo e($nar->color_code); ?>;">
                                <?php echo e($nar->grade_status); ?>

                            </span>
                        <?php endif; ?>
                        <p class="text-gray-600 leading-relaxed whitespace-pre-wrap"><?php echo e($nar->bio); ?></p>
                    </div>
                </div>
            </section>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>

        <!-- Related Hadiths -->
        <?php if($relatedHadiths->count() > 0): ?>
            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 animate-up"
                style="animation-delay: 0.2s;">
                <h3 class="font-tajawal font-bold text-xl text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-layer-group text-emerald-500"></i>
                    أحاديث ذات صلة
                </h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <?php $__currentLoopData = $relatedHadiths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $related): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('hadith.show', $related->id)); ?>"
                            class="block p-5 border border-gray-100 rounded-xl hover:border-emerald-300 hover:shadow-md transition-all group">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-xs bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full font-bold">#<?php echo e($related->number_in_book); ?></span>
                                <span class="text-xs bg-green-50 text-green-700 px-3 py-1 rounded-full font-bold"><?php echo e($related->grade); ?></span>
                            </div>
                            <p class="font-scheherazade text-sm text-gray-700 line-clamp-2 group-hover:text-gray-900">
                                <?php echo e(Str::limit($related->content, 100)); ?>

                            </p>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </section>
        <?php endif; ?>

    </div>

    <!-- Share Modal -->
    <div id="shareModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeShareModal()"></div>

        <!-- Modal Content -->
        <div class="relative bg-white rounded-3xl shadow-2xl p-6 md:p-8 w-full max-w-md mx-4 transform scale-95 opacity-0 transition-all duration-300" id="shareModalContent">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-black text-gray-900">
                    <i class="fa-solid fa-share-nodes text-emerald-500 ml-2"></i>
                    مشاركة الحديث
                </h3>
                <button onclick="closeShareModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>

            <!-- Share Buttons Grid -->
            <div class="grid grid-cols-2 gap-3 mb-6">
                <!-- WhatsApp -->
                <button onclick="shareToWhatsApp()" class="flex items-center justify-center gap-3 bg-[#25D366] hover:bg-[#20bd5a] text-white py-4 px-4 rounded-2xl font-bold transition-all hover:scale-105 shadow-lg shadow-green-200">
                    <i class="fa-brands fa-whatsapp text-2xl"></i>
                    <span>واتساب</span>
                </button>

                <!-- Twitter/X -->
                <button onclick="shareToTwitter()" class="flex items-center justify-center gap-3 bg-black hover:bg-gray-800 text-white py-4 px-4 rounded-2xl font-bold transition-all hover:scale-105 shadow-lg">
                    <i class="fa-brands fa-x-twitter text-2xl"></i>
                    <span>تويتر</span>
                </button>

                <!-- Telegram -->
                <button onclick="shareToTelegram()" class="flex items-center justify-center gap-3 bg-[#0088cc] hover:bg-[#0077b5] text-white py-4 px-4 rounded-2xl font-bold transition-all hover:scale-105 shadow-lg shadow-blue-200">
                    <i class="fa-brands fa-telegram text-2xl"></i>
                    <span>تيليجرام</span>
                </button>

                <!-- Facebook -->
                <button onclick="shareToFacebook()" class="flex items-center justify-center gap-3 bg-[#1877F2] hover:bg-[#166fe5] text-white py-4 px-4 rounded-2xl font-bold transition-all hover:scale-105 shadow-lg shadow-blue-200">
                    <i class="fa-brands fa-facebook-f text-2xl"></i>
                    <span>فيسبوك</span>
                </button>
            </div>

            <!-- Divider -->
            <div class="flex items-center gap-3 mb-4">
                <div class="flex-grow h-px bg-gray-200"></div>
                <span class="text-gray-400 text-sm">أو</span>
                <div class="flex-grow h-px bg-gray-200"></div>
            </div>

            <!-- Copy Link -->
            <button onclick="copyLinkOnly()" class="w-full flex items-center justify-center gap-3 bg-gray-100 hover:bg-gray-200 text-gray-700 py-4 px-4 rounded-2xl font-bold transition-all">
                <i class="fa-solid fa-link text-xl text-emerald-500"></i>
                <span>نسخ الرابط فقط</span>
            </button>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        // Toggle النص الأصلي (الأمانة العلمية)
        function toggleRawText() {
            const content = document.getElementById('rawTextContent');
            const arrow = document.getElementById('rawTextArrow');

            if (content.style.maxHeight === '0px') {
                // مغلق ← نفتحه
                content.style.maxHeight = content.scrollHeight + 'px';
                content.style.opacity = '1';
                arrow.style.transform = 'rotate(180deg)';

                // بعد انتهاء الحركة، نجعله none ليتمدد بحرية مع الشاشات
                setTimeout(() => {
                    if (content.style.maxHeight !== '0px') {
                        content.style.maxHeight = 'none';
                    }
                }, 500);
            } else {
                // مفتوح ← نغلقه
                // إذا لم يكن له ارتفاع محدد، نحدد ارتفاعه ليسهل عمل الـ animation
                if (!content.style.maxHeight || content.style.maxHeight === 'none') {
                    content.style.maxHeight = content.scrollHeight + 'px';
                    // Force DOM reflow
                    content.offsetHeight;
                }

                // إغلاق القسم
                content.style.maxHeight = '0px';
                content.style.opacity = '0';
                arrow.style.transform = 'rotate(0deg)';
            }
        }

        // Helper function to copy text with fallback for HTTP
        function copyToClipboard(text) {
            return new Promise((resolve, reject) => {
                // Try modern clipboard API first (requires HTTPS)
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text)
                        .then(resolve)
                        .catch(() => fallbackCopy(text, resolve, reject));
                } else {
                    // Fallback for HTTP or older browsers
                    fallbackCopy(text, resolve, reject);
                }
            });
        }

        function fallbackCopy(text, resolve, reject) {
            try {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.left = '-9999px';
                textarea.style.top = '-9999px';
                document.body.appendChild(textarea);
                textarea.focus();
                textarea.select();

                const successful = document.execCommand('copy');
                document.body.removeChild(textarea);

                if (successful) {
                    resolve();
                } else {
                    reject(new Error('Copy failed'));
                }
            } catch (err) {
                reject(err);
            }
        }

        function showToast(message, isError = false) {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-6 left-1/2 transform -translate-x-1/2 ${isError ? 'bg-red-600' : 'bg-emerald-600'} text-white px-6 py-3 rounded-xl shadow-lg z-50 font-bold flex items-center gap-2`;
            toast.innerHTML = `<i class="fa-solid ${isError ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i> ${message}`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2500);
        }

        function copyHadith() {
            // Build formatted hadith text
            const hadithContent = `<?php echo e($hadith->content); ?>`;
            const narrator = `<?php echo e($hadith->narrators->pluck('name')->join('، ') ?: 'غير محدد'); ?>`;
            const grade = `<?php echo e($hadith->grade); ?>`;
            const book = `<?php echo e($hadith->book?->name ?? ''); ?>`;
            const hadithNumber = `<?php echo e($hadith->number_in_book); ?>`;
            const sources = `<?php echo e($hadith->sources->pluck('name')->join('، ')); ?>`;
            const url = window.location.href;

            // Format: Hadith first, then source, link, then metadata
            let formattedText = `« ${hadithContent} »\n\n`;
            formattedText += `📚 المصدر: موسوعة الحديث الصحيح\n`;
            formattedText += `🔗 الرابط: ${url}\n\n`;
            formattedText += `📜 الراوي: ${narrator}\n`;
            formattedText += `✅ الدرجة: ${grade}\n`;
            if (book) {
                formattedText += `📖 الكتاب: ${book}\n`;
            }
            formattedText += `🔢 رقم الحديث: ${hadithNumber}\n`;
            if (sources) {
                formattedText += `📑 التخريج: ${sources}\n`;
            }

            copyToClipboard(formattedText)
                .then(() => showToast('تم نسخ الحديث مع المعلومات'))
                .catch(() => showToast('فشل النسخ - جرب التحديد اليدوي', true));
        }

        // Share Modal Functions
        function openShareModal() {
            const modal = document.getElementById('shareModal');
            const content = document.getElementById('shareModalContent');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeShareModal() {
            const modal = document.getElementById('shareModal');
            const content = document.getElementById('shareModalContent');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }

        // Share Text - Same format as copy but without emojis (URL encoding issues)
        const shareUrl = window.location.href;
        const narrator = `<?php echo e($hadith->narrators->pluck('name')->join('، ') ?: 'غير محدد'); ?>`;
        const grade = `<?php echo e($hadith->grade); ?>`;
        const book = `<?php echo e($hadith->book?->name ?? ''); ?>`;
        const hadithNumber = `<?php echo e($hadith->number_in_book); ?>`;
        const sources = `<?php echo e($hadith->sources->pluck('name')->join('، ')); ?>`;

        let shareTextRaw = `« <?php echo e($hadith->content); ?> »\n\n`;
        shareTextRaw += `المصدر: موسوعة الحديث الصحيح\n`;
        shareTextRaw += `الرابط: ${shareUrl}\n\n`;
        shareTextRaw += `الراوي: ${narrator}\n`;
        shareTextRaw += `الدرجة: ${grade}\n`;
        if (book) {
            shareTextRaw += `الكتاب: ${book}\n`;
        }
        shareTextRaw += `رقم الحديث: ${hadithNumber}\n`;
        if (sources) {
            shareTextRaw += `التخريج: ${sources}\n`;
        }

        const shareText = encodeURIComponent(shareTextRaw);

        function shareToWhatsApp() {
            window.open(`https://wa.me/?text=${shareText}`, '_blank');
            closeShareModal();
        }

        function shareToTwitter() {
            // Twitter has character limit, so use shorter version
            const twitterText = encodeURIComponent(`« <?php echo e(Str::limit($hadith->content, 200)); ?> »\n\n• <?php echo e($hadith->narrators->pluck('name')->join('، ') ?: ''); ?> | <?php echo e($hadith->grade); ?>\n\nموسوعة الحديث الصحيح`);
            window.open(`https://twitter.com/intent/tweet?text=${twitterText}&url=${encodeURIComponent(shareUrl)}`, '_blank');
            closeShareModal();
        }

        function shareToTelegram() {
            window.open(`https://t.me/share/url?url=${encodeURIComponent(shareUrl)}&text=${shareText}`, '_blank');
            closeShareModal();
        }

        function shareToFacebook() {
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`, '_blank');
            closeShareModal();
        }

        function copyLinkOnly() {
            copyToClipboard(window.location.href)
                .then(() => {
                    closeShareModal();
                    showToast('تم نسخ الرابط');
                })
                .catch(() => showToast('فشل النسخ', true));
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\hadeeth\resources\views/frontend/hadith-show.blade.php ENDPATH**/ ?>