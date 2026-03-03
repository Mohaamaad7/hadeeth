

<?php $__env->startSection('title', 'مراجعة حديث #' . $hadith->number_in_book); ?>

<?php $__env->startSection('content_header'); ?>
<div class="row">
    <div class="col-sm-6">
        <h1><i class="fas fa-clipboard-check text-warning"></i> مراجعة حديث</h1>
    </div>
    <div class="col-sm-6 text-right">
        <a href="<?php echo e(route('dashboard.review.index')); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right"></i> العودة للقائمة
        </a>
        <?php if($previousPending): ?>
            <a href="<?php echo e(route('dashboard.review.show', $previousPending)); ?>" class="btn btn-outline-info">
                <i class="fas fa-arrow-right"></i> السابق
            </a>
        <?php endif; ?>
        <?php if($nextPending): ?>
            <a href="<?php echo e(route('dashboard.review.show', $nextPending)); ?>" class="btn btn-outline-info">
                التالي <i class="fas fa-arrow-left"></i>
            </a>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle"></i>
        <?php echo session('success'); ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<div class="row">
    
    <div class="col-lg-8">
        
        <?php if($hadith->raw_text): ?>
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-scroll"></i> النص الأصلي (كما وُرد)</h3>
                </div>
                <div class="card-body"
                    style="font-size: 1.2rem; line-height: 2; font-family: 'Amiri', serif; background: #fffef5;">
                    <?php echo e($hadith->raw_text); ?>

                </div>
            </div>
        <?php endif; ?>

        
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-alt"></i> النص المستخرج (المعالَج)</h3>
            </div>
            <div class="card-body" style="font-size: 1.15rem; line-height: 2; font-family: 'Amiri', serif;">
                <?php echo e($hadith->content); ?>

            </div>
        </div>

        
        <?php if($hadith->additions): ?>
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-plus-circle"></i> الزيادات</h3>
                </div>
                <div class="card-body">
                    <?php $__currentLoopData = $hadith->additions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $addition): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="badge badge-info p-2 m-1" style="font-size: 0.9rem;"><?php echo e($addition); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    
    <div class="col-lg-4">
        
        <div class="card card-outline card-dark">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle"></i> بيانات الحديث</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="font-weight-bold" width="40%">الرقم</td>
                        <td><span class="badge badge-primary badge-lg"><?php echo e($hadith->number_in_book); ?></span></td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">الحكم</td>
                        <td><span class="badge badge-info"><?php echo e($hadith->grade); ?></span></td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">الحالة</td>
                        <td><span class="badge badge-<?php echo e($hadith->status_badge); ?>"><?php echo e($hadith->status_name); ?></span></td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">الراوي</td>
                        <td><?php echo e($hadith->narrator->name ?? '—'); ?></td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">الكتاب</td>
                        <td><?php echo e($hadith->book->name ?? '—'); ?></td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">المصادر</td>
                        <td>
                            <?php $__empty_1 = true; $__currentLoopData = $hadith->sources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $source): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <span class="badge badge-success"><?php echo e($source->name); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">أدخله</td>
                        <td><?php echo e($hadith->enteredBy->name ?? '—'); ?></td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">تاريخ الإدخال</td>
                        <td><small><?php echo e($hadith->created_at->format('Y-m-d H:i')); ?></small></td>
                    </tr>
                    <?php if($hadith->reviewer): ?>
                        <tr>
                            <td class="font-weight-bold">راجعه</td>
                            <td><?php echo e($hadith->reviewer->name); ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">تاريخ المراجعة</td>
                            <td><small><?php echo e($hadith->reviewed_at?->format('Y-m-d H:i')); ?></small></td>
                        </tr>
                    <?php endif; ?>
                    <?php if($hadith->review_notes): ?>
                        <tr>
                            <td class="font-weight-bold">ملاحظات</td>
                            <td class="text-<?php echo e($hadith->status === 'rejected' ? 'danger' : 'muted'); ?>">
                                <?php echo e($hadith->review_notes); ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        
        <?php if($hadith->isPending() || $hadith->isRejected()): ?>
            <div class="card card-outline card-success">
                <div class="card-header bg-gradient-success">
                    <h3 class="card-title text-white"><i class="fas fa-gavel"></i> إجراء المراجعة</h3>
                </div>
                <div class="card-body">
                    
                    <form method="POST" action="<?php echo e(route('dashboard.review.approve', $hadith)); ?>" class="mb-3">
                        <?php echo csrf_field(); ?>
                        <div class="form-group">
                            <label>ملاحظات (اختياري)</label>
                            <input type="text" name="notes" class="form-control form-control-sm"
                                placeholder="ملاحظة اختيارية...">
                        </div>
                        <button type="submit" class="btn btn-success btn-block btn-lg">
                            <i class="fas fa-check-circle"></i> اعتماد ✅
                        </button>
                    </form>

                    <hr>

                    
                    <form method="POST" action="<?php echo e(route('dashboard.review.reject', $hadith)); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="form-group">
                            <label>سبب الرفض <span class="text-danger">*</span></label>
                            <textarea name="review_notes" class="form-control" rows="3"
                                placeholder="اكتب سبب الرفض بوضوح..." required><?php echo e(old('review_notes')); ?></textarea>
                            <?php $__errorArgs = ['review_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <small class="text-danger"><?php echo e($message); ?></small>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-times-circle"></i> رفض ❌
                        </button>
                    </form>
                </div>
            </div>
        <?php elseif($hadith->isApproved()): ?>
            <div class="callout callout-success">
                <h5><i class="fas fa-check-circle"></i> معتمد</h5>
                <p>تم اعتماد هذا الحديث بواسطة <strong><?php echo e($hadith->reviewer->name ?? '—'); ?></strong>
                    <?php echo e($hadith->reviewed_at ? 'في ' . $hadith->reviewed_at->format('Y-m-d H:i') : ''); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\hadeeth\resources\views/dashboard/review/show.blade.php ENDPATH**/ ?>