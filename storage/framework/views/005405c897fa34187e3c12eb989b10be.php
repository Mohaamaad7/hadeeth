<?php $__env->startSection('title', 'تعديل ' . $narrator->name); ?>

<?php $__env->startSection('content_header'); ?>
<h1>تعديل: <?php echo e($narrator->name); ?></h1>
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

<form action="<?php echo e(route('dashboard.narrators.update', $narrator)); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">تعديل البيانات</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label>الاسم <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $narrator->name)); ?>" required>
            </div>

            <div class="form-group">
                <label>السيرة الذاتية</label>
                <textarea name="bio" class="form-control" rows="5"><?php echo e(old('bio', $narrator->bio)); ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>الدرجة</label>
                        <select name="grade_status" class="form-control" id="gradeStatus">
                            <option value="">-- اختر الدرجة --</option>
                            <option value="صحابي" <?php echo e(old('grade_status', $narrator->grade_status) === 'صحابي' ? 'selected' : ''); ?>>صحابي</option>
                            <option value="ثقة" <?php echo e(old('grade_status', $narrator->grade_status) === 'ثقة' ? 'selected' : ''); ?>>ثقة</option>
                            <option value="صدوق" <?php echo e(old('grade_status', $narrator->grade_status) === 'صدوق' ? 'selected' : ''); ?>>صدوق</option>
                            <option value="ضعيف" <?php echo e(old('grade_status', $narrator->grade_status) === 'ضعيف' ? 'selected' : ''); ?>>ضعيف</option>
                            <option value="متروك" <?php echo e(old('grade_status', $narrator->grade_status) === 'متروك' ? 'selected' : ''); ?>>متروك</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>لون الدرجة</label>
                        <input type="color" name="color_code" class="form-control"
                            value="<?php echo e(old('color_code', $narrator->color_code)); ?>" style="height: 38px;">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-save"></i> حفظ التعديلات
            </button>
            <a href="<?php echo e(route('dashboard.narrators.show', $narrator)); ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> إلغاء
            </a>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\hadeeth\resources\views/dashboard/narrators/edit.blade.php ENDPATH**/ ?>