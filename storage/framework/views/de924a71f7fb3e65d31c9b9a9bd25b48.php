<?php $__env->startSection('title', $source->name); ?>

<?php $__env->startSection('content_header'); ?>
<div class="row">
    <div class="col-sm-6">
        <h1><?php echo e($source->name); ?></h1>
    </div>
    <div class="col-sm-6">
        <div class="float-left">
            <a href="<?php echo e(route('dashboard.sources.edit', $source)); ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <a href="<?php echo e(route('dashboard.sources.index')); ?>" class="btn btn-secondary">
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
    <div class="col-md-4">
        <!-- بطاقة معلومات المصدر -->
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <div class="rounded-circle bg-primary d-inline-flex justify-content-center align-items-center"
                        style="width: 100px; height: 100px; font-size: 32px; color: white; font-weight: bold;">
                        <?php echo e($source->code); ?>

                    </div>
                </div>

                <h3 class="profile-username text-center mt-3"><?php echo e($source->name); ?></h3>

                <?php if($source->type): ?>
                    <p class="text-center">
                        <span class="badge badge-info badge-lg" style="font-size: 14px; padding: 8px 12px;">
                            <?php echo e($source->type); ?>

                        </span>
                    </p>
                <?php endif; ?>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>الرمز</b>
                        <a class="float-right">
                            <span class="badge badge-primary"><?php echo e($source->code); ?></span>
                        </a>
                    </li>
                    <li class="list-group-item">
                        <b>عدد الأحاديث</b>
                        <a class="float-right">
                            <span class="badge badge-success"><?php echo e($source->hadiths_count); ?></span>
                        </a>
                    </li>
                    <li class="list-group-item">
                        <b>تاريخ الإضافة</b>
                        <a class="float-right text-muted">
                            <?php echo e($source->created_at->diffForHumans()); ?>

                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- بطاقة الإجراءات -->
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cogs"></i> الإجراءات
                </h3>
            </div>
            <div class="card-body">
                <a href="<?php echo e(route('dashboard.sources.edit', $source)); ?>" class="btn btn-warning btn-block">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <button type="button" class="btn btn-danger btn-block" onclick="confirmDelete()">
                    <i class="fas fa-trash"></i> حذف
                </button>
                <a href="<?php echo e(route('dashboard.sources.index')); ?>" class="btn btn-secondary btn-block">
                    <i class="fas fa-list"></i> العودة للقائمة
                </a>
            </div>
        </div>

        <form id="delete-form" action="<?php echo e(route('dashboard.sources.destroy', $source)); ?>" method="POST"
            style="display: none;">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
        </form>
    </div>

    <div class="col-md-8">
        <!-- الأحاديث المرتبطة -->
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-book-open"></i> الأحاديث في هذا المصدر
                    <span class="badge badge-light"><?php echo e($source->hadiths_count); ?></span>
                </h3>
                <div class="card-tools">
                    <a href="<?php echo e(route('dashboard.hadiths.create')); ?>" class="btn btn-sm btn-success">
                        <i class="fas fa-plus"></i> إضافة حديث
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if($source->hadiths->count() > 0): ?>
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th style="width: 60px">#</th>
                                <th>النص</th>
                                <th style="width: 120px">الراوي</th>
                                <th style="width: 100px">الدرجة</th>
                                <th style="width: 100px">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $source->hadiths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hadith): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($hadith->number_in_book); ?></td>
                                                <td><?php echo e(Str::limit($hadith->content, 80)); ?></td>
                                                <td>
                                                    <?php if($hadith->narrators->count() > 0): ?>
                                                        <small
                                                            class="text-muted"><?php echo e($hadith->narrators->pluck('name')->map(fn($n) => Str::limit($n, 20))->join('، ')); ?></small>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?php echo e($hadith->grade === 'صحيح' ? 'success' :
                                ($hadith->grade === 'حسن' ? 'info' : 'warning')); ?>">
                                                        <?php echo e($hadith->grade); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="<?php echo e(route('dashboard.hadiths.show', $hadith)); ?>" class="btn btn-xs btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php if($source->hadiths_count > 10): ?>
                        <div class="card-footer text-center">
                            <a href="<?php echo e(route('dashboard.hadiths.index')); ?>?source_id=<?php echo e($source->id); ?>"
                                class="btn btn-sm btn-primary">
                                عرض جميع الأحاديث (<?php echo e($source->hadiths_count); ?>)
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>لا توجد أحاديث في هذا المصدر بعد</p>
                        <a href="<?php echo e(route('dashboard.hadiths.create')); ?>" class="btn btn-success">
                            <i class="fas fa-plus"></i> إضافة أول حديث
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
    function confirmDelete() {
        if (confirm('هل أنت متأكد من حذف هذا المصدر؟')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\hadeeth\resources\views/dashboard/sources/show.blade.php ENDPATH**/ ?>