<?php $__env->startSection('title', 'إضافة مصدر جديد'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1>إضافة مصدر جديد</h1>
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

    <form action="<?php echo e(route('dashboard.sources.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">بيانات المصدر</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>الرمز <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" 
                                   value="<?php echo e(old('code')); ?>" 
                                   placeholder="مثال: خ، م، د"
                                   maxlength="10"
                                   required>
                            <small class="form-text text-muted">رمز مختصر فريد للمصدر</small>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group">
                            <label>اسم المصدر <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" 
                                   value="<?php echo e(old('name')); ?>" 
                                   placeholder="مثال: صحيح البخاري، سنن أبي داود"
                                   required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>نوع المصدر</label>
                    <select name="type" class="form-control">
                        <option value="">-- اختر النوع --</option>
                        <option value="كتب الصحيح" <?php echo e(old('type') === 'كتب الصحيح' ? 'selected' : ''); ?>>كتب الصحيح</option>
                        <option value="كتب السنن" <?php echo e(old('type') === 'كتب السنن' ? 'selected' : ''); ?>>كتب السنن</option>
                        <option value="كتب المسانيد" <?php echo e(old('type') === 'كتب المسانيد' ? 'selected' : ''); ?>>كتب المسانيد</option>
                        <option value="كتب المعاجم" <?php echo e(old('type') === 'كتب المعاجم' ? 'selected' : ''); ?>>كتب المعاجم</option>
                        <option value="كتب التاريخ" <?php echo e(old('type') === 'كتب التاريخ' ? 'selected' : ''); ?>>كتب التاريخ</option>
                        <option value="كتب الأجزاء" <?php echo e(old('type') === 'كتب الأجزاء' ? 'selected' : ''); ?>>كتب الأجزاء</option>
                    </select>
                    <small class="form-text text-muted">تصنيف المصدر (اختياري)</small>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>ملاحظة:</strong> الرمز يجب أن يكون فريداً ويُستخدم في نظام Parser الأحاديث
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> حفظ
                </button>
                <a href="<?php echo e(route('dashboard.sources.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\hadeeth\resources\views/dashboard/sources/create.blade.php ENDPATH**/ ?>