

<?php $__env->startSection('title', 'إدخال جماعي للأحاديث'); ?>

<?php $__env->startSection('content_header'); ?>
<div class="row">
    <div class="col-sm-6">
        <h1><i class="fas fa-layer-group text-primary"></i> إدخال جماعي للأحاديث</h1>
    </div>
    <div class="col-sm-6">
        <div class="float-left">
            <a href="<?php echo e(route('dashboard.hadiths.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> رجوع للقائمة
            </a>
            <a href="<?php echo e(route('dashboard.hadiths.create')); ?>" class="btn btn-outline-primary">
                <i class="fas fa-plus"></i> إدخال فردي
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php if($errors->any()): ?>
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h5><i class="icon fas fa-ban"></i> يوجد أخطاء!</h5>
        <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>


<div class="card card-primary card-outline" id="step1">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-paste"></i> الخطوة 1: اختر الكتاب والصق أحاديث الباب</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="mainBookSelect"><i class="fas fa-book text-primary"></i> الكتاب الرئيسي <span
                            class="text-danger">*</span></label>
                    <select id="mainBookSelect" class="form-control select2" style="width: 100%;">
                        <option value="">-- اختر الكتاب الرئيسي --</option>
                        <?php $__currentLoopData = $mainBooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($book->id); ?>"><?php echo e($book->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group" id="chapterGroup" style="display: none;">
                    <label for="chapterSelect"><i class="fas fa-bookmark text-warning"></i> الباب الفرعي</label>
                    <select id="chapterSelect" class="form-control select2" style="width: 100%;">
                        <option value="">-- الأحاديث تابعة للكتاب الرئيسي مباشرة --</option>
                    </select>
                </div>
            </div>
        </div>
        <!-- الحقل الفعلي الذي سيتم إرساله -->
        <input type="hidden" id="bookId" value="">

        <div class="form-group">
            <label for="bulkText"><i class="fas fa-scroll text-warning"></i> نصوص الأحاديث</label>
            <textarea id="bulkText" class="form-control" rows="12" dir="rtl"
                style="font-family: 'Scheherazade New', serif; font-size: 1.1rem; line-height: 2.2;" placeholder="الصق أحاديث الباب هنا... كل حديث يبدأ برقم وشرطة مثل:
1- نص الحديث الأول [رقم] (درجة) (رموز المصادر) عن الراوي.
2- نص الحديث الثاني [رقم] (درجة) (رموز المصادر) عن الراوي."></textarea>
            <small class="text-muted">
                <i class="fas fa-info-circle"></i>
                كل حديث يبدأ برقمه التسلسلي متبوعاً بشرطة (مثلاً: <code>3-</code>). يمكن أن يكون على سطر واحد أو أكثر.
            </small>
        </div>

        <button type="button" id="btnParse" class="btn btn-primary btn-lg btn-block">
            <i class="fas fa-magic"></i> تحليل الأحاديث
        </button>
    </div>
</div>


<div class="card card-success card-outline" id="step2" style="display: none;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-check-double"></i> الخطوة 2: مراجعة النتائج</h3>
        <div class="card-tools">
            <span class="badge badge-success" id="parsedCount">0</span> حديث تم تحليله
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th style="width: 60px;">الرقم</th>
                        <th>النص النظيف</th>
                        <th style="width: 80px;">الدرجة</th>
                        <th style="width: 120px;">الراوي</th>
                        <th style="width: 200px;">المصادر</th>
                        <th style="width: 50px;">زيادات</th>
                        <th style="width: 50px;">
                            <input type="checkbox" id="checkAll" checked title="تحديد / إلغاء الكل">
                        </th>
                    </tr>
                </thead>
                <tbody id="previewBody">
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-center">
        <form id="bulkForm" method="POST" action="<?php echo e(route('dashboard.hadiths.bulk.store')); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="book_id" id="formBookId">
            <div id="hiddenInputs"></div>

            <button type="button" id="btnBack" class="btn btn-outline-secondary ml-2">
                <i class="fas fa-arrow-right"></i> رجوع للتعديل
            </button>
            <button type="submit" id="btnSave" class="btn btn-success btn-lg">
                <i class="fas fa-save"></i> حفظ جميع الأحاديث المحددة
            </button>
        </form>
    </div>
