<?php $__env->startSection('title', 'تعديل ' . $source->name); ?>

<?php $__env->startSection('content_header'); ?>
<h1>تعديل: <?php echo e($source->name); ?></h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php if($errors->any()): ?>
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?php echo e(route('dashboard.sources.update', $source)); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">تعديل البيانات</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>الرمز</label>
                        <input type="text" name="code" class="form-control" value="<?php echo e(old('code', $source->code)); ?>"
                            maxlength="10">
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="form-group">
                        <label>اسم المصدر <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $source->name)); ?>"
                            required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>المؤلف</label>
                        <input type="text" name="author" class="form-control"
                            value="<?php echo e(old('author', $source->author)); ?>" placeholder="مثال: ابن أبي الدنيا">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>نوع المصدر</label>
                <select name="type" class="form-control">
                    <option value="">-- اختر النوع --</option>
                    <option value="كتب الصحيح" <?php echo e(old('type', $source->type) === 'كتب الصحيح' ? 'selected' : ''); ?>>كتب
                        الصحيح</option>
                    <option value="كتب السنن" <?php echo e(old('type', $source->type) === 'كتب السنن' ? 'selected' : ''); ?>>كتب السنن
                    </option>
                    <option value="كتب المسانيد" <?php echo e(old('type', $source->type) === 'كتب المسانيد' ? 'selected' : ''); ?>>كتب
                        المسانيد</option>
                    <option value="كتب المعاجم" <?php echo e(old('type', $source->type) === 'كتب المعاجم' ? 'selected' : ''); ?>>كتب
                        المعاجم</option>
                    <option value="كتب التاريخ" <?php echo e(old('type', $source->type) === 'كتب التاريخ' ? 'selected' : ''); ?>>كتب
                        التاريخ</option>
                    <option value="كتب الأجزاء" <?php echo e(old('type', $source->type) === 'كتب الأجزاء' ? 'selected' : ''); ?>>كتب
                        الأجزاء</option>
                </select>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-save"></i> حفظ التعديلات
            </button>
            <a href="<?php echo e(route('dashboard.sources.show', $source)); ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> إلغاء
            </a>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\hadeeth\resources\views/dashboard/sources/edit.blade.php ENDPATH**/ ?>