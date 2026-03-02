# 🔧 إصلاح عرض أسماء المصادر (Source Display Fix)

## تاريخ التنفيذ
2026-03-01

## المشكلة
في صفحات عرض الأحاديث (تصفح الكتب + نتائج البحث)، كانت المصادر تُعرض بالرموز المختصرة فقط مثل:
```
حم | طب | م
```
بدلاً من الأسماء الكاملة:
```
مسند أحمد | المعجم الكبير للطبراني | صحيح مسلم
```

## السبب
الكود كان يستخدم `$source->code` بدلاً من `$source->name`.

## الحل

### الملفات المُعدَّلة

#### 1. `resources/views/frontend/books/show.blade.php`
```diff
- <span>{{ $source->code }}</span>
+ <span>{{ $source->name }}</span>
```
- أيضاً تمت إضافة `flex-wrap` على div المصادر لمنع الطفح (overflow) على الشاشات الصغيرة.

#### 2. `resources/views/frontend/search.blade.php`
```diff
- <span>{{ $source->code }}</span>
+ <span>{{ $source->name }}</span>
```

## نموذج `Source`
```php
// app/Models/Source.php
class Source extends Model {
    protected $fillable = ['name', 'code', 'type'];
}
```

| الحقل | مثال | الاستخدام |
|---|---|---|
| `name` | صحيح البخاري | العرض في الواجهة ✅ |
| `code` | خ | التحليل الآلي (Parser) |
| `type` | صحيح | تصنيف المصدر |

## ملاحظة
صفحة `hadith-show.blade.php` كانت تعرض `$source->name` بالفعل — الإصلاح كان فقط لجعل باقي الصفحات متسقة.
