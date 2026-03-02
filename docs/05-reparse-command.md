# 🔄 أمر إعادة تحليل الأحاديث (ReparseHadiths Command)

## تاريخ التنفيذ
2026-03-01

## الملف
`app/Console/Commands/ReparseHadiths.php`

---

## الهدف
عند إضافة مصدر جديد لقاعدة البيانات، الأحاديث المُدخلة سابقاً لا يتم ربطها بالمصدر الجديد تلقائياً. هذا الأمر يُعيد تحليل كل الأحاديث ويُعيد ربط المصادر.

---

## الاستخدام

### معاينة بدون حفظ (Dry Run):
```bash
php artisan hadiths:reparse --dry-run
```

### تنفيذ فعلي:
```bash
php artisan hadiths:reparse
```

---

## Signature
```php
protected $signature = 'hadiths:reparse {--dry-run : Show what would change without saving}';
protected $description = 'Re-parse all hadiths raw_text to re-link sources, narrators, and grades';
```

---

## المنطق التفصيلي

### 1. جلب الأحاديث
```php
$hadiths = Hadith::whereNotNull('raw_text')
    ->where('raw_text', '!=', '')
    ->with(['sources', 'narrator'])
    ->get();
```
> يجلب فقط الأحاديث التي لديها `raw_text` غير فارغ.

### 2. تحليل كل حديث
```php
$parsed = $parser->parse($hadith->raw_text);
```

### 3. البحث عن المصادر

**طريقة 1 — بالاسم:**
```php
foreach ($parsed['sources'] as $sourceName) {
    $source = Source::where('name', $sourceName)->first();
    if ($source) $sourceIds[] = $source->id;
}
```

**طريقة 2 — بالرمز (fallback):**
```php
foreach ($parsed['source_codes'] as $code) {
    $source = Source::where('code', $code)->first();
    if ($source && !in_array($source->id, $sourceIds)) {
        $sourceIds[] = $source->id;
    }
}
```

### 4. المقارنة والتحديث
```php
$currentSourceIds = $hadith->sources->pluck('id')->toArray();
$newIds = array_diff($sourceIds, $currentSourceIds);

if (!empty($newIds) || count($sourceIds) !== count($currentSourceIds)) {
    if (!$isDryRun) {
        $hadith->sources()->sync($sourceIds);
    }
}
```

> استخدام `sync()` وليس `attach()` لأنه يُعيد تعيين كل العلاقات (يحذف القديم ويضيف الجديد).

---

## المخرجات

### شريط التقدم (Progress Bar):
```
 48/48 [============================] 100%
```

### جدول التغييرات:
```
+----------+-----------------------+------------------+
| الحديث   | المصادر (قبل → بعد)   | مصادر جديدة      |
+----------+-----------------------+------------------+
| حديث #35 | 2 → 3                 | مسند الطيالسي    |
| حديث #64 | 1 → 2                 | مسند الطيالسي    |
+----------+-----------------------+------------------+
```

### رسالة النتيجة:
```
✅ تم تحديث 2 حديث — 2 ربط مصدر جديد
```

### في وضع Dry Run:
```
✅ سيتم تحديث 2 حديث — 2 ربط مصدر جديد
⚠️  شغّل الأمر بدون --dry-run لتطبيق التغييرات
```

---

## ملاحظات
- الأمر **آمن للتشغيل المتكرر** — `sync()` لا يُكرر العلاقات الموجودة
- يستخدم `HadithParser` المُحدَّث الذي يحمّل المصادر من DB
- يُنصح بتشغيل `--dry-run` أولاً دائماً
