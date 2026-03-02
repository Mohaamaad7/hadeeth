# ⛔ تحسين الإدخال الجماعي — التحقق من التحليل (Bulk Import Validation)

## تاريخ التنفيذ
2026-03-01

## الهدف
عند إدخال أحاديث جماعياً، إذا لم يستطع النظام تحليل حديث ما (لم يتعرف على الرقم، الحكم، الراوي، أو المصادر) ← **يتوقف كل شيء** ويعرض رسالة خطأ تفصيلية تحدد المشكلة بالضبط.

---

## الملفات المُعدَّلة

### 1. `app/Http/Controllers/Dashboard/HadithController.php`

#### دالة `bulkPreview()` — خط الدفاع الأول

**السلوك القديم:**
```php
// يُرسل النتائج بدون أي تحقق
return response()->json([
    'success' => true,
    'count' => count($results),
    'hadiths' => $results,
]);
```

**السلوك الجديد:**
```php
$errors = [];
foreach ($results as $index => $item) {
    $parsed = $item['parsed'];
    $hadithErrors = [];

    // 1. فحص رقم الحديث
    if (empty($parsed['number'])) {
        $hadithErrors[] = 'لم يتم العثور على رقم الحديث [xxx]';
    }

    // 2. فحص الحكم
    if (empty($parsed['grade'])) {
        $hadithErrors[] = 'لم يتم العثور على الحكم (صحيح/حسن/ضعيف)';
    }

    // 3. فحص الراوي
    if (empty($parsed['narrator'])) {
        $hadithErrors[] = 'لم يتم العثور على الراوي (عن xxx)';
    }

    // 4. فحص المصادر
    if (empty($parsed['sources']) && empty($parsed['source_codes'])) {
        $hadithErrors[] = 'لم يتم العثور على أي مصدر (خ م د ت ...)';
    }

    // 5. فحص النص النظيف
    if (empty($parsed['clean_text'])) {
        $hadithErrors[] = 'لم يتم استخراج نص الحديث';
    }

    // 6. فحص رموز مصادر غير معروفة
    if (!empty($parsed['source_codes'])) {
        foreach ($parsed['source_codes'] as $code) {
            $source = Source::where('code', $code)->first();
            if (!$source) {
                $hadithErrors[] = "رمز مصدر غير معروف: «{$code}» — أضفه من لوحة التحكم أولاً";
            }
        }
    }

    if (!empty($hadithErrors)) {
        $errors[] = [
            'index' => $index + 1,
            'snippet' => mb_substr($item['raw'], 0, 60) . '...',
            'errors' => $hadithErrors,
        ];
    }
}

// إذا وُجدت أخطاء → 422 مع التفاصيل
if (!empty($errors)) {
    return response()->json([
        'success' => false,
        'message' => 'يوجد ' . count($errors) . ' حديث به مشاكل في التحليل',
        'errors' => $errors,
        'count' => count($results),
        'hadiths' => $results,
    ], 422);
}
```

#### الاستجابة عند الخطأ (HTTP 422):
```json
{
  "success": false,
  "message": "يوجد 2 حديث به مشاكل في التحليل",
  "errors": [
    {
      "index": 3,
      "snippet": "فتر الوحي عني فترة فبينا أنا أمشي سمعت...",
      "errors": [
        "رمز مصدر غير معروف: «الطيالسي» — أضفه من لوحة التحكم أولاً"
      ]
    },
    {
      "index": 7,
      "snippet": "نص حديث آخر...",
      "errors": [
        "لم يتم العثور على رقم الحديث [xxx]",
        "لم يتم العثور على الحكم (صحيح/حسن/ضعيف)"
      ]
    }
  ],
  "count": 10,
  "hadiths": [...]
}
```

---

#### دالة `bulkStore()` — خط الدفاع الثاني

**تحقق إضافي قبل الحفظ:**
```php
$errors = [];
foreach ($request->hadiths as $index => $hadithData) {
    $hadithErrors = [];

    if (empty($hadithData['clean_text']) || mb_strlen(trim($hadithData['clean_text'])) < 5) {
        $hadithErrors[] = 'نص الحديث فارغ أو قصير جداً';
    }
    if (empty($hadithData['grade'])) {
        $hadithErrors[] = 'لم يتم تحديد الحكم';
    }

    if (!empty($hadithErrors)) {
        $errors[] = "حديث #" . ($index + 1) . ": " . implode('، ', $hadithErrors);
    }
}

if (!empty($errors)) {
    return redirect()->back()
        ->withInput()
        ->with('error', '⛔ تم إيقاف الإدخال!')
        ->with('parsing_errors', $errors);
}
```

