@extends('adminlte::page')

@section('title', 'إضافة حديث جديد')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>إضافة حديث جديد</h1>
        </div>
        <div class="col-sm-6">
            <div class="float-left">
                <a href="{{ route('dashboard.hadiths.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i> رجوع للقائمة
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

    <div class="row">
        <!-- النموذج الرئيسي -->
        <div class="col-md-8">
            <form action="{{ route('dashboard.hadiths.store') }}" method="POST" id="hadithForm">
                @csrf
                
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-magic"></i> الإدخال الذكي (Parser)
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>نصيحة:</strong> الصق النص الكامل هنا، وسيقوم النظام باستخراج البيانات تلقائياً!
                            <br>
                            <small>مثال: [436] (صحيح) (د ت ن) عن أبي هريرة: قال رسول الله ﷺ...</small>
                        </div>
                        
                        <div class="form-group">
                            <label>النص الخام (اختياري)</label>
                            <textarea name="raw_text" 
                                      id="rawText" 
                                      class="form-control" 
                                      rows="4" 
                                      placeholder="مثال: [436] (صحيح) (د ت ن) عن أبي هريرة: قال رسول الله ﷺ..."
                            >{{ old('raw_text') }}</textarea>
                        </div>
                        
                        <button type="button" 
                                class="btn btn-primary btn-block" 
                                id="parseBtn">
                            <i class="fas fa-cogs"></i> تحليل النص تلقائياً
                        </button>
                        
                        <div id="parseResult" class="mt-3" style="display: none;">
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> تم التحليل بنجاح! راجع الحقول أدناه.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">بيانات الحديث</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>نص الحديث <span class="text-danger">*</span></label>
                            <textarea name="content" 
                                      id="content" 
                                      class="form-control" 
                                      rows="6" 
                                      required
                            >{{ old('content') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>الشرح والتفسير</label>
                            <textarea name="explanation" 
                                      class="form-control" 
                                      rows="3"
                            >{{ old('explanation') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>رقم الحديث في الكتاب <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           name="number_in_book" 
                                           id="numberInBook"
                                           class="form-control" 
                                           value="{{ old('number_in_book') }}" 
                                           required 
                                           min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>درجة الحديث <span class="text-danger">*</span></label>
                                    <select name="grade" id="grade" class="form-control" required>
                                        <option value="">-- اختر الدرجة --</option>
                                        <option value="صحيح" {{ old('grade') == 'صحيح' ? 'selected' : '' }}>صحيح</option>
                                        <option value="حسن" {{ old('grade') == 'حسن' ? 'selected' : '' }}>حسن</option>
                                        <option value="ضعيف" {{ old('grade') == 'ضعيف' ? 'selected' : '' }}>ضعيف</option>
                                        <option value="موضوع" {{ old('grade') == 'موضوع' ? 'selected' : '' }}>موضوع</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>الكتاب <span class="text-danger">*</span></label>
                                    <select name="book_id" class="form-control" required>
                                        <option value="">-- اختر الكتاب --</option>
                                        @foreach($books as $book)
                                            <option value="{{ $book->id }}" 
                                                    {{ old('book_id') == $book->id ? 'selected' : '' }}>
                                                {{ $book->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>الصحابي</label>
                                    <select name="narrator_id" id="narratorId" class="form-control">
                                        <option value="">-- اختر الصحابي --</option>
                                        @foreach($companions as $companion)
                                            <option value="{{ $companion->id }}" 
                                                    {{ old('narrator_id') == $companion->id ? 'selected' : '' }}>
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
                                @foreach($sources as $source)
                                    <div class="col-md-4">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" 
                                                   class="custom-control-input source-checkbox" 
                                                   id="source_{{ $source->id }}" 
                                                   name="source_ids[]" 
                                                   value="{{ $source->id }}"
                                                   {{ is_array(old('source_ids')) && in_array($source->id, old('source_ids')) ? 'checked' : '' }}>
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
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> حفظ الحديث
                        </button>
                        <a href="{{ route('dashboard.hadiths.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> إلغاء
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- معاينة مباشرة -->
        <div class="col-md-4">
            <div class="card card-success sticky-top" style="top: 10px;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-eye"></i> معاينة مباشرة
                    </h3>
                </div>
                <div class="card-body">
                    <div id="preview">
                        <p class="text-muted text-center">
                            <i class="fas fa-info-circle"></i><br>
                            ستظهر المعاينة هنا بعد ملء الحقول
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .custom-control-label {
        cursor: pointer;
    }
    #preview {
        font-size: 14px;
        line-height: 1.8;
    }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // الـ Parser الذكي
    $('#parseBtn').click(function() {
        const rawText = $('#rawText').val().trim();
        
        if (!rawText) {
            alert('الرجاء إدخال النص الخام أولاً');
            return;
        }
        
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري التحليل...');
        
        $.ajax({
            url: '{{ route("dashboard.hadiths.parse") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                raw_text: rawText
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    
                    // ملء الحقول تلقائياً
                    if (data.clean_text) {
                        $('#content').val(data.clean_text);
                    }
                    if (data.number) {
                        $('#numberInBook').val(data.number);
                    }
                    if (data.grade) {
                        $('#grade').val(data.grade);
                    }
                    
                    // البحث عن الراوي في القائمة
                    if (data.narrator) {
                        const narratorSelect = $('#narratorId');
                        let found = false;
                        narratorSelect.find('option').each(function() {
                            if ($(this).text().includes(data.narrator)) {
                                $(this).prop('selected', true);
                                found = true;
                                return false;
                            }
                        });
                        if (!found) {
                            alert('تنبيه: لم يتم العثور على الراوي "' + data.narrator + '" في القائمة. قد تحتاج لإضافته.');
                        }
                    }
                    
                    // تحديد المصادر
                    if (data.sources && data.sources.length > 0) {
                        $('.source-checkbox').prop('checked', false);
                        data.sources.forEach(function(sourceName) {
                            $('.source-checkbox').each(function() {
                                const label = $(this).next('label').text();
                                if (label.includes(sourceName)) {
                                    $(this).prop('checked', true);
                                }
                            });
                        });
                    }
                    
                    $('#parseResult').slideDown();
                    updatePreview();
                }
            },
            error: function() {
                alert('حدث خطأ أثناء التحليل');
            },
            complete: function() {
                $('#parseBtn').prop('disabled', false).html('<i class="fas fa-cogs"></i> تحليل النص تلقائياً');
            }
        });
    });
    
    // المعاينة المباشرة
    function updatePreview() {
        const content = $('#content').val();
        const grade = $('#grade option:selected').text();
        const number = $('#numberInBook').val();
        
        let html = '<div class="border-bottom pb-2 mb-2">';
        if (number) {
            html += '<span class="badge badge-primary">#' + number + '</span> ';
        }
        if (grade && grade !== '-- اختر الدرجة --') {
            let badgeClass = 'secondary';
            if (grade === 'صحيح') badgeClass = 'success';
            else if (grade === 'حسن') badgeClass = 'info';
            else if (grade === 'ضعيف') badgeClass = 'warning';
            else if (grade === 'موضوع') badgeClass = 'danger';
            html += '<span class="badge badge-' + badgeClass + '">' + grade + '</span>';
        }
        html += '</div>';
        
        if (content) {
            html += '<p>' + content + '</p>';
        } else {
            html = '<p class="text-muted text-center"><i class="fas fa-info-circle"></i><br>ستظهر المعاينة هنا</p>';
        }
        
        $('#preview').html(html);
    }
    
    $('#content, #grade, #numberInBook').on('input change', updatePreview);
});
</script>
@stop
