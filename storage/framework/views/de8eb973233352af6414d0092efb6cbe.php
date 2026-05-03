<?php $__env->startSection('title', 'لوحة التحكم - صحيح الجامع'); ?>

<?php $__env->startSection('content_header'); ?>
<h1>لوحة التحكم</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <!-- إحصائيات سريعة -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?php echo e(number_format($stats['total_hadiths'])); ?></h3>
                <p>إجمالي الأحاديث</p>
            </div>
            <div class="icon">
                <i class="fas fa-book"></i>
            </div>
            <a href="<?php echo e(url('dashboard/hadiths')); ?>" class="small-box-footer">
                المزيد <i class="fas fa-arrow-circle-left"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?php echo e(number_format($stats['total_books'])); ?></h3>
                <p>عدد الكتب</p>
            </div>
            <div class="icon">
                <i class="fas fa-book-open"></i>
            </div>
            <a href="<?php echo e(url('dashboard/books')); ?>" class="small-box-footer">
                المزيد <i class="fas fa-arrow-circle-left"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?php echo e(number_format($stats['total_narrators'])); ?></h3>
                <p>عدد الرواة</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="<?php echo e(url('dashboard/narrators')); ?>" class="small-box-footer">
                المزيد <i class="fas fa-arrow-circle-left"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?php echo e(number_format($stats['total_sources'])); ?></h3>
                <p>عدد المصادر</p>
            </div>
            <div class="icon">
                <i class="fas fa-database"></i>
            </div>
            <a href="<?php echo e(url('dashboard/sources')); ?>" class="small-box-footer">
                المزيد <i class="fas fa-arrow-circle-left"></i>
            </a>
        </div>
    </div>
</div>

<!-- أحدث الأحاديث -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">آخر الأحاديث المضافة</h3>
                <div class="card-tools">
                    <a href="<?php echo e(url('dashboard/hadiths')); ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> عرض الكل
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>نص الحديث</th>
                            <th>الراوي</th>
                            <th>الكتاب</th>
                            <th>الدرجة</th>
                            <th>تاريخ الإضافة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $recent_hadiths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hadith): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($hadith->number_in_book); ?></td>
                                <td><?php echo e(Str::limit($hadith->content, 100)); ?></td>
                                <td><?php echo e($hadith->narrators->pluck('name')->join('، ') ?: 'غير محدد'); ?></td>
                                <td><?php echo e($hadith->book?->name ?? 'غير محدد'); ?></td>
                                <td>
                                    <span
                                        class="badge badge-<?php echo e($hadith->grade === 'صحيح' ? 'success' : ($hadith->grade === 'ضعيف' ? 'danger' : 'warning')); ?>">
                                        <?php echo e($hadith->grade); ?>

                                    </span>
                                </td>
                                <td><?php echo e($hadith->created_at->diffForHumans()); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center">لا توجد أحاديث مضافة بعد</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- معلومات إضافية -->
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">عن المشروع</h3>
            </div>
            <div class="card-body">
                <p><strong>الموسوعة الرقمية لصحيح الجامع</strong></p>
                <p>منصة شاملة لإدارة وعرض أحاديث صحيح الجامع الصغير وزيادته</p>
                <ul>
                    <li>نظام بحث متقدم مع دعم التشكيل</li>
                    <li>إدارة محتوى احترافية</li>
                    <li>واجهة عربية بالكامل مع دعم RTL</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">روابط سريعة</h3>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="<?php echo e(url('dashboard/hadiths/create')); ?>" class="list-group-item list-group-item-action">
                        <i class="fas fa-plus-circle text-success"></i> إضافة حديث جديد
                    </a>
                    <a href="<?php echo e(url('dashboard/books/create')); ?>" class="list-group-item list-group-item-action">
                        <i class="fas fa-plus-circle text-primary"></i> إضافة كتاب جديد
                    </a>
                    <a href="<?php echo e(url('dashboard/narrators/create')); ?>" class="list-group-item list-group-item-action">
                        <i class="fas fa-plus-circle text-warning"></i> إضافة راوي جديد
                    </a>
                    <a href="<?php echo e(url('/')); ?>" class="list-group-item list-group-item-action" target="_blank">
                        <i class="fas fa-external-link-alt text-info"></i> عرض الموقع الرئيسي
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    /* تحسينات إضافية للعربي */
    .card-title,
    .card-header {
        font-weight: bold;
    }

    .small-box h3 {
        font-size: 2.2rem;
        font-weight: bold;
    }

    .list-group-item i {
        margin-left: 10px;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
    console.log('Dashboard loaded successfully!');
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\hadeeth\resources\views/dashboard.blade.php ENDPATH**/ ?>