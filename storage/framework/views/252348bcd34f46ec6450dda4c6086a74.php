

<?php $__env->startSection('title', 'تنظيف قاعدة البيانات'); ?>

<?php $__env->startSection('content_header'); ?>
<div class="row">
    <div class="col-sm-6">
        <h1><i class="fas fa-broom text-danger"></i> تنظيف قاعدة البيانات</h1>
    </div>
    <div class="col-sm-6 text-right">
        <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right"></i> العودة
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>


<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?php echo session('success'); ?>

        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> <?php echo session('error'); ?>

        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>


<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?php echo e($stats['hadiths']['total']); ?></h3>
                <p>الأحاديث</p>
            </div>
            <div class="icon"><i class="fas fa-scroll"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?php echo e($stats['books']['total']); ?></h3>
                <p>الكتب والأبواب</p>
            </div>
            <div class="icon"><i class="fas fa-book"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?php echo e($stats['narrators']['total']); ?></h3>
                <p>الرواة</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3><?php echo e($stats['sources']['total']); ?></h3>
                <p>المصادر</p>
            </div>
            <div class="icon"><i class="fas fa-book-open"></i></div>
        </div>
    </div>
</div>


<div class="card card-danger card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-scroll"></i> 1. الأحاديث</h3>
        <div class="card-tools">
            <span class="badge badge-info"><?php echo e($stats['hadiths']['total']); ?> حديث</span>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><i class="fas fa-check text-success"></i> بدون راوي</td>
                        <td class="font-weight-bold"><?php echo e($stats['hadiths']['without_narrator']); ?></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-check text-success"></i> مع راوي</td>
                        <td class="font-weight-bold"><?php echo e($stats['hadiths']['with_narrator']); ?></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-link text-primary"></i> مع مصادر</td>
                        <td class="font-weight-bold"><?php echo e($stats['hadiths']['with_sources']); ?></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-unlink text-warning"></i> بدون مصادر</td>
                        <td class="font-weight-bold"><?php echo e($stats['hadiths']['without_sources']); ?></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-file-alt text-secondary"></i> مع نص أصلي</td>
                        <td class="font-weight-bold"><?php echo e($stats['hadiths']['with_raw_text']); ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6 d-flex align-items-center justify-content-center">
                <?php if($stats['hadiths']['total'] > 0): ?>
                    <form method="POST" action="<?php echo e(route('dashboard.cleanup.hadiths')); ?>" class="cleanup-form">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="confirm" value="">
                        <button type="button" class="btn btn-danger btn-lg cleanup-btn" data-action="حذف جميع الأحاديث"
                            data-count="<?php echo e($stats['hadiths']['total']); ?>" data-type="DELETE"
                            data-warning="سيتم حذف <?php echo e($stats['hadiths']['total']); ?> حديث + ربط المصادر + سلاسل الإسناد">
                            <i class="fas fa-trash-alt"></i> حذف جميع الأحاديث
                        </button>
                    </form>
                <?php else: ?>
                    <span class="text-muted"><i class="fas fa-check-circle text-success"></i> لا توجد أحاديث</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<div class="card card-warning card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-users"></i> 2. الرواة</h3>
        <div class="card-tools">
            <span class="badge badge-warning"><?php echo e($stats['narrators']['total']); ?> راوي</span>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><i class="fas fa-link text-success"></i> مرتبطون بأحاديث</td>
                        <td class="font-weight-bold"><?php echo e($stats['narrators']['with_hadiths']); ?></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-ghost text-danger"></i> أيتام (بلا أحاديث)</td>
                        <td class="font-weight-bold text-danger"><?php echo e($stats['narrators']['orphan']); ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4 d-flex align-items-center justify-content-center">
                <?php if($stats['narrators']['orphan'] > 0): ?>
                    <form method="POST" action="<?php echo e(route('dashboard.cleanup.narrators.orphan')); ?>" class="cleanup-form">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="confirm" value="">
                        <button type="button" class="btn btn-outline-warning cleanup-btn" data-action="حذف الرواة الأيتام"
                            data-count="<?php echo e($stats['narrators']['orphan']); ?>" data-type="DELETE"
                            data-warning="سيتم حذف <?php echo e($stats['narrators']['orphan']); ?> راوي ليس لديهم أحاديث">
                            <i class="fas fa-ghost"></i> حذف الأيتام فقط
                        </button>
                    </form>
                <?php else: ?>
                    <span class="text-muted"><i class="fas fa-check-circle text-success"></i> لا يوجد رواة أيتام</span>
                <?php endif; ?>
            </div>
            <div class="col-md-4 d-flex align-items-center justify-content-center">
                <?php if($stats['narrators']['total'] > 0): ?>
                    <form method="POST" action="<?php echo e(route('dashboard.cleanup.narrators.all')); ?>" class="cleanup-form">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="confirm" value="">
                        <button type="button" class="btn btn-danger cleanup-btn" data-action="حذف جميع الرواة"
                            data-count="<?php echo e($stats['narrators']['total']); ?>" data-type="DELETE"
                            data-warning="يتطلب أن تكون جميع الأحاديث محذوفة أولاً<?php echo e($stats['narrators']['with_hadiths'] > 0 ? ' ⛔ ' . $stats['narrators']['with_hadiths'] . ' مرتبطون بأحاديث!' : ''); ?>"
                            <?php echo e($stats['narrators']['with_hadiths'] > 0 ? 'disabled' : ''); ?>>
                            <i class="fas fa-users-slash"></i> حذف الكل
                        </button>
                    </form>
                <?php else: ?>
                    <span class="text-muted"><i class="fas fa-check-circle text-success"></i> لا يوجد رواة</span>
                <?php endif; ?>
            </div>
        </div>
        <?php if($stats['narrators']['with_hadiths'] > 0 && $stats['hadiths']['total'] > 0): ?>
            <div class="callout callout-warning mt-2">
                <small><i class="fas fa-info-circle"></i> لا يمكن حذف جميع الرواة لأن
                    <?php echo e($stats['narrators']['with_hadiths']); ?> راوي مرتبطون بأحاديث. احذف الأحاديث أولاً.</small>
            </div>
        <?php endif; ?>
    </div>
