@extends('adminlte::page')

@section('title', 'مراجعة حديث #' . $hadith->number_in_book)

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1><i class="fas fa-clipboard-check text-warning"></i> مراجعة حديث</h1>
    </div>
    <div class="col-sm-6 text-right">
        <a href="{{ route('dashboard.review.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right"></i> العودة للقائمة
        </a>
        @if($previousPending)
            <a href="{{ route('dashboard.review.show', $previousPending) }}" class="btn btn-outline-info">
                <i class="fas fa-arrow-right"></i> السابق
            </a>
        @endif
        @if($nextPending)
            <a href="{{ route('dashboard.review.show', $nextPending) }}" class="btn btn-outline-info">
                التالي <i class="fas fa-arrow-left"></i>
            </a>
        @endif
    </div>
</div>
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle"></i>
        {!! session('success') !!}<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

<div class="row">
    {{-- العمود الأيسر: محتوى الحديث --}}
    <div class="col-lg-8">
        {{-- النص الأصلي --}}
        @if($hadith->raw_text)
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-scroll"></i> النص الأصلي (كما وُرد)</h3>
                </div>
                <div class="card-body"
                    style="font-size: 1.2rem; line-height: 2; font-family: 'Amiri', serif; background: #fffef5;">
                    {{ $hadith->raw_text }}
                </div>
            </div>
        @endif

        {{-- النص المستخرج --}}
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-alt"></i> النص المستخرج (المعالَج)</h3>
            </div>
            <div class="card-body" style="font-size: 1.15rem; line-height: 2; font-family: 'Amiri', serif;">
                {{ $hadith->content }}
            </div>
        </div>

        {{-- الزيادات --}}
        @if($hadith->additions)
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-plus-circle"></i> الزيادات</h3>
                </div>
                <div class="card-body">
                    @foreach($hadith->additions as $addition)
                        <span class="badge badge-info p-2 m-1" style="font-size: 0.9rem;">{{ $addition }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- العمود الأيمن: معلومات + إجراءات --}}
    <div class="col-lg-4">
        {{-- معلومات الحديث --}}
        <div class="card card-outline card-dark">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle"></i> بيانات الحديث</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="font-weight-bold" width="40%">الرقم</td>
                        <td><span class="badge badge-primary badge-lg">{{ $hadith->number_in_book }}</span></td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">الحكم</td>
                        <td><span class="badge badge-info">{{ $hadith->grade }}</span></td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">الحالة</td>
                        <td><span class="badge badge-{{ $hadith->status_badge }}">{{ $hadith->status_name }}</span></td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">الراوي</td>
                        <td>{{ $hadith->narrators->pluck('name')->join('، ') ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">الكتاب</td>
                        <td>{{ $hadith->book->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">المصادر</td>
                        <td>
                            @forelse($hadith->sources as $source)
                                <span class="badge badge-success">{{ $source->name }}</span>
                            @empty
                                <span class="text-muted">—</span>
                            @endforelse
                        </td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">أدخله</td>
                        <td>{{ $hadith->enteredBy->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">تاريخ الإدخال</td>
                        <td><small>{{ $hadith->created_at->format('Y-m-d H:i') }}</small></td>
                    </tr>
                    @if($hadith->reviewer)
                        <tr>
                            <td class="font-weight-bold">راجعه</td>
                            <td>{{ $hadith->reviewer->name }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">تاريخ المراجعة</td>
                            <td><small>{{ $hadith->reviewed_at?->format('Y-m-d H:i') }}</small></td>
                        </tr>
                    @endif
                    @if($hadith->review_notes)
                        <tr>
                            <td class="font-weight-bold">ملاحظات</td>
                            <td class="text-{{ $hadith->status === 'rejected' ? 'danger' : 'muted' }}">
                                {{ $hadith->review_notes }}
                            </td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>

        {{-- إجراءات المراجعة --}}
        @if($hadith->isPending() || $hadith->isRejected())
            <div class="card card-outline card-success">
                <div class="card-header bg-gradient-success">
                    <h3 class="card-title text-white"><i class="fas fa-gavel"></i> إجراء المراجعة</h3>
                </div>
                <div class="card-body">
                    {{-- زر الاعتماد --}}
                    <form method="POST" action="{{ route('dashboard.review.approve', $hadith) }}" class="mb-3">
                        @csrf
                        <div class="form-group">
                            <label>ملاحظات (اختياري)</label>
                            <input type="text" name="notes" class="form-control form-control-sm"
                                placeholder="ملاحظة اختيارية...">
                        </div>
                        <button type="submit" class="btn btn-success btn-block btn-lg">
                            <i class="fas fa-check-circle"></i> اعتماد ✅
                        </button>
                    </form>

                    <hr>

                    {{-- نموذج الرفض --}}
                    <form method="POST" action="{{ route('dashboard.review.reject', $hadith) }}">
                        @csrf
                        <div class="form-group">
                            <label>سبب الرفض <span class="text-danger">*</span></label>
                            <textarea name="review_notes" class="form-control" rows="3"
                                placeholder="اكتب سبب الرفض بوضوح..." required>{{ old('review_notes') }}</textarea>
                            @error('review_notes')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-times-circle"></i> رفض ❌
                        </button>
                    </form>
                </div>
            </div>
        @elseif($hadith->isApproved())
            <div class="callout callout-success">
                <h5><i class="fas fa-check-circle"></i> معتمد</h5>
                <p>تم اعتماد هذا الحديث بواسطة <strong>{{ $hadith->reviewer->name ?? '—' }}</strong>
                    {{ $hadith->reviewed_at ? 'في ' . $hadith->reviewed_at->format('Y-m-d H:i') : '' }}</p>
            </div>
        @endif
    </div>
</div>
@stop