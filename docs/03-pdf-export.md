# 📄 تصدير PDF التفاعلي (Interactive PDF Export)

## تاريخ التنفيذ
2026-03-01

## الهدف
تمكين المستخدم من تحميل كتاب أو باب كملف PDF تفاعلي مع فهرس قابل للنقر، بنسختين: النص المستخرج والنص الأصلي.

---

## المتطلبات المُثبتة

### مكتبة mPDF
```bash
composer require carlos-meneses/laravel-mpdf
```
- **الحزمة:** `carlos-meneses/laravel-mpdf` (الإصدار ^2.1)
- **المحرك:** mPDF — يدعم العربية و RTL بشكل ممتاز
- **لماذا mPDF وليس DomPDF؟** دعم أفضل للعربية، الخطوط، والتشكيل.

---

## الملفات

### 1. `app/Http/Controllers/Frontend/BookController.php` → `exportPdf()`

**المسار:** `GET /books/{book}/pdf`
**الاسم:** `books.pdf`
**المعاملات:**
| المعامل | النوع | الوصف |
|---|---|---|
| `{book}` | Route Model Binding | الكتاب أو الباب |
| `?type=original` | Query String | إذا وُجد ← يستخدم `raw_text` (النص الأصلي) |

#### المنطق التفصيلي:

```php
public function exportPdf(Book $book)
{
    $book->load(['parent']);
    $parentBook = $book->parent;
    $useOriginal = request('type') === 'original';

    // تحديد نوع الكتاب
    if ($book->children()->count() > 0) {
        // كتاب رئيسي: تحميل كل الأبواب مع أحاديثها
        $chapters = $book->children()
            ->withCount('hadiths')
            ->with(['hadiths' => fn($q) => $q->with(['narrator', 'sources'])->orderBy('number_in_book')])
            ->orderBy('sort_order')
            ->get();
        $totalHadiths = $chapters->sum('hadiths_count');
    } else {
        // باب واحد أو كتاب بدون أبواب
        $hadiths = $book->hadiths()
            ->with(['narrator', 'sources'])
            ->orderBy('number_in_book')
            ->get();
        $totalHadiths = $hadiths->count();
    }

    // عرض القالب
    $html = view('frontend.books.pdf', compact(
        'book', 'parentBook', 'chapters', 'hadiths', 'totalHadiths', 'useOriginal'
    ))->render();

    // إعداد mPDF
    $mpdf = new \Mpdf\Mpdf([
        'mode'              => 'utf-8',
        'format'            => 'A4',
        'default_font_size' => 13,
        'default_font'      => 'aealarabiya',
        'direction'         => 'rtl',
        'autoArabic'        => true,
        'autoLangToFont'    => true,
        'margin_top'        => 25,
        'margin_bottom'     => 20,
        'margin_left'       => 20,
        'margin_right'      => 20,
    ]);

    // منع التخزين المؤقت
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');

    return $mpdf->Output($fileName, \Mpdf\Output\Destination::INLINE);
}
```

#### إعدادات mPDF المهمة:
| الإعداد | القيمة | السبب |
|---|---|---|
| `mode` | `utf-8` | دعم العربية |
| `default_font` | `aealarabiya` | خط عربي مدمج في mPDF |
| `direction` | `rtl` | من اليمين لليسار |
| `autoArabic` | `true` | تحسين عرض النصوص العربية |
| `autoLangToFont` | `true` | اختيار الخط المناسب تلقائياً |

---

### 2. `resources/views/frontend/books/pdf.blade.php`

**الغرض:** قالب HTML الذي يتحول إلى PDF عبر mPDF.

#### الهيكل:

