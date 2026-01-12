@extends('adminlte::page')

@section('title', 'إضافة مستخدم جديد')

@section('content_header')
    <h1>إضافة مستخدم جديد</h1>
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

    <form action="{{ route('dashboard.users.store') }}" method="POST">
        @csrf
        
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">بيانات المستخدم</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>الاسم <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" 
                           value="{{ old('name') }}" 
                           placeholder="الاسم الكامل"
                           required>
                </div>

                <div class="form-group">
                    <label>البريد الإلكتروني <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" 
                           value="{{ old('email') }}" 
                           placeholder="example@domain.com"
                           required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>كلمة المرور <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" 
                                   placeholder="8 أحرف على الأقل"
                                   required>
                            <small class="form-text text-muted">يجب أن تكون 8 أحرف على الأقل</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>تأكيد كلمة المرور <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" 
                                   placeholder="أعد إدخال كلمة المرور"
                                   required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> حفظ
                </button>
                <a href="{{ route('dashboard.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            </div>
        </div>
    </form>
@stop
