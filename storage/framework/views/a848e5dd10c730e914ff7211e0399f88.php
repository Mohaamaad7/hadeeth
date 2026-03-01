<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <style>
        /* ========== Base Styles ========== */
        @page {
            margin: 25mm 20mm 20mm 20mm;
            footer: html_footer;
        }

        body {
            font-family: 'aealarabiya', 'XB Riyaz', 'Tahoma', sans-serif;
            font-size: 13pt;
            line-height: 2;
            color: #1a1a1a;
            direction: rtl;
        }

        /* ========== Cover Page ========== */
        .cover {
            text-align: center;
            padding-top: 120px;
        }

        .cover-ornament {
            color: #059669;
            font-size: 40pt;
            margin-bottom: 20px;
        }

        .cover-title {
            font-size: 28pt;
            font-weight: bold;
            color: #064e3b;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .cover-parent {
            font-size: 16pt;
            color: #6b7280;
            margin-bottom: 30px;
        }

        .cover-subtitle {
            font-size: 12pt;
            color: #9ca3af;
            margin-top: 60px;
        }

        .cover-line {
            width: 200px;
            height: 3px;
            background: #059669;
            margin: 30px auto;
        }

        .cover-stats {
            font-size: 14pt;
            color: #374151;
            margin-top: 20px;
        }

        /* ========== TOC Styles ========== */
        .toc-title {
            font-size: 22pt;
            font-weight: bold;
            color: #064e3b;
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #059669;
        }

        .toc-item {
            padding: 8px 15px;
            margin: 4px 0;
            border-bottom: 1px dotted #d1d5db;
            font-size: 13pt;
        }

        .toc-item a {
            color: #1f2937;
            text-decoration: none;
        }

        .toc-item a:hover {
            color: #059669;
        }

        .toc-number {
            display: inline-block;
            width: 30px;
            height: 30px;
            background: #ecfdf5;
            color: #059669;
            text-align: center;
            line-height: 30px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 11pt;
            margin-left: 10px;
        }

        .toc-count {
            float: left;
            color: #9ca3af;
            font-size: 10pt;
        }

        /* ========== Chapter Header ========== */
        .chapter-header {
            background: #f0fdf4;
            border: 2px solid #a7f3d0;
            border-radius: 10px;
            padding: 15px 25px;
            margin-bottom: 25px;
            margin-top: 10px;
        }

        .chapter-title {
            font-size: 18pt;
            font-weight: bold;
            color: #064e3b;
            margin: 0;
        }

        .chapter-count {
            font-size: 11pt;
            color: #6b7280;
            margin-top: 5px;
        }

        /* ========== Hadith Card ========== */
        .hadith {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 15px;
            page-break-inside: avoid;
            background: #fafafa;
        }

        .hadith-header {
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f3f4f6;
        }

        .hadith-number {
            display: inline-block;
            background: #ecfdf5;
            color: #059669;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 10pt;
            font-weight: bold;
        }

        .hadith-grade {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 10pt;
            font-weight: bold;
            margin-right: 5px;
        }

        .grade-sahih {
            background: #dcfce7;
            color: #166534;
        }

        .grade-hasan {
            background: #dbeafe;
            color: #1e40af;
        }

        .grade-daif {
            background: #fef9c3;
            color: #854d0e;
        }

        .hadith-narrator {
            display: inline-block;
            color: #6b7280;
            font-size: 10pt;
            margin-right: 5px;
        }

        .hadith-text {
            font-size: 14pt;
            line-height: 2.2;
            color: #1f2937;
            text-align: justify;
        }

        .hadith-sources {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #f3f4f6;
            font-size: 10pt;
            color: #6b7280;
        }

        .source-badge {
            display: inline-block;
            background: #f3f4f6;
            padding: 1px 8px;
            border-radius: 8px;
            font-size: 9pt;
            margin-left: 3px;
        }

        /* ========== Footer ========== */
        .pdf-footer {
            text-align: center;
            font-size: 9pt;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
        }

        /* ========== Utilities ========== */
        .page-break {
            page-break-before: always;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    
    <htmlpagefooter name="footer">
        <div class="pdf-footer">
            موسوعة الحديث الصحيح — <?php echo e($book->name); ?>

            &nbsp;|&nbsp;
            <?php echo e($useOriginal ? '📜 النص الأصلي' : '📝 النص المستخرج'); ?>

            &nbsp;|&nbsp;
            صفحة {PAGENO} من {nbpg}
        </div>
    </htmlpagefooter>

    
    <div class="cover">
        <div class="cover-ornament">﷽</div>
        <div class="cover-line"></div>

        <?php if($parentBook): ?>
            <div class="cover-parent"><?php echo e($parentBook->name); ?></div>
        <?php endif; ?>

        <div class="cover-title"><?php echo e($book->name); ?></div>
        <?php if($useOriginal): ?>
            <div style="font-size: 13pt; color: #b45309; margin-top: 10px; font-weight: bold;">📜 النص كما ورد في المصدر
                (الأصل)</div>
        <?php else: ?>
            <div style="font-size: 13pt; color: #059669; margin-top: 10px; font-weight: bold;">📝 النص المستخرج</div>
        <?php endif; ?>
        <div class="cover-line"></div>

        <div class="cover-stats">
            <?php if($chapters && $chapters->count() > 0): ?>
                <?php echo e($chapters->count()); ?> باب — <?php echo e($totalHadiths); ?> حديث
            <?php else: ?>
                <?php echo e($totalHadiths); ?> حديث
            <?php endif; ?>
        </div>

        <div class="cover-subtitle">
            موسوعة الحديث الصحيح<br>
            تم التصدير: <?php echo e(now()->format('Y/m/d')); ?>

        </div>
    </div>

    
    <?php if($chapters && $chapters->count() > 0): ?>
        <div class="page-break"></div>
        <a name="toc"></a>
        <div class="toc-title">📑 فهرس الأبواب</div>

        <?php $__currentLoopData = $chapters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $chapter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="toc-item">
                <span class="toc-number"><?php echo e($index + 1); ?></span>
                <a href="#chapter-<?php echo e($chapter->id); ?>"><?php echo e($chapter->name); ?></a>
                <span class="toc-count"><?php echo e($chapter->hadiths_count); ?> حديث</span>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    
    <?php if($chapters && $chapters->count() > 0): ?>
        
        <?php $__currentLoopData = $chapters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chIndex => $chapter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="page-break"></div>

            
            <bookmark content="<?php echo e($chapter->name); ?>" level="0" />
            <a name="chapter-<?php echo e($chapter->id); ?>"></a>
            <div class="chapter-header">
                <div class="chapter-title">
                    <?php echo e($chIndex + 1); ?>. <?php echo e($chapter->name); ?>

                </div>
                <div class="chapter-count">
                    <?php echo e($chapter->hadiths_count); ?> حديث
                </div>
            </div>

            
            <?php $__currentLoopData = $chapter->hadiths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hadith): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="hadith">
                    <div class="hadith-header">
                        <span class="hadith-number"># <?php echo e($hadith->number_in_book); ?></span>
                        <?php
                            $gradeClass = match ($hadith->grade) {
                                'صحيح' => 'grade-sahih',
                                'حسن' => 'grade-hasan',
                                default => 'grade-daif',
                            };
                        ?>
                        <span class="hadith-grade <?php echo e($gradeClass); ?>"><?php echo e($hadith->grade); ?></span>
                        <?php if($hadith->narrator): ?>
                            <span class="hadith-narrator">🧑 <?php echo e($hadith->narrator->name); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="hadith-text"><?php echo e($useOriginal ? ($hadith->raw_text ?: $hadith->content) : $hadith->content); ?></div>
                    <?php if($useOriginal && $hadith->raw_text): ?>
                        <div style="font-size: 8pt; color: #b45309; margin-top: 3px;">📜 النص الأصلي كما ورد في المصدر</div>
                    <?php endif; ?>
                    <?php if($hadith->sources->count() > 0): ?>
                        <div class="hadith-sources">
                            📚 التخريج:
                            <?php $__currentLoopData = $hadith->sources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $source): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="source-badge"><?php echo e($source->name); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <div style="text-align: center; margin-top: 15px; font-size: 10pt;">
                <a href="#toc" style="color: #059669; text-decoration: none;">🔼 العودة للفهرس</a>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
        
        <div class="page-break"></div>
        <bookmark content="<?php echo e($book->name); ?>" level="0" />
        <div class="chapter-header">
            <div class="chapter-title"><?php echo e($book->name); ?></div>
            <div class="chapter-count"><?php echo e($totalHadiths); ?> حديث</div>
        </div>

        <?php $__currentLoopData = $hadiths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hadith): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="hadith">
                <div class="hadith-header">
                    <span class="hadith-number"># <?php echo e($hadith->number_in_book); ?></span>
                    <?php
                        $gradeClass = match ($hadith->grade) {
                            'صحيح' => 'grade-sahih',
                            'حسن' => 'grade-hasan',
                            default => 'grade-daif',
                        };
                    ?>
                    <span class="hadith-grade <?php echo e($gradeClass); ?>"><?php echo e($hadith->grade); ?></span>
                    <?php if($hadith->narrator): ?>
                        <span class="hadith-narrator">🧑 <?php echo e($hadith->narrator->name); ?></span>
                    <?php endif; ?>
                </div>
                <div class="hadith-text"><?php echo e($useOriginal ? ($hadith->raw_text ?: $hadith->content) : $hadith->content); ?></div>
                <?php if($useOriginal && $hadith->raw_text): ?>
                    <div style="font-size: 8pt; color: #b45309; margin-top: 3px;">📜 النص الأصلي كما ورد في المصدر</div>
                <?php endif; ?>
                <?php if($hadith->sources->count() > 0): ?>
                    <div class="hadith-sources">
                        📚 التخريج:
                        <?php $__currentLoopData = $hadith->sources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $source): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="source-badge"><?php echo e($source->name); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

</body>

</html><?php /**PATH C:\laragon\www\hadeeth\resources\views/frontend/books/pdf.blade.php ENDPATH**/ ?>