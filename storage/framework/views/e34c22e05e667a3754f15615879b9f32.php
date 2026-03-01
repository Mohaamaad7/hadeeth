<?php $__env->startSection('title', 'إدارة الكتب والأبواب'); ?>

<?php $__env->startSection('content_header'); ?>
<div class="row">
    <div class="col-sm-6">
        <h1>إدارة الكتب والأبواب</h1>
    </div>
    <div class="col-sm-6">
        <div class="float-left">
            <a href="<?php echo e(route('dashboard.books.create')); ?>" class="btn btn-success">
                <i class="fas fa-plus"></i> إضافة كتاب/باب جديد
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

<?php if($errors->any()): ?>
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <p class="mb-0"><?php echo e($error); ?></p>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endif; ?>

    <!-- فلاتر البحث المتقدمة -->
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter"></i> بحث وفلترة
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('dashboard.books.index')); ?>" id="filterForm">
                <div class="row">
                    <!-- بحث نصي -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-search"></i> بحث في الاسم</label>
                            <input type="text" name="search" class="form-control" 
                                   value="<?php echo e(request('search')); ?>"
                                   placeholder="اكتب اسم الكتاب أو الباب...">
                        </div>
                    </div>
                    
                    <!-- فلتر النوع -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label><i class="fas fa-tag"></i> النوع</label>
                            <select name="type" class="form-control" onchange="document.getElementById('filterForm').submit()">
                                <option value="">الكل</option>
                                <option value="main" <?php echo e(request('type') == 'main' ? 'selected' : ''); ?>>
                                    📚 الكتب الرئيسية
                                </option>
                                <option value="sub" <?php echo e(request('type') == 'sub' ? 'selected' : ''); ?>>
                                    📖 الأبواب الفرعية
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- فلتر الكتاب الرئيسي -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><i class="fas fa-book"></i> الكتاب الرئيسي</label>
                            <select name="parent_id" class="form-control" onchange="document.getElementById('filterForm').submit()">
                                <option value="">جميع الكتب</option>
                                <?php $__currentLoopData = $mainBooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mainBook): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($mainBook->id); ?>" <?php echo e(request('parent_id') == $mainBook->id ? 'selected' : ''); ?>>
                                        <?php echo e($mainBook->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- عدد النتائج -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label><i class="fas fa-list-ol"></i> عرض</label>
                            <select name="per_page" class="form-control" onchange="document.getElementById('filterForm').submit()">
                                <option value="20" <?php echo e($perPage == 20 ? 'selected' : ''); ?>>20 نتيجة</option>
                                <option value="50" <?php echo e($perPage == 50 ? 'selected' : ''); ?>>50 نتيجة</option>
                                <option value="100" <?php echo e($perPage == 100 ? 'selected' : ''); ?>>100 نتيجة</option>
                                <option value="200" <?php echo e($perPage == 200 ? 'selected' : ''); ?>>200 نتيجة</option>
                                <option value="500" <?php echo e($perPage == 500 ? 'selected' : ''); ?>>500 نتيجة</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- أزرار -->
                    <div class="col-md-1">
                        <label>&nbsp;</label>
                        <div class="btn-group-vertical btn-block">
                            <button type="submit" class="btn btn-primary btn-sm" title="بحث">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="<?php echo e(route('dashboard.books.index')); ?>" class="btn btn-secondary btn-sm" title="إعادة تعيين">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- الفلاتر النشطة -->
                <?php if(request()->hasAny(['search', 'type', 'parent_id'])): ?>
                    <div class="mt-2">
                        <small class="text-muted">الفلاتر النشطة:</small>
                        <?php if(request('search')): ?>
                            <span class="badge badge-info">
                                بحث: <?php echo e(request('search')); ?>

                                <a href="<?php echo e(route('dashboard.books.index', array_merge(request()->except('search'), ['page' => 1]))); ?>" class="text-white">×</a>
                            </span>
                        <?php endif; ?>
                        <?php if(request('type')): ?>
                            <span class="badge badge-primary">
                                النوع: <?php echo e(request('type') == 'main' ? 'كتب رئيسية' : 'أبواب فرعية'); ?>

                                <a href="<?php echo e(route('dashboard.books.index', array_merge(request()->except('type'), ['page' => 1]))); ?>" class="text-white">×</a>
                            </span>
                        <?php endif; ?>
                        <?php if(request('parent_id')): ?>
                            <span class="badge badge-success">
                                الكتاب: <?php echo e($mainBooks->find(request('parent_id'))?->name); ?>

                                <a href="<?php echo e(route('dashboard.books.index', array_merge(request()->except('parent_id'), ['page' => 1]))); ?>" class="text-white">×</a>
                            </span>
                        <?php endif; ?>
                        <a href="<?php echo e(route('dashboard.books.index')); ?>" class="btn btn-xs btn-outline-danger">
                            <i class="fas fa-times"></i> مسح الكل
                        </a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

<!-- جدول الكتب -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list"></i> قائمة الكتب والأبواب
            <span class="badge badge-info"><?php echo e($books->total()); ?></span>
        </h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th style="width: 80px">الترتيب</th>
                    <th>الاسم</th>
                    <th style="width: 150px">النوع</th>
                    <th style="width: 150px">الكتاب الرئيسي</th>
                    <th style="width: 100px">الأحاديث</th>
                    <th style="width: 100px">الأبواب</th>
                    <th style="width: 150px">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <span class="badge badge-secondary"><?php echo e($book->sort_order); ?></span>
                        </td>
                        <td>
                            <?php if($book->parent_id): ?>
                                <i class="fas fa-level-up-alt text-muted"></i>
                            <?php else: ?>
                                <i class="fas fa-book text-primary"></i>
                            <?php endif; ?>
                            <strong><?php echo e($book->name); ?></strong>
                        </td>
                        <td>
                            <?php if($book->parent_id): ?>
                                <span class="badge badge-info">باب فرعي</span>
                            <?php else: ?>
                                <span class="badge badge-primary">كتاب رئيسي</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($book->parent): ?>
                                <small><?php echo e($book->parent->name); ?></small>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-success">
                                <?php echo e($book->hadiths_count ?? 0); ?>

                            </span>
                        </td>
                        <td>
                            <?php if(!$book->parent_id): ?>
                                <span class="badge badge-warning">
                                    <?php echo e($book->children->count()); ?>

                                </span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('dashboard.books.show', $book)); ?>" class="btn btn-info" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('dashboard.books.edit', $book)); ?>" class="btn btn-warning" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger" onclick="confirmDelete(<?php echo e($book->id); ?>)"
                                    title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <form id="delete-form-<?php echo e($book->id); ?>" action="<?php echo e(route('dashboard.books.destroy', $book)); ?>"
                                method="POST" style="display: none;">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">لا توجد كتب مضافة بعد</p>
                            <a href="<?php echo e(route('dashboard.books.create')); ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> إضافة أول كتاب
                            </a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($books->hasPages()): ?>
        <div class="card-footer clearfix">
            <div class="float-left">
                <small class="text-muted">
                    عرض <?php echo e($books->firstItem()); ?> - <?php echo e($books->lastItem()); ?> من <?php echo e($books->total()); ?> نتيجة
                </small>
            </div>
            <div class="float-right">
                <?php echo e($books->links('pagination::bootstrap-5')); ?>

            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
    function confirmDelete(id) {
        if (confirm('هل أنت متأكد من حذف هذا الكتاب/الباب؟\n\nملاحظة: لا يمكن حذف كتاب يحتوي على أحاديث أو أبواب فرعية.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\hadeeth\resources\views/dashboard/books/index.blade.php ENDPATH**/ ?>