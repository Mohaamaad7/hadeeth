@extends('adminlte::page')

@section('title', 'تعديل الحديث')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>تعديل الحديث #{{ $hadith->number_in_book }}</h1>
        </div>
        <div class="col-sm-6">
            <div class="float-left">
                <a href="{{ route('dashboard.hadiths.show', $hadith) }}" class="btn btn-info">
                    <i class="fas fa-eye"></i> عرض
                </a>
                <a href="{{ route('dashboard.hadiths.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i> رجوع
                </a>
            </div>
        </div>
    </div>
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h5><i class="icon fas fa-ban"></i> يوجد أخطاء في النموذج!</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dashboard.hadiths.update', $hadith) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">بيانات الحديث</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>نص الحديث <span class="text-danger">*</span></label>
                    <textarea name="content" 
                              class="form-control" 
                              rows="6" 
                              required
                    >{{ old('content', $hadith->content) }}</textarea>
                </div>

                <div class="form-group">
                    <label>الشرح والتفسير</label>
                    <textarea name="explanation" 
                              class="form-control" 
                              rows="3"
                    >{{ old('explanation', $hadith->explanation) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>رقم الحديث في الكتاب <span class="text-danger">*</span></label>
                            <input type="number" 
                                   name="number_in_book" 
                                   class="form-control" 
                                   value="{{ old('number_in_book', $hadith->number_in_book) }}" 
                                   required 
                                   min="1">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>درجة الحديث <span class="text-danger">*</span></label>
                            <select name="grade" class="form-control" required>
                                <option value="صحيح" {{ old('grade', $hadith->grade) == 'صحيح' ? 'selected' : '' }}>صحيح</option>
                                <option value="حسن" {{ old('grade', $hadith->grade) == 'حسن' ? 'selected' : '' }}>حسن</option>
                                <option value="ضعيف" {{ old('grade', $hadith->grade) == 'ضعيف' ? 'selected' : '' }}>ضعيف</option>
                                <option value="موضوع" {{ old('grade', $hadith->grade) == 'موضوع' ? 'selected' : '' }}>موضوع</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>الكتاب <span class="text-danger">*</span></label>
                            <select name="book_id" class="form-control" required>
                                @foreach($books as $book)
                                    <option value="{{ $book->id }}" 
                                            {{ old('book_id', $hadith->book_id) == $book->id ? 'selected' : '' }}>
                                        {{ $book->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>الصحابي</label>
                            <select name="narrator_id" class="form-control">
                                <option value="">-- اختر الصحابي --</option>
                                @foreach($companions as $companion)
                                    <option value="{{ $companion->id }}" 
                                            {{ old('narrator_id', $hadith->narrator_id) == $companion->id ? 'selected' : '' }}>
                                        {{ $companion->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>المصادر</label>
                    <div class="row">
                        @php
                            $selectedSources = old('source_ids', $hadith->sources->pluck('id')->toArray());
                        @endphp
                        @foreach($sources as $source)
                            <div class="col-md-4">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="source_{{ $source->id }}" 
                                           name="source_ids[]" 
                                           value="{{ $source->id }}"
                                           {{ in_array($source->id, $selectedSources) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="source_{{ $source->id }}">
                                        {{ $source->name }}
                                        <small class="text-muted">({{ $source->code }})</small>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save"></i> حفظ التعديلات
                </button>
                <a href="{{ route('dashboard.hadiths.show', $hadith) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            </div>
        </div>

        <!-- قسم رجال الحديث (السلاسل) -->
        <div class="card card-primary mt-3">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users"></i> رجال الحديث (سلاسل الإسناد)</h3>
            </div>
            <div class="card-body">
                @if($hadith->sources->count() > 0)
                    <div id="chains-container">
                        @foreach($hadith->sources as $index => $source)
                            <div class="chain-section mb-4 p-3 border rounded bg-light" data-source-id="{{ $source->id }}">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">
                                        <i class="fas fa-book-open text-primary"></i> 
                                        {{ $source->name }} <span class="badge badge-info">{{ $source->code }}</span>
                                    </h5>
                                </div>

                                @php
                                    $existingChain = $hadith->chains->where('source_id', $source->id)->first();
                                @endphp

                                <input type="hidden" name="chains[{{ $index }}][source_id]" value="{{ $source->id }}">

                                <div class="form-group">
                                    <label class="text-muted small">وصف السلسلة (اختياري)</label>
                                    <input type="text" 
                                           name="chains[{{ $index }}][description]" 
                                           class="form-control form-control-sm" 
                                           placeholder="مثال: طريق الإمام البخاري في الصحيح"
                                           value="{{ $existingChain?->description }}">
                                </div>

                                <div class="narrators-list" data-chain-index="{{ $index }}">
                                    @if($existingChain && $existingChain->narrators->count() > 0)
                                        @foreach($existingChain->narrators as $narrator)
                                            @php
                                                $isCompanion = $narrator->is_companion;
                                            @endphp
                                            <div class="narrator-row mb-2">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <select class="form-control form-control-sm role-selector" data-chain="{{ $index }}" data-narrator-index="{{ $loop->index }}">
                                                            <option value="">-- النوع --</option>
                                                            <option value="companion" {{ $isCompanion ? 'selected' : '' }}>صحابي</option>
                                                            <option value="narrator" {{ !$isCompanion ? 'selected' : '' }}>رجل الحديث</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <select name="chains[{{ $index }}][narrators][{{ $loop->index }}][id]" class="form-control form-control-sm narrator-select">
                                                            <option value="">-- اختر --</option>
                                                            @if($isCompanion)
                                                                @foreach($companions as $c)
                                                                    <option value="{{ $c->id }}" {{ $c->id == $narrator->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                                                @endforeach
                                                            @else
                                                                @foreach($narrators->where('is_companion', false) as $n)
                                                                    <option value="{{ $n->id }}" {{ $n->id == $narrator->id ? 'selected' : '' }}>{{ $n->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="hidden" name="chains[{{ $index }}][narrators][{{ $loop->index }}][role]" value="{{ $narrator->pivot->role }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-sm btn-danger btn-block remove-narrator">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="narrator-row mb-2">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <select class="form-control form-control-sm role-selector" data-chain="{{ $index }}" data-narrator-index="0">
                                                        <option value="">-- النوع --</option>
                                                        <option value="companion">صحابي</option>
                                                        <option value="narrator">رجل الحديث</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                    <select name="chains[{{ $index }}][narrators][0][id]" class="form-control form-control-sm narrator-select" disabled>
                                                        <option value="">-- اختر النوع أولاً --</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="hidden" name="chains[{{ $index }}][narrators][0][role]" value="">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-sm btn-danger btn-block remove-narrator">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <button type="button" class="btn btn-sm btn-success add-narrator" data-chain-index="{{ $index }}">
                                    <i class="fas fa-plus"></i> إضافة راوي
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        يجب اختيار المصادر أولاً من القسم أعلاه لتتمكن من إضافة سلاسل الإسناد.
                    </div>
                @endif
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> حفظ جميع التعديلات (البيانات + السلاسل)
                </button>
                <a href="{{ route('dashboard.hadiths.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i> رجوع للقائمة
                </a>
            </div>
        </div>
    </form>
@stop

@section('js')
<script>
// بيانات الرواة والصحابة
const companions = @json($companions->map(fn($c) => ['id' => $c->id, 'name' => $c->name]));
const regularNarrators = @json($narrators->where('is_companion', false)->values()->map(fn($n) => ['id' => $n->id, 'name' => $n->name]));

$(document).ready(function() {
    // عند تغيير نوع الراوي (صحابي / رجل الحديث)
    $(document).on('change', '.role-selector', function() {
        const roleType = $(this).val();
        const narratorSelect = $(this).closest('.row').find('.narrator-select');
        const hiddenRole = $(this).closest('.row').find('input[type="hidden"]');
        
        // تفريغ وتعبئة القائمة حسب النوع
        narratorSelect.empty();
        
        if (roleType === 'companion') {
            narratorSelect.prop('disabled', false);
            narratorSelect.append('<option value="">-- اختر الصحابي --</option>');
            companions.forEach(c => {
                narratorSelect.append(`<option value="${c.id}">${c.name}</option>`);
            });
            hiddenRole.val('الصحابي');
        } else if (roleType === 'narrator') {
            narratorSelect.prop('disabled', false);
            narratorSelect.append('<option value="">-- اختر رجل الحديث --</option>');
            regularNarrators.forEach(n => {
                narratorSelect.append(`<option value="${n.id}">${n.name}</option>`);
            });
            hiddenRole.val('');
        } else {
            narratorSelect.prop('disabled', true);
            narratorSelect.append('<option value="">-- اختر النوع أولاً --</option>');
            hiddenRole.val('');
        }
    });

    // إضافة راوي جديد
    $(document).on('click', '.add-narrator', function() {
        const chainIndex = $(this).data('chain-index');
        const narratorsList = $(this).prev('.narrators-list');
        const currentCount = narratorsList.find('.narrator-row').length;
        
        const narratorRow = `
            <div class="narrator-row mb-2">
                <div class="row">
                    <div class="col-md-3">
                        <select class="form-control form-control-sm role-selector" data-chain="${chainIndex}" data-narrator-index="${currentCount}">
                            <option value="">-- النوع --</option>
                            <option value="companion">صحابي</option>
                            <option value="narrator">رجل الحديث</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <select name="chains[${chainIndex}][narrators][${currentCount}][id]" class="form-control form-control-sm narrator-select" disabled>
                            <option value="">-- اختر النوع أولاً --</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="hidden" name="chains[${chainIndex}][narrators][${currentCount}][role]" value="">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-danger btn-block remove-narrator">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        narratorsList.append(narratorRow);
    });

    // حذف راوي
    $(document).on('click', '.remove-narrator', function() {
        const narratorsList = $(this).closest('.narrators-list');
        const rowCount = narratorsList.find('.narrator-row').length;
        
        if (rowCount > 1) {
            $(this).closest('.narrator-row').remove();
            updateNarratorIndexes(narratorsList);
        } else {
            // Reset the row instead of deleting
            const row = $(this).closest('.narrator-row');
            row.find('.role-selector').val('');
            row.find('.narrator-select').prop('disabled', true).empty().append('<option value="">-- اختر النوع أولاً --</option>');
            row.find('input[type="hidden"]').val('');
        }
    });

    function updateNarratorIndexes(narratorsList) {
        const chainIndex = narratorsList.data('chain-index');
        narratorsList.find('.narrator-row').each(function(index) {
            $(this).find('select[name], input[name]').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    const newName = name.replace(/\[narrators\]\[\d+\]/, `[narrators][${index}]`);
                    $(this).attr('name', newName);
                }
            });
            $(this).find('.role-selector').attr('data-narrator-index', index);
        });
    }
});
</script>
@stop

