# 🗺️ مرجع المسارات (Routes Reference)

## تاريخ التحديث
2026-03-01

---

## المسارات الأمامية (Frontend)

### الصفحة الرئيسية والبحث
| Method | URI | Controller | Name | الوصف |
|---|---|---|---|---|
| GET | `/` | HomeController@index | `home` | الصفحة الرئيسية |
| GET | `/search` | SearchController@index | `search` | البحث |
| GET | `/hadith/{hadith}` | HadithController@show | `hadith.show` | عرض حديث |
| GET | `/random` | HadithController@random | `hadith.random` | حديث عشوائي |

### تصفح الكتب (جديد)
| Method | URI | Controller | Name | الوصف |
|---|---|---|---|---|
| GET | `/books` | Frontend\BookController@index | `books.index` | فهرس الكتب |
| GET | `/books/{book}` | Frontend\BookController@show | `books.show` | عرض كتاب/باب |
| GET | `/books/{book}/pdf` | Frontend\BookController@exportPdf | `books.pdf` | تصدير PDF |

#### معاملات PDF:
```
/books/2/pdf              ← PDF المستخرج لكتاب الإيمان
/books/2/pdf?type=original ← PDF الأصل لكتاب الإيمان
/books/354/pdf            ← PDF المستخرج لباب فضل الإيمان فقط
/books/354/pdf?type=original ← PDF الأصل لباب فضل الإيمان فقط
```

---

## مسارات لوحة التحكم (Dashboard)

### إدارة الكتب
| Method | URI | Name | الوصف |
|---|---|---|---|
| GET | `/dashboard/books` | `dashboard.books.index` | قائمة الكتب |
| GET | `/dashboard/books/create` | `dashboard.books.create` | إنشاء كتاب |
| POST | `/dashboard/books` | `dashboard.books.store` | حفظ كتاب |
| GET | `/dashboard/books/{book}` | `dashboard.books.show` | عرض كتاب |
| GET | `/dashboard/books/{book}/edit` | `dashboard.books.edit` | تعديل كتاب |
| PUT | `/dashboard/books/{book}` | `dashboard.books.update` | تحديث كتاب |
| DELETE | `/dashboard/books/{book}` | `dashboard.books.destroy` | حذف كتاب |

### إدارة الأحاديث
| Method | URI | Name | الوصف |
|---|---|---|---|
| GET | `/dashboard/hadiths` | `dashboard.hadiths.index` | قائمة الأحاديث |
| POST | `/dashboard/hadiths` | `dashboard.hadiths.store` | حفظ حديث |
| PUT | `/dashboard/hadiths/{hadith}` | `dashboard.hadiths.update` | تحديث حديث |
| DELETE | `/dashboard/hadiths/{hadith}` | `dashboard.hadiths.destroy` | حذف حديث |
| POST | `/dashboard/hadiths/parse` | `dashboard.hadiths.parse` | تحليل نص (AJAX) |

### الإدخال الجماعي
| Method | URI | Name | الوصف |
|---|---|---|---|
| GET | `/dashboard/hadiths/bulk` | `dashboard.hadiths.bulk.create` | صفحة الإدخال الجماعي |
| POST | `/dashboard/hadiths/bulk/preview` | `dashboard.hadiths.bulk.preview` | معاينة (AJAX) |
| POST | `/dashboard/hadiths/bulk/store` | `dashboard.hadiths.bulk.store` | حفظ جماعي |

---

## أوامر Artisan المخصصة

| الأمر | الوصف |
|---|---|
| `php artisan hadiths:reparse` | إعادة تحليل الأحاديث وربط المصادر |
| `php artisan hadiths:reparse --dry-run` | معاينة التغييرات بدون حفظ |
