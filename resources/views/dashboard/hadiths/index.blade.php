@extends('adminlte::page')

@section('title', 'إدارة الأحاديث')

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1>إدارة الأحاديث</h1>
    </div>
    <div class="col-sm-6">
        <div class="float-left">
            <a href="{{ route('dashboard.hadiths.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> إضافة حديث جديد
            </a>
        </div>
    </div>
</div>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session('success') }}
    </div>
@endif

<!-- فلاتر البحث -->
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">بحث وفلترة</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('dashboard.hadiths.index') }}">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>بحث في النص</label>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                            placeholder="ابحث في نص الحديث أو الرقم">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>الكتاب</label>
                        <select name="book_id" class="form-control">
                            <option value="">جميع الكتب</option>
                            @foreach($books as $book)
                                <option value="{{ $book->id }}" {{ request('book_id') == $book->id ? 'selected' : '' }}>
                                    {{ $book->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>الدرجة</label>
                        <select name="grade" class="form-control">
                            <option value="">جميع الدرجات</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade }}" {{ request('grade') == $grade ? 'selected' : '' }}>
                                    {{ $grade }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i> بحث
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- جدول الأحاديث -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list"></i> قائمة الأحاديث
            <span class="badge badge-info">{{ $hadiths->total() }}</span>
        </h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th style="width: 80px">#</th>
                    <th>نص الحديث</th>
                    <th style="width: 150px">الراوي</th>
                    <th style="width: 150px">الكتاب</th>
                    <th style="width: 100px">الدرجة</th>
                    <th style="width: 120px">المصادر</th>
                    <th style="width: 150px">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hadiths as $hadith)
                            <tr>
                                <td>
                                    <strong>{{ $hadith->number_in_book }}</strong>
                                </td>
                                <td>
                                    <div style="max-height: 60px; overflow: hidden;">
                                        {{ Str::limit($hadith->content, 120) }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">
                                        {{ $hadith->narrator?->name ?? 'غير محدد' }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $hadith->book?->name ?? 'غير محدد' }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-{{ 
                                                $hadith->grade === 'صحيح' ? 'success' :
                    ($hadith->grade === 'حسن' ? 'info' :
                        ($hadith->grade === 'ضعيف' ? 'warning' : 'danger')) 
                                            }}">
                                        {{ $hadith->grade }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-light">
                                        {{ $hadith->sources->count() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('dashboard.hadiths.show', $hadith) }}" class="btn btn-info" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('dashboard.hadiths.edit', $hadith) }}" class="btn btn-warning"
                                            title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $hadith->id }})"
                                            title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <form id="delete-form-{{ $hadith->id }}"
                                        action="{{ route('dashboard.hadiths.destroy', $hadith) }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">لا توجد أحاديث مضافة بعد</p>
                            <a href="{{ route('dashboard.hadiths.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> إضافة أول حديث
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($hadiths->hasPages())
        <div class="card-footer clearfix">
            <div class="float-left">
                <small class="text-muted">
                    عرض {{ $hadiths->firstItem() }} - {{ $hadiths->lastItem() }} من {{ $hadiths->total() }} نتيجة
                </small>
            </div>
            <div class="float-right">
                {{ $hadiths->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>
@stop

@section('js')
<script>
    function confirmDelete(id) {
        if (confirm('هل أنت متأكد من حذف هذا الحديث؟ لا يمكن التراجع عن هذا الإجراء.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@stop