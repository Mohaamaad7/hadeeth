<?php $__env->startSection('title', 'المصادر'); ?>

<?php $__env->startSection('content_header'); ?>
<div class="row">
    <div class="col-sm-6">
        <h1>المصادر</h1>
    </div>
    <div class="col-sm-6">
        <div class="float-left">
            <a href="<?php echo e(route('dashboard.sources.create')); ?>" class="btn btn-success">
                <i class="fas fa-plus"></i> إضافة مصدر جديد
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
        <form method="GET" action="<?php echo e(route('dashboard.sources.index')); ?>">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <input type="text" name="search" class="form-control"
                            placeholder="ابحث في الاسم، الرمز، أو النوع..." value="<?php echo e(request('search')); ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <select name="type" class="form-control">
                            <option value="">-- جميع الأنواع --</option>
                            <option value="كتب الصحيح" <?php echo e(request('type') === 'كتب الصحيح' ? 'selected' : ''); ?>>كتب الصحيح
                            </option>
                            <option value="كتب السنن" <?php echo e(request('type') === 'كتب السنن' ? 'selected' : ''); ?>>كتب السنن
                            </option>
                            <option value="كتب المسانيد" <?php echo e(request('type') === 'كتب المسانيد' ? 'selected' : ''); ?>>كتب
                                المسانيد</option>
                            <option value="كتب المعاجم" <?php echo e(request('type') === 'كتب المعاجم' ? 'selected' : ''); ?>>كتب
                                المعاجم</option>
                            <option value="كتب التاريخ" <?php echo e(request('type') === 'كتب التاريخ' ? 'selected' : ''); ?>>كتب
                                التاريخ</option>
                            <option value="كتب الأجزاء" <?php echo e(request('type') === 'كتب الأجزاء' ? 'selected' : ''); ?>>كتب
                                الأجزاء</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> بحث
                    </button>
                    <a href="<?php echo e(route('dashboard.sources.index')); ?>" class="btn btn-secondary">
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
            <i class="fas fa-database"></i> قائمة المصادر
            <span class="badge badge-info"><?php echo e($sources->total()); ?></span>
        </h3>
    </div>
    <div class="card-body p-0">
        <?php if($sources->count() > 0): ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th style="width: 80px">الرمز</th>
                        <th>اسم المصدر</th>
                        <th style="width: 200px">النوع</th>
                        <th style="width: 100px" class="text-center">الأحاديث</th>
                        <th style="width: 150px" class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $sources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $source): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <span class="badge badge-primary badge-lg"><?php echo e($source->code); ?></span>
                            </td>
                            <td>
                                <strong><?php echo e($source->name); ?></strong>
                                <?php if($source->author): ?>
                                    <div class="text-muted small">المؤلف: <?php echo e($source->author); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($source->type): ?>
                                    <span class="badge badge-info"><?php echo e($source->type); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-success">
                                    <?php echo e($source->hadiths_count); ?>

                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="<?php echo e(route('dashboard.sources.show', $source)); ?>" class="btn btn-sm btn-info"
                                        title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('dashboard.sources.edit', $source)); ?>" class="btn btn-sm btn-warning"
                                        title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="confirmDelete(<?php echo e($source->id); ?>)" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-<?php echo e($source->id); ?>"
                                    action="<?php echo e(route('dashboard.sources.destroy', $source)); ?>" method="POST"
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
                <i class="fas fa-database fa-3x mb-3"></i>
                <p>لا توجد مصادر بعد</p>
                <a href="<?php echo e(route('dashboard.sources.create')); ?>" class="btn btn-success">
                    <i class="fas fa-plus"></i> إضافة أول مصدر
                </a>
            </div>
        <?php endif; ?>
    </div>
    <?php if($sources->hasPages()): ?>
        <div class="card-footer clearfix">
            <div class="float-left">
                <small class="text-muted">
                    عرض <?php echo e($sources->firstItem()); ?> - <?php echo e($sources->lastItem()); ?> من <?php echo e($sources->total()); ?> نتيجة
                </small>
            </div>
            <div class="float-right">
                <?php echo e($sources->links('pagination::bootstrap-5')); ?>

            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
    function confirmDelete(id) {
        if (confirm('هل أنت متأكد من حذف هذا المصدر؟')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\hadeeth\resources\views/dashboard/sources/index.blade.php ENDPATH**/ ?>