</div>


<div class="card card-success card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-book"></i> 3. الكتب والأبواب</h3>
        <div class="card-tools">
            <span class="badge badge-success"><?php echo e($stats['books']['main']); ?> كتاب + <?php echo e($stats['books']['chapters']); ?>

                باب</span>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><i class="fas fa-book text-primary"></i> كتب رئيسية</td>
                        <td class="font-weight-bold"><?php echo e($stats['books']['main']); ?></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-bookmark text-info"></i> أبواب</td>
                        <td class="font-weight-bold"><?php echo e($stats['books']['chapters']); ?></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-ghost text-danger"></i> أبواب فارغة</td>
                        <td class="font-weight-bold text-danger"><?php echo e($stats['books']['empty_chapters']); ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4 d-flex align-items-center justify-content-center">
                <?php if($stats['books']['empty_chapters'] > 0): ?>
                    <form method="POST" action="<?php echo e(route('dashboard.cleanup.books.empty')); ?>" class="cleanup-form">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="confirm" value="">
                        <button type="button" class="btn btn-outline-success cleanup-btn" data-action="حذف الأبواب الفارغة"
                            data-count="<?php echo e($stats['books']['empty_chapters']); ?>" data-type="DELETE"
                            data-warning="سيتم حذف <?php echo e($stats['books']['empty_chapters']); ?> باب فارغ بدون أحاديث">
                            <i class="fas fa-ghost"></i> حذف الفارغة فقط
                        </button>
                    </form>
                <?php else: ?>
                    <span class="text-muted"><i class="fas fa-check-circle text-success"></i> لا توجد أبواب فارغة</span>
                <?php endif; ?>
            </div>
            <div class="col-md-4 d-flex align-items-center justify-content-center">
                <?php if($stats['books']['total'] > 0): ?>
                    <form method="POST" action="<?php echo e(route('dashboard.cleanup.books.all')); ?>" class="cleanup-form">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="confirm" value="">
                        <button type="button" class="btn btn-danger cleanup-btn" data-action="حذف جميع الكتب"
                            data-count="<?php echo e($stats['books']['total']); ?>" data-type="DELETE"
                            data-warning="يتطلب حذف الأحاديث أولاً<?php echo e($stats['hadiths']['total'] > 0 ? ' ⛔ يوجد ' . $stats['hadiths']['total'] . ' حديث!' : ''); ?>"
                            <?php echo e($stats['hadiths']['total'] > 0 ? 'disabled' : ''); ?>>
                            <i class="fas fa-book-dead"></i> حذف الكل
                        </button>
                    </form>
                <?php else: ?>
                    <span class="text-muted"><i class="fas fa-check-circle text-success"></i> لا توجد كتب</span>
                <?php endif; ?>
            </div>
        </div>
        <?php if($stats['hadiths']['total'] > 0): ?>
            <div class="callout callout-success mt-2">
                <small><i class="fas fa-info-circle"></i> لا يمكن حذف جميع الكتب لأن هناك <?php echo e($stats['hadiths']['total']); ?>

                    حديث مرتبط. احذف الأحاديث أولاً.</small>
            </div>
        <?php endif; ?>
    </div>
</div>


<div class="card card-secondary card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-book-open"></i> 4. المصادر</h3>
        <div class="card-tools">
            <span class="badge badge-secondary"><?php echo e($stats['sources']['total']); ?> مصدر</span>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><i class="fas fa-link text-success"></i> مرتبطون بأحاديث</td>
                        <td class="font-weight-bold"><?php echo e($stats['sources']['with_hadiths']); ?></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-ghost text-danger"></i> أيتام (بلا أحاديث)</td>
                        <td class="font-weight-bold text-danger"><?php echo e($stats['sources']['orphan']); ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6 d-flex align-items-center justify-content-center">
                <?php if($stats['sources']['orphan'] > 0): ?>
                    <form method="POST" action="<?php echo e(route('dashboard.cleanup.sources.orphan')); ?>" class="cleanup-form">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="confirm" value="">
                        <button type="button" class="btn btn-outline-secondary cleanup-btn"
                            data-action="حذف المصادر الأيتام" data-count="<?php echo e($stats['sources']['orphan']); ?>"
                            data-type="DELETE"
                            data-warning="سيتم حذف <?php echo e($stats['sources']['orphan']); ?> مصدر ليس مرتبطاً بأي حديث">
                            <i class="fas fa-ghost"></i> حذف الأيتام فقط
                        </button>
                    </form>
                <?php else: ?>
                    <span class="text-muted"><i class="fas fa-check-circle text-success"></i> لا توجد مصادر أيتام</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="callout callout-info mt-2">
            <small><i class="fas fa-shield-alt"></i> المصادر هي القاموس الأساسي للنظام — لا يُنصح بحذفها كلها. يمكنك فقط
                حذف الأيتام.</small>
        </div>
    </div>
