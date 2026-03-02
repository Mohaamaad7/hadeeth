# 📚 ميزة تصفح الكتب (Browse Books)

## تاريخ التنفيذ
2026-03-01

## الهدف
إضافة واجهة أمامية تمكّن المستخدم من تصفح الكتب والأبواب بشكل هرمي، بدلاً من الاعتماد فقط على محرك البحث.

---

## الملفات المُنشأة

### 1. `app/Http/Controllers/Frontend/BookController.php`

**الغرض:** التحكم في عرض صفحات الكتب والأبواب في الواجهة الأمامية.

#### الدوال:

##### `index(): View`
- **المسار:** `GET /books`
- **الاسم:** `books.index`
- **الوظيفة:** عرض جميع الكتب الرئيسية (التي ليس لها `parent_id`) كشبكة بطاقات.
- **المنطق:**
  ```php
  $books = Book::mainBooks()
      ->withCount(['hadiths', 'children'])
      ->with(['children' => fn($q) => $q->withCount('hadiths')->orderBy('sort_order')])
      ->orderBy('sort_order')
      ->get();
  ```
- **حساب إجمالي الأحاديث:**
  ```php
  foreach ($books as $book) {
      $childrenHadithsCount = $book->children->sum('hadiths_count');
      $book->total_hadiths = $book->hadiths_count + $childrenHadithsCount;
  }
  ```
  > ملاحظة: بعض الكتب عندها أحاديث مباشرة + أحاديث في أبوابها الفرعية، لذلك نجمع الاثنين.

##### `show(Book $book): View`
- **المسار:** `GET /books/{book}`
- **الاسم:** `books.show`
- **الوظيفة:** عرض تفاصيل كتاب واحد.
- **المنطق الذكي:**
  - إذا الكتاب له أبناء (أبواب) ← يعرض قائمة الأبواب كبطاقات
  - إذا الكتاب ليس له أبناء (أو هو باب بذاته) ← يعرض الأحاديث مباشرة مع pagination (20 حديث/صفحة)
- **البيانات المُمررة للعرض:**
  ```php
  return view('frontend.books.show', [
      'book'       => $book,
      'chapters'   => $chapters,    // null إذا لم يكن هناك أبواب
      'hadiths'    => $hadiths,     // null إذا كان هناك أبواب
      'parentBook' => $book->parent,
  ]);
  ```

---

### 2. `resources/views/frontend/books/index.blade.php`

**الغرض:** صفحة فهرس الكتب الرئيسية.

**التصميم:**
- شبكة بطاقات (Grid) responsive: 3 أعمدة على الشاشات الكبيرة، 2 متوسطة، 1 صغيرة
- كل بطاقة تحتوي:
  - اسم الكتاب
  - رقم الكتاب (badge)
  - عدد الأحاديث
  - عدد الأبواب
  - شريط ملون (color-coded bar) في أعلى البطاقة

**نظام الألوان:**
```php
$colors = ['emerald', 'blue', 'purple', 'amber', 'rose', 'teal', 'indigo', 'orange'];
$color = $colors[$index % count($colors)];
```

---

### 3. `resources/views/frontend/books/show.blade.php`

**الغرض:** عرض تفاصيل كتاب أو باب.

**الأقسام الرئيسية:**

1. **Breadcrumb Navigation:**
   ```
   🏠 > الكتب > كتاب الإيمان > باب فضل الإيمان
   ```

2. **Hero Section:**
   - اسم الكتاب الأب (إن وجد)
   - اسم الكتاب/الباب
   - عدد الأحاديث أو الأبواب
   - أزرار تحميل PDF (تفصيل في `03-pdf-export.md`)

3. **المحتوى الديناميكي:**
   - **إذا أبواب:** عرض بطاقات الأبواب مع عدد أحاديث كل باب
   - **إذا أحاديث:** عرض بطاقات الأحاديث مع:
     - رقم الحديث
     - الدرجة (صحيح/حسن/ضعيف) بألوان مختلفة
     - اسم الراوي
     - نص الحديث (خط Scheherazade New)
     - التخريج (أسماء المصادر الكاملة)
     - رابط "التفاصيل" للحديث

4. **التنقل بين الأبواب:**
   ```
   ← الباب السابق | الباب التالي →
   ```

---

## الملفات المُعدَّلة

### `routes/web.php`
```php
// إضافة المسارات
Route::get('/books', [FrontendBookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [FrontendBookController::class, 'show'])->name('books.show');
```

### `resources/views/frontend/home.blade.php`
- تحديث رابط "الكتب" في Navbar (desktop + mobile) للإشارة إلى `route('books.index')` بدلاً من `#`

### `resources/views/frontend/search.blade.php`
- نفس التحديث في Navbar

---

## النماذج المستخدمة

### `Book` Model
| الحقل | النوع | الوصف |
|---|---|---|
| `id` | int | المعرف |
| `name` | string | اسم الكتاب/الباب |
| `parent_id` | int/null | معرف الكتاب الأب |
| `sort_order` | int | ترتيب العرض |
| `number_in_book` | int | رقم الباب داخل الكتاب |

**العلاقات:**
- `parent()` → `belongsTo(Book::class, 'parent_id')`
- `children()` → `hasMany(Book::class, 'parent_id')`
- `hadiths()` → `hasMany(Hadith::class, 'book_id')`

**Scope:**
- `mainBooks()` → `whereNull('parent_id')`

---

## ملاحظات تقنية
- الخط المستخدم لنص الحديث: `Scheherazade New` (Google Fonts)
- الخط المستخدم للعناوين: `Cairo` و `IBM Plex Sans Arabic`
- التصميم يستخدم Tailwind CSS مع ألوان emerald كلون رئيسي
- كل الصفحات RTL بالكامل
