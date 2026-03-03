@extends('adminlte::page')

@section('title', 'تعديل ' . $user->name)

@section('content_header')
<h1>تعديل: {{ $user->name }}</h1>
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

<form action="{{ route('dashboard.users.update', $user) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">تعديل البيانات</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label>الاسم <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="form-group">
                <label>البريد الإلكتروني <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>

            <hr>
            <h5>تغيير كلمة المرور</h5>
            <p class="text-muted">اترك الحقول التالية فارغة إذا كنت لا تريد تغيير كلمة المرور</p>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>كلمة المرور الجديدة</label>
                        <input type="password" name="password" class="form-control" placeholder="8 أحرف على الأقل">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>تأكيد كلمة المرور</label>
                        <input type="password" name="password_confirmation" class="form-control"
                            placeholder="أعد إدخال كلمة المرور">
                    </div>
                </div>
            </div>

            <hr>
            <div class="form-group">
                <label>الدور <span class="text-danger">*</span></label>
                <select name="role" class="form-control" required>
                    <option value="data_entry" {{ old('role', $user->role) === 'data_entry' ? 'selected' : '' }}>مدخل
                        بيانات</option>
                    <option value="reviewer" {{ old('role', $user->role) === 'reviewer' ? 'selected' : '' }}>مراجع
                    </option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>مدير النظام
                    </option>
                </select>
                <small class="form-text text-muted">
                    <strong>مدخل بيانات:</strong> إدخال أحاديث فقط &bull;
                    <strong>مراجع:</strong> مراجعة واعتماد &bull;
                    <strong>مدير:</strong> كل الصلاحيات
                </small>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-save"></i> حفظ التعديلات
            </button>
            <a href="{{ route('dashboard.users.show', $user) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> إلغاء
            </a>
        </div>
    </div>
</form>
@stop