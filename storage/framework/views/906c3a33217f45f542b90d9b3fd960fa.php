<?php $__env->startSection('title', 'الرواة'); ?>

<?php $__env->startSection('content_header'); ?>
<div class="row">
    <div class="col-sm-6">
        <h1>الرواة</h1>
    </div>
    <div class="col-sm-6">
        <div class="float-left">
            <a href="<?php echo e(route('dashboard.narrators.create')); ?>" class="btn btn-success">
                <i class="fas fa-plus"></i> إضافة راوي جديد
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

<?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?php echo e(session('error')); ?>

    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-search"></i> البحث والتصفية
        </h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('dashboard.narrators.index')); ?>">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <input type="text" name="search" class="form-control"
                            placeholder="ابحث في الاسم، السيرة، أو الدرجة..." value="<?php echo e(request('search')); ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <select name="grade_status" class="form-control">
                            <option value="">-- جميع الدرجات --</option>
                            <option value="صحابي" <?php echo e(request('grade_status') === 'صحابي' ? 'selected' : ''); ?>>صحابي
                            </option>
                            <option value="ثقة" <?php echo e(request('grade_status') === 'ثقة' ? 'selected' : ''); ?>>ثقة</option>
                            <option value="صدوق" <?php echo e(request('grade_status') === 'صدوق' ? 'selected' : ''); ?>>صدوق</option>
                            <option value="ضعيف" <?php echo e(request('grade_status') === 'ضعيف' ? 'selected' : ''); ?>>ضعيف</option>
                            <option value="متروك" <?php echo e(request('grade_status') === 'متروك' ? 'selected' : ''); ?>>متروك
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> بحث
                    </button>
                    <a href="<?php echo e(route('dashboard.narrators.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> إعادة تعيين
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-users"></i> قائمة الرواة
            <span class="badge badge-info"><?php echo e($narrators->total()); ?></span>
        </h3>
    </div>
    <div class="card-body p-0">
        <?php if($narrators->count() > 0): ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px">#</th>
                        <th>الاسم</th>
                        <th style="width: 150px">الدرجة</th>
                        <th style="width: 100px" class="text-center">الأحاديث</th>
                        <th style="width: 150px" class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $narrators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $narrator): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($narrator->id); ?></td>
                            <td>
                                <strong><?php echo e($narrator->name); ?></strong>
                                <?php if($narrator->bio): ?>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo e(Str::limit($narrator->bio, 60)); ?>

                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($narrator->grade_status): ?>
                                    <span class="badge" style="background-color: <?php echo e($narrator->color_code); ?>; color: white;">
                                        <?php echo e($narrator->grade_status); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-primary">
                                    <?php echo e($narrator->hadiths_count); ?>

                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="<?php echo e(route('dashboard.narrators.show', $narrator)); ?>" class="btn btn-sm btn-info"
                                        title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('dashboard.narrators.edit', $narrator)); ?>" class="btn btn-sm btn-warning"
                                        title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="confirmDelete(<?php echo e($narrator->id); ?>)" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-<?php echo e($narrator->id); ?>"
                                    action="<?php echo e(route('dashboard.narrators.destroy', $narrator)); ?>" method="POST"
                                    style="display: none;">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="p-5 text-center text-muted">
                <i class="fas fa-users fa-3x mb-3"></i>
                <p>لا توجد رواة بعد</p>
                <a href="<?php echo e(route('dashboard.narrators.create')); ?>" class="btn btn-success">
                    <i class="fas fa-plus"></i> إضافة أول راوي
                </a>
            </div>
        <?php endif; ?>
    </div>
    <?php if($narrators->hasPages()): ?>
        <div class="card-footer clearfix">
            <div class="float-left">
                <small class="text-muted">
                    عرض <?php echo e($narrators->firstItem()); ?> - <?php echo e($narrators->lastItem()); ?> من <?php echo e($narrators->total()); ?> نتيجة
                </small>
            </div>
            <div class="float-right">
                <?php echo e($narrators->links('pagination::bootstrap-5')); ?>

            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
    function confirmDelete(id) {
        if (confirm('هل أنت متأكد من حذف هذا الراوي؟')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\hadeeth\resources\views/dashboard/narrators/index.blade.php ENDPATH**/ ?>