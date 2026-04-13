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
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="text" name="search" class="form-control"
                            placeholder="ابحث في الاسم أو السيرة..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select name="rank" class="form-control" id="filterRank">
                            <option value="">-- جميع الرتب --</option>
                            @foreach($ranks as $rank)
                                <option value="{{ $rank->value }}"
                                    {{ request('rank') === $rank->value ? 'selected' : '' }}
                                    data-needs-judgment="{{ $rank->needsJudgment() ? '1' : '0' }}">
                                    {{ $rank->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3" id="filterJudgmentGroup"
                    style="{{ request('rank') && !in_array(request('rank'), ['sahabi', 'sahabiyyah']) ? '' : 'display:none;' }}">
                    <div class="form-group">
                        <select name="judgment" class="form-control" id="filterJudgment">
                            <option value="">-- جميع الأحكام --</option>
                            @foreach($judgments as $judgment)
                                <option value="{{ $judgment->value }}"
                                    {{ request('judgment') === $judgment->value ? 'selected' : '' }}>
                                    {{ $judgment->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> بحث
                    </button>
                    <a href="{{ route('dashboard.narrators.index') }}" class="btn btn-secondary" title="إعادة تعيين">
                        <i class="fas fa-redo"></i>
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
                        <th style="width: 120px">الرتبة</th>
                        <th style="width: 120px">حكم العلماء</th>
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
                                @if($narrator->fame_name)
                                    <span class="badge badge-outline-secondary"
                                        style="border: 1px solid #aaa; color: #888;">{{ $narrator->fame_name }}</span>
                                @endif
                                @if($narrator->bio)
                                    <br>
                                    <small class="text-muted">
                                        {{ Str::limit($narrator->bio, 60) }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($narrator->rank)
                                    <span class="badge" style="background-color: {{ $narrator->rank_color }}; color: white;">
                                        {{ $narrator->rank_label }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($narrator->judgment)
                                    <span class="badge" style="background-color: {{ $narrator->judgment_color }}; color: white;">
                                        {{ $narrator->judgment_label }}
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

    $(function () {
        $('#filterRank').on('change', function () {
            const selected = $(this).find('option:selected');
            const needsJudgment = selected.data('needs-judgment');
            if (needsJudgment == 1) {
                $('#filterJudgmentGroup').slideDown(200);
            } else {
                $('#filterJudgmentGroup').slideUp(200);
                $('#filterJudgment').val('');
            }
        });
    });
</script>
@stop