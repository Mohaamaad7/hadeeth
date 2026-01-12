@extends('adminlte::page')

@section('title', 'تعديل ' . $source->name)

@section('content_header')
    <h1>تعديل: {{ $source->name }}</h1>
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

    <form action="{{ route('dashboard.sources.update', $source) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">تعديل البيانات</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>الرمز <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" 
                                   value="{{ old('code', $source->code) }}" 
                                   maxlength="10"
                                   required>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group">
                            <label>اسم المصدر <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" 
                                   value="{{ old('name', $source->name) }}" 
                                   required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>نوع المصدر</label>
                    <select name="type" class="form-control">
                        <option value="">-- اختر النوع --</option>
                        <option value="كتب الصحيح" {{ old('type', $source->type) === 'كتب الصحيح' ? 'selected' : '' }}>كتب الصحيح</option>
                        <option value="كتب السنن" {{ old('type', $source->type) === 'كتب السنن' ? 'selected' : '' }}>كتب السنن</option>
                        <option value="كتب المسانيد" {{ old('type', $source->type) === 'كتب المسانيد' ? 'selected' : '' }}>كتب المسانيد</option>
                        <option value="كتب المعاجم" {{ old('type', $source->type) === 'كتب المعاجم' ? 'selected' : '' }}>كتب المعاجم</option>
                        <option value="كتب التاريخ" {{ old('type', $source->type) === 'كتب التاريخ' ? 'selected' : '' }}>كتب التاريخ</option>
                        <option value="كتب الأجزاء" {{ old('type', $source->type) === 'كتب الأجزاء' ? 'selected' : '' }}>كتب الأجزاء</option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save"></i> حفظ التعديلات
                </button>
                <a href="{{ route('dashboard.sources.show', $source) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            </div>
        </div>
    </form>
@stop