</div>


<?php if($stats['chains']['total'] > 0): ?>
    <div class="card card-info card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-link"></i> 5. سلاسل الإسناد</h3>
            <div class="card-tools">
                <span class="badge badge-info"><?php echo e($stats['chains']['total']); ?> سلسلة</span>
            </div>
        </div>
        <div class="card-body d-flex align-items-center justify-content-between">
            <div>
                <p class="mb-0">عدد السلاسل: <strong><?php echo e($stats['chains']['total']); ?></strong></p>
            </div>
            <form method="POST" action="<?php echo e(route('dashboard.cleanup.chains')); ?>" class="cleanup-form">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="confirm" value="">
                <button type="button" class="btn btn-outline-info cleanup-btn" data-action="حذف سلاسل الإسناد"
                    data-count="<?php echo e($stats['chains']['total']); ?>" data-type="DELETE"
                    data-warning="سيتم حذف <?php echo e($stats['chains']['total']); ?> سلسلة إسناد">
                    <i class="fas fa-unlink"></i> حذف السلاسل
                </button>
            </form>
        </div>
    </div>
<?php endif; ?>


<div class="card card-dark">
    <div class="card-header bg-gradient-dark">
        <h3 class="card-title"><i class="fas fa-radiation"></i> تنظيف شامل</h3>
    </div>
    <div class="card-body text-center">
        <p class="text-muted mb-3">حذف <strong>كل شيء</strong>: الأحاديث + الكتب + الرواة + السلاسل (ما عدا المصادر
            والمستخدمين)</p>
        <form method="POST" action="<?php echo e(route('dashboard.cleanup.nuke')); ?>" class="cleanup-form">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="confirm" value="">
            <button type="button" class="btn btn-dark btn-lg cleanup-btn" data-action="تنظيف شامل لقاعدة البيانات"
                data-count="<?php echo e($stats['hadiths']['total'] + $stats['books']['total'] + $stats['narrators']['total']); ?>"
                data-type="NUKE"
                data-warning="⚠️ سيتم حذف: <?php echo e($stats['hadiths']['total']); ?> حديث + <?php echo e($stats['books']['total']); ?> كتاب/باب + <?php echo e($stats['narrators']['total']); ?> راوي + <?php echo e($stats['chains']['total']); ?> سلسلة. هذا الإجراء لا يمكن التراجع عنه!">
                <i class="fas fa-radiation"></i> تنظيف شامل
            </button>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    .cleanup-btn {
        min-width: 160px;
    }

    .small-box .icon i {
        font-size: 60px;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function () {
        $('.cleanup-btn').on('click', function () {
            const btn = $(this);
            const form = btn.closest('.cleanup-form');
            const action = btn.data('action');
            const count = btn.data('count');
            const type = btn.data('type');
            const warning = btn.data('warning');

            Swal.fire({
                icon: 'warning',
                title: `<span style="font-size:18px;">${action}</span>`,
                html: `
                <div style="text-align:right; direction:rtl;">
                    <div style="background:#fff3cd; border:1px solid #ffc107; border-radius:8px; padding:12px 16px; margin-bottom:16px; color:#856404;">
                        ${warning}
                    </div>
                    <div style="background:#f8d7da; border:1px solid #f5c6cb; border-radius:8px; padding:16px; text-align:center;">
                        <p style="margin-bottom:8px; font-size:14px; color:#721c24;">للتأكيد اكتب <strong style="font-size:16px; letter-spacing:2px;">${type}</strong></p>
                        <input type="text" id="confirmInput" class="form-control text-center"
                            style="font-size:18px; font-weight:bold; letter-spacing:3px; max-width:200px; margin:0 auto;"
                            placeholder="${type}" autocomplete="off">
                    </div>
                </div>`,
                showCancelButton: true,
                confirmButtonText: '🗑️ تنفيذ الحذف',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                focusCancel: true,
                width: '550px',
                preConfirm: () => {
                    const input = document.getElementById('confirmInput').value;
                    if (input !== type) {
                        Swal.showValidationMessage(`اكتب "${type}" للتأكيد`);
                        return false;
                    }
                    return true;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.find('input[name="confirm"]').val(type);
                    form.submit();
                }
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\hadeeth\resources\views/dashboard/cleanup/index.blade.php ENDPATH**/ ?>