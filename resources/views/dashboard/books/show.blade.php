@extends('adminlte::page')

@section('title', $book->name)

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>{{ $book->name }}</h1>
        </div>
        <div class="col-sm-6">
            <div class="float-left">
                <a href="{{ route('dashboard.books.edit', $book) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <a href="{{ route('dashboard.books.index') }}" class="btn btn-secondary">
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
            <!-- معلومات الكتاب -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> المعلومات
                    </h3>
                </div>
                <div class="card-body">
                    <dl>
                        <dt>النوع:</dt>
                        <dd>
                            @if($book->parent_id)
                                <span class="badge badge-info">باب فرعي</span>
                            @else
                                <span class="badge badge-primary">كتاب رئيسي</span>
                            @endif
                        </dd>

                        @if($book->parent)
                            <dt>الكتاب الرئيسي:</dt>
                            <dd>
                                <a href="{{ route('dashboard.books.show', $book->parent) }}">
                                    {{ $book->parent->name }}
                                </a>
                            </dd>
                        @endif

                        <dt>رقم الترتيب:</dt>
                        <dd>{{ $book->sort_order }}</dd>

                        <dt>عدد الأحاديث:</dt>
                        <dd>
                            <span class="badge badge-success">{{ $book->hadiths->count() }}</span>
                        </dd>

                        @if(!$book->parent_id)
                            <dt>عدد الأبواب:</dt>
                            <dd>
                                <span class="badge badge-warning">{{ $book->children->count() }}</span>
                            </dd>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- الإجراءات -->
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs"></i> الإجراءات
                    </h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('dashboard.books.edit', $book) }}" class="btn btn-warning btn-block">
                        <i class="fas fa-edit"></i> تعديل
                    </a>
                    <button type="button" class="btn btn-danger btn-block" onclick="confirmDelete()">
                        <i class="fas fa-trash"></i> حذف
                    </button>
                    <a href="{{ route('dashboard.books.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-list"></i> العودة للقائمة
                    </a>
                </div>
            </div>

            <form id="delete-form" action="{{ route('dashboard.books.destroy', $book) }}" 
                  method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>

        <div class="col-md-8">
            @if(!$book->parent_id && $book->children->count() > 0)
                <!-- الأبواب الفرعية -->
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-folder-open"></i> الأبواب الفرعية
                            <span class="badge badge-light">{{ $book->children->count() }}</span>
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 60px">الترتيب</th>
                                    <th>اسم الباب</th>
                                    <th style="width: 100px">الأحاديث</th>
                                    <th style="width: 100px">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($book->children as $child)
                                    <tr>
                                        <td>{{ $child->sort_order }}</td>
                                        <td>{{ $child->name }}</td>
                                        <td>
                                            <span class="badge badge-success">
                                                {{ $child->hadiths_count }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('dashboard.books.show', $child) }}" 
                                               class="btn btn-xs btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('dashboard.books.edit', $child) }}" 
                                               class="btn btn-xs btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- الأحاديث -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-book-open"></i> الأحاديث
                        <span class="badge badge-light">{{ $book->hadiths->count() }}</span>
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('dashboard.hadiths.create') }}?book_id={{ $book->id }}" 
                           class="btn btn-sm btn-success">
                            <i class="fas fa-plus"></i> إضافة حديث
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($book->hadiths->count() > 0)
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 60px">#</th>
                                    <th>النص</th>
                                    <th style="width: 100px">الدرجة</th>
                                    <th style="width: 100px">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($book->hadiths as $hadith)
                                    <tr>
                                        <td>{{ $hadith->number_in_book }}</td>
                                        <td>{{ Str::limit($hadith->content, 80) }}</td>
                                        <td>
                                            <span class="badge badge-{{ 
                                                $hadith->grade === 'صحيح' ? 'success' : 
                                                ($hadith->grade === 'حسن' ? 'info' : 'warning') 
                                            }}">
                                                {{ $hadith->grade }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('dashboard.hadiths.show', $hadith) }}" 
                                               class="btn btn-xs btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($book->hadiths->count() >= 10)
                            <div class="card-footer text-center">
                                <a href="{{ route('dashboard.hadiths.index') }}?book_id={{ $book->id }}" 
                                   class="btn btn-sm btn-primary">
                                    عرض جميع الأحاديث
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>لا توجد أحاديث في هذا الكتاب/الباب بعد</p>
                            <a href="{{ route('dashboard.hadiths.create') }}?book_id={{ $book->id }}" 
                               class="btn btn-success">
                                <i class="fas fa-plus"></i> إضافة أول حديث
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
function confirmDelete() {
    if (confirm('هل أنت متأكد من حذف هذا الكتاب/الباب؟')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@stop