> **ملاحظة:** `bulkStore` يتحقق فقط من النص والحكم (لأن الباقي اختياري في مرحلة الحفظ). التحقق الشامل يكون في `bulkPreview`.

---

### 2. `resources/views/dashboard/hadiths/bulk-create.blade.php`

#### تحديث معالج خطأ AJAX:

**السلوك القديم:**
```javascript
error: function (xhr) {
    alert('حدث خطأ أثناء التحليل: ' + (xhr.responseJSON?.message || 'خطأ غير معروف'));
}
```

**السلوك الجديد:**
```javascript
error: function (xhr) {
    if (xhr.status === 422 && xhr.responseJSON?.errors) {
        const data = xhr.responseJSON;

        // بناء جدول HTML بالأخطاء
        let errorHtml = `<div style="text-align:right; direction:rtl;">`;
        errorHtml += `<p style="color:#dc3545;">⛔ ${data.message}</p>`;
        errorHtml += `<table style="width:100%;">`;

        data.errors.forEach(function(err) {
            errorHtml += `<tr>`;
            errorHtml += `<td>حديث #${err.index}</td>`;
            errorHtml += `<td>`;
            errorHtml += `<div style="color:#666;">${err.snippet}</div>`;
            err.errors.forEach(function(e) {
                errorHtml += `<div style="color:#dc3545;">❌ ${e}</div>`;
            });
            errorHtml += `</td></tr>`;
        });
        errorHtml += `</table></div>`;

        // SweetAlert إذا متاح، وإلا alert عادي
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'مشاكل في التحليل',
                html: errorHtml,
                width: '700px',
                confirmButtonText: 'فهمت، سأصلح النصوص',
            });
        } else {
            // Fallback: alert عادي بتنسيق نصي
            let plainErrors = data.message + '\n\n';
            data.errors.forEach(function(err) {
                plainErrors += `حديث #${err.index}:\n`;
                err.errors.forEach(function(e) {
                    plainErrors += `  ❌ ${e}\n`;
                });
            });
            alert(plainErrors);
        }
    } else {
        alert('حدث خطأ أثناء التحليل');
    }
}
```

---

## مخطط سير العمل (Flow)

```
المستخدم يلصق الأحاديث → "تحليل"
    ↓
bulkPreview() [AJAX POST]
    ↓
parseMultiple() → تحليل كل حديث
    ↓
فحص كل حديث:
├── رقم؟ ✅/❌
├── حكم؟ ✅/❌
├── راوي؟ ✅/❌
├── مصادر؟ ✅/❌
├── رمز غير معروف؟ ✅/❌
└── نص نظيف؟ ✅/❌
    ↓
┌── كل شيء ✅ → عرض جدول المعاينة → المستخدم يراجع → "حفظ"
│                                                         ↓
│                                                    bulkStore()
│                                                         ↓
│                                                    تحقق ثاني (نص + حكم)
│                                                    ├── ✅ → حفظ → نجاح 🎉
│                                                    └── ❌ → إيقاف + رسالة خطأ
│
└── أي ❌ → عرض رسالة خطأ تفصيلية (422)
         → المستخدم يصلح النصوص ويعيد المحاولة
```

---

## قائمة الفحوصات (Validation Checklist)

| الفحص | في Preview | في Store | مثال خطأ |
|---|:---:|:---:|---|
| رقم الحديث `[xxx]` | ✅ | — | لم يتم العثور على رقم الحديث |
| حكم `(صحيح)` | ✅ | ✅ | لم يتم العثور على الحكم |
| راوي `عن xxx` | ✅ | — | لم يتم العثور على الراوي |
| مصادر `(حم م)` | ✅ | — | لم يتم العثور على أي مصدر |
| نص نظيف | ✅ | ✅ | لم يتم استخراج نص الحديث / قصير جداً |
| رمز مصدر غير معروف | ✅ | — | رمز مصدر غير معروف: «الطيالسي» |
