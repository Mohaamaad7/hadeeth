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

---

## ملخص التغييرات

### ملفات جديدة
```
app/Http/Controllers/Frontend/BookController.php
app/Console/Commands/ReparseHadiths.php
resources/views/frontend/books/index.blade.php
resources/views/frontend/books/show.blade.php
resources/views/frontend/books/pdf.blade.php
docs/ (هذا المجلد)
```

### ملفات مُعدَّلة
```
app/Services/HadithParser.php                           ← تحميل ديناميكي للمصادر
app/Http/Controllers/Dashboard/HadithController.php     ← تحقق في الإدخال الجماعي
routes/web.php                                          ← 3 مسارات جديدة
resources/views/frontend/home.blade.php                 ← ربط Navbar
resources/views/frontend/search.blade.php               ← ربط Navbar + إصلاح المصادر
resources/views/dashboard/hadiths/bulk-create.blade.php ← عرض أخطاء التحليل
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
