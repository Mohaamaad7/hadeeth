@extends('adminlte::page')

@section('title', 'إدخال جماعي للأحاديث')

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1><i class="fas fa-layer-group text-primary"></i> إدخال جماعي للأحاديث</h1>
    </div>
    <div class="col-sm-6">
        <div class="float-left">
            <a href="{{ route('dashboard.hadiths.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> رجوع للقائمة
            </a>
            <a href="{{ route('dashboard.hadiths.create') }}" class="btn btn-outline-primary">
                <i class="fas fa-plus"></i> إدخال فردي
            </a>
        </div>
    </div>
</div>
@stop

@section('content')
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h5><i class="icon fas fa-ban"></i> يوجد أخطاء!</h5>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- خطوة 1: لصق النصوص --}}
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
                        @foreach($mainBooks as $book)
                            <option value="{{ $book->id }}">{{ $book->name }}</option>
                        @endforeach
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

{{-- خطوة 2: معاينة النتائج --}}
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
        <form id="bulkForm" method="POST" action="{{ route('dashboard.hadiths.bulk.store') }}">
            @csrf
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
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css"
    rel="stylesheet" />
<style>
    /* Select2 RTL fixes */
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        right: auto !important;
        left: 10px !important;
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
                url: '{{ route("dashboard.hadiths.bulk.preview") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    bulk_text: bulkText,
                },
                success: function (response) {
                    if (response.success && response.count > 0) {
                        renderPreview(response.hadiths);
                        $('#formBookId').val(bookId);
                        $('#parsedCount').text(response.count);
                        $('#step1').slideUp(300);
                        $('#step2').slideDown(300);
                    } else {
                        alert('لم يتم التعرف على أي أحاديث. تأكد من أن كل حديث يبدأ برقم وشرطة.');
                    }
                },
                error: function (xhr) {
                    alert('حدث خطأ أثناء التحليل: ' + (xhr.responseJSON?.message || 'خطأ غير معروف'));
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
        function renderPreview(hadiths) {
            let html = '';
            hadiths.forEach(function (item, index) {
                const p = item.parsed;
                const sourcesStr = (p.sources || []).join('، ');
                const additionsCount = (p.additions || []).length;

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
                    <td class="text-center">${p.narrator || '—'}</td>
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
                container.append(`<input type="hidden" name="${prefix}[narrator]" value="${escapeHtml(p.narrator || '')}">`);
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
    });
</script>
@stop