</div>


<div class="modal fade" id="quickNarratorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white"><i class="fas fa-plus-circle"></i> إضافة راوي سريع</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>الاسم الكامل <span class="text-danger">*</span></label>
                    <input type="text" id="quickNarratorName" class="form-control" placeholder="مثال: عتبان بن مالك">
                </div>
                <div class="form-group">
                    <label>اسم الشهرة</label>
                    <input type="text" id="quickNarratorFame" class="form-control" placeholder="مثال: عتبان">
                </div>
                <div class="form-group">
                    <label>الدرجة</label>
                    <select id="quickNarratorGrade" class="form-control">
                        <option value="">-- اختياري --</option>
                        <option value="صحابي">صحابي</option>
                        <option value="ثقة">ثقة</option>
                        <option value="صدوق">صدوق</option>
                        <option value="ضعيف">ضعيف</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="saveQuickNarrator">
                    <i class="fas fa-save"></i> حفظ وربط
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<link href="<?php echo e(asset('vendor/select2/css/select2.min.css')); ?>" rel="stylesheet" />
<link href="<?php echo e(asset('vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css')); ?>" rel="stylesheet" />
<style>
    /* ===== Narrator column in bulk preview ===== */
    .narrator-fix {
        min-width: 170px;
    }

    .narrator-fix .select2-container {
        width: 100% !important;
    }

    .narrator-fix .select2-container--bootstrap4 .select2-selection--multiple {
        min-height: 38px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background-color: #f8f9fa;
        padding: 4px 6px;
    }

    /* Pill chips — vibrant gradient style */
    .narrator-fix .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
        display: inline-flex;
        align-items: center;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border: none;
        border-radius: 5px;
        color: #fff;
        font-size: 12px;
        font-weight: 500;
        padding: 4px 10px;
        margin: 2px 3px 2px 0;
        line-height: 1.4;
        gap: 0;
    }

    .narrator-fix .select2-selection__choice:nth-child(2) {
        background: linear-gradient(135deg, #f093fb, #f5576c) !important;
    }

    .narrator-fix .select2-selection__choice:nth-child(3) {
        background: linear-gradient(135deg, #4facfe, #00f2fe) !important;
        color: #003d5b !important;
    }

    .narrator-fix .select2-selection__choice:nth-child(4) {
        background: linear-gradient(135deg, #43e97b, #38f9d7) !important;
        color: #1a4d3a !important;
    }

    /* × remove button — hide Bootstrap4 theme gray × and use our own clean style */
    .narrator-fix .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
        color: rgba(255, 255, 255, 0.9) !important;
        font-size: 13px !important;
        font-weight: bold;
        line-height: 1;
        padding: 0 !important;
        border: none !important;
        background: transparent !important;
        background-color: transparent !important;
        border-radius: 0 !important;
        width: auto !important;
        height: auto !important;
        margin-left: 8px !important;
        margin-right: 0 !important;
        float: none;
        display: inline;
        order: 2;
        box-shadow: none !important;
        /* أخفي أي pseudo-element من الثيم */
        text-indent: 0;
    }

    /* أخفي الـ × الرمادي من Bootstrap4 theme */
    .narrator-fix .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove::before,
    .narrator-fix .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove::after {
        display: none !important;
    }

    .narrator-fix .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #fff !important;
        background: transparent !important;
    }

    .narrator-fix .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__rendered {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        padding: 0;
        gap: 2px;
    }

    .narrator-fix .select2-container--bootstrap4 .select2-search--inline .select2-search__field {
        font-size: 12px;
        color: #495057;
        margin: 2px 0;
        min-width: 80px;
    }

    .narrator-fix .select2-container--bootstrap4:not(.select2-container--open) .select2-search--inline {
        width: 0;
        overflow: hidden;
    }

    .narrator-missing {
        font-size: 11px;
        color: #dc3545;
        margin-top: 4px;
        display: block;
        font-weight: 500;
    }

    /* Select2 RTL fixes */
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        right: auto !important;
        left: 10px !important;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script src="<?php echo e(asset('vendor/select2/js/select2.full.min.js')); ?>"></script>
<script src="<?php echo e(asset('vendor/sweetalert2/sweetalert2.all.min.js')); ?>"></script>
<script>
    $(function () {
        // ========== Arabic Text Normalization ==========
        function normalizeArabic(str) {
            if (!str) return '';
            return str
                // توحيد أشكال الألف: أ إ آ ← ا
                .replace(/[أإآٱٲٳ]/g, 'ا')
                // توحيد التاء المربوطة: ة ← ه
                .replace(/ة/g, 'ه')
                // توحيد الياء: ى ← ي
                .replace(/ى/g, 'ي')
                // إزالة التشكيل (الحركات)
                .replace(/[\u064B-\u065F\u0670]/g, '')
                // إزالة الهمزات على الحروف
                .replace(/ؤ/g, 'و')
                .replace(/ئ/g, 'ي')
                // تنظيف المسافات المتعددة
                .replace(/\s+/g, ' ')
                .trim();
        }

        // Custom matcher for Select2 with Arabic normalization
        function arabicMatcher(params, data) {
            // If no search term, show all
            if ($.trim(params.term) === '') {
                return data;
            }

            // If the option has no text
            if (typeof data.text === 'undefined') {
                return null;
            }

            // Normalize both the search term and option text
            const normalizedTerm = normalizeArabic(params.term);
            const normalizedText = normalizeArabic(data.text);

            if (normalizedText.indexOf(normalizedTerm) > -1) {
                return data;
            }

            return null;
        }

        // تفعيل Select2 مع البحث العربي المحسّن
        $('.select2').select2({
            theme: 'bootstrap4',
            language: "ar",
            dir: "rtl",
            matcher: arabicMatcher
        });

        // ========== إدارة الكتب والأبواب ==========
        const mainBookSelect = $('#mainBookSelect');
        const chapterSelect = $('#chapterSelect');
        const chapterGroup = $('#chapterGroup');
        const bookIdInput = $('#bookId');

        mainBookSelect.on('change', function () {
            const mainId = $(this).val();

            // إعادة تعيين الفرعي
            chapterSelect.empty().append('<option value="">-- الأحاديث تابعة للكتاب الرئيسي مباشرة --</option>');
            chapterSelect.trigger('change.select2');
            bookIdInput.val(mainId); // مبدئياً القيمة هي الكتاب الرئيسي

            if (mainId) {
                // جلب الأبواب
                $.ajax({
                    url: '/dashboard/books/' + mainId + '/chapters',
                    method: 'GET',
                    success: function (chapters) {
                        if (chapters.length > 0) {
                            chapters.forEach(function (chapter) {
                                chapterSelect.append(new Option(chapter.name, chapter.id));
                            });
                            chapterGroup.slideDown();
                        } else {
                            chapterGroup.slideUp();
                        }
                    }
                });
            } else {
                chapterGroup.slideUp();
                bookIdInput.val('');
            }
        });

        chapterSelect.on('change', function () {
            const chapterId = $(this).val();
            if (chapterId) {
                bookIdInput.val(chapterId);
            } else {
                bookIdInput.val(mainBookSelect.val());
            }
        });

        // ========== Toggle all checkboxes ==========
        $('#checkAll').on('change', function () {
            $('.hadith-check').prop('checked', $(this).is(':checked'));
        });

        // ========== Parse button ==========
        $('#btnParse').on('click', function () {
            const bulkText = $('#bulkText').val().trim();
            const bookId = bookIdInput.val();

            if (!bookId) {
                alert('الرجاء اختيار الكتاب أولاً');
                return;
            }

            if (!bulkText) {
                alert('الرجاء لصق نصوص الأحاديث');
                return;
            }

            const btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري التحليل...');

            $.ajax({
                url: '<?php echo e(route("dashboard.hadiths.bulk.preview")); ?>',
                method: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    bulk_text: bulkText,
                },
                success: function (response) {
                    if (response.success && response.count > 0) {
                        renderPreview(response.hadiths, response.warnings);
                        $('#formBookId').val(bookId);
                        $('#parsedCount').text(response.count);
                        $('#step1').slideUp(300);
                        $('#step2').slideDown(300);
                    } else {
                        alert('لم يتم التعرف على أي أحاديث. تأكد من أن كل حديث يبدأ برقم وشرطة.');
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        const data = xhr.responseJSON;
                        let errorHtml = `
                            <div style="text-align:right; direction:rtl; max-height:450px; overflow-y:auto; padding:5px;">
                                <div style="background:#fff3cd; border:1px solid #ffc107; border-radius:8px; padding:12px 16px; margin-bottom:16px; font-size:14px; color:#856404;">
                                    ⚠️ <strong>${data.message}</strong><br>
                                    <span style="font-size:12px;">أصلح المشاكل أدناه ثم أعد التحليل</span>
                                </div>`;

                        data.errors.forEach(function (err) {
                            errorHtml += `
                                <div style="background:#f8f9fa; border:1px solid #dee2e6; border-radius:8px; padding:12px 16px; margin-bottom:10px; border-right:4px solid #dc3545;">
                                    <div style="font-weight:bold; color:#0d6efd; font-size:15px; margin-bottom:6px;">📌 حديث رقم ${err.index}</div>
                                    <div style="color:#6c757d; font-size:12px; font-family:'Scheherazade New',serif; line-height:1.8; margin-bottom:8px; padding:6px; background:#fff; border-radius:4px; border:1px dashed #dee2e6;">${err.snippet}</div>
                                    <div style="padding-right:8px;">`;
                            err.errors.forEach(function (e) {
                                errorHtml += `<div style="color:#dc3545; font-size:13px; margin-bottom:3px;">❌ ${e}</div>`;
                            });
                            errorHtml += `</div></div>`;
                        });

                        errorHtml += `</div>`;

                        Swal.fire({
                            icon: 'error',
                            title: '<span style="font-size:20px;">⛔ مشاكل في التحليل</span>',
                            html: errorHtml,
                            width: '650px',
                            confirmButtonText: '✏️ فهمت، سأصلح النصوص',
                            confirmButtonColor: '#0d6efd',
                            customClass: { popup: 'text-right' },
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: xhr.responseJSON?.message || 'حدث خطأ أثناء التحليل',
                            confirmButtonText: 'حسنًا',
                        });
                    }
                },
                complete: function () {
                    btn.prop('disabled', false).html('<i class="fas fa-magic"></i> تحليل الأحاديث');
                }
            });
        });

        // ========== Back button ==========
        $('#btnBack').on('click', function () {
            $('#step2').slideUp(300);
            $('#step1').slideDown(300);
        });

        // ========== Build preview table ==========
        function renderPreview(hadiths, warnings) {
            // عرض التحذيرات (رواة غير معروفين)
            if (warnings && warnings.length > 0) {
                let warnHtml = '<div class="alert alert-warning alert-dismissible" id="warningsAlert">';
                warnHtml += '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                warnHtml += '<h6><i class="fas fa-exclamation-triangle"></i> تحذيرات — يمكنك تصحيح الرواة أدناه مباشرة:</h6><ul class="mb-0">';
                warnings.forEach(function (w) {
                    w.warnings.forEach(function (msg) {
                        warnHtml += '<li>حديث #' + w.index + ': ' + msg + '</li>';
                    });
                });
                warnHtml += '</ul></div>';
                $('#step2 .card-body').prepend(warnHtml);
            }

            let html = '';
            hadiths.forEach(function (item, index) {
                const p = item.parsed;
                const sourcesStr = (p.sources || []).join('، ');
                const additionsCount = (p.additions || []).length;

                // عمود الراوي (Select2 المتعدد)
                let narratorCell = `
                    <div class="narrator-fix" data-index="${index}">
                        <select class="narrator-select" data-index="${index}" style="width:100%;" multiple="multiple">
                `;

                let hasMissing = false;
                let missingNames = [];

                if (p.narrators_data && p.narrators_data.length > 0) {
                    p.narrators_data.forEach(nd => {
                        if (nd.found && nd.id) {
                            narratorCell += `<option value="${nd.id}" selected>${nd.name}</option>`;
                        } else {
                            hasMissing = true;
                            missingNames.push(nd.original);
                        }
                    });
                } else if (p.narrators && p.narrators.length > 0) {
                    hasMissing = true;
                    missingNames = p.narrators;
                } else {
                    hasMissing = true;
                    missingNames.push('لا يوجد');
                }

                narratorCell += `</select>`;

                if (hasMissing && missingNames[0] !== 'لا يوجد') {
                    narratorCell += `<small class="narrator-missing"><i class="fas fa-exclamation-circle"></i> غير معروف: ${missingNames.join('، ')}</small>`;
                } else if (hasMissing && missingNames[0] === 'لا يوجد') {
                    narratorCell += `<small class="text-muted d-block" style="font-size:11px;">لم يتم العثور على أي راوي</small>`;
                }

                narratorCell += `</div>`;

                html += `
                <tr>
                    <td class="text-center font-weight-bold">${index + 1}</td>
                    <td class="text-center">
                        <span class="badge badge-info">${p.number || '—'}</span>
                    </td>
                    <td style="font-family: 'Scheherazade New', serif; font-size: 1rem; line-height: 2; max-width: 400px;">
                        ${truncate(p.clean_text, 120)}
                    </td>
                    <td class="text-center">
                        <span class="badge badge-${gradeColor(p.grade)}">${p.grade || '—'}</span>
                    </td>
                    <td>${narratorCell}</td>
                    <td><small>${sourcesStr || '—'}</small></td>
                    <td class="text-center">
                        ${additionsCount > 0 ? '<span class="badge badge-warning">' + additionsCount + '</span>' : '—'}
                    </td>
                    <td class="text-center">
                        <input type="checkbox" class="hadith-check" data-index="${index}" checked>
                    </td>
                </tr>
            `;
            });

            $('#previewBody').html(html);
            window._parsedHadiths = hadiths;

            // تفعيل Select2 AJAX على خلايا الراوي المفقودين
            initNarratorSelects();
        }

        // ========== Initialize inline narrator Select2 ==========
        let activeSelectIndex = null;
        let lastSearchTerm = '';

        function initNarratorSelects() {
            $('.narrator-select').each(function () {
                const idx = $(this).data('index');

                $(this).select2({
                    theme: 'bootstrap4',
                    language: 'ar',
                    dir: 'rtl',
                    placeholder: 'ابحث عن الرواة...',
                    allowClear: true,
                    minimumInputLength: 2,
                    width: '100%',
                    ajax: {
                        url: '<?php echo e(route("dashboard.narrators.search")); ?>',
                        dataType: 'json',
                        delay: 300,
                        data: function (params) {
                            lastSearchTerm = params.term;
                            return { q: params.term };
                        },
                        processResults: function (data) {
                            let results = data.map(function (item) {
                                let label = item.name;
                                if (item.fame_name) label += ' (' + item.fame_name + ')';
                                if (item.match_type === 'alternative') label += ' ⚠️';
                                return { id: item.id, text: label };
                            });
                            results.push({ id: '__new__', text: '➕ إضافة كراوي جديد' });
                            return { results: results };
                        }
                    }
                });

                // عند الاختيار
                $(this).on('select2:select', function (e) {
                    if (e.params.data.id === '__new__') {
                        activeSelectIndex = idx;
                        $(this).find('option[value="__new__"]').remove(); // إزالة خيار الإضافة المؤقت
                        $('#quickNarratorName').val(lastSearchTerm);
                        $('#quickNarratorModal').modal('show');
                    }
                });
            });
        }

        // ========== Submit form ==========
        $('#bulkForm').on('submit', function (e) {
            const container = $('#hiddenInputs');
            container.empty();

            let checkedCount = 0;
            $('.hadith-check:checked').each(function () {
                const idx = $(this).data('index');
                const item = window._parsedHadiths[idx];
                const p = item.parsed;

                const prefix = `hadiths[${checkedCount}]`;
                container.append(`<input type="hidden" name="${prefix}[raw_text]" value="${escapeHtml(item.raw)}">`);
                container.append(`<input type="hidden" name="${prefix}[clean_text]" value="${escapeHtml(p.clean_text)}">`);
                container.append(`<input type="hidden" name="${prefix}[number]" value="${p.number || ''}">`);
                container.append(`<input type="hidden" name="${prefix}[grade]" value="${p.grade || 'صحيح'}">`);

                // إرسال معلومات الرواة كـ JSON
                if (p.narrators_data) {
                    container.append(`<input type="hidden" name="${prefix}[narrators_data]" value="${escapeHtml(JSON.stringify(p.narrators_data))}">`);
                }

                // إرسال IDs المحددة من Select2 المتعدد
                const selectedIds = $(`.narrator-select[data-index="${idx}"]`).val();
                if (selectedIds && selectedIds.length > 0) {
                    selectedIds.forEach(nId => {
                        if (nId !== '__new__') {
                            container.append(`<input type="hidden" name="${prefix}[narrator_ids][]" value="${nId}">`);
                        }
                    });
                }

                container.append(`<input type="hidden" name="${prefix}[sources]" value="${escapeHtml(JSON.stringify(p.sources || []))}">`);
                container.append(`<input type="hidden" name="${prefix}[additions]" value="${escapeHtml(JSON.stringify(p.additions || []))}">`);
                checkedCount++;
            });

            if (checkedCount === 0) {
                e.preventDefault();
                alert('الرجاء تحديد حديث واحد على الأقل');
            }
        });

        // ========== Helpers ==========
        function gradeColor(grade) {
            const colors = { 'صحيح': 'success', 'حسن': 'primary', 'ضعيف': 'warning', 'موضوع': 'danger' };
            return colors[grade] || 'secondary';
        }

        function truncate(str, len) {
            if (!str) return '—';
            return str.length > len ? str.substring(0, len) + '...' : str;
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }

        // ========== Quick narrator modal ==========
        $('#saveQuickNarrator').click(function () {
            const name = $('#quickNarratorName').val().trim();
            if (!name) { alert('الاسم مطلوب'); return; }

            const btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...');

            $.ajax({
                url: '<?php echo e(route("dashboard.narrators.quick-store")); ?>',
                method: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    name: name,
                    fame_name: $('#quickNarratorFame').val().trim(),
                    grade_status: $('#quickNarratorGrade').val()
                },
                success: function (response) {
                    if (response.success) {
                        const narrator = response.narrator;
                        $('#quickNarratorModal').modal('hide');
                        $('#quickNarratorName').val('');
                        $('#quickNarratorFame').val('');
                        $('#quickNarratorGrade').val('');

                        // Update the inline select if from bulk preview
                        if (activeSelectIndex !== null) {
                            const option = new Option(narrator.name, narrator.id, true, true);
                            $(`.narrator-select[data-index="${activeSelectIndex}"]`).append(option).trigger('change');
                            activeSelectIndex = null;
                        }
                    }
                },
                error: function (xhr) {
                    let msg = 'حدث خطأ';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                    alert(msg);
                },
                complete: function () {
                    btn.prop('disabled', false).html('<i class="fas fa-save"></i> حفظ وربط');
                }
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\hadeeth\resources\views/dashboard/hadiths/bulk-create.blade.php ENDPATH**/ ?>