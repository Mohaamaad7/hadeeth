@extends('adminlte::page')

@section('title', 'مراجعة الأحاديث')

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1><i class="fas fa-clipboard-check text-warning"></i> مراجعة الأحاديث</h1>
    </div>
</div>
@stop

@section('content')

{{-- رسائل --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle"></i>
        {!! session('success') !!}<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

{{-- إحصائيات سريعة --}}
<div class="row">
    <div class="col-lg-4 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $counts['pending'] }}</h3>
                <p>بانتظار المراجعة</p>
            </div>
            <div class="icon"><i class="fas fa-hourglass-half"></i></div>
            <a href="{{ route('dashboard.review.index', ['status' => 'pending']) }}" class="small-box-footer">عرض <i
                    class="fas fa-arrow-circle-left"></i></a>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $counts['approved'] }}</h3>
                <p>معتمدة</p>
            </div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <a href="{{ route('dashboard.review.index', ['status' => 'approved']) }}" class="small-box-footer">عرض <i
                    class="fas fa-arrow-circle-left"></i></a>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $counts['rejected'] }}</h3>
                <p>مرفوضة</p>
            </div>
            <div class="icon"><i class="fas fa-times-circle"></i></div>
            <a href="{{ route('dashboard.review.index', ['status' => 'rejected']) }}" class="small-box-footer">عرض <i
                    class="fas fa-arrow-circle-left"></i></a>
        </div>
    </div>
</div>

{{-- اعتماد جماعي (أدمن فقط) --}}
@if(auth()->user()->isAdmin() && $counts['pending'] > 0 && $status === 'pending')
    <div class="card card-warning card-outline mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-bolt text-warning"></i> <strong>{{ $counts['pending'] }}</strong> حديث بانتظار المراجعة
            </div>
            <div>
                <form method="POST" action="{{ route('dashboard.review.approve-all') }}" class="d-inline"
                    id="approveAllForm">
                    @csrf
                    <button type="button" class="btn btn-success" onclick="confirmApproveAll()">
                        <i class="fas fa-check-double"></i> اعتماد الكل
                    </button>
                </form>
            </div>
        </div>
    </div>
@endif

{{-- فلتر + بحث --}}
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="btn-group">
                <a href="{{ route('dashboard.review.index', ['status' => 'pending']) }}"
                    class="btn btn-sm {{ $status === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                    <i class="fas fa-hourglass-half"></i> معلّقة <span
                        class="badge badge-light">{{ $counts['pending'] }}</span>
                </a>
                <a href="{{ route('dashboard.review.index', ['status' => 'approved']) }}"
                    class="btn btn-sm {{ $status === 'approved' ? 'btn-success' : 'btn-outline-success' }}">
                    <i class="fas fa-check"></i> معتمدة <span class="badge badge-light">{{ $counts['approved'] }}</span>
                </a>
                <a href="{{ route('dashboard.review.index', ['status' => 'rejected']) }}"
                    class="btn btn-sm {{ $status === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                    <i class="fas fa-times"></i> مرفوضة <span class="badge badge-light">{{ $counts['rejected'] }}</span>
                </a>
                <a href="{{ route('dashboard.review.index', ['status' => 'all']) }}"
                    class="btn btn-sm {{ $status === 'all' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                    الكل
                </a>
            </div>
            <form class="form-inline mt-1" action="{{ route('dashboard.review.index') }}">
                <input type="hidden" name="status" value="{{ $status }}">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="بحث..."
                    value="{{ request('search') }}">
                <button class="btn btn-sm btn-primary mr-1"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        @if(auth()->user()->isAdmin() && $status === 'pending' && $hadiths->count() > 0)
            <form method="POST" action="{{ route('dashboard.review.bulk-approve') }}" id="bulkForm">
                @csrf
        @endif
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        @if(auth()->user()->isAdmin() && $status === 'pending')
                            <th width="30"><input type="checkbox" id="selectAll"></th>
                        @endif
                        <th width="60">#</th>
                        <th>النص</th>
                        <th width="80">الحكم</th>
                        <th width="120">الراوي</th>
                        <th width="100">الحالة</th>
                        <th width="120">المُدخِل</th>
                        <th width="100">التاريخ</th>
                        <th width="80">إجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hadiths as $hadith)
                        <tr>
                            @if(auth()->user()->isAdmin() && $status === 'pending')
                                <td><input type="checkbox" name="hadith_ids[]" value="{{ $hadith->id }}" class="select-hadith">
                                </td>
                            @endif
                            <td><span class="badge badge-primary">{{ $hadith->number_in_book }}</span></td>
                            <td>{{ Str::limit($hadith->content, 100) }}</td>
                            <td><span class="badge badge-info">{{ $hadith->grade }}</span></td>
                            <td>{{ $hadith->narrators->pluck('name')->join('، ') ?: '—' }}</td>
                            <td><span class="badge badge-{{ $hadith->status_badge }}">{{ $hadith->status_name }}</span></td>
                            <td>{{ $hadith->enteredBy->name ?? '—' }}</td>
                            <td><small>{{ $hadith->created_at->diffForHumans() }}</small></td>
                            <td>
                                <a href="{{ route('dashboard.review.show', $hadith) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-check-circle fa-2x text-success mb-2 d-block"></i>
                                لا توجد أحاديث في هذه الحالة
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if(auth()->user()->isAdmin() && $status === 'pending' && $hadiths->count() > 0)
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-sm" id="bulkApproveBtn" disabled>
                            <i class="fas fa-check-double"></i> اعتماد المحدد (<span id="selectedCount">0</span>)
                        </button>
                    </div>
                </form>
            @endif
    </div>
    <div class="card-footer">
        {{ $hadiths->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function () {
        // Select all checkbox
        $('#selectAll').on('change', function () {
            $('.select-hadith').prop('checked', this.checked);
            updateBulkBtn();
        });
        $('.select-hadith').on('change', updateBulkBtn);

        function updateBulkBtn() {
            var count = $('.select-hadith:checked').length;
            $('#selectedCount').text(count);
            $('#bulkApproveBtn').prop('disabled', count === 0);
        }
    });

    function confirmApproveAll() {
        Swal.fire({
            icon: 'warning',
            title: 'اعتماد جماعي شامل',
            html: '<div style="direction:rtl">سيتم اعتماد <strong>جميع</strong> الأحاديث المعلّقة دفعة واحدة.<br><br>هل أنت متأكد؟</div>',
            showCancelButton: true,
            confirmButtonText: '✅ اعتماد الكل',
            cancelButtonText: 'إلغاء',
            confirmButtonColor: '#28a745',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('approveAllForm').submit();
            }
        });
    }
</script>
@stop