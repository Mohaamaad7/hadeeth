<?php $__env->startSection('title', 'عرض الحديث'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="row">
        <div class="col-sm-6">
            <h1>تفاصيل الحديث #<?php echo e($hadith->number_in_book); ?></h1>
        </div>
        <div class="col-sm-6">
            <div class="float-left">
                <a href="<?php echo e(route('dashboard.hadiths.edit', $hadith)); ?>" class="btn btn-warning">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <a href="<?php echo e(route('dashboard.hadiths.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i> رجوع
                </a>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <!-- نص الحديث -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-book-open"></i> نص الحديث
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-<?php echo e($hadith->grade === 'صحيح' ? 'success' : 
                            ($hadith->grade === 'حسن' ? 'info' : 
                            ($hadith->grade === 'ضعيف' ? 'warning' : 'danger'))); ?>">
                            <?php echo e($hadith->grade); ?>

                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="lead" style="font-size: 1.1rem; line-height: 2;">
                        <?php echo e($hadith->content); ?>

                    </p>
                </div>
            </div>

            <?php if($hadith->explanation): ?>
            <!-- الشرح -->
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-lightbulb"></i> الشرح والتفسير
                    </h3>
                </div>
                <div class="card-body">
                    <p style="line-height: 1.8;"><?php echo e($hadith->explanation); ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <!-- البيانات الأساسية -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> البيانات الأساسية
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <tr>
                            <th style="width: 40%">رقم الحديث:</th>
                            <td><strong>#<?php echo e($hadith->number_in_book); ?></strong></td>
                        </tr>
                        <tr>
                            <th>الدرجة:</th>
                            <td>
                                <span class="badge badge-<?php echo e($hadith->grade === 'صحيح' ? 'success' : 
                                    ($hadith->grade === 'حسن' ? 'info' : 
                                    ($hadith->grade === 'ضعيف' ? 'warning' : 'danger'))); ?>">
                                    <?php echo e($hadith->grade); ?>

                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>الكتاب:</th>
                            <td><?php echo e($hadith->book?->name ?? 'غير محدد'); ?></td>
                        </tr>
                        <tr>
                            <th>الراوي:</th>
                            <td>
                                <?php if($hadith->narrator): ?>
                                    <span class="badge badge-secondary">
                                        <?php echo e($hadith->narrator->name); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">غير محدد</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>تاريخ الإضافة:</th>
                            <td>
                                <small><?php echo e($hadith->created_at->format('Y-m-d H:i')); ?></small>
                            </td>
                        </tr>
                        <tr>
                            <th>آخر تحديث:</th>
                            <td>
                                <small><?php echo e($hadith->updated_at->format('Y-m-d H:i')); ?></small>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- المصادر -->
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-database"></i> المصادر
                        <span class="badge badge-light"><?php echo e($hadith->sources->count()); ?></span>
                    </h3>
                </div>
                <div class="card-body">
                    <?php if($hadith->sources->count() > 0): ?>
                        <ul class="list-unstyled">
                            <?php $__currentLoopData = $hadith->sources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $source): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success"></i>
                                    <strong><?php echo e($source->name); ?></strong>
                                    <span class="badge badge-secondary"><?php echo e($source->code); ?></span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted text-center mb-0">
                            <i class="fas fa-info-circle"></i><br>
                            لا توجد مصادر مرتبطة
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- الإجراءات -->
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs"></i> الإجراءات
                    </h3>
                </div>
                <div class="card-body">
                    <a href="<?php echo e(route('dashboard.hadiths.edit', $hadith)); ?>" 
                       class="btn btn-warning btn-block">
                        <i class="fas fa-edit"></i> تعديل الحديث
                    </a>
                    <button type="button" 
                            class="btn btn-danger btn-block" 
                            onclick="confirmDelete()">
                        <i class="fas fa-trash"></i> حذف الحديث
                    </button>
                    <a href="<?php echo e(route('dashboard.hadiths.index')); ?>" 
                       class="btn btn-secondary btn-block">
                        <i class="fas fa-list"></i> العودة للقائمة
                    </a>
                </div>
            </div>

            <form id="delete-form" 
                  action="<?php echo e(route('dashboard.hadiths.destroy', $hadith)); ?>" 
                  method="POST" 
                  style="display: none;">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
function confirmDelete() {
    if (confirm('هل أنت متأكد من حذف هذا الحديث؟\n\nلا يمكن التراجع عن هذا الإجراء!')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\hadeeth\resources\views/dashboard/hadiths/show.blade.php ENDPATH**/ ?>