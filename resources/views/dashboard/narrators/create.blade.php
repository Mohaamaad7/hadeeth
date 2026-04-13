@extends('adminlte::page')

@section('title', 'إضافة راوي جديد')

@section('content_header')
<h1>إضافة راوي جديد</h1>
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

<form action="{{ route('dashboard.narrators.store') }}" method="POST">
    @csrf

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">بيانات الراوي</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label>الاسم <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                    placeholder="مثال: أبو هريرة، عبد الله بن عمر" required>
            </div>

            <div class="form-group">
                <label>اسم الشهرة</label>
                <input type="text" name="fame_name" class="form-control" value="{{ old('fame_name') }}"
                    placeholder="مثال: عائشة، جابر، ابن عمر، أنس">
                <small class="form-text text-muted">
                    الاسم المختصر المشهور به الراوي في كتب الحديث. مثلاً: "جابر" بدلاً من "جابر بن عبد الله"
                </small>
            </div>

            <div class="form-group">
                <label>السيرة الذاتية</label>
                <textarea name="bio" class="form-control" rows="5"
                    placeholder="نبذة عن الراوي، حياته، وعلمه...">{{ old('bio') }}</textarea>
                <small class="form-text text-muted">اختياري - يمكن إضافة سيرة مختصرة للراوي</small>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>الرتبة <span class="text-danger">*</span></label>
                        <select name="rank" class="form-control" id="rankSelect">
                            <option value="">-- اختر الرتبة --</option>
                            @foreach($ranks as $rank)
                                <option value="{{ $rank->value }}"
                                    {{ old('rank') === $rank->value ? 'selected' : '' }}
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
                                    {{ old('judgment') === $judgment->value ? 'selected' : '' }}
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
        <div class="card-footer">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> حفظ
            </button>
            <a href="{{ route('dashboard.narrators.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> إلغاء
            </a>
        </div>
    </div>
</form>
@stop

@section('js')
<script>
    $(function () {
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

        // تطبيق الحالة الأولية (عند old values)
        toggleJudgment();
    });
</script>
@stop