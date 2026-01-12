@extends('adminlte::page')

@section('title', $source->name)

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>{{ $source->name }}</h1>
        </div>
        <div class="col-sm-6">
            <div class="float-left">
                <a href="{{ route('dashboard.sources.edit', $source) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <a href="{{ route('dashboard.sources.index') }}" class="btn btn-secondary">
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
            <!-- بطاقة معلومات المصدر -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <div class="rounded-circle bg-primary d-inline-flex justify-content-center align-items-center"
                             style="width: 100px; height: 100px; font-size: 32px; color: white; font-weight: bold;">
                            {{ $source->code }}
                        </div>
                    </div>

                    <h3 class="profile-username text-center mt-3">{{ $source->name }}</h3>

                    @if($source->type)
                        <p class="text-center">
                            <span class="badge badge-info badge-lg" style="font-size: 14px; padding: 8px 12px;">
                                {{ $source->type }}
                            </span>
                        </p>
                    @endif

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>الرمز</b>
                            <a class="float-right">
                                <span class="badge badge-primary">{{ $source->code }}</span>
                            </a>
                        </li>
                        <li class="list-group-item">
                            <b>عدد الأحاديث</b>
                            <a class="float-right">
                                <span class="badge badge-success">{{ $source->hadiths_count }}</span>
                            </a>
                        </li>
                        <li class="list-group-item">
                            <b>تاريخ الإضافة</b>
                            <a class="float-right text-muted">
                                {{ $source->created_at->diffForHumans() }}
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
                    <a href="{{ route('dashboard.sources.edit', $source) }}" class="btn btn-warning btn-block">
                        <i class="fas fa-edit"></i> تعديل
                    </a>
                    <button type="button" class="btn btn-danger btn-block" onclick="confirmDelete()">
                        <i class="fas fa-trash"></i> حذف
                    </button>
                    <a href="{{ route('dashboard.sources.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-list"></i> العودة للقائمة
                    </a>
                </div>
            </div>

            <form id="delete-form" action="{{ route('dashboard.sources.destroy', $source) }}" 
                  method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>

        <div class="col-md-8">
            <!-- الأحاديث المرتبطة -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-book-open"></i> الأحاديث في هذا المصدر
                        <span class="badge badge-light">{{ $source->hadiths_count }}</span>
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('dashboard.hadiths.create') }}" 
                           class="btn btn-sm btn-success">
                            <i class="fas fa-plus"></i> إضافة حديث
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($source->hadiths->count() > 0)
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 60px">#</th>
                                    <th>النص</th>
                                    <th style="width: 120px">الراوي</th>
                                    <th style="width: 100px">الدرجة</th>
                                    <th style="width: 100px">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($source->hadiths as $hadith)
                                    <tr>
                                        <td>{{ $hadith->number_in_book }}</td>
                                        <td>{{ Str::limit($hadith->content, 80) }}</td>
                                        <td>
                                            @if($hadith->narrator)
                                                <small class="text-muted">{{ Str::limit($hadith->narrator->name, 20) }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
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
                        @if($source->hadiths_count > 10)
                            <div class="card-footer text-center">
                                <a href="{{ route('dashboard.hadiths.index') }}?source_id={{ $source->id }}" 
                                   class="btn btn-sm btn-primary">
                                    عرض جميع الأحاديث ({{ $source->hadiths_count }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>لا توجد أحاديث في هذا المصدر بعد</p>
                            <a href="{{ route('dashboard.hadiths.create') }}" 
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
    if (confirm('هل أنت متأكد من حذف هذا المصدر؟')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@stop
