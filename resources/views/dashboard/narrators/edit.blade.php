@extends('adminlte::page')

@section('title', 'تعديل ' . $narrator->name)

@section('content_header')
<h1>تعديل: {{ $narrator->name }}</h1>
@stop

@section('content')
@if($errors->any())
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('dashboard.narrators.update', $narrator) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">تعديل البيانات</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label>الاسم <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $narrator->name) }}" required>
            </div>

            <div class="form-group">
                <label>اسم الشهرة</label>
                <input type="text" name="fame_name" class="form-control"
                    value="{{ old('fame_name', $narrator->fame_name) }}" placeholder="مثال: عائشة، جابر، ابن عمر">
                <small class="form-text text-muted">
                    الاسم المختصر المشهور به في كتب الحديث
                </small>
            </div>

            <div class="form-group">
                <label>السيرة الذاتية</label>
                <textarea name="bio" class="form-control" rows="5">{{ old('bio', $narrator->bio) }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>الرتبة <span class="text-danger">*</span></label>
                        <select name="rank" class="form-control" id="rankSelect">
                            <option value="">-- اختر الرتبة --</option>
                            @foreach($ranks as $rank)
                                <option value="{{ $rank->value }}"
                                    {{ old('rank', $narrator->rank?->value) === $rank->value ? 'selected' : '' }}
                                    data-needs-judgment="{{ $rank->needsJudgment() ? '1' : '0' }}">
                                    {{ $rank->label() }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">صحابي، صحابية، تابعي، أو راوي</small>
                    </div>
                </div>
                <div class="col-md-6" id="judgmentGroup" style="display: none;">
                    <div class="form-group">
                        <label>حكم العلماء</label>
                        <select name="judgment" class="form-control" id="judgmentSelect">
                            <option value="">-- اختر حكم العلماء --</option>
                            @foreach($judgments as $judgment)
                                <option value="{{ $judgment->value }}"
                                    {{ old('judgment', $narrator->judgment?->value) === $judgment->value ? 'selected' : '' }}
                                    data-color="{{ $judgment->color() }}">
                                    {{ $judgment->label() }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">حكم علماء الجرح والتعديل على هذا الراوي</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- الأسماء البديلة --}}
    <div class="card card-outline card-info">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-exchange-alt"></i> الأسماء البديلة
            </h3>
            <small class="text-muted mr-2">أخطاء نساخ، تهجئات مختلفة، ألقاب — تساعد في البحث الذكي</small>
        </div>
        <div class="card-body" id="alternativesContainer">
            @forelse($narrator->alternatives as $i => $alt)
                <div class="row alternative-row mb-2 align-items-center">
                    <div class="col-md-4">
                        <input type="text" name="alternatives[{{ $i }}][alternative_name]"
                            class="form-control form-control-sm"
                            value="{{ old("alternatives.{$i}.alternative_name", $alt->alternative_name) }}"
                            placeholder="الاسم البديل">
                    </div>
                    <div class="col-md-3">
                        <select name="alternatives[{{ $i }}][type]" class="form-control form-control-sm">
                            <option value="misspelling" {{ $alt->type?->value === 'misspelling' ? 'selected' : '' }}>خطأ نساخ
                            </option>
                            <option value="variation" {{ $alt->type?->value === 'variation' ? 'selected' : '' }}>تهجئة بديلة
                            </option>
                            <option value="title" {{ $alt->type?->value === 'title' ? 'selected' : '' }}>لقب</option>
                            <option value="kunya" {{ $alt->type?->value === 'kunya' ? 'selected' : '' }}>كنية</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="alternatives[{{ $i }}][notes]" class="form-control form-control-sm"
                            value="{{ old("alternatives.{$i}.notes", $alt->notes) }}" placeholder="ملاحظة (اختياري)">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-sm btn-danger remove-alt" title="حذف">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-muted" id="noAltsMsg">لا توجد أسماء بديلة. اضغط "إضافة" لإضافة اسم.</p>
            @endforelse
        </div>
        <div class="card-footer">
            <button type="button" class="btn btn-sm btn-info" id="addAltBtn">
                <i class="fas fa-plus"></i> إضافة اسم بديل
            </button>
        </div>
    </div>

    <div class="mb-3">
        <button type="submit" class="btn btn-warning">
            <i class="fas fa-save"></i> حفظ التعديلات
        </button>
        <a href="{{ route('dashboard.narrators.show', $narrator) }}" class="btn btn-secondary">
            <i class="fas fa-times"></i> إلغاء
        </a>
    </div>
</form>
@stop

@section('js')
<script>
    $(function () {
        // ===== الرتبة / حكم العلماء =====
        function toggleJudgment() {
            const selected = $('#rankSelect option:selected');
            const needsJudgment = selected.data('needs-judgment');
            if (needsJudgment == 1) {
                $('#judgmentGroup').slideDown(200);
            } else {
                $('#judgmentGroup').slideUp(200);
                $('#judgmentSelect').val('');
            }
        }

        $('#rankSelect').on('change', toggleJudgment);
        toggleJudgment(); // تطبيق الحالة الأولية

        // ===== الأسماء البديلة =====
        let altIndex = {{ $narrator->alternatives->count() }};

        $('#addAltBtn').click(function () {
            $('#noAltsMsg').hide();
            const row = `
        <div class="row alternative-row mb-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="alternatives[${altIndex}][alternative_name]" class="form-control form-control-sm" placeholder="الاسم البديل" required>
            </div>
            <div class="col-md-3">
                <select name="alternatives[${altIndex}][type]" class="form-control form-control-sm">
                    <option value="misspelling">خطأ نساخ</option>
                    <option value="variation">تهجئة بديلة</option>
                    <option value="title">لقب</option>
                    <option value="kunya">كنية</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="alternatives[${altIndex}][notes]" class="form-control form-control-sm" placeholder="ملاحظة (اختياري)">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-danger remove-alt" title="حذف">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>`;
            $('#alternativesContainer').append(row);
            altIndex++;
        });

        $(document).on('click', '.remove-alt', function () {
            $(this).closest('.alternative-row').remove();
            if ($('.alternative-row').length === 0) {
                $('#noAltsMsg').show();
            }
        });
    });
</script>
@stop