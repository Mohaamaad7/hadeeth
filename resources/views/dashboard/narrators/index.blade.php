@extends('adminlte::page')

@section('title', 'الرواة')

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1>الرواة</h1>
    </div>
    <div class="col-sm-6">
        <div class="float-left">
            <a href="{{ route('dashboard.narrators.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> إضافة راوي جديد
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
            <i class="fas fa-search"></i> البحث والتصفية
        </h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('dashboard.narrators.index') }}">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <input type="text" name="search" class="form-control"
                            placeholder="ابحث في الاسم، السيرة، أو الدرجة..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <select name="grade_status" class="form-control">
                            <option value="">-- جميع الدرجات --</option>
                            <option value="صحابي" {{ request('grade_status') === 'صحابي' ? 'selected' : '' }}>صحابي
                            </option>
                            <option value="ثقة" {{ request('grade_status') === 'ثقة' ? 'selected' : '' }}>ثقة</option>
                            <option value="صدوق" {{ request('grade_status') === 'صدوق' ? 'selected' : '' }}>صدوق</option>
                            <option value="ضعيف" {{ request('grade_status') === 'ضعيف' ? 'selected' : '' }}>ضعيف</option>
                            <option value="متروك" {{ request('grade_status') === 'متروك' ? 'selected' : '' }}>متروك
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> بحث
                    </button>
                    <a href="{{ route('dashboard.narrators.index') }}" class="btn btn-secondary">
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
            <i class="fas fa-users"></i> قائمة الرواة
            <span class="badge badge-info">{{ $narrators->total() }}</span>
        </h3>
    </div>
    <div class="card-body p-0">
        @if($narrators->count() > 0)
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px">#</th>
                        <th>الاسم</th>
                        <th style="width: 150px">الدرجة</th>
                        <th style="width: 100px" class="text-center">الأحاديث</th>
                        <th style="width: 150px" class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($narrators as $narrator)
                        <tr>
                            <td>{{ $narrator->id }}</td>
                            <td>
                                <strong>{{ $narrator->name }}</strong>
                                @if($narrator->bio)
                                    <br>
                                    <small class="text-muted">
                                        {{ Str::limit($narrator->bio, 60) }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($narrator->grade_status)
                                    <span class="badge" style="background-color: {{ $narrator->color_code }}; color: white;">
                                        {{ $narrator->grade_status }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-primary">
                                    {{ $narrator->hadiths_count }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('dashboard.narrators.show', $narrator) }}" class="btn btn-sm btn-info"
                                        title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('dashboard.narrators.edit', $narrator) }}" class="btn btn-sm btn-warning"
                                        title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="confirmDelete({{ $narrator->id }})" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $narrator->id }}"
                                    action="{{ route('dashboard.narrators.destroy', $narrator) }}" method="POST"
                                    style="display: none;">
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
                <p>لا توجد رواة بعد</p>
                <a href="{{ route('dashboard.narrators.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> إضافة أول راوي
                </a>
            </div>
        @endif
    </div>
    @if($narrators->hasPages())
        <div class="card-footer clearfix">
            <div class="float-left">
                <small class="text-muted">
                    عرض {{ $narrators->firstItem() }} - {{ $narrators->lastItem() }} من {{ $narrators->total() }} نتيجة
                </small>
            </div>
            <div class="float-right">
                {{ $narrators->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>
@stop

@section('js')
<script>
    function confirmDelete(id) {
        if (confirm('هل أنت متأكد من حذف هذا الراوي؟')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@stop