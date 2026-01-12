@extends('adminlte::page')

@section('title', 'عرض الحديث')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>تفاصيل الحديث #{{ $hadith->number_in_book }}</h1>
        </div>
        <div class="col-sm-6">
            <div class="float-left">
                <a href="{{ route('dashboard.hadiths.edit', $hadith) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <a href="{{ route('dashboard.hadiths.index') }}" class="btn btn-secondary">
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
        <div class="col-md-8">
            <!-- نص الحديث -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-book-open"></i> نص الحديث
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ 
                            $hadith->grade === 'صحيح' ? 'success' : 
                            ($hadith->grade === 'حسن' ? 'info' : 
                            ($hadith->grade === 'ضعيف' ? 'warning' : 'danger')) 
                        }}">
                            {{ $hadith->grade }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="lead" style="font-size: 1.1rem; line-height: 2;">
                        {{ $hadith->content }}
                    </p>
                </div>
            </div>

            @if($hadith->explanation)
            <!-- الشرح -->
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-lightbulb"></i> الشرح والتفسير
                    </h3>
                </div>
                <div class="card-body">
                    <p style="line-height: 1.8;">{{ $hadith->explanation }}</p>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- البيانات الأساسية -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> البيانات الأساسية
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <tr>
                            <th style="width: 40%">رقم الحديث:</th>
                            <td><strong>#{{ $hadith->number_in_book }}</strong></td>
                        </tr>
                        <tr>
                            <th>الدرجة:</th>
                            <td>
                                <span class="badge badge-{{ 
                                    $hadith->grade === 'صحيح' ? 'success' : 
                                    ($hadith->grade === 'حسن' ? 'info' : 
                                    ($hadith->grade === 'ضعيف' ? 'warning' : 'danger')) 
                                }}">
                                    {{ $hadith->grade }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>الكتاب:</th>
                            <td>{{ $hadith->book?->name ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <th>الراوي:</th>
                            <td>
                                @if($hadith->narrator)
                                    <span class="badge badge-secondary">
                                        {{ $hadith->narrator->name }}
                                    </span>
                                @else
                                    <span class="text-muted">غير محدد</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>تاريخ الإضافة:</th>
                            <td>
                                <small>{{ $hadith->created_at->format('Y-m-d H:i') }}</small>
                            </td>
                        </tr>
                        <tr>
                            <th>آخر تحديث:</th>
                            <td>
                                <small>{{ $hadith->updated_at->format('Y-m-d H:i') }}</small>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- المصادر -->
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-database"></i> المصادر
                        <span class="badge badge-light">{{ $hadith->sources->count() }}</span>
                    </h3>
                </div>
                <div class="card-body">
                    @if($hadith->sources->count() > 0)
                        <ul class="list-unstyled">
                            @foreach($hadith->sources as $source)
                                <li class="mb-2">
                                    <i class="fas fa-check text-success"></i>
                                    <strong>{{ $source->name }}</strong>
                                    <span class="badge badge-secondary">{{ $source->code }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted text-center mb-0">
                            <i class="fas fa-info-circle"></i><br>
                            لا توجد مصادر مرتبطة
                        </p>
                    @endif
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
                    <a href="{{ route('dashboard.hadiths.edit', $hadith) }}" 
                       class="btn btn-warning btn-block">
                        <i class="fas fa-edit"></i> تعديل الحديث
                    </a>
                    <button type="button" 
                            class="btn btn-danger btn-block" 
                            onclick="confirmDelete()">
                        <i class="fas fa-trash"></i> حذف الحديث
                    </button>
                    <a href="{{ route('dashboard.hadiths.index') }}" 
                       class="btn btn-secondary btn-block">
                        <i class="fas fa-list"></i> العودة للقائمة
                    </a>
                </div>
            </div>

            <form id="delete-form" 
                  action="{{ route('dashboard.hadiths.destroy', $hadith) }}" 
                  method="POST" 
                  style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
@stop

@section('js')
<script>
function confirmDelete() {
    if (confirm('هل أنت متأكد من حذف هذا الحديث؟\n\nلا يمكن التراجع عن هذا الإجراء!')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@stop
