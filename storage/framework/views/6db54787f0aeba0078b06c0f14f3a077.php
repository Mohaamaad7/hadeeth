<?php $__env->startSection('title', 'إدارة الأحاديث'); ?>

<?php $__env->startSection('content_header'); ?>
<div class="row">
    <div class="col-sm-6">
        <h1>إدارة الأحاديث</h1>
    </div>
    <div class="col-sm-6">
        <div class="float-left">
            <a href="<?php echo e(route('dashboard.hadiths.bulk.create')); ?>" class="btn btn-primary ml-2">
                <i class="fas fa-layer-group"></i> إدخال جماعي
            </a>
            <a href="<?php echo e(route('dashboard.hadiths.create')); ?>" class="btn btn-success">
                <i class="fas fa-plus"></i> إضافة حديث جديد
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>

<!-- فلاتر البحث -->
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">بحث وفلترة</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('dashboard.hadiths.index')); ?>">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>بحث في النص</label>
                        <input type="text" name="search" class="form-control" value="<?php echo e(request('search')); ?>"
                            placeholder="ابحث في نص الحديث أو الرقم">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>الكتاب</label>
                        <select name="book_id" class="form-control">
                            <option value="">جميع الكتب</option>
                            <?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($book->id); ?>" <?php echo e(request('book_id') == $book->id ? 'selected' : ''); ?>>
                                    <?php echo e($book->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>الدرجة</label>
                        <select name="grade" class="form-control">
                            <option value="">جميع الدرجات</option>
                            <?php $__currentLoopData = $grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($grade); ?>" <?php echo e(request('grade') == $grade ? 'selected' : ''); ?>>
                                    <?php echo e($grade); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i> بحث
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- جدول الأحاديث -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list"></i> قائمة الأحاديث
            <span class="badge badge-info"><?php echo e($hadiths->total()); ?></span>
        </h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th style="width: 80px">#</th>
                    <th>نص الحديث</th>
                    <th style="width: 150px">الراوي</th>
                    <th style="width: 150px">الكتاب</th>
                    <th style="width: 100px">الدرجة</th>
                    <th style="width: 120px">المصادر</th>
                    <th style="width: 150px">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $hadiths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hadith): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <strong><?php echo e($hadith->number_in_book); ?></strong>
                                </td>
                                <td>
                                    <div style="max-height: 60px; overflow: hidden;">
                                        <?php echo e(Str::limit($hadith->content, 120)); ?>

                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">
                                        <?php echo e($hadith->narrators->pluck('name')->join('، ') ?: 'غير محدد'); ?>

                                    </span>
                                </td>
                                <td>
                                    <small><?php echo e($hadith->book?->name ?? 'غير محدد'); ?></small>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo e($hadith->grade === 'صحيح' ? 'success' :
                    ($hadith->grade === 'حسن' ? 'info' :
                        ($hadith->grade === 'ضعيف' ? 'warning' : 'danger'))); ?>">
                                        <?php echo e($hadith->grade); ?>

                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-light">
                                        <?php echo e($hadith->sources->count()); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo e(route('dashboard.hadiths.show', $hadith)); ?>" class="btn btn-info" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('dashboard.hadiths.edit', $hadith)); ?>" class="btn btn-warning"
                                            title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger" onclick="confirmDelete(<?php echo e($hadith->id); ?>)"
                                            title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <form id="delete-form-<?php echo e($hadith->id); ?>"
                                        action="<?php echo e(route('dashboard.hadiths.destroy', $hadith)); ?>" method="POST"
                                        style="display: none;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                    </form>
                                </td>
                            </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">لا توجد أحاديث مضافة بعد</p>
                            <a href="<?php echo e(route('dashboard.hadiths.create')); ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> إضافة أول حديث
                            </a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($hadiths->hasPages()): ?>
        <div class="card-footer clearfix">
            <div class="float-left">
                <small class="text-muted">
                    عرض <?php echo e($hadiths->firstItem()); ?> - <?php echo e($hadiths->lastItem()); ?> من <?php echo e($hadiths->total()); ?> نتيجة
                </small>
            </div>
            <div class="float-right">
                <?php echo e($hadiths->links('pagination::bootstrap-5')); ?>

            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
    function confirmDelete(id) {
        if (confirm('هل أنت متأكد من حذف هذا الحديث؟ لا يمكن التراجع عن هذا الإجراء.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\hadeeth\resources\views/dashboard/hadiths/index.blade.php ENDPATH**/ ?>