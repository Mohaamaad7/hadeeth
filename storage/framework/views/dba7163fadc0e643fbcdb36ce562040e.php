<?php $__env->startSection('title', 'إضافة كتاب/باب جديد'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1>إضافة كتاب/باب جديد</h1>
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

    <form action="<?php echo e(route('dashboard.books.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">بيانات الكتاب/الباب</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>النوع <span class="text-danger">*</span></label>
                    <select class="form-control" id="bookType" onchange="toggleParentField()">
                        <option value="main">كتاب رئيسي</option>
                        <option value="sub">باب فرعي</option>
                    </select>
                </div>

                <div class="form-group" id="parentField" style="display: none;">
                    <label>الكتاب الرئيسي <span class="text-danger">*</span></label>
                    <select name="parent_id" class="form-control">
                        <option value="">-- اختر الكتاب الرئيسي --</option>
                        <?php $__currentLoopData = $mainBooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mainBook): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($mainBook->id); ?>" 
                                    <?php echo e(old('parent_id') == $mainBook->id ? 'selected' : ''); ?>>
                                <?php echo e($mainBook->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <small class="form-text text-muted">
                        اختر الكتاب الذي سيتبع له هذا الباب
                    </small>
                </div>

                <div class="form-group">
                    <label>الاسم <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" 
                           value="<?php echo e(old('name')); ?>" 
                           placeholder="مثال: كتاب الإيمان، باب فضل الوضوء"
                           required>
                </div>

                <div class="form-group">
                    <label>رقم الترتيب</label>
                    <input type="number" name="sort_order" class="form-control" 
                           value="<?php echo e(old('sort_order')); ?>" 
                           min="0"
                           placeholder="اتركه فارغاً للترتيب التلقائي">
                    <small class="form-text text-muted">
                        سيتم الترتيب تلقائياً إذا تركت هذا الحقل فارغاً
                    </small>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> حفظ
                </button>
                <a href="<?php echo e(route('dashboard.books.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
function toggleParentField() {
    const bookType = document.getElementById('bookType').value;
    const parentField = document.getElementById('parentField');
    const parentSelect = parentField.querySelector('select');
    
    if (bookType === 'sub') {
        parentField.style.display = 'block';
        parentSelect.required = true;
    } else {
        parentField.style.display = 'none';
        parentSelect.required = false;
        parentSelect.value = '';
    }
}

// Check on page load if editing with parent_id
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('select[name="parent_id"]').value) {
        document.getElementById('bookType').value = 'sub';
        toggleParentField();
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\hadeeth\resources\views/dashboard/books/create.blade.php ENDPATH**/ ?>