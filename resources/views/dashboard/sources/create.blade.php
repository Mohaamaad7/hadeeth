@extends('adminlte::page')

@section('title', 'إضافة مصدر جديد')

@section('content_header')
    <h1>إضافة مصدر جديد</h1>
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

    <form action="{{ route('dashboard.sources.store') }}" method="POST">
        @csrf
        
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">بيانات المصدر</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>الرمز <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" 
                                   value="{{ old('code') }}" 
                                   placeholder="مثال: خ، م، د"
                                   maxlength="10"
                                   required>
                            <small class="form-text text-muted">رمز مختصر فريد للمصدر</small>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group">
                            <label>اسم المصدر <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" 
                                   value="{{ old('name') }}" 
                                   placeholder="مثال: صحيح البخاري، سنن أبي داود"
                                   required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>نوع المصدر</label>
                    <select name="type" class="form-control">
                        <option value="">-- اختر النوع --</option>
                        <option value="كتب الصحيح" {{ old('type') === 'كتب الصحيح' ? 'selected' : '' }}>كتب الصحيح</option>
                        <option value="كتب السنن" {{ old('type') === 'كتب السنن' ? 'selected' : '' }}>كتب السنن</option>
                        <option value="كتب المسانيد" {{ old('type') === 'كتب المسانيد' ? 'selected' : '' }}>كتب المسانيد</option>
                        <option value="كتب المعاجم" {{ old('type') === 'كتب المعاجم' ? 'selected' : '' }}>كتب المعاجم</option>
                        <option value="كتب التاريخ" {{ old('type') === 'كتب التاريخ' ? 'selected' : '' }}>كتب التاريخ</option>
                        <option value="كتب الأجزاء" {{ old('type') === 'كتب الأجزاء' ? 'selected' : '' }}>كتب الأجزاء</option>
                    </select>
                    <small class="form-text text-muted">تصنيف المصدر (اختياري)</small>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>ملاحظة:</strong> الرمز يجب أن يكون فريداً ويُستخدم في نظام Parser الأحاديث
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> حفظ
                </button>
                <a href="{{ route('dashboard.sources.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            </div>
        </div>
    </form>
@stop
