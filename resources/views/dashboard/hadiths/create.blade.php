@extends('adminlte::page')

@section('title', 'إضافة حديث جديد')

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1>إضافة حديث جديد</h1>
    </div>
    <div class="col-sm-6">
        <div class="float-left">
            <a href="{{ route('dashboard.hadiths.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> رجوع للقائمة
            </a>
        </div>
    </div>
</div>
@stop

@section('content')
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h5><i class="icon fas fa-ban"></i> يوجد أخطاء في النموذج!</h5>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <!-- النموذج الرئيسي -->
    <div class="col-md-8">
        <form action="{{ route('dashboard.hadiths.store') }}" method="POST" id="hadithForm">
            @csrf

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-magic"></i> الإدخال الذكي (Parser)
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>نصيحة:</strong> الصق النص الكامل هنا، وسيقوم النظام باستخراج البيانات تلقائياً!
                        <br>
                        <small>مثال: [436] (صحيح) (د ت ن) عن أبي هريرة: قال رسول الله ﷺ...</small>
                    </div>

                    <div class="form-group">
                        <label>النص الخام (اختياري)</label>
                        <textarea name="raw_text" id="rawText" class="form-control" rows="4"
                            placeholder="مثال: [436] (صحيح) (د ت ن) عن أبي هريرة: قال رسول الله ﷺ...">{{ old('raw_text') }}</textarea>
                    </div>

                    <button type="button" class="btn btn-primary btn-block" id="parseBtn">
                        <i class="fas fa-cogs"></i> تحليل النص تلقائياً
                    </button>

                    <div id="parseResult" class="mt-3" style="display: none;">
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> تم التحليل بنجاح! راجع الحقول أدناه.
                        </div>
                    </div>

                    {{-- حفظ النص الأصلي كما ورد (الأمانة العلمية) --}}
                    <input type="hidden" name="raw_text" id="rawTextHidden" value="{{ old('raw_text') }}">
                </div>
            </div>

            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">بيانات الحديث</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>نص الحديث <span class="text-danger">*</span></label>
                        <textarea name="content" id="content" class="form-control" rows="6"
                            required>{{ old('content') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>الشرح والتفسير</label>
                        <textarea name="explanation" class="form-control summernote" rows="3"
                            placeholder="أضف شرحاً أو تفسيراً للحديث...">{{ old('explanation') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>رقم الحديث في الكتاب <span class="text-danger">*</span></label>
                                <input type="text" name="number_in_book" id="numberInBook" class="form-control"
                                    value="{{ old('number_in_book') }}" required placeholder="مثال: 3222 أو 3222-1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>درجة الحديث <span class="text-danger">*</span></label>
                                <select name="grade" id="grade" class="form-control" required>
                                    <option value="">-- اختر الدرجة --</option>
                                    <option value="صحيح" {{ old('grade') == 'صحيح' ? 'selected' : '' }}>صحيح</option>
                                    <option value="حسن" {{ old('grade') == 'حسن' ? 'selected' : '' }}>حسن</option>
                                    <option value="ضعيف" {{ old('grade') == 'ضعيف' ? 'selected' : '' }}>ضعيف</option>
                                    <option value="موضوع" {{ old('grade') == 'موضوع' ? 'selected' : '' }}>موضوع</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>الكتاب الرئيسي <span class="text-danger">*</span></label>
                                <select id="mainBookSelect" class="form-control select2" style="width: 100%;">
                                    <option value="">-- اختر الكتاب الرئيسي --</option>
                                    @foreach($mainBooks as $book)
                                        <option value="{{ $book->id }}">{{ $book->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" id="chapterGroup" style="display: none;">
                                <label>الباب الفرعي</label>
                                <select id="chapterSelect" class="form-control select2" style="width: 100%;">
                                    <option value="">-- الحديث تابع للكتاب الرئيسي مباشرة --</option>
                                </select>
                            </div>
                            <!-- الحقل الفعلي الذي سيتم إرساله -->
                            <input type="hidden" name="book_id" id="bookId" required>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>الصحابي (أو الرواة)</label>
                                <select name="narrator_ids[]" id="narratorId" class="form-control" style="width: 100%;"
                                    multiple="multiple">
                                    @if(old('narrator_ids'))
                                        @foreach(old('narrator_ids') as $nId)
                                            @php $oldNarrator = \App\Models\Narrator::find($nId); @endphp
                                            @if($oldNarrator)
                                                <option value="{{ $oldNarrator->id }}" selected>{{ $oldNarrator->name }}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                <small class="form-text text-muted">
                                    ابدأ بكتابة الاسم للبحث — يمكن ادخال اكثر من راوي للحديث الواحد
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>المصادر</label>
                        <div class="row">
                            @foreach($sources as $source)
                                <div class="col-md-4">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input source-checkbox"
                                            id="source_{{ $source->id }}" name="source_ids[]" value="{{ $source->id }}" {{ is_array(old('source_ids')) && in_array($source->id, old('source_ids')) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="source_{{ $source->id }}">
                                            {{ $source->name }}
                                            <small class="text-muted">({{ $source->code }})</small>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> حفظ الحديث
                    </button>
                    <a href="{{ route('dashboard.hadiths.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> إلغاء
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- معاينة مباشرة -->
    <div class="col-md-4">
        <div class="card card-success sticky-top" style="top: 10px;">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-eye"></i> معاينة مباشرة
                </h3>
            </div>
            <div class="card-body">
                <div id="preview">
                    <p class="text-muted text-center">
                        <i class="fas fa-info-circle"></i><br>
                        ستظهر المعاينة هنا بعد ملء الحقول
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal: إضافة راوي سريع --}}
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
                    <label>الرتبة</label>
                    <select id="quickNarratorRank" class="form-control">
                        <option value="">-- اختياري --</option>
                        @foreach (\App\Enums\NarratorRank::cases() as $rank)
                            <option value="{{ $rank->value }}">{{ $rank->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" id="quickJudgmentGroup" style="display: none;">
                    <label>حكم العلماء</label>
                    <select id="quickNarratorJudgment" class="form-control">
                        <option value="">-- اختياري --</option>
                        @foreach (\App\Enums\ScholarJudgment::cases() as $judg)
                            <option value="{{ $judg->value }}">{{ $judg->label() }}</option>
                        @endforeach
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
@stop

@section('css')
<style>
    .custom-control-label {
        cursor: pointer;
    }

    #preview {
        font-size: 14px;
        line-height: 1.8;
    }

    /* Select2 RTL fixes */
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        right: auto !important;
        left: 10px !important;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function () {
        // ========== Arabic Text Normalization ==========
        function normalizeArabic(str) {
            if (!str) return '';
            return str
                .replace(/[أإآٱٲٳ]/g, 'ا')
                .replace(/ة/g, 'ه')
                .replace(/ى/g, 'ي')
                .replace(/[\u064B-\u065F\u0670]/g, '')
                .replace(/ؤ/g, 'و')
                .replace(/ئ/g, 'ي')
                .replace(/\s+/g, ' ')
                .trim();
        }

        function arabicMatcher(params, data) {
            if ($.trim(params.term) === '') return data;
            if (typeof data.text === 'undefined') return null;
            const normalizedTerm = normalizeArabic(params.term);
            const normalizedText = normalizeArabic(data.text);
            if (normalizedText.indexOf(normalizedTerm) > -1) return data;
            return null;
        }

        // تفعيل Select2 للكتب
        $('.select2').select2({
            theme: 'bootstrap4',
            language: "ar",
            dir: "rtl",
            matcher: arabicMatcher
        });

        // ========== Select2 AJAX — بحث ذكي عن الراوي ==========
        let lastSearchTerm = '';

        $('#narratorId').select2({
            theme: 'bootstrap4',
            language: "ar",
            dir: "rtl",
            placeholder: '-- ابحث عن الصحابي --',
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("dashboard.narrators.search") }}',
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

                    // إضافة خيار "إضافة راوي جديد" في نهاية النتائج
                    results.push({
                        id: '__new__',
                        text: '➕ إضافة "' + lastSearchTerm + '" كراوي جديد'
                    });

                    return { results: results };
                }
            }
        });

        // عند اختيار "إضافة جديد" → فتح Modal
        $('#narratorId').on('select2:select', function (e) {
            if (e.params.data.id === '__new__') {
                $(this).val(null).trigger('change');
                $('#quickNarratorName').val(lastSearchTerm);
                $('#quickNarratorModal').modal('show');
            }
        });

        // Toggle Judgment based on Rank
        $('#quickNarratorRank').on('change', function() {
            const val = $(this).val();
            if (val === 'tabii' || val === 'rawi') {
                $('#quickJudgmentGroup').slideDown();
            } else {
                $('#quickJudgmentGroup').slideUp();
                $('#quickNarratorJudgment').val('');
            }
        });

        // ========== حفظ الراوي السريع ==========
        $('#saveQuickNarrator').click(function () {
            const name = $('#quickNarratorName').val().trim();
            if (!name) {
                alert('الاسم مطلوب');
                return;
            }

            const btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...');

            $.ajax({
                url: '{{ route("dashboard.narrators.quick-store") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: name,
                    fame_name: $('#quickNarratorFame').val().trim(),
                    rank: $('#quickNarratorRank').val(),
                    judgment: $('#quickNarratorJudgment').val()
                },
                success: function (response) {
                    if (response.success) {
                        const narrator = response.narrator;
                        const option = new Option(narrator.name, narrator.id, true, true);
                        $('#narratorId').append(option).trigger('change');

                        $('#quickNarratorModal').modal('hide');
                        $('#quickNarratorName').val('');
                        $('#quickNarratorFame').val('');
                        $('#quickNarratorRank').val('');
                        $('#quickNarratorJudgment').val('');
                        $('#quickJudgmentGroup').hide();
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

        // ========== إدارة الكتب والأبواب ==========
        const mainBookSelect = $('#mainBookSelect');
        const chapterSelect = $('#chapterSelect');
        const chapterGroup = $('#chapterGroup');
        const bookIdInput = $('#bookId');

        mainBookSelect.on('change', function () {
            const mainId = $(this).val();

            chapterSelect.empty().append('<option value="">-- الحديث تابع للكتاب الرئيسي مباشرة --</option>');
            bookIdInput.val(mainId);

            if (mainId) {
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

        // ========== الـ Parser الذكي ==========
        $('#parseBtn').click(function () {
            const rawText = $('#rawText').val().trim();

            if (!rawText) {
                alert('الرجاء إدخال النص الخام أولاً');
                return;
            }

            $('#rawTextHidden').val(rawText);

            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري التحليل...');

            $.ajax({
                url: '{{ route("dashboard.hadiths.parse") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    raw_text: rawText
                },
                success: function (response) {
                    if (response.success) {
                        const data = response.data;

                        if (data.clean_text) {
                            $('#content').val(data.clean_text);
                        }
                        if (data.number) {
                            $('#numberInBook').val(data.number);
                        }
                        if (data.grade) {
                            $('#grade').val(data.grade);
                        }

                        // بحث عن الرواة عبر AJAX بدلاً من البحث المحلي
                        if (data.narrators && data.narrators.length > 0) {
                            $('#narratorId').val(null).trigger('change'); // مسح الاختيارات السابقة
                            let notFound = [];

                            const promises = data.narrators.map(narratorName => {
                                return $.ajax({
                                    url: '{{ route("dashboard.narrators.search") }}',
                                    data: { q: narratorName }
                                }).then(narrators => {
                                    if (narrators.length > 0) {
                                        const best = narrators[0];
                                        // تحقق إذا الخيار غير موجود مسبقاً لمنع التكرار
                                        if (!$('#narratorId').find("option[value='" + best.id + "']").length) {
                                            const option = new Option(best.name, best.id, true, true);
                                            $('#narratorId').append(option);
                                        } else {
                                            // الخيار موجود، نجعله selected فقط
                                            $('#narratorId').find("option[value='" + best.id + "']").prop('selected', true);
                                        }

                                        if (best.match_type === 'alternative') {
                                            alert('⚠️ تم ربط "' + narratorName + '" بـ "' + best.name + '" (اسم بديل)');
                                        }
                                    } else {
                                        notFound.push(narratorName);
                                    }
                                });
                            });

                            Promise.all(promises).then(() => {
                                $('#narratorId').trigger('change');
                                if (notFound.length > 0) {
                                    alert('تنبيه: لم يتم العثور على الرواة: ' + notFound.join('، ') + '. يمكنك البحث عنهم أو إضافتهم من حقل الصحابي.');
                                }
                            });
                        }

                        // تحديد المصادر
                        if (data.sources && data.sources.length > 0) {
                            $('.source-checkbox').prop('checked', false);
                            data.sources.forEach(function (sourceName) {
                                $('.source-checkbox').each(function () {
                                    const label = $(this).next('label').text();
                                    if (label.includes(sourceName)) {
                                        $(this).prop('checked', true);
                                    }
                                });
                            });
                        }

                        $('#parseResult').slideDown();
                        updatePreview();
                    }
                },
                error: function () {
                    alert('حدث خطأ أثناء التحليل');
                },
                complete: function () {
                    $('#parseBtn').prop('disabled', false).html('<i class="fas fa-cogs"></i> تحليل النص تلقائياً');
                }
            });
        });

        // ========== المعاينة المباشرة ==========
        function updatePreview() {
            const content = $('#content').val();
            const grade = $('#grade option:selected').text();
            const number = $('#numberInBook').val();

            let html = '<div class="border-bottom pb-2 mb-2">';
            if (number) {
                html += '<span class="badge badge-primary">#' + number + '</span> ';
            }
            if (grade && grade !== '-- اختر الدرجة --') {
                let badgeClass = 'secondary';
                if (grade === 'صحيح') badgeClass = 'success';
                else if (grade === 'حسن') badgeClass = 'info';
                else if (grade === 'ضعيف') badgeClass = 'warning';
                else if (grade === 'موضوع') badgeClass = 'danger';
                html += '<span class="badge badge-' + badgeClass + '">' + grade + '</span>';
            }
            html += '</div>';

            if (content) {
                html += '<p>' + content + '</p>';
            } else {
                html = '<p class="text-muted text-center"><i class="fas fa-info-circle"></i><br>ستظهر المعاينة هنا</p>';
            }

            $('#preview').html(html);
        }

        $('#content, #grade, #numberInBook').on('input change', updatePreview);
    });
</script>
@stop