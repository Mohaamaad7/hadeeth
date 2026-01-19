@extends('adminlte::page')

@section('title', 'تعديل الحديث')

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1>تعديل الحديث #{{ $hadith->number_in_book }}</h1>
    </div>
    <div class="col-sm-6">
        <div class="float-left">
            <a href="{{ route('dashboard.hadiths.show', $hadith) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> عرض
            </a>
            <a href="{{ route('dashboard.hadiths.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> رجوع
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

<form action="{{ route('dashboard.hadiths.update', $hadith) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">بيانات الحديث</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label>نص الحديث <span class="text-danger">*</span></label>
                <textarea name="content" class="form-control" rows="6"
                    required>{{ old('content', $hadith->content) }}</textarea>
            </div>


            <div class="form-group">
                <label><i class="fas fa-book-open text-info"></i> الشرح والتفسير</label>
                <textarea name="explanation" id="explanation-editor" class="form-control"
                    rows="6">{{ old('explanation', $hadith->explanation) }}</textarea>
            </div>


            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>رقم الحديث في الكتاب <span class="text-danger">*</span></label>
                        <input type="number" name="number_in_book" class="form-control"
                            value="{{ old('number_in_book', $hadith->number_in_book) }}" required min="1">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>درجة الحديث <span class="text-danger">*</span></label>
                        <select name="grade" class="form-control" required>
                            <option value="صحيح" {{ old('grade', $hadith->grade) == 'صحيح' ? 'selected' : '' }}>صحيح
                            </option>
                            <option value="حسن" {{ old('grade', $hadith->grade) == 'حسن' ? 'selected' : '' }}>حسن</option>
                            <option value="ضعيف" {{ old('grade', $hadith->grade) == 'ضعيف' ? 'selected' : '' }}>ضعيف
                            </option>
                            <option value="موضوع" {{ old('grade', $hadith->grade) == 'موضوع' ? 'selected' : '' }}>موضوع
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    @php
                        $currentBookId = old('book_id', $hadith->book_id);
                        $currentBook = \App\Models\Book::find($currentBookId);
                        $isSub = $currentBook && $currentBook->parent_id;
                        $initialMainBookId = $isSub ? $currentBook->parent_id : $currentBookId;
                        $initialSubBookId = $isSub ? $currentBookId : null;
                    @endphp

                    <div class="form-group">
                        <label>الكتاب الرئيسي <span class="text-danger">*</span></label>
                        <select id="mainBookSelect" class="form-control select2" style="width: 100%;">
                            <option value="">-- اختر الكتاب الرئيسي --</option>
                            @foreach($mainBooks as $book)
                                <option value="{{ $book->id }}" {{ $book->id == $initialMainBookId ? 'selected' : '' }}>
                                    {{ $book->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" id="chapterGroup" style="{{ $initialSubBookId ? '' : 'display: none;' }}">
                        <label>الباب الفرعي</label>
                        <select id="chapterSelect" class="form-control select2" style="width: 100%;">
                            <option value="">-- الحديث تابع للكتاب الرئيسي مباشرة --</option>
                            <!-- سيتم تعبئته بواسطة JS، لكن يمكننا وضع القيمة الحالية إذا وجدت -->
                            @if($isSub)
                                <option value="{{ $initialSubBookId }}" selected>{{ $currentBook->name }}</option>
                            @endif
                        </select>
                    </div>
                    <!-- الحقل الفعلي -->
                    <input type="hidden" name="book_id" id="bookId" value="{{ $currentBookId }}" required>

                    <!-- تخزين مبدأي للبيانات المشحونة -->
                    <input type="hidden" id="initialSubBookId" value="{{ $initialSubBookId }}">
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>الصحابي</label>
                        <select name="narrator_id" class="form-control">
                            <option value="">-- اختر الصحابي --</option>
                            @foreach($companions as $companion)
                                <option value="{{ $companion->id }}" {{ old('narrator_id', $hadith->narrator_id) == $companion->id ? 'selected' : '' }}>
                                    {{ $companion->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>المصادر</label>
                <div class="row">
                    @php
                        $selectedSources = old('source_ids', $hadith->sources->pluck('id')->toArray());
                    @endphp
                    @foreach($sources as $source)
                        <div class="col-md-4">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="source_{{ $source->id }}"
                                    name="source_ids[]" value="{{ $source->id }}" {{ in_array($source->id, $selectedSources) ? 'checked' : '' }}>
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
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-save"></i> حفظ التعديلات
            </button>
            <a href="{{ route('dashboard.hadiths.show', $hadith) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> إلغاء
            </a>
        </div>
    </div>

    <!-- قسم الشرح المنظم -->
    @php
        $hasStructuredSharh = $hadith->sharh_context || $hadith->sharh_obstacles || $hadith->sharh_commands || $hadith->sharh_conclusion;
    @endphp
    <div class="card card-success mt-3">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-book-reader"></i> الشرح المنظم (اختياري)</h3>
            <div class="card-tools">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="enableStructuredSharh"
                        name="has_structured_sharh" value="1" {{ $hasStructuredSharh ? 'checked' : '' }}>
                    <label class="custom-control-label" for="enableStructuredSharh">تفعيل الشرح المنظم</label>
                </div>
            </div>
        </div>
        <div class="card-body" id="structuredSharhSection" style="{{ $hasStructuredSharh ? '' : 'display: none;' }}">
            <!-- سياق الحديث -->
            <div class="form-group">
                <label><i class="fas fa-mosque text-success"></i> سياق الحديث وموضوعه</label>
                <textarea name="sharh_context" class="form-control" rows="3"
                    placeholder="اكتب سياق الحديث وموضوعه العام...">{{ old('sharh_context', $hadith->sharh_context) }}</textarea>
            </div>

            <!-- الموانع والمشاكل -->
            <div class="form-group">
                <label><i class="fas fa-ban text-danger"></i> الموانع والمشاكل المذكورة</label>
                <div id="obstacles-container">
                    @if($hadith->sharh_obstacles && count($hadith->sharh_obstacles) > 0)
                        @foreach($hadith->sharh_obstacles as $index => $obstacle)
                            <div class="obstacle-row mb-2 p-2 border rounded bg-light">
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" name="sharh_obstacles[{{ $index }}][title]"
                                            class="form-control form-control-sm" placeholder="عنوان المانع"
                                            value="{{ $obstacle['title'] ?? '' }}">
                                    </div>
                                    <div class="col-md-7">
                                        <input type="text" name="sharh_obstacles[{{ $index }}][description]"
                                            class="form-control form-control-sm" placeholder="الوصف والتفصيل"
                                            value="{{ $obstacle['description'] ?? '' }}">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-sm btn-danger btn-block remove-obstacle"><i
                                                class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="obstacle-row mb-2 p-2 border rounded bg-light">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="sharh_obstacles[0][title]" class="form-control form-control-sm"
                                        placeholder="عنوان المانع">
                                </div>
                                <div class="col-md-7">
                                    <input type="text" name="sharh_obstacles[0][description]"
                                        class="form-control form-control-sm" placeholder="الوصف والتفصيل">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-sm btn-danger btn-block remove-obstacle"><i
                                            class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <button type="button" class="btn btn-sm btn-info" id="add-obstacle"><i class="fas fa-plus"></i> إضافة
                    مانع</button>
            </div>

            <!-- الأوامر والحلول -->
            <div class="form-group">
                <label><i class="fas fa-list-check text-primary"></i> الأوامر والحلول</label>
                <div id="commands-container">
                    @if($hadith->sharh_commands && count($hadith->sharh_commands) > 0)
                        @foreach($hadith->sharh_commands as $index => $command)
                            <div class="command-row mb-2 p-2 border rounded bg-light">
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="text" name="sharh_commands[{{ $index }}][title]"
                                            class="form-control form-control-sm" placeholder="العنوان"
                                            value="{{ $command['title'] ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="sharh_commands[{{ $index }}][ruling]"
                                            class="form-control form-control-sm" placeholder="الحكم"
                                            value="{{ $command['ruling'] ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="sharh_commands[{{ $index }}][explanation]"
                                            class="form-control form-control-sm" placeholder="التوضيح"
                                            value="{{ $command['explanation'] ?? '' }}">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-sm btn-danger btn-block remove-command"><i
                                                class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="command-row mb-2 p-2 border rounded bg-light">
                            <div class="row">
                                <div class="col-md-3">
                                    <input type="text" name="sharh_commands[0][title]" class="form-control form-control-sm"
                                        placeholder="العنوان">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="sharh_commands[0][ruling]" class="form-control form-control-sm"
                                        placeholder="الحكم">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="sharh_commands[0][explanation]"
                                        class="form-control form-control-sm" placeholder="التوضيح">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-sm btn-danger btn-block remove-command"><i
                                            class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <button type="button" class="btn btn-sm btn-info" id="add-command"><i class="fas fa-plus"></i> إضافة
                    أمر</button>
            </div>

            <!-- الخلاصة -->
            <div class="form-group">
                <label><i class="fas fa-lightbulb text-warning"></i> الخلاصة والأحكام المستنبطة</label>
                <textarea name="sharh_conclusion" class="form-control" rows="3"
                    placeholder="اكتب الخلاصة والأحكام المستنبطة من الحديث...">{{ old('sharh_conclusion', $hadith->sharh_conclusion) }}</textarea>
            </div>
        </div>
    </div>

    <!-- قسم رجال الحديث (السلاسل) -->
    <div class="card card-primary mt-3">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-users"></i> رجال الحديث (سلاسل الإسناد)</h3>
        </div>
        <div class="card-body">
            @if($hadith->sources->count() > 0)
                <div id="chains-container">
                    @foreach($hadith->sources as $index => $source)
                        <div class="chain-section mb-4 p-3 border rounded bg-light" data-source-id="{{ $source->id }}">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-book-open text-primary"></i>
                                    {{ $source->name }} <span class="badge badge-info">{{ $source->code }}</span>
                                </h5>
                            </div>

                            @php
                                $existingChain = $hadith->chains->where('source_id', $source->id)->first();
                            @endphp

                            <input type="hidden" name="chains[{{ $index }}][source_id]" value="{{ $source->id }}">

                            <div class="form-group">
                                <label class="text-muted small">وصف السلسلة (اختياري)</label>
                                <input type="text" name="chains[{{ $index }}][description]" class="form-control form-control-sm"
                                    placeholder="مثال: طريق الإمام البخاري في الصحيح"
                                    value="{{ $existingChain?->description }}">
                            </div>

                            <div class="narrators-list" data-chain-index="{{ $index }}">
                                @if($existingChain && $existingChain->narrators->count() > 0)
                                    @foreach($existingChain->narrators as $narrator)
                                        @php
                                            $isCompanion = $narrator->is_companion;
                                        @endphp
                                        <div class="narrator-row mb-2">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <select class="form-control form-control-sm role-selector" data-chain="{{ $index }}"
                                                        data-narrator-index="{{ $loop->index }}">
                                                        <option value="">-- النوع --</option>
                                                        <option value="companion" {{ $isCompanion ? 'selected' : '' }}>صحابي</option>
                                                        <option value="narrator" {{ !$isCompanion ? 'selected' : '' }}>رجل الحديث
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                    <select name="chains[{{ $index }}][narrators][{{ $loop->index }}][id]"
                                                        class="form-control form-control-sm narrator-select">
                                                        <option value="">-- اختر --</option>
                                                        @if($isCompanion)
                                                            @foreach($companions as $c)
                                                                <option value="{{ $c->id }}" {{ $c->id == $narrator->id ? 'selected' : '' }}>
                                                                    {{ $c->name }}</option>
                                                            @endforeach
                                                        @else
                                                            @foreach($narrators->where('is_companion', false) as $n)
                                                                <option value="{{ $n->id }}" {{ $n->id == $narrator->id ? 'selected' : '' }}>
                                                                    {{ $n->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="hidden"
                                                        name="chains[{{ $index }}][narrators][{{ $loop->index }}][role]"
                                                        value="{{ $narrator->pivot->role }}">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-sm btn-danger btn-block remove-narrator">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="narrator-row mb-2">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <select class="form-control form-control-sm role-selector" data-chain="{{ $index }}"
                                                    data-narrator-index="0">
                                                    <option value="">-- النوع --</option>
                                                    <option value="companion">صحابي</option>
                                                    <option value="narrator">رجل الحديث</option>
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <select name="chains[{{ $index }}][narrators][0][id]"
                                                    class="form-control form-control-sm narrator-select" disabled>
                                                    <option value="">-- اختر النوع أولاً --</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="hidden" name="chains[{{ $index }}][narrators][0][role]" value="">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-sm btn-danger btn-block remove-narrator">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <button type="button" class="btn btn-sm btn-success add-narrator" data-chain-index="{{ $index }}">
                                <i class="fas fa-plus"></i> إضافة راوي
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    يجب اختيار المصادر أولاً من القسم أعلاه لتتمكن من إضافة سلاسل الإسناد.
                </div>
            @endif
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save"></i> حفظ جميع التعديلات (البيانات + السلاسل)
            </button>
            <a href="{{ route('dashboard.hadiths.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> رجوع للقائمة
            </a>
        </div>
    </div>
</form>
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
    
    /* CKEditor 4 RTL fixes */
    .cke_rtl .cke_editable {
        direction: rtl;
        text-align: right;
    }
    .cke_editable {
        font-family: 'Cairo', sans-serif;
        font-size: 16px;
        line-height: 1.8;
        padding: 15px;
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
<script>
    // بيانات الرواة والصحابة
    const companions = @json($companions->map(fn($c) => ['id' => $c->id, 'name' => $c->name]));
    const regularNarrators = @json($narrators->where('is_companion', false)->values()->map(fn($n) => ['id' => $n->id, 'name' => $n->name]));

    $(document).ready(function () {
        // تفعيل Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            language: "ar",
            dir: "rtl"
        });

        // تهيئة CKEditor 4 للشرح
        if (document.querySelector('#explanation-editor')) {
            CKEDITOR.replace('explanation-editor', {
                language: 'ar',
                contentsLangDirection: 'rtl',
                height: 300,
                removeButtons: 'Save,NewPage,Preview,Print,Templates,PasteFromWord',
                toolbar: [
                    { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', '-', 'Undo', 'Redo'] },
                    { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll'] },
                    '/',
                    { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'] },
                    { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
                    '/',
                    { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
                    { name: 'colors', items: ['TextColor', 'BGColor'] },
                    { name: 'tools', items: ['Maximize'] }
                ],
                contentsCss: ['https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap'],
                font_names: 'Cairo;Arial;Times New Roman;Verdana',
                font_defaultLabel: 'Cairo',
                fontSize_defaultLabel: '16px'
            });
        }

        // إدارة الكتب والأبواب
        const mainBookSelect = $('#mainBookSelect');
        const chapterSelect = $('#chapterSelect');
        const chapterGroup = $('#chapterGroup');
        const bookIdInput = $('#bookId');
        const initialSubId = $('#initialSubBookId').val();

        // دالة تحميل الفروع
        function loadChapters(mainId, selectedChapterId = null) {
            if (!mainId) {
                chapterGroup.slideUp();
                return;
            }

            $.ajax({
                url: '/dashboard/books/' + mainId + '/chapters',
                method: 'GET',
                success: function (chapters) {
                    chapterSelect.empty().append('<option value="">-- الحديث تابع للكتاب الرئيسي مباشرة --</option>');

                    if (chapters.length > 0) {
                        chapters.forEach(function (chapter) {
                            const isSelected = selectedChapterId && selectedChapterId == chapter.id;
                            chapterSelect.append(new Option(chapter.name, chapter.id, isSelected, isSelected));
                        });
                        chapterGroup.slideDown();
                    } else {
                        chapterGroup.slideUp();
                    }
                }
            });
        }

        // التحميل الأولي (إذا كان هناك كتاب فرعي مختار مسبقاً)
        if (mainBookSelect.val()) {
            // إذا كان هناك initialSubId سنقوم بتحميل القائمة وتحديده
            // لكننا قمنا بالفعل بوضعه يدوياً في الـ Blade كحل مؤقت، 
            // الأفضل إعادة تحميل القائمة لضمان وجود باقي الخيارات
            loadChapters(mainBookSelect.val(), initialSubId);
        }

        mainBookSelect.on('change', function () {
            const mainId = $(this).val();
            bookIdInput.val(mainId); // الافتراضي
            loadChapters(mainId);
        });

        chapterSelect.on('change', function () {
            const chapterId = $(this).val();
            if (chapterId) {
                bookIdInput.val(chapterId);
            } else {
                bookIdInput.val(mainBookSelect.val());
            }
        });

        // --- بقية الكود القديم ---
        // عند تغيير نوع الراوي (صحابي / رجل الحديث)
        $(document).on('change', '.role-selector', function () {
            const roleType = $(this).val();
            const narratorSelect = $(this).closest('.row').find('.narrator-select');
            const hiddenRole = $(this).closest('.row').find('input[type="hidden"]');

            // تفريغ وتعبئة القائمة حسب النوع
            narratorSelect.empty();

            if (roleType === 'companion') {
                narratorSelect.prop('disabled', false);
                narratorSelect.append('<option value="">-- اختر الصحابي --</option>');
                companions.forEach(c => {
                    narratorSelect.append(`<option value="${c.id}">${c.name}</option>`);
                });
                hiddenRole.val('الصحابي');
            } else if (roleType === 'narrator') {
                narratorSelect.prop('disabled', false);
                narratorSelect.append('<option value="">-- اختر رجل الحديث --</option>');
                regularNarrators.forEach(n => {
                    narratorSelect.append(`<option value="${n.id}">${n.name}</option>`);
                });
                hiddenRole.val('');
            } else {
                narratorSelect.prop('disabled', true);
                narratorSelect.append('<option value="">-- اختر النوع أولاً --</option>');
                hiddenRole.val('');
            }
        });

        // إضافة راوي جديد
        $(document).on('click', '.add-narrator', function () {
            const chainIndex = $(this).data('chain-index');
            const narratorsList = $(this).prev('.narrators-list');
            const currentCount = narratorsList.find('.narrator-row').length;

            const narratorRow = `
            <div class="narrator-row mb-2">
                <div class="row">
                    <div class="col-md-3">
                        <select class="form-control form-control-sm role-selector" data-chain="${chainIndex}" data-narrator-index="${currentCount}">
                            <option value="">-- النوع --</option>
                            <option value="companion">صحابي</option>
                            <option value="narrator">رجل الحديث</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <select name="chains[${chainIndex}][narrators][${currentCount}][id]" class="form-control form-control-sm narrator-select" disabled>
                            <option value="">-- اختر النوع أولاً --</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="hidden" name="chains[${chainIndex}][narrators][${currentCount}][role]" value="">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-danger btn-block remove-narrator">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

            narratorsList.append(narratorRow);
        });

        // حذف راوي
        $(document).on('click', '.remove-narrator', function () {
            const narratorsList = $(this).closest('.narrators-list');
            const rowCount = narratorsList.find('.narrator-row').length;

            if (rowCount > 1) {
                $(this).closest('.narrator-row').remove();
                updateNarratorIndexes(narratorsList);
            } else {
                // Reset the row instead of deleting
                const row = $(this).closest('.narrator-row');
                row.find('.role-selector').val('');
                row.find('.narrator-select').prop('disabled', true).empty().append('<option value="">-- اختر النوع أولاً --</option>');
                row.find('input[type="hidden"]').val('');
            }
        });

        function updateNarratorIndexes(narratorsList) {
            const chainIndex = narratorsList.data('chain-index');
            narratorsList.find('.narrator-row').each(function (index) {
                $(this).find('select[name], input[name]').each(function () {
                    const name = $(this).attr('name');
                    if (name) {
                        const newName = name.replace(/\[narrators\]\[\d+\]/, `[narrators][${index}]`);
                        $(this).attr('name', newName);
                    }
                });
                $(this).find('.role-selector').attr('data-narrator-index', index);
            });
        }
    // ============ إدارة قسم الشرح المنظم ============
    
    // Toggle الشرح المنظم
    $('#enableStructuredSharh').on('change', function() {
        if ($(this).is(':checked')) {
            $('#structuredSharhSection').slideDown();
        } else {
            $('#structuredSharhSection').slideUp();
        }
    });

    // إضافة مانع جديد
    $('#add-obstacle').on('click', function() {
        const container = $('#obstacles-container');
        const currentCount = container.find('.obstacle-row').length;
        const newRow = `
            <div class="obstacle-row mb-2 p-2 border rounded bg-light">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="sharh_obstacles[${currentCount}][title]" class="form-control form-control-sm" placeholder="عنوان المانع">
                    </div>
                    <div class="col-md-7">
                        <input type="text" name="sharh_obstacles[${currentCount}][description]" class="form-control form-control-sm" placeholder="الوصف والتفصيل">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-sm btn-danger btn-block remove-obstacle"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        `;
        container.append(newRow);
    });

    // حذف مانع
    $(document).on('click', '.remove-obstacle', function() {
        const container = $('#obstacles-container');
        if (container.find('.obstacle-row').length > 1) {
            $(this).closest('.obstacle-row').remove();
            updateObstacleIndexes();
        } else {
            // مسح المحتوى بدلاً من الحذف
            $(this).closest('.obstacle-row').find('input').val('');
        }
    });

    function updateObstacleIndexes() {
        $('#obstacles-container .obstacle-row').each(function(index) {
            $(this).find('input[name]').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    const newName = name.replace(/sharh_obstacles\[\d+\]/, `sharh_obstacles[${index}]`);
                    $(this).attr('name', newName);
                }
            });
        });
    }

    // إضافة أمر جديد
    $('#add-command').on('click', function() {
        const container = $('#commands-container');
        const currentCount = container.find('.command-row').length;
        const newRow = `
            <div class="command-row mb-2 p-2 border rounded bg-light">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="sharh_commands[${currentCount}][title]" class="form-control form-control-sm" placeholder="العنوان">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="sharh_commands[${currentCount}][ruling]" class="form-control form-control-sm" placeholder="الحكم">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="sharh_commands[${currentCount}][explanation]" class="form-control form-control-sm" placeholder="التوضيح">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-sm btn-danger btn-block remove-command"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        `;
        container.append(newRow);
    });

    // حذف أمر
    $(document).on('click', '.remove-command', function() {
        const container = $('#commands-container');
        if (container.find('.command-row').length > 1) {
            $(this).closest('.command-row').remove();
            updateCommandIndexes();
        } else {
            // مسح المحتوى بدلاً من الحذف
            $(this).closest('.command-row').find('input').val('');
        }
    });

    function updateCommandIndexes() {
        $('#commands-container .command-row').each(function(index) {
            $(this).find('input[name]').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    const newName = name.replace(/sharh_commands\[\d+\]/, `sharh_commands[${index}]`);
                    $(this).attr('name', newName);
                }
            });
        });
    }
});
</script>
@stop


