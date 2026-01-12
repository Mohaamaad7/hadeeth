@extends('adminlte::page')

@section('title', 'إدارة الكتب والأبواب')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>إدارة الكتب والأبواب</h1>
        </div>
        <div class="col-sm-6">
            <div class="float-left">
                <a href="{{ route('dashboard.books.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> إضافة كتاب/باب جديد
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

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            @foreach($errors->all() as $error)
                <p class="mb-0">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- فلاتر البحث -->
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">بحث وفلترة</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard.books.index') }}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>بحث في اسم الكتاب/الباب</label>
                            <input type="text" name="search" class="form-control" 
                                   value="{{ request('search') }}" 
                                   placeholder="اكتب اسم الكتاب أو الباب">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>النوع</label>
                            <select name="type" class="form-control">
                                <option value="">الكل</option>
                                <option value="main" {{ request('type') == 'main' ? 'selected' : '' }}>
                                    الكتب الرئيسية فقط
                                </option>
                                <option value="sub" {{ request('type') == 'sub' ? 'selected' : '' }}>
                                    الأبواب الفرعية فقط
                                </option>
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

    <!-- جدول الكتب -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> قائمة الكتب والأبواب
                <span class="badge badge-info">{{ $books->total() }}</span>
            </h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th style="width: 80px">الترتيب</th>
                        <th>الاسم</th>
                        <th style="width: 150px">النوع</th>
                        <th style="width: 150px">الكتاب الرئيسي</th>
                        <th style="width: 100px">الأحاديث</th>
                        <th style="width: 100px">الأبواب</th>
                        <th style="width: 150px">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $book)
                        <tr>
                            <td>
                                <span class="badge badge-secondary">{{ $book->sort_order }}</span>
                            </td>
                            <td>
                                @if($book->parent_id)
                                    <i class="fas fa-level-up-alt text-muted"></i>
                                @else
                                    <i class="fas fa-book text-primary"></i>
                                @endif
                                <strong>{{ $book->name }}</strong>
                            </td>
                            <td>
                                @if($book->parent_id)
                                    <span class="badge badge-info">باب فرعي</span>
                                @else
                                    <span class="badge badge-primary">كتاب رئيسي</span>
                                @endif
                            </td>
                            <td>
                                @if($book->parent)
                                    <small>{{ $book->parent->name }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-success">
                                    {{ $book->hadiths_count ?? 0 }}
                                </span>
                            </td>
                            <td>
                                @if(!$book->parent_id)
                                    <span class="badge badge-warning">
                                        {{ $book->children->count() }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('dashboard.books.show', $book) }}" 
                                       class="btn btn-info" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('dashboard.books.edit', $book) }}" 
                                       class="btn btn-warning" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-danger" 
                                            onclick="confirmDelete({{ $book->id }})"
                                            title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <form id="delete-form-{{ $book->id }}" 
                                      action="{{ route('dashboard.books.destroy', $book) }}" 
                                      method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">لا توجد كتب مضافة بعد</p>
                                <a href="{{ route('dashboard.books.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> إضافة أول كتاب
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($books->hasPages())
            <div class="card-footer">
                {{ $books->links() }}
            </div>
        @endif
    </div>
@stop

@section('js')
<script>
function confirmDelete(id) {
    if (confirm('هل أنت متأكد من حذف هذا الكتاب/الباب؟\n\nملاحظة: لا يمكن حذف كتاب يحتوي على أحاديث أو أبواب فرعية.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@stop
