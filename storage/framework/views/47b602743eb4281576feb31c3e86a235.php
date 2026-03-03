<?php $__env->startSection('title', 'المستخدمين'); ?>

<?php $__env->startSection('content_header'); ?>
<div class="row">
    <div class="col-sm-6">
        <h1>المستخدمين</h1>
    </div>
    <div class="col-sm-6">
        <div class="float-left">
            <a href="<?php echo e(route('dashboard.users.create')); ?>" class="btn btn-success">
                <i class="fas fa-plus"></i> إضافة مستخدم جديد
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
            <i class="fas fa-search"></i> البحث
        </h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('dashboard.users.index')); ?>">
            <div class="row">
                <div class="col-md-9">
                    <div class="form-group">
                        <input type="text" name="search" class="form-control"
                            placeholder="ابحث في الاسم أو البريد الإلكتروني..." value="<?php echo e(request('search')); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> بحث
                    </button>
                    <a href="<?php echo e(route('dashboard.users.index')); ?>" class="btn btn-secondary">
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
            <i class="fas fa-users"></i> قائمة المستخدمين
            <span class="badge badge-info"><?php echo e($users->total()); ?></span>
        </h3>
    </div>
    <div class="card-body p-0">
        <?php if($users->count() > 0): ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px">#</th>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th style="width: 120px">الدور</th>
                        <th style="width: 150px">تاريخ التسجيل</th>
                        <th style="width: 150px" class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($user->id); ?></td>
                            <td>
                                <strong><?php echo e($user->name); ?></strong>
                                <?php if($user->id === auth()->id()): ?>
                                    <span class="badge badge-success">أنت</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($user->email); ?></td>
                            <td><span class="badge badge-<?php echo e($user->role_badge); ?>"><?php echo e($user->role_name); ?></span></td>
                            <td>
                                <small class="text-muted">
                                    <?php echo e($user->created_at->diffForHumans()); ?>

                                </small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="<?php echo e(route('dashboard.users.show', $user)); ?>" class="btn btn-sm btn-info"
                                        title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('dashboard.users.edit', $user)); ?>" class="btn btn-sm btn-warning"
                                        title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if($user->id !== auth()->id()): ?>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo e($user->id); ?>)"
                                            title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <form id="delete-form-<?php echo e($user->id); ?>" action="<?php echo e(route('dashboard.users.destroy', $user)); ?>"
                                    method="POST" style="display: none;">
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
                <p>لا توجد مستخدمين</p>
            </div>
        <?php endif; ?>
    </div>
    <?php if($users->hasPages()): ?>
        <div class="card-footer clearfix">
            <div class="float-left">
                <small class="text-muted">
                    عرض <?php echo e($users->firstItem()); ?> - <?php echo e($users->lastItem()); ?> من <?php echo e($users->total()); ?> نتيجة
                </small>
            </div>
            <div class="float-right">
                <?php echo e($users->links('pagination::bootstrap-5')); ?>

            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
    function confirmDelete(id) {
        if (confirm('هل أنت متأكد من حذف هذا المستخدم؟')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\hadeeth\resources\views/dashboard/users/index.blade.php ENDPATH**/ ?>