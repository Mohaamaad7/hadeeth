

<?php $__env->startSection('title', 'مراجعة الأحاديث'); ?>

<?php $__env->startSection('content_header'); ?>
<div class="row">
    <div class="col-sm-6">
        <h1><i class="fas fa-clipboard-check text-warning"></i> مراجعة الأحاديث</h1>
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
    <div class="col-lg-4 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?php echo e($counts['pending']); ?></h3>
                <p>بانتظار المراجعة</p>
            </div>
            <div class="icon"><i class="fas fa-hourglass-half"></i></div>
            <a href="<?php echo e(route('dashboard.review.index', ['status' => 'pending'])); ?>" class="small-box-footer">عرض <i
                    class="fas fa-arrow-circle-left"></i></a>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?php echo e($counts['approved']); ?></h3>
                <p>معتمدة</p>
            </div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <a href="<?php echo e(route('dashboard.review.index', ['status' => 'approved'])); ?>" class="small-box-footer">عرض <i
                    class="fas fa-arrow-circle-left"></i></a>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?php echo e($counts['rejected']); ?></h3>
                <p>مرفوضة</p>
            </div>
            <div class="icon"><i class="fas fa-times-circle"></i></div>
            <a href="<?php echo e(route('dashboard.review.index', ['status' => 'rejected'])); ?>" class="small-box-footer">عرض <i
                    class="fas fa-arrow-circle-left"></i></a>
        </div>
    </div>
</div>


<?php if(auth()->user()->isAdmin() && $counts['pending'] > 0 && $status === 'pending'): ?>
    <div class="card card-warning card-outline mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-bolt text-warning"></i> <strong><?php echo e($counts['pending']); ?></strong> حديث بانتظار المراجعة
            </div>
            <div>
                <form method="POST" action="<?php echo e(route('dashboard.review.approve-all')); ?>" class="d-inline"
                    id="approveAllForm">
                    <?php echo csrf_field(); ?>
                    <button type="button" class="btn btn-success" onclick="confirmApproveAll()">
                        <i class="fas fa-check-double"></i> اعتماد الكل
                    </button>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>


<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="btn-group">
                <a href="<?php echo e(route('dashboard.review.index', ['status' => 'pending'])); ?>"
                    class="btn btn-sm <?php echo e($status === 'pending' ? 'btn-warning' : 'btn-outline-warning'); ?>">
                    <i class="fas fa-hourglass-half"></i> معلّقة <span
                        class="badge badge-light"><?php echo e($counts['pending']); ?></span>
                </a>
                <a href="<?php echo e(route('dashboard.review.index', ['status' => 'approved'])); ?>"
                    class="btn btn-sm <?php echo e($status === 'approved' ? 'btn-success' : 'btn-outline-success'); ?>">
                    <i class="fas fa-check"></i> معتمدة <span class="badge badge-light"><?php echo e($counts['approved']); ?></span>
                </a>
                <a href="<?php echo e(route('dashboard.review.index', ['status' => 'rejected'])); ?>"
                    class="btn btn-sm <?php echo e($status === 'rejected' ? 'btn-danger' : 'btn-outline-danger'); ?>">
                    <i class="fas fa-times"></i> مرفوضة <span class="badge badge-light"><?php echo e($counts['rejected']); ?></span>
                </a>
                <a href="<?php echo e(route('dashboard.review.index', ['status' => 'all'])); ?>"
                    class="btn btn-sm <?php echo e($status === 'all' ? 'btn-secondary' : 'btn-outline-secondary'); ?>">
                    الكل
                </a>
            </div>
            <form class="form-inline mt-1" action="<?php echo e(route('dashboard.review.index')); ?>">
                <input type="hidden" name="status" value="<?php echo e($status); ?>">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="بحث..."
                    value="<?php echo e(request('search')); ?>">
                <button class="btn btn-sm btn-primary mr-1"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <?php if(auth()->user()->isAdmin() && $status === 'pending' && $hadiths->count() > 0): ?>
            <form method="POST" action="<?php echo e(route('dashboard.review.bulk-approve')); ?>" id="bulkForm">
                <?php echo csrf_field(); ?>
        <?php endif; ?>
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <?php if(auth()->user()->isAdmin() && $status === 'pending'): ?>
                            <th width="30"><input type="checkbox" id="selectAll"></th>
                        <?php endif; ?>
                        <th width="60">#</th>
                        <th>النص</th>
                        <th width="80">الحكم</th>
                        <th width="120">الراوي</th>
                        <th width="100">الحالة</th>
                        <th width="120">المُدخِل</th>
                        <th width="100">التاريخ</th>
                        <th width="80">إجراء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $hadiths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hadith): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <?php if(auth()->user()->isAdmin() && $status === 'pending'): ?>
                                <td><input type="checkbox" name="hadith_ids[]" value="<?php echo e($hadith->id); ?>" class="select-hadith">
                                </td>
                            <?php endif; ?>
                            <td><span class="badge badge-primary"><?php echo e($hadith->number_in_book); ?></span></td>
                            <td><?php echo e(Str::limit($hadith->content, 100)); ?></td>
                            <td><span class="badge badge-info"><?php echo e($hadith->grade); ?></span></td>
                            <td><?php echo e($hadith->narrators->pluck('name')->join('، ') ?: '—'); ?></td>
                            <td><span class="badge badge-<?php echo e($hadith->status_badge); ?>"><?php echo e($hadith->status_name); ?></span></td>
                            <td><?php echo e($hadith->enteredBy->name ?? '—'); ?></td>
                            <td><small><?php echo e($hadith->created_at->diffForHumans()); ?></small></td>
                            <td>
                                <a href="<?php echo e(route('dashboard.review.show', $hadith)); ?>"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-check-circle fa-2x text-success mb-2 d-block"></i>
                                لا توجد أحاديث في هذه الحالة
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if(auth()->user()->isAdmin() && $status === 'pending' && $hadiths->count() > 0): ?>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-sm" id="bulkApproveBtn" disabled>
                            <i class="fas fa-check-double"></i> اعتماد المحدد (<span id="selectedCount">0</span>)
                        </button>
                    </div>
                </form>
            <?php endif; ?>
    </div>
    <div class="card-footer">
        <?php echo e($hadiths->appends(request()->query())->links('pagination::bootstrap-5')); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function () {
        // Select all checkbox
        $('#selectAll').on('change', function () {
            $('.select-hadith').prop('checked', this.checked);
            updateBulkBtn();
        });
        $('.select-hadith').on('change', updateBulkBtn);

        function updateBulkBtn() {
            var count = $('.select-hadith:checked').length;
            $('#selectedCount').text(count);
            $('#bulkApproveBtn').prop('disabled', count === 0);
        }
    });

    function confirmApproveAll() {
        Swal.fire({
            icon: 'warning',
            title: 'اعتماد جماعي شامل',
            html: '<div style="direction:rtl">سيتم اعتماد <strong>جميع</strong> الأحاديث المعلّقة دفعة واحدة.<br><br>هل أنت متأكد؟</div>',
            showCancelButton: true,
            confirmButtonText: '✅ اعتماد الكل',
            cancelButtonText: 'إلغاء',
            confirmButtonColor: '#28a745',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('approveAllForm').submit();
            }
        });
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\hadeeth\resources\views/dashboard/review/index.blade.php ENDPATH**/ ?>