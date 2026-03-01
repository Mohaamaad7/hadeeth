<?php $__env->startSection('title', 'تعديل ' . $book->name); ?>

<?php $__env->startSection('content_header'); ?>
    <h1>تعديل: <?php echo e($book->name); ?></h1>
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

    <form action="<?php echo e(route('dashboard.books.update', $book)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">تعديل البيانات</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>النوع</label>
                    <select class="form-control" id="bookType" onchange="toggleParentField()">
                        <option value="main" <?php echo e(!$book->parent_id ? 'selected' : ''); ?>>كتاب رئيسي</option>
                        <option value="sub" <?php echo e($book->parent_id ? 'selected' : ''); ?>>باب فرعي</option>
                    </select>
                </div>

                <div class="form-group" id="parentField" style="<?php echo e($book->parent_id ? '' : 'display: none;'); ?>">
                    <label>الكتاب الرئيسي</label>
                    <select name="parent_id" class="form-control">
                        <option value="">-- اختر الكتاب الرئيسي --</option>
                        <?php $__currentLoopData = $mainBooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mainBook): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($mainBook->id); ?>" 
                                    <?php echo e(old('parent_id', $book->parent_id) == $mainBook->id ? 'selected' : ''); ?>>
                                <?php echo e($mainBook->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>الاسم <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" 
                           value="<?php echo e(old('name', $book->name)); ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label>رقم الترتيب <span class="text-danger">*</span></label>
                    <input type="number" name="sort_order" class="form-control" 
                           value="<?php echo e(old('sort_order', $book->sort_order)); ?>" 
                           min="0"
                           required>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save"></i> حفظ التعديلات
                </button>
                <a href="<?php echo e(route('dashboard.books.show', $book)); ?>" class="btn btn-secondary">
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
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\hadeeth\resources\views/dashboard/books/edit.blade.php ENDPATH**/ ?>