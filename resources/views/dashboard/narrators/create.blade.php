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
                <label>السيرة الذاتية</label>
                <textarea name="bio" class="form-control" rows="5"
                    placeholder="نبذة عن الراوي، حياته، وعلمه...">{{ old('bio') }}</textarea>
                <small class="form-text text-muted">اختياري - يمكن إضافة سيرة مختصرة للراوي</small>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>الدرجة</label>
                        <select name="grade_status" class="form-control" id="gradeStatus">
                            <option value="">-- اختر الدرجة --</option>
                            <option value="صحابي" {{ old('grade_status') === 'صحابي' ? 'selected' : '' }}>صحابي</option>
                            <option value="ثقة" {{ old('grade_status') === 'ثقة' ? 'selected' : '' }}>ثقة</option>
                            <option value="صدوق" {{ old('grade_status') === 'صدوق' ? 'selected' : '' }}>صدوق</option>
                            <option value="ضعيف" {{ old('grade_status') === 'ضعيف' ? 'selected' : '' }}>ضعيف</option>
                            <option value="متروك" {{ old('grade_status') === 'متروك' ? 'selected' : '' }}>متروك</option>
                        </select>
                        <small class="form-text text-muted">حكم العلماء على الراوي</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>لون الدرجة</label>
                        <input type="color" name="color_code" class="form-control"
                            value="{{ old('color_code', '#22c55e') }}" style="height: 38px;">
                        <small class="form-text text-muted">اللون المستخدم لعرض الدرجة</small>
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