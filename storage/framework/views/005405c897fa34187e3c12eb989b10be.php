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
                <label>اسم الشهرة</label>
                <input type="text" name="fame_name" class="form-control"
                    value="<?php echo e(old('fame_name', $narrator->fame_name)); ?>" placeholder="مثال: عائشة، جابر، ابن عمر">
                <small class="form-text text-muted">
                    الاسم المختصر المشهور به في كتب الحديث
                </small>
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
    </div>

    
    <div class="card card-outline card-info">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-exchange-alt"></i> الأسماء البديلة
            </h3>
            <small class="text-muted mr-2">أخطاء نساخ، تهجئات مختلفة، ألقاب — تساعد في البحث الذكي</small>
        </div>
        <div class="card-body" id="alternativesContainer">
            <?php $__empty_1 = true; $__currentLoopData = $narrator->alternatives; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $alt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="row alternative-row mb-2 align-items-center">
                    <div class="col-md-4">
                        <input type="text" name="alternatives[<?php echo e($i); ?>][alternative_name]"
                            class="form-control form-control-sm"
                            value="<?php echo e(old("alternatives.{$i}.alternative_name", $alt->alternative_name)); ?>"
                            placeholder="الاسم البديل">
                    </div>
                    <div class="col-md-3">
                        <select name="alternatives[<?php echo e($i); ?>][type]" class="form-control form-control-sm">
                            <option value="misspelling" <?php echo e($alt->type?->value === 'misspelling' ? 'selected' : ''); ?>>خطأ نساخ
                            </option>
                            <option value="variation" <?php echo e($alt->type?->value === 'variation' ? 'selected' : ''); ?>>تهجئة بديلة
                            </option>
                            <option value="title" <?php echo e($alt->type?->value === 'title' ? 'selected' : ''); ?>>لقب</option>
                            <option value="kunya" <?php echo e($alt->type?->value === 'kunya' ? 'selected' : ''); ?>>كنية</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="alternatives[<?php echo e($i); ?>][notes]" class="form-control form-control-sm"
                            value="<?php echo e(old("alternatives.{$i}.notes", $alt->notes)); ?>" placeholder="ملاحظة (اختياري)">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-sm btn-danger remove-alt" title="حذف">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-muted" id="noAltsMsg">لا توجد أسماء بديلة. اضغط "إضافة" لإضافة اسم.</p>
            <?php endif; ?>
        </div>
        <div class="card-footer">
            <button type="button" class="btn btn-sm btn-info" id="addAltBtn">
                <i class="fas fa-plus"></i> إضافة اسم بديل
            </button>
        </div>
    </div>

    <div class="mb-3">
        <button type="submit" class="btn btn-warning">
            <i class="fas fa-save"></i> حفظ التعديلات
        </button>
        <a href="<?php echo e(route('dashboard.narrators.show', $narrator)); ?>" class="btn btn-secondary">
            <i class="fas fa-times"></i> إلغاء
        </a>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
    let altIndex = <?php echo e($narrator->alternatives->count()); ?>;

    $('#addAltBtn').click(function () {
        $('#noAltsMsg').hide();
        const row = `
    <div class="row alternative-row mb-2 align-items-center">
        <div class="col-md-4">
            <input type="text" name="alternatives[${altIndex}][alternative_name]" class="form-control form-control-sm" placeholder="الاسم البديل" required>
        </div>
        <div class="col-md-3">
            <select name="alternatives[${altIndex}][type]" class="form-control form-control-sm">
                <option value="misspelling">خطأ نساخ</option>
                <option value="variation">تهجئة بديلة</option>
                <option value="title">لقب</option>
                <option value="kunya">كنية</option>
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" name="alternatives[${altIndex}][notes]" class="form-control form-control-sm" placeholder="ملاحظة (اختياري)">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-sm btn-danger remove-alt" title="حذف">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>`;
        $('#alternativesContainer').append(row);
        altIndex++;
    });

    $(document).on('click', '.remove-alt', function () {
        $(this).closest('.alternative-row').remove();
        if ($('.alternative-row').length === 0) {
            $('#noAltsMsg').show();
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\hadeeth\resources\views/dashboard/narrators/edit.blade.php ENDPATH**/ ?>