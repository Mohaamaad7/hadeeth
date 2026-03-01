<?php $__env->startSection('title', $book->name); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="row">
        <div class="col-sm-6">
            <h1><?php echo e($book->name); ?></h1>
        </div>
        <div class="col-sm-6">
            <div class="float-left">
                <a href="<?php echo e(route('dashboard.books.edit', $book)); ?>" class="btn btn-warning">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <a href="<?php echo e(route('dashboard.books.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i> رجوع
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

    <div class="row">
        <div class="col-md-4">
            <!-- معلومات الكتاب -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> المعلومات
                    </h3>
                </div>
                <div class="card-body">
                    <dl>
                        <dt>النوع:</dt>
                        <dd>
                            <?php if($book->parent_id): ?>
                                <span class="badge badge-info">باب فرعي</span>
                            <?php else: ?>
                                <span class="badge badge-primary">كتاب رئيسي</span>
                            <?php endif; ?>
                        </dd>

                        <?php if($book->parent): ?>
                            <dt>الكتاب الرئيسي:</dt>
                            <dd>
                                <a href="<?php echo e(route('dashboard.books.show', $book->parent)); ?>">
                                    <?php echo e($book->parent->name); ?>

                                </a>
                            </dd>
                        <?php endif; ?>

                        <dt>رقم الترتيب:</dt>
                        <dd><?php echo e($book->sort_order); ?></dd>

                        <dt>عدد الأحاديث:</dt>
                        <dd>
                            <span class="badge badge-success"><?php echo e($book->hadiths->count()); ?></span>
                        </dd>

                        <?php if(!$book->parent_id): ?>
                            <dt>عدد الأبواب:</dt>
                            <dd>
                                <span class="badge badge-warning"><?php echo e($book->children->count()); ?></span>
                            </dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>

            <!-- الإجراءات -->
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs"></i> الإجراءات
                    </h3>
                </div>
                <div class="card-body">
                    <a href="<?php echo e(route('dashboard.books.edit', $book)); ?>" class="btn btn-warning btn-block">
                        <i class="fas fa-edit"></i> تعديل
                    </a>
                    <button type="button" class="btn btn-danger btn-block" onclick="confirmDelete()">
                        <i class="fas fa-trash"></i> حذف
                    </button>
                    <a href="<?php echo e(route('dashboard.books.index')); ?>" class="btn btn-secondary btn-block">
                        <i class="fas fa-list"></i> العودة للقائمة
                    </a>
                </div>
            </div>

            <form id="delete-form" action="<?php echo e(route('dashboard.books.destroy', $book)); ?>" 
                  method="POST" style="display: none;">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
            </form>
        </div>

        <div class="col-md-8">
            <?php if(!$book->parent_id && $book->children->count() > 0): ?>
                <!-- الأبواب الفرعية -->
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-folder-open"></i> الأبواب الفرعية
                            <span class="badge badge-light"><?php echo e($book->children->count()); ?></span>
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 60px">الترتيب</th>
                                    <th>اسم الباب</th>
                                    <th style="width: 100px">الأحاديث</th>
                                    <th style="width: 100px">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $book->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($child->sort_order); ?></td>
                                        <td><?php echo e($child->name); ?></td>
                                        <td>
                                            <span class="badge badge-success">
                                                <?php echo e($child->hadiths_count); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('dashboard.books.show', $child)); ?>" 
                                               class="btn btn-xs btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('dashboard.books.edit', $child)); ?>" 
                                               class="btn btn-xs btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <!-- الأحاديث -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-book-open"></i> الأحاديث
                        <span class="badge badge-light"><?php echo e($book->hadiths->count()); ?></span>
                    </h3>
                    <div class="card-tools">
                        <a href="<?php echo e(route('dashboard.hadiths.create')); ?>?book_id=<?php echo e($book->id); ?>" 
                           class="btn btn-sm btn-success">
                            <i class="fas fa-plus"></i> إضافة حديث
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if($book->hadiths->count() > 0): ?>
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 60px">#</th>
                                    <th>النص</th>
                                    <th style="width: 100px">الدرجة</th>
                                    <th style="width: 100px">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $book->hadiths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hadith): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($hadith->number_in_book); ?></td>
                                        <td><?php echo e(Str::limit($hadith->content, 80)); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo e($hadith->grade === 'صحيح' ? 'success' : 
                                                ($hadith->grade === 'حسن' ? 'info' : 'warning')); ?>">
                                                <?php echo e($hadith->grade); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('dashboard.hadiths.show', $hadith)); ?>" 
                                               class="btn btn-xs btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                        <?php if($book->hadiths->count() >= 10): ?>
                            <div class="card-footer text-center">
                                <a href="<?php echo e(route('dashboard.hadiths.index')); ?>?book_id=<?php echo e($book->id); ?>" 
                                   class="btn btn-sm btn-primary">
                                    عرض جميع الأحاديث
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>لا توجد أحاديث في هذا الكتاب/الباب بعد</p>
                            <a href="<?php echo e(route('dashboard.hadiths.create')); ?>?book_id=<?php echo e($book->id); ?>" 
                               class="btn btn-success">
                                <i class="fas fa-plus"></i> إضافة أول حديث
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
function confirmDelete() {
    if (confirm('هل أنت متأكد من حذف هذا الكتاب/الباب؟')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\hadeeth\resources\views/dashboard/books/show.blade.php ENDPATH**/ ?>