```
PDF Document
├── Footer Template (htmlpagefooter)
│   └── اسم الكتاب | نوع النسخة (أصلي/مستخرج) | صفحة X من Y
├── Cover Page (صفحة الغلاف)
│   ├── بسملة ﷽
│   ├── اسم الكتاب الأب (إن وجد)
│   ├── اسم الكتاب
│   ├── نوع النسخة (📜 الأصل أو 📝 المستخرج)
│   ├── إحصائيات (عدد الأبواب + الأحاديث)
│   └── تاريخ التصدير
├── Table of Contents (فهرس الأبواب) ← فقط إذا كان الكتاب له أبواب
│   ├── <a name="toc"> ← anchor للعودة
│   └── لكل باب: <a href="#chapter-{id}"> ← رابط للباب
├── Content (المحتوى)
│   ├── لكل باب:
│   │   ├── <bookmark> ← bookmark في الشريط الجانبي
│   │   ├── <a name="chapter-{id}"> ← anchor للتنقل
│   │   ├── Chapter Header (اسم الباب + عدد الأحاديث)
│   │   ├── Hadith Cards (بطاقات الأحاديث)
│   │   └── <a href="#toc"> ← رابط العودة للفهرس
│   └── أو: أحاديث مباشرة (بدون أبواب)
```

#### نظام النص المزدوج:
```blade
{{-- في كل بطاقة حديث --}}
<div class="hadith-text">
    {{ $useOriginal ? ($hadith->raw_text ?: $hadith->content) : $hadith->content }}
</div>

{{-- مؤشر النسخة الأصلية --}}
@if($useOriginal && $hadith->raw_text)
    <div style="...">📜 النص الأصلي كما ورد في المصدر</div>
@endif
```

> **Fallback:** إذا `raw_text` فارغ ← يرجع لـ `content` تلقائياً.

#### الفرق بين `content` و `raw_text`:
```
content:  أحياناً يأتيني في مثل صلصلة الجرس وهو أشدّه عليّ...
raw_text: أحياناً يأتيني في مثل صلصلة الجرس وهو أشدّه عليّ... [213] (صحيح) (مالك حم ق ت ن) عن عائشة. زاد (طب) في آخره: وهو أهونه علي.
```

#### CSS Classes الرئيسية:
| Class | الوظيفة |
|---|---|
| `.cover` | غلاف الكتاب |
| `.toc-item` | عنصر في الفهرس |
| `.toc-number` | رقم الباب في الفهرس |
| `.chapter-header` | رأس الباب |
| `.hadith` | بطاقة الحديث |
| `.hadith-number` | رقم الحديث |
| `.hadith-grade` | درجة الحديث |
| `.grade-sahih` | لون أخضر للصحيح |
| `.grade-hasan` | لون أزرق للحسن |
| `.grade-daif` | لون أصفر للضعيف |
| `.hadith-text` | نص الحديث |
| `.hadith-sources` | قسم التخريج |
| `.source-badge` | شارة المصدر |
| `.page-break` | فاصل صفحة |

---

### 3. أزرار PDF في `show.blade.php`

**المنطق:**
```php
@php
    $pdfBook = $parentBook ?? $book;
    $isChapter = $parentBook !== null;
@endphp
```

**الأزرار المعروضة:**

| الزر | الرابط | يظهر متى |
|---|---|---|
| PDF المستخرج (الكتاب) | `/books/{pdfBook}/pdf` | دائماً |
| PDF الأصل (الكتاب) | `/books/{pdfBook}/pdf?type=original` | دائماً |
| PDF هذا الباب | `/books/{book}/pdf` | فقط إذا `$isChapter` |
| PDF هذا الباب (الأصل) | `/books/{book}/pdf?type=original` | فقط إذا `$isChapter` |

---

## مسار (Route) في `routes/web.php`
```php
Route::get('/books/{book}/pdf', [FrontendBookController::class, 'exportPdf'])->name('books.pdf');
```

---

## ملاحظات تقنية
- mPDF لا يدعم Tailwind CSS — القالب يستخدم CSS عادي (vanilla CSS)
- `<bookmark>` و `<htmlpagefooter>` هي عناصر خاصة بـ mPDF وليست HTML قياسي
- `{PAGENO}` و `{nbpg}` هي متغيرات mPDF لرقم الصفحة وإجمالي الصفحات
- الـ Footer يتضمن نوع النسخة (أصلي/مستخرج) لتمييز الملفات
- تمت إضافة headers لمنع التخزين المؤقت (`Cache-Control: no-store`)
