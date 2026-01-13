@extends('adminlte::page')

@section('title', 'المصادر')

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1>المصادر</h1>
    </div>
    <div class="col-sm-6">
        <div class="float-left">
            <a href="{{ route('dashboard.sources.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> إضافة مصدر جديد
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
        <form method="GET" action="{{ route('dashboard.sources.index') }}">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <input type="text" name="search" class="form-control"
                            placeholder="ابحث في الاسم، الرمز، أو النوع..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <select name="type" class="form-control">
                            <option value="">-- جميع الأنواع --</option>
                            <option value="كتب الصحيح" {{ request('type') === 'كتب الصحيح' ? 'selected' : '' }}>كتب الصحيح
                            </option>
                            <option value="كتب السنن" {{ request('type') === 'كتب السنن' ? 'selected' : '' }}>كتب السنن
                            </option>
                            <option value="كتب المسانيد" {{ request('type') === 'كتب المسانيد' ? 'selected' : '' }}>كتب
                                المسانيد</option>
                            <option value="كتب المعاجم" {{ request('type') === 'كتب المعاجم' ? 'selected' : '' }}>كتب
                                المعاجم</option>
                            <option value="كتب التاريخ" {{ request('type') === 'كتب التاريخ' ? 'selected' : '' }}>كتب
                                التاريخ</option>
                            <option value="كتب الأجزاء" {{ request('type') === 'كتب الأجزاء' ? 'selected' : '' }}>كتب
                                الأجزاء</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> بحث
                    </button>
                    <a href="{{ route('dashboard.sources.index') }}" class="btn btn-secondary">
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
            <i class="fas fa-database"></i> قائمة المصادر
            <span class="badge badge-info">{{ $sources->total() }}</span>
        </h3>
    </div>
    <div class="card-body p-0">
        @if($sources->count() > 0)
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th style="width: 80px">الرمز</th>
                        <th>اسم المصدر</th>
                        <th style="width: 200px">النوع</th>
                        <th style="width: 100px" class="text-center">الأحاديث</th>
                        <th style="width: 150px" class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sources as $source)
                        <tr>
                            <td>
                                <span class="badge badge-primary badge-lg">{{ $source->code }}</span>
                            </td>
                            <td>
                                <strong>{{ $source->name }}</strong>
                            </td>
                            <td>
                                @if($source->type)
                                    <span class="badge badge-info">{{ $source->type }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-success">
                                    {{ $source->hadiths_count }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('dashboard.sources.show', $source) }}" class="btn btn-sm btn-info"
                                        title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('dashboard.sources.edit', $source) }}" class="btn btn-sm btn-warning"
                                        title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="confirmDelete({{ $source->id }})" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $source->id }}"
                                    action="{{ route('dashboard.sources.destroy', $source) }}" method="POST"
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
                <i class="fas fa-database fa-3x mb-3"></i>
                <p>لا توجد مصادر بعد</p>
                <a href="{{ route('dashboard.sources.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> إضافة أول مصدر
                </a>
            </div>
        @endif
    </div>
    @if($sources->hasPages())
        <div class="card-footer clearfix">
            <div class="float-left">
                <small class="text-muted">
                    عرض {{ $sources->firstItem() }} - {{ $sources->lastItem() }} من {{ $sources->total() }} نتيجة
                </small>
            </div>
            <div class="float-right">
                {{ $sources->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>
@stop

@section('js')
<script>
    function confirmDelete(id) {
        if (confirm('هل أنت متأكد من حذف هذا المصدر؟')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@stop