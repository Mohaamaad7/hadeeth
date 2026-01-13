@extends('adminlte::page')

@section('title', 'المستخدمين')

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1>المستخدمين</h1>
    </div>
    <div class="col-sm-6">
        <div class="float-left">
            <a href="{{ route('dashboard.users.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> إضافة مستخدم جديد
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

@if(session('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session('error') }}
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-search"></i> البحث
        </h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('dashboard.users.index') }}">
            <div class="row">
                <div class="col-md-9">
                    <div class="form-group">
                        <input type="text" name="search" class="form-control"
                            placeholder="ابحث في الاسم أو البريد الإلكتروني..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> بحث
                    </button>
                    <a href="{{ route('dashboard.users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> إعادة تعيين
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-users"></i> قائمة المستخدمين
            <span class="badge badge-info">{{ $users->total() }}</span>
        </h3>
    </div>
    <div class="card-body p-0">
        @if($users->count() > 0)
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px">#</th>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th style="width: 150px">تاريخ التسجيل</th>
                        <th style="width: 150px" class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <strong>{{ $user->name }}</strong>
                                @if($user->id === auth()->id())
                                    <span class="badge badge-success">أنت</span>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <small class="text-muted">
                                    {{ $user->created_at->diffForHumans() }}
                                </small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('dashboard.users.show', $user) }}" class="btn btn-sm btn-info"
                                        title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('dashboard.users.edit', $user) }}" class="btn btn-sm btn-warning"
                                        title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $user->id }})"
                                            title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                                <form id="delete-form-{{ $user->id }}" action="{{ route('dashboard.users.destroy', $user) }}"
                                    method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="p-5 text-center text-muted">
                <i class="fas fa-users fa-3x mb-3"></i>
                <p>لا توجد مستخدمين</p>
            </div>
        @endif
    </div>
    @if($users->hasPages())
        <div class="card-footer clearfix">
            <div class="float-left">
                <small class="text-muted">
                    عرض {{ $users->firstItem() }} - {{ $users->lastItem() }} من {{ $users->total() }} نتيجة
                </small>
            </div>
            <div class="float-right">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>
@stop

@section('js')
<script>
    function confirmDelete(id) {
        if (confirm('هل أنت متأكد من حذف هذا المستخدم؟')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@stop