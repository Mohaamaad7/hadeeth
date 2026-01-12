@extends('adminlte::page')

@section('title', $user->name)

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>{{ $user->name }}</h1>
        </div>
        <div class="col-sm-6">
            <div class="float-left">
                <a href="{{ route('dashboard.users.edit', $user) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <a href="{{ route('dashboard.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i> رجوع
                </a>
            </div>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <!-- بطاقة معلومات المستخدم -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <div class="rounded-circle bg-primary d-inline-flex justify-content-center align-items-center"
                             style="width: 100px; height: 100px; font-size: 48px; color: white;">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>

                    <h3 class="profile-username text-center mt-3">{{ $user->name }}</h3>

                    @if($user->id === auth()->id())
                        <p class="text-center">
                            <span class="badge badge-success badge-lg" style="font-size: 14px; padding: 8px 12px;">
                                حسابك الشخصي
                            </span>
                        </p>
                    @endif

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>البريد الإلكتروني</b>
                            <a class="float-right">{{ $user->email }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>تاريخ التسجيل</b>
                            <a class="float-right text-muted">
                                {{ $user->created_at->format('Y-m-d') }}
                            </a>
                        </li>
                        <li class="list-group-item">
                            <b>منذ</b>
                            <a class="float-right text-muted">
                                {{ $user->created_at->diffForHumans() }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- بطاقة الإجراءات -->
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs"></i> الإجراءات
                    </h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('dashboard.users.edit', $user) }}" class="btn btn-warning btn-block">
                        <i class="fas fa-edit"></i> تعديل البيانات
                    </a>
                    @if($user->id !== auth()->id())
                        <button type="button" class="btn btn-danger btn-block" onclick="confirmDelete()">
                            <i class="fas fa-trash"></i> حذف المستخدم
                        </button>
                    @else
                        <button type="button" class="btn btn-secondary btn-block" disabled>
                            <i class="fas fa-ban"></i> لا يمكن حذف حسابك
                        </button>
                    @endif
                    <a href="{{ route('dashboard.users.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-list"></i> العودة للقائمة
                    </a>
                </div>
            </div>

            <form id="delete-form" action="{{ route('dashboard.users.destroy', $user) }}" 
                  method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>

        <div class="col-md-8">
            <!-- معلومات إضافية -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> معلومات الحساب
                    </h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">الاسم الكامل:</dt>
                        <dd class="col-sm-8">{{ $user->name }}</dd>

                        <dt class="col-sm-4">البريد الإلكتروني:</dt>
                        <dd class="col-sm-8">{{ $user->email }}</dd>

                        <dt class="col-sm-4">حالة التفعيل:</dt>
                        <dd class="col-sm-8">
                            @if($user->email_verified_at)
                                <span class="badge badge-success">مفعّل</span>
                                <small class="text-muted">
                                    ({{ $user->email_verified_at->format('Y-m-d') }})
                                </small>
                            @else
                                <span class="badge badge-warning">غير مفعّل</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">تاريخ الإنشاء:</dt>
                        <dd class="col-sm-8">
                            {{ $user->created_at->format('Y-m-d h:i A') }}
                            <small class="text-muted">({{ $user->created_at->diffForHumans() }})</small>
                        </dd>

                        <dt class="col-sm-4">آخر تحديث:</dt>
                        <dd class="col-sm-8">
                            {{ $user->updated_at->format('Y-m-d h:i A') }}
                            <small class="text-muted">({{ $user->updated_at->diffForHumans() }})</small>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
function confirmDelete() {
    if (confirm('هل أنت متأكد من حذف هذا المستخدم؟')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@stop
