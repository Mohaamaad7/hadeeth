@extends('adminlte::page')

@section('title', 'تعديل ' . $book->name)

@section('content_header')
    <h1>تعديل: {{ $book->name }}</h1>
@stop

@section('content')
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dashboard.books.update', $book) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">تعديل البيانات</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>النوع</label>
                    <select class="form-control" id="bookType" onchange="toggleParentField()">
                        <option value="main" {{ !$book->parent_id ? 'selected' : '' }}>كتاب رئيسي</option>
                        <option value="sub" {{ $book->parent_id ? 'selected' : '' }}>باب فرعي</option>
                    </select>
                </div>

                <div class="form-group" id="parentField" style="{{ $book->parent_id ? '' : 'display: none;' }}">
                    <label>الكتاب الرئيسي</label>
                    <select name="parent_id" class="form-control">
                        <option value="">-- اختر الكتاب الرئيسي --</option>
                        @foreach($mainBooks as $mainBook)
                            <option value="{{ $mainBook->id }}" 
                                    {{ old('parent_id', $book->parent_id) == $mainBook->id ? 'selected' : '' }}>
                                {{ $mainBook->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>الاسم <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" 
                           value="{{ old('name', $book->name) }}" 
                           required>
                </div>

                <div class="form-group">
                    <label>رقم الترتيب <span class="text-danger">*</span></label>
                    <input type="number" name="sort_order" class="form-control" 
                           value="{{ old('sort_order', $book->sort_order) }}" 
                           min="0"
                           required>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save"></i> حفظ التعديلات
                </button>
                <a href="{{ route('dashboard.books.show', $book) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            </div>
        </div>
    </form>
@stop

@section('js')
<script>
function toggleParentField() {
    const bookType = document.getElementById('bookType').value;
    const parentField = document.getElementById('parentField');
    const parentSelect = parentField.querySelector('select');
    
    if (bookType === 'sub') {
        parentField.style.display = 'block';
        parentSelect.required = true;
    } else {
        parentField.style.display = 'none';
        parentSelect.required = false;
        parentSelect.value = '';
    }
}
</script>
@stop
