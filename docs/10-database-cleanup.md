# 🧹 صفحة تنظيف قاعدة البيانات (Database Cleanup)

## تاريخ التنفيذ
2026-03-02

## الملفات
- `app/Http/Controllers/Dashboard/CleanupController.php`
- `resources/views/dashboard/cleanup/index.blade.php`
- `config/adminlte.php` (إضافة رابط في القائمة الجانبية)
- `routes/web.php` (9 مسارات جديدة)

---

## الرابط
```
http://hadeeth.test/dashboard/cleanup
```
موجود في القائمة الجانبية تحت **الإعدادات** بأيقونة مكنسة حمراء 🧹

---

## الإحصائيات المعروضة

### بطاقات رئيسية (4 بطاقات):
| البطاقة | المعلومة |
|---|---|
| الأحاديث | العدد الكلي |
| الكتب والأبواب | العدد الكلي |
| الرواة | العدد الكلي |
| المصادر | العدد الكلي |

### تفاصيل كل قسم:
| القسم | الإحصائيات |
|---|---|
| الأحاديث | مع/بدون راوي، مع/بدون مصادر، مع نص أصلي |
| الكتب | رئيسية، أبواب، أبواب فارغة |
| الرواة | مرتبطون بأحاديث، أيتام (بلا أحاديث) |
| المصادر | مرتبطة بأحاديث، أيتام |

---

## عمليات الحذف (8 عمليات)

### 1. حذف جميع الأحاديث
- **المسار:** `POST /dashboard/cleanup/hadiths`
- **يحذف:** الأحاديث + `hadith_source` + سلاسل الإسناد + `chain_narrators`
- **التأكيد:** كتابة `DELETE`

### 2. حذف الرواة الأيتام
- **المسار:** `POST /dashboard/cleanup/narrators/orphan`
- **يحذف:** فقط الرواة بدون أحاديث وبدون سلاسل
- **التأكيد:** كتابة `DELETE`

### 3. حذف جميع الرواة
- **المسار:** `POST /dashboard/cleanup/narrators/all`
- **الشرط:** ⛔ لا يمكن إذا هناك أحاديث مرتبطة (الزر معطّل)
- **التأكيد:** كتابة `DELETE`

### 4. حذف الأبواب الفارغة
- **المسار:** `POST /dashboard/cleanup/books/empty`
- **يحذف:** فقط الأبواب (`parent_id != null`) بدون أحاديث
- **التأكيد:** كتابة `DELETE`

### 5. حذف جميع الكتب
- **المسار:** `POST /dashboard/cleanup/books/all`
- **الشرط:** ⛔ لا يمكن إذا هناك أحاديث مرتبطة (الزر معطّل)
- **ترتيب الحذف:** الأبواب (children) أولاً ثم الكتب الرئيسية
- **التأكيد:** كتابة `DELETE`

### 6. حذف المصادر الأيتام
- **المسار:** `POST /dashboard/cleanup/sources/orphan`
- **ملاحظة:** المصادر هي القاموس الأساسي — لا يُنصح بحذفها كلها
- **التأكيد:** كتابة `DELETE`

### 7. حذف سلاسل الإسناد
- **المسار:** `POST /dashboard/cleanup/chains`
- **يحذف:** `chain_narrators` + `hadith_chains`
- **التأكيد:** كتابة `DELETE`

### 8. 💣 تنظيف شامل (Nuke)
- **المسار:** `POST /dashboard/cleanup/nuke`
- **يحذف كل شيء بالترتيب:**
  1. `hadith_source` (الربط)
  2. `chain_narrators` (ربط السلاسل)
  3. `hadith_chains` (السلاسل)
  4. `hadiths` (الأحاديث)
  5. الأبواب (`books` where `parent_id != null`)
  6. الكتب الرئيسية (`books`)
  7. الرواة (`narrators`)
- **لا يحذف:** المصادر (`sources`) + المستخدمين (`users`)
- **التأكيد:** كتابة `NUKE`

---

## الحماية المنطقية

```
الأحاديث ← يمكن حذفها دائماً (تحذف العلاقات معها)
    ↓
الرواة ← لا يمكن حذف الكل إلا بعد حذف الأحاديث
    ↓
الكتب ← لا يمكن حذف الكل إلا بعد حذف الأحاديث
```

### أزرار معطّلة (disabled):
- زر "حذف جميع الرواة" ← معطّل إذا هناك رواة مرتبطون بأحاديث
- زر "حذف جميع الكتب" ← معطّل إذا هناك أحاديث

---

## التأكيد المزدوج (SweetAlert2)

كل عملية حذف تتطلب:
1. **نقر الزر** ← يظهر SweetAlert2
2. **قراءة التحذير** ← يوضح ما سيُحذف
3. **كتابة كلمة التأكيد** ← `DELETE` أو `NUKE`
4. **نقر "تنفيذ الحذف"** ← يُرسل الفورم

```javascript
// مثال: إذا كتب المستخدم كلمة خاطئة
Swal.showValidationMessage('اكتب "DELETE" للتأكيد');
```

---

## المسارات الجديدة

| Method | URI | Name |
|---|---|---|
| GET | `/dashboard/cleanup` | `dashboard.cleanup.index` |
| POST | `/dashboard/cleanup/hadiths` | `dashboard.cleanup.hadiths` |
| POST | `/dashboard/cleanup/narrators/orphan` | `dashboard.cleanup.narrators.orphan` |
| POST | `/dashboard/cleanup/narrators/all` | `dashboard.cleanup.narrators.all` |
| POST | `/dashboard/cleanup/books/empty` | `dashboard.cleanup.books.empty` |
| POST | `/dashboard/cleanup/books/all` | `dashboard.cleanup.books.all` |
| POST | `/dashboard/cleanup/sources/orphan` | `dashboard.cleanup.sources.orphan` |
| POST | `/dashboard/cleanup/chains` | `dashboard.cleanup.chains` |
| POST | `/dashboard/cleanup/nuke` | `dashboard.cleanup.nuke` |
