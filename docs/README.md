# 📖 التوثيق الفني — الفهرس العام

## نظرة عامة
هذا التوثيق يغطي جميع التعديلات والإضافات التي تمت على مشروع **موسوعة الحديث الصحيح** بتاريخ 2026-03-01.

---

## الملفات

| # | الملف | الموضوع | الأهمية |
|---|---|---|---|
| 01 | [browse-books-feature](./01-browse-books-feature.md) | ميزة تصفح الكتب والأبواب | ⭐⭐⭐ |
| 02 | [source-display-fix](./02-source-display-fix.md) | إصلاح عرض أسماء المصادر | ⭐ |
| 03 | [pdf-export](./03-pdf-export.md) | تصدير PDF التفاعلي (mPDF) | ⭐⭐⭐ |
| 04 | [hadith-parser-refactoring](./04-hadith-parser-refactoring.md) | تطوير محلل الأحاديث (تحميل ديناميكي من DB) | ⭐⭐⭐ |
| 05 | [reparse-command](./05-reparse-command.md) | أمر إعادة تحليل الأحاديث | ⭐⭐ |
| 06 | [bulk-import-validation](./06-bulk-import-validation.md) | تحسين الإدخال الجماعي (التحقق والإيقاف عند الخطأ) | ⭐⭐ |
| 07 | [source-dictionary](./07-source-dictionary.md) | قاموس المصادر الكامل | ⭐⭐ |
| 08 | [routes-reference](./08-routes-reference.md) | مرجع المسارات | ⭐ |
| 09 | [smart-narrator-matching](./09-smart-narrator-matching.md) | البحث الذكي عن الرواة (قاموس الاختصارات) | ⭐⭐⭐ |
| 10 | [database-cleanup](./10-database-cleanup.md) | صفحة تنظيف قاعدة البيانات | ⭐⭐ |

---

## ملخص التغييرات

### ملفات جديدة
```
app/Http/Controllers/Frontend/BookController.php
app/Http/Controllers/Dashboard/CleanupController.php
app/Console/Commands/ReparseHadiths.php
resources/views/frontend/books/index.blade.php
resources/views/frontend/books/show.blade.php
resources/views/frontend/books/pdf.blade.php
resources/views/dashboard/cleanup/index.blade.php
docs/ (هذا المجلد)
```

### ملفات مُعدَّلة
```
app/Services/HadithParser.php                           ← تحميل ديناميكي للمصادر + تطبيع التطويلة
app/Http/Controllers/Dashboard/HadithController.php     ← بحث ذكي عن الرواة + تحقق في الإدخال الجماعي
routes/web.php                                          ← 3 مسارات frontend + 9 مسارات cleanup
config/adminlte.php                                     ← رابط التنظيف في القائمة الجانبية
resources/views/frontend/home.blade.php                 ← ربط Navbar
resources/views/frontend/search.blade.php               ← ربط Navbar + إصلاح المصادر
resources/views/dashboard/hadiths/bulk-create.blade.php ← SweetAlert2 + عرض أخطاء التحليل
composer.json / composer.lock                           ← مكتبة mPDF
```

### حزم مُثبتة
```
carlos-meneses/laravel-mpdf ^2.1
```

---

## الهيكل المعماري

```
User Request
    ↓
routes/web.php
    ├── /books           → Frontend\BookController@index    → books/index.blade.php
    ├── /books/{book}    → Frontend\BookController@show     → books/show.blade.php
    ├── /books/{book}/pdf → Frontend\BookController@exportPdf → books/pdf.blade.php → mPDF
    └── /dashboard/...   → Dashboard\HadithController       → dashboard views
                              ↓
                         HadithParser (Service)
                              ↓
                    Source::pluck('name', 'code') ← DB
                              ↓
                    Parse: number, grade, narrator, sources, clean_text
```

---

## كيفية الرجوع للتوثيق

- **عند إضافة مصدر جديد** ← راجع `07-source-dictionary.md` و `05-reparse-command.md`
- **عند مشكلة في التحليل** ← راجع `04-hadith-parser-refactoring.md`
- **عند تعديل PDF** ← راجع `03-pdf-export.md`
- **عند إضافة صفحة جديدة** ← راجع `01-browse-books-feature.md` و `08-routes-reference.md`
- **عند مشكلة في الإدخال الجماعي** ← راجع `06-bulk-import-validation.md`
- **عند مشكلة في اسم الراوي** ← راجع `09-smart-narrator-matching.md`
- **لتنظيف قاعدة البيانات** ← راجع `10-database-cleanup.md`
