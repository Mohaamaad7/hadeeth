# 🗺️ خارطة الطريق الكاملة - الموسوعة الرقمية لصحيح الجامع الصغير

## 📋 نظرة عامة على المشروع

**الهدف النهائي:** بناء محرك بحث حديثي ذكي ومتعدد اللغات لكتاب "صحيح الجامع الصغير" مع واجهة فخمة وتجربة مستخدم استثنائية.

**التقنيات المستخدمة:**
- Laravel 12 (Backend Framework)
- Filament 4 (Admin Panel)
- Tailwind CSS (Styling)
- Alpine.js (Frontend Interactivity)
- MySQL 8+ (Database)
- Laravel Scout + Meilisearch (Search Engine)

**مدة التنفيذ المتوقعة:** 8-12 أسبوع

---

## 🎯 المراحل الأساسية

```
المرحلة 1: البنية التحتية والإعداد        [أسبوع 1]
المرحلة 2: قاعدة البيانات والنماذج        [أسبوع 2]
المرحلة 3: لوحة التحكم (Filament)         [أسبوع 3-4]
المرحلة 4: معالج البيانات (Parser)        [أسبوع 5]
المرحلة 5: الواجهة الأمامية الأساسية      [أسبوع 6-7]
المرحلة 6: محرك البحث                     [أسبوع 8]
المرحلة 7: نظام الترجمة متعدد اللغات      [أسبوع 9]
المرحلة 8: التحسين والأداء                [أسبوع 10]
المرحلة 9: الصفحات الإضافية               [أسبوع 11]
المرحلة 10: الاختبار والإطلاق             [أسبوع 12]
```

---

# 📍 المرحلة 1: البنية التحتية والإعداد

## 🎯 الهدف
إعداد بيئة عمل احترافية مع جميع الأدوات والإعدادات الأساسية.

## ✅ المهام

### 1.1 تثبيت Laravel 12
```bash
composer create-project laravel/laravel sahih-jami "^12.0"
cd sahih-jami
```

**الإعدادات:**
- ضبط `.env`:
  - اسم التطبيق، URL
  - بيانات قاعدة البيانات
  - اللغة الافتراضية: `APP_LOCALE=ar`
  - المنطقة الزمنية: `APP_TIMEZONE=Africa/Cairo`
  - اتجاه النص: إضافة `APP_RTL=true`

### 1.2 تثبيت Filament 4
```bash
composer require filament/filament:"^4.0"
php artisan filament:install --panels
```

**الإعدادات:**
- إنشاء مستخدم Admin أولي:
```bash
php artisan make:filament-user
```

- تفعيل RTL في `app/Providers/Filament/AdminPanelProvider.php`:
```php
->default()
->id('admin')
->path('admin')
->login()
->colors([...])
->discoverResources(...)
->discoverPages(...)
->discoverWidgets(...)
->middleware([...])
->authMiddleware([...])
->spa() // لسرعة أكبر
->locale('ar')
->direction('rtl'); // دعم RTL
```

### 1.3 تثبيت الحزم الأساسية
```bash
# البحث
composer require laravel/scout
composer require meilisearch/meilisearch-php

# SEO
composer require artesaos/seotools

# Slugs عربية
composer require cviebrock/eloquent-sluggable

# أدوات إضافية
composer require spatie/laravel-query-builder
composer require spatie/laravel-permission
```

### 1.4 إعداد Git
```bash
git init
git add .
git commit -m "Initial commit: Laravel 12 + Filament 4"
```

**إنشاء `.gitignore` محسّن:**
```
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.phpunit.result.cache
npm-debug.log
yarn-error.log
```

### 1.5 إعداد TailwindCSS
```bash
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
```

**تخصيص `tailwind.config.js`:**
```javascript
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./app/Filament/**/*.php",
    "./vendor/filament/**/*.blade.php",
  ],
  theme: {
    extend: {
      colors: {
        'islamic-gold': '#D4AF37',
        'dark-brown': '#3E2723',
        'light-cream': '#FFF8E1',
      },
      fontFamily: {
        'arabic': ['Amiri', 'serif'],
        'modern': ['Cairo', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
```

## 📊 النتيجة المتوقعة
- ✅ Laravel 12 يعمل بنجاح
- ✅ Filament 4 مثبت مع دعم RTL
- ✅ جميع الحزم الأساسية جاهزة
- ✅ Git repository محلي نشط
- ✅ TailwindCSS جاهز للتخصيص

## ⚠️ نقاط الانتباه
- تأكد من PHP 8.2+ و Composer 2.x
- تأكد من MySQL 8+ أو MariaDB 10.3+
- احفظ نسخة من ملف `.env`

---

# 📍 المرحلة 2: قاعدة البيانات والنماذج (Models)

## 🎯 الهدف
بناء Schema متكامل وعلاقات قوية بين الجداول.

## ✅ المهام

### 2.1 تصميم Schema الأساسي

**إنشاء Migrations:**
```bash
# الكتب
php artisan make:migration create_books_table

# الأبواب (Chapters)
php artisan make:migration create_chapters_table

# الأحاديث
php artisan make:migration create_hadiths_table

# الرواة
php artisan make:migration create_narrators_table

# المصادر
php artisan make:migration create_sources_table

# جدول ربط الأحاديث بالمصادر
php artisan make:migration create_hadith_source_table

# الترجمات
php artisan make:migration create_hadith_translations_table

# التصنيفات (Categories) - شجرية
php artisan make:migration create_categories_table
```

### 2.2 محتوى Migrations التفصيلي

#### `books` - الكتب
```php
Schema::create('books', function (Blueprint $table) {
    $table->id();
    $table->string('name_ar');
    $table->string('name_en')->nullable();
    $table->string('slug')->unique();
    $table->text('description_ar')->nullable();
    $table->text('description_en')->nullable();
    $table->integer('order_index')->default(0); // ترتيب العرض
    $table->integer('hadiths_count')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index('order_index');
    $table->index('is_active');
});
```

#### `chapters` - الأبواب
```php
Schema::create('chapters', function (Blueprint $table) {
    $table->id();
    $table->foreignId('book_id')->constrained()->cascadeOnDelete();
    $table->string('name_ar');
    $table->string('name_en')->nullable();
    $table->string('slug')->unique();
    $table->text('description_ar')->nullable();
    $table->integer('start_hadith_number')->nullable();
    $table->integer('end_hadith_number')->nullable();
    $table->integer('order_index')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index(['book_id', 'order_index']);
});
```

#### `narrators` - الرواة
```php
Schema::create('narrators', function (Blueprint $table) {
    $table->id();
    $table->string('name_ar');
    $table->string('name_en')->nullable();
    $table->string('slug')->unique();
    $table->text('biography_ar')->nullable(); // نبذة مختصرة
    $table->text('biography_en')->nullable();
    $table->string('full_name_ar')->nullable(); // الاسم الكامل
    $table->integer('hadiths_count')->default(0);
    $table->string('era')->nullable(); // الطبقة (صحابي، تابعي...)
    $table->boolean('is_sahabi')->default(false); // هل هو صحابي
    $table->timestamps();
    
    $table->index('hadiths_count');
    $table->index('is_sahabi');
});
```

#### `hadiths` - الأحاديث
```php
Schema::create('hadiths', function (Blueprint $table) {
    $table->id();
    $table->integer('number')->unique(); // رقم الحديث [144]
    $table->text('text_ar'); // نص الحديث
    $table->string('slug')->unique();
    
    // العلاقات
    $table->foreignId('book_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('chapter_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('narrator_id')->constrained()->cascadeOnDelete();
    
    // الحكم
    $table->enum('ruling', ['صحيح', 'حسن', 'ضعيف', 'موضوع'])->default('صحيح');
    
    // الإحصائيات
    $table->integer('views_count')->default(0); // عدد المشاهدات
    $table->integer('favorites_count')->default(0);
    
    // SEO
    $table->text('meta_description')->nullable();
    
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    // Indexes
    $table->index('number');
    $table->index('ruling');
    $table->index(['book_id', 'chapter_id']);
    $table->index('narrator_id');
    $table->index('views_count');
    $table->fullText(['text_ar']); // للبحث النصي
});
```

#### `sources` - المصادر
```php
Schema::create('sources', function (Blueprint $table) {
    $table->id();
    $table->string('code', 10)->unique(); // خ، م، ق، د...
    $table->string('name_ar');
    $table->string('name_en')->nullable();
    $table->string('full_name_ar'); // الجامع الصحيح للبخاري
    $table->string('author_ar'); // الإمام البخاري
    $table->text('description_ar')->nullable();
    $table->integer('order_index')->default(0);
    $table->string('color')->default('#000000'); // لون مميز للواجهة
    $table->timestamps();
    
    $table->index('code');
});
```

#### `hadith_source` - جدول الربط (Many-to-Many)
```php
Schema::create('hadith_source', function (Blueprint $table) {
    $table->id();
    $table->foreignId('hadith_id')->constrained()->cascadeOnDelete();
    $table->foreignId('source_id')->constrained()->cascadeOnDelete();
    $table->string('reference_number')->nullable(); // رقم الحديث في المصدر
    $table->timestamps();
    
    $table->unique(['hadith_id', 'source_id']);
});
```

#### `hadith_translations` - الترجمات
```php
Schema::create('hadith_translations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('hadith_id')->constrained()->cascadeOnDelete();
    $table->string('locale', 5); // en, fr, ur, id...
    $table->text('text'); // النص المترجم
    $table->string('translator_name')->nullable();
    $table->boolean('is_verified')->default(false); // هل مراجعة علميًا
    $table->timestamps();
    
    $table->unique(['hadith_id', 'locale']);
    $table->index('locale');
    $table->fullText(['text']);
});
```

#### `categories` - التصنيفات الشجرية
```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('parent_id')->nullable()->constrained('categories')->cascadeOnDelete();
    $table->string('name_ar');
    $table->string('name_en')->nullable();
    $table->string('slug')->unique();
    $table->text('description_ar')->nullable();
    $table->integer('order_index')->default(0);
    $table->integer('depth')->default(0); // المستوى في الشجرة
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index('parent_id');
    $table->index(['parent_id', 'order_index']);
});

// جدول ربط الأحاديث بالتصنيفات (Many-to-Many)
Schema::create('category_hadith', function (Blueprint $table) {
    $table->foreignId('category_id')->constrained()->cascadeOnDelete();
    $table->foreignId('hadith_id')->constrained()->cascadeOnDelete();
    
    $table->primary(['category_id', 'hadith_id']);
});
```

### 2.3 إنشاء Models

```bash
php artisan make:model Book
php artisan make:model Chapter
php artisan make:model Hadith
php artisan make:model Narrator
php artisan make:model Source
php artisan make:model HadithTranslation
php artisan make:model Category
```

### 2.4 كتابة العلاقات في Models

#### `Book.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Cviebrock\EloquentSluggable\Sluggable;

class Book extends Model
{
    use Sluggable;

    protected $fillable = [
        'name_ar', 'name_en', 'slug', 
        'description_ar', 'description_en',
        'order_index', 'hadiths_count', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name_ar'
            ]
        ];
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('order_index');
    }

    public function hadiths(): HasMany
    {
        return $this->hasMany(Hadith::class);
    }

    // Scope للكتب النشطة فقط
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

#### `Chapter.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Cviebrock\EloquentSluggable\Sluggable;

class Chapter extends Model
{
    use Sluggable;

    protected $fillable = [
        'book_id', 'name_ar', 'name_en', 'slug',
        'description_ar', 'start_hadith_number',
        'end_hadith_number', 'order_index', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name_ar'
            ]
        ];
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function hadiths(): HasMany
    {
        return $this->hasMany(Hadith::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

#### `Narrator.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Cviebrock\EloquentSluggable\Sluggable;

class Narrator extends Model
{
    use Sluggable;

    protected $fillable = [
        'name_ar', 'name_en', 'slug',
        'biography_ar', 'biography_en',
        'full_name_ar', 'hadiths_count',
        'era', 'is_sahabi'
    ];

    protected $casts = [
        'is_sahabi' => 'boolean',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name_ar'
            ]
        ];
    }

    public function hadiths(): HasMany
    {
        return $this->hasMany(Hadith::class);
    }

    // Scope للصحابة
    public function scopeSahaba($query)
    {
        return $query->where('is_sahabi', true);
    }

    // Scope للأكثر رواية
    public function scopeTopNarrators($query, $limit = 10)
    {
        return $query->orderBy('hadiths_count', 'desc')->limit($limit);
    }
}
```

#### `Hadith.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use Cviebrock\EloquentSluggable\Sluggable;

class Hadith extends Model
{
    use Searchable, Sluggable;

    protected $fillable = [
        'number', 'text_ar', 'slug',
        'book_id', 'chapter_id', 'narrator_id',
        'ruling', 'views_count', 'favorites_count',
        'meta_description', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['number', 'text_ar'],
                'maxLength' => 100,
            ]
        ];
    }

    // Scout Searchable Configuration
    public function toSearchableArray()
    {
        return [
            'number' => $this->number,
            'text_ar' => $this->text_ar,
            'narrator' => $this->narrator->name_ar,
            'ruling' => $this->ruling,
        ];
    }

    // Relations
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function narrator(): BelongsTo
    {
        return $this->belongsTo(Narrator::class);
    }

    public function sources(): BelongsToMany
    {
        return $this->belongsToMany(Source::class)
                    ->withPivot('reference_number')
                    ->withTimestamps();
    }

    public function translations(): HasMany
    {
        return $this->hasMany(HadithTranslation::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    // Scopes
    public function scopeSahih($query)
    {
        return $query->where('ruling', 'صحيح');
    }

    public function scopeHasan($query)
    {
        return $query->where('ruling', 'حسن');
    }

    public function scopeByBook($query, $bookId)
    {
        return $query->where('book_id', $bookId);
    }

    public function scopeByNarrator($query, $narratorId)
    {
        return $query->where('narrator_id', $narratorId);
    }

    public function scopePopular($query, $limit = 10)
    {
        return $query->orderBy('views_count', 'desc')->limit($limit);
    }

    // Helper Methods
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function getTranslation($locale)
    {
        return $this->translations()
                    ->where('locale', $locale)
                    ->first();
    }
}
```

#### `Source.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Source extends Model
{
    protected $fillable = [
        'code', 'name_ar', 'name_en',
        'full_name_ar', 'author_ar',
        'description_ar', 'order_index', 'color'
    ];

    public function hadiths(): BelongsToMany
    {
        return $this->belongsToMany(Hadith::class)
                    ->withPivot('reference_number')
                    ->withTimestamps();
    }

    // Scope للترتيب
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index');
    }
}
```

#### `Category.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Cviebrock\EloquentSluggable\Sluggable;

class Category extends Model
{
    use Sluggable;

    protected $fillable = [
        'parent_id', 'name_ar', 'name_en', 'slug',
        'description_ar', 'order_index', 'depth', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name_ar'
            ]
        ];
    }

    // Relations
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')
                    ->orderBy('order_index');
    }

    public function hadiths(): BelongsToMany
    {
        return $this->belongsToMany(Hadith::class);
    }

    // Scopes
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id')->orderBy('order_index');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper للحصول على شجرة كاملة
    public function getFullPath()
    {
        $path = [$this->name_ar];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($path, $parent->name_ar);
            $parent = $parent->parent;
        }
        
        return implode(' > ', $path);
    }
}
```

### 2.5 Seeders للبيانات الأولية

```bash
php artisan make:seeder SourcesTableSeeder
```

**محتوى `SourcesTableSeeder.php`:**
```php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Source;

class SourcesTableSeeder extends Seeder
{
    public function run()
    {
        $sources = [
            ['code' => 'خ', 'name_ar' => 'البخاري', 'full_name_ar' => 'الجامع الصحيح', 'author_ar' => 'الإمام البخاري', 'order_index' => 1, 'color' => '#1B5E20'],
            ['code' => 'م', 'name_ar' => 'مسلم', 'full_name_ar' => 'الصحيح', 'author_ar' => 'الإمام مسلم', 'order_index' => 2, 'color' => '#0D47A1'],
            ['code' => 'ق', 'name_ar' => 'متفق عليه', 'full_name_ar' => 'البخاري ومسلم', 'author_ar' => 'البخاري ومسلم', 'order_index' => 3, 'color' => '#B71C1C'],
            ['code' => 'د', 'name_ar' => 'أبو داود', 'full_name_ar' => 'السنن', 'author_ar' => 'الإمام أبو داود', 'order_index' => 4, 'color' => '#F57C00'],
            ['code' => 'ت', 'name_ar' => 'الترمذي', 'full_name_ar' => 'الجامع', 'author_ar' => 'الإمام الترمذي', 'order_index' => 5, 'color' => '#7B1FA2'],
            ['code' => 'ن', 'name_ar' => 'النسائي', 'full_name_ar' => 'السنن', 'author_ar' => 'الإمام النسائي', 'order_index' => 6, 'color' => '#00838F'],
            ['code' => 'هـ', 'name_ar' => 'ابن ماجه', 'full_name_ar' => 'السنن', 'author_ar' => 'الإمام ابن ماجه', 'order_index' => 7, 'color' => '#558B2F'],
            ['code' => '4', 'name_ar' => 'أصحاب السنن الأربعة', 'full_name_ar' => 'أبو داود والترمذي والنسائي وابن ماجه', 'author_ar' => 'أصحاب السنن الأربعة', 'order_index' => 8, 'color' => '#6D4C41'],
            ['code' => '3', 'name_ar' => 'ثلاثة إلا ابن ماجه', 'full_name_ar' => 'أبو داود والترمذي والنسائي', 'author_ar' => 'أبو داود والترمذي والنسائي', 'order_index' => 9, 'color' => '#455A64'],
            ['code' => 'حم', 'name_ar' => 'أحمد', 'full_name_ar' => 'المسند', 'author_ar' => 'الإمام أحمد بن حنبل', 'order_index' => 10, 'color' => '#5D4037'],
            ['code' => 'عم', 'name_ar' => 'عبد الله بن أحمد', 'full_name_ar' => 'زوائد المسند', 'author_ar' => 'عبد الله بن أحمد', 'order_index' => 11, 'color' => '#4E342E'],
            ['code' => 'ك', 'name_ar' => 'الحاكم', 'full_name_ar' => 'المستدرك على الصحيحين', 'author_ar' => 'الإمام الحاكم', 'order_index' => 12, 'color' => '#BF360C'],
            ['code' => 'خد', 'name_ar' => 'البخاري في الأدب', 'full_name_ar' => 'الأدب المفرد', 'author_ar' => 'الإمام البخاري', 'order_index' => 13, 'color' => '#1B5E20'],
            ['code' => 'تخ', 'name_ar' => 'البخاري في التاريخ', 'full_name_ar' => 'التاريخ الكبير', 'author_ar' => 'الإمام البخاري', 'order_index' => 14, 'color' => '#2E7D32'],
            ['code' => 'حب', 'name_ar' => 'ابن حبان', 'full_name_ar' => 'الصحيح', 'author_ar' => 'الإمام ابن حبان', 'order_index' => 15, 'color' => '#1565C0'],
            ['code' => 'طب', 'name_ar' => 'الطبراني الكبير', 'full_name_ar' => 'المعجم الكبير', 'author_ar' => 'الإمام الطبراني', 'order_index' => 16, 'color' => '#283593'],
            ['code' => 'طس', 'name_ar' => 'الطبراني الأوسط', 'full_name_ar' => 'المعجم الأوسط', 'author_ar' => 'الإمام الطبراني', 'order_index' => 17, 'color' => '#303F9F'],
            ['code' => 'طص', 'name_ar' => 'الطبراني الصغير', 'full_name_ar' => 'المعجم الصغير', 'author_ar' => 'الإمام الطبراني', 'order_index' => 18, 'color' => '#3949AB'],
            ['code' => 'ص', 'name_ar' => 'سعيد بن منصور', 'full_name_ar' => 'السنن', 'author_ar' => 'سعيد بن منصور', 'order_index' => 19, 'color' => '#6A1B9A'],
            ['code' => 'ش', 'name_ar' => 'ابن أبي شيبة', 'full_name_ar' => 'المصنف', 'author_ar' => 'ابن أبي شيبة', 'order_index' => 20, 'color' => '#8E24AA'],
            ['code' => 'عب', 'name_ar' => 'عبد الرزاق', 'full_name_ar' => 'المصنف', 'author_ar' => 'عبد الرزاق الصنعاني', 'order_index' => 21, 'color' => '#C2185B'],
            ['code' => 'ع', 'name_ar' => 'أبو يعلى', 'full_name_ar' => 'المسند', 'author_ar' => 'الإمام أبو يعلى', 'order_index' => 22, 'color' => '#AD1457'],
            ['code' => 'قط', 'name_ar' => 'الدارقطني', 'full_name_ar' => 'السنن', 'author_ar' => 'الإمام الدارقطني', 'order_index' => 23, 'color' => '#D32F2F'],
            ['code' => 'فر', 'name_ar' => 'الديلمي', 'full_name_ar' => 'مسند الفردوس', 'author_ar' => 'الديلمي', 'order_index' => 24, 'color' => '#C62828'],
            ['code' => 'حل', 'name_ar' => 'أبو نعيم', 'full_name_ar' => 'حلية الأولياء', 'author_ar' => 'أبو نعيم الأصبهاني', 'order_index' => 25, 'color' => '#FF6F00'],
            ['code' => 'هب', 'name_ar' => 'البيهقي في الشعب', 'full_name_ar' => 'شعب الإيمان', 'author_ar' => 'الإمام البيهقي', 'order_index' => 26, 'color' => '#F57F17'],
            ['code' => 'هق', 'name_ar' => 'البيهقي', 'full_name_ar' => 'السنن الكبرى', 'author_ar' => 'الإمام البيهقي', 'order_index' => 27, 'color' => '#FBC02D'],
            ['code' => 'عد', 'name_ar' => 'ابن عدي', 'full_name_ar' => 'الكامل في ضعفاء الرجال', 'author_ar' => 'ابن عدي', 'order_index' => 28, 'color' => '#AFB42B'],
            ['code' => 'عق', 'name_ar' => 'العقيلي', 'full_name_ar' => 'الضعفاء', 'author_ar' => 'العقيلي', 'order_index' => 29, 'color' => '#827717'],
            ['code' => 'خط', 'name_ar' => 'الخطيب البغدادي', 'full_name_ar' => 'تاريخ بغداد', 'author_ar' => 'الخطيب البغدادي', 'order_index' => 30, 'color' => '#33691E'],
        ];

        foreach ($sources as $source) {
            Source::create($source);
        }
    }
}
```

**تشغيل Seeders:**
```bash
php artisan db:seed --class=SourcesTableSeeder
```

### 2.6 تشغيل Migrations

```bash
php artisan migrate:fresh --seed
```

## 📊 النتيجة المتوقعة
- ✅ قاعدة بيانات كاملة بـ 10 جداول رئيسية
- ✅ جميع العلاقات محددة بدقة (One-to-Many, Many-to-Many)
- ✅ Models جاهزة مع Scopes مفيدة
- ✅ 30 مصدراً حديثياً مُدخلة ومرتبة
- ✅ دعم Slugs عربية جاهز
- ✅ جاهزية للبحث مع Laravel Scout

## ⚠️ نقاط الانتباه
- احفظ نسخة احتياطية قبل `migrate:fresh`
- تأكد من صحة العلاقات بين الجداول
- راجع Indexes للتأكد من الأداء الأمثل

---

# 📍 المرحلة 3: لوحة التحكم (Filament 4)

## 🎯 الهدف
بناء لوحة تحكم احترافية لإدارة جميع عناصر الموقع بسهولة.

## ✅ المهام

### 3.1 إنشاء Resources لـ Filament

```bash
php artisan make:filament-resource Book --generate
php artisan make:filament-resource Chapter --generate
php artisan make:filament-resource Hadith --generate
php artisan make:filament-resource Narrator --generate
php artisan make:filament-resource Source --generate
php artisan make:filament-resource Category --generate
```

### 3.2 تخصيص BookResource

**الملف: `app/Filament/Resources/BookResource.php`**

```php
namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'الكتب';
    protected static ?string $modelLabel = 'كتاب';
    protected static ?string $pluralModelLabel = 'الكتب';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الكتاب')
                    ->schema([
                        Forms\Components\TextInput::make('name_ar')
                            ->label('الاسم بالعربية')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('name_en')
                            ->label('الاسم بالإنجليزية')
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('description_ar')
                            ->label('الوصف بالعربية')
                            ->rows(3),
                        
                        Forms\Components\Textarea::make('description_en')
                            ->label('الوصف بالإنجليزية')
                            ->rows(3),
                    ])->columns(2),
                
                Forms\Components\Section::make('الإعدادات')
                    ->schema([
                        Forms\Components\TextInput::make('order_index')
                            ->label('ترتيب العرض')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_ar')
                    ->label('اسم الكتاب')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('hadiths_count')
                    ->label('عدد الأحاديث')
                    ->numeric()
                    ->sortable()
                    ->badge(),
                
                Tables\Columns\TextColumn::make('order_index')
                    ->label('الترتيب')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('الحالة')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('الكتب النشطة فقط'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order_index', 'asc');
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
```

### 3.3 تخصيص HadithResource (الأهم)

**الملف: `app/Filament/Resources/HadithResource.php`**

```php
namespace App\Filament\Resources;

use App\Filament\Resources\HadithResource\Pages;
use App\Models\Hadith;
use App\Models\Source;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HadithResource extends Resource
{
    protected static ?string $model = Hadith::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'الأحاديث';
    protected static ?string $modelLabel = 'حديث';
    protected static ?string $pluralModelLabel = 'الأحاديث';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات الحديث الأساسية')
                    ->schema([
                        Forms\Components\TextInput::make('number')
                            ->label('رقم الحديث')
                            ->required()
                            ->numeric()
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\Select::make('ruling')
                            ->label('الحكم')
                            ->options([
                                'صحيح' => 'صحيح',
                                'حسن' => 'حسن',
                                'ضعيف' => 'ضعيف',
                                'موضوع' => 'موضوع',
                            ])
                            ->required()
                            ->default('صحيح'),
                    ])->columns(2),
                
                Forms\Components\Section::make('نص الحديث')
                    ->schema([
                        Forms\Components\Textarea::make('text_ar')
                            ->label('النص العربي')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('التصنيف')
                    ->schema([
                        Forms\Components\Select::make('book_id')
                            ->label('الكتاب')
                            ->relationship('book', 'name_ar')
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('chapter_id', null)),
                        
                        Forms\Components\Select::make('chapter_id')
                            ->label('الباب')
                            ->relationship('chapter', 'name_ar', function ($query, $get) {
                                if ($bookId = $get('book_id')) {
                                    return $query->where('book_id', $bookId);
                                }
                            })
                            ->searchable()
                            ->preload(),
                        
                        Forms\Components\Select::make('narrator_id')
                            ->label('الراوي')
                            ->relationship('narrator', 'name_ar')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(3),
                
                Forms\Components\Section::make('المصادر')
                    ->schema([
                        Forms\Components\Repeater::make('sources')
                            ->label('مصادر التخريج')
                            ->relationship('sources')
                            ->schema([
                                Forms\Components\Select::make('id')
                                    ->label('المصدر')
                                    ->options(Source::pluck('name_ar', 'id'))
                                    ->required(),
                                
                                Forms\Components\TextInput::make('reference_number')
                                    ->label('رقم الحديث في المصدر')
                                    ->maxLength(50),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('إضافة مصدر')
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('التصنيفات (Categories)')
                    ->schema([
                        Forms\Components\Select::make('categories')
                            ->label('التصنيفات')
                            ->relationship('categories', 'name_ar')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\Textarea::make('meta_description')
                            ->label('وصف SEO')
                            ->rows(2)
                            ->maxLength(160)
                            ->hint('يُستخدم في محركات البحث (160 حرف كحد أقصى)')
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('رقم')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('text_ar')
                    ->label('نص الحديث')
                    ->limit(60)
                    ->searchable()
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('narrator.name_ar')
                    ->label('الراوي')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('ruling')
                    ->label('الحكم')
                    ->colors([
                        'success' => 'صحيح',
                        'warning' => 'حسن',
                        'danger' => 'ضعيف',
                    ]),
                
                Tables\Columns\TextColumn::make('book.name_ar')
                    ->label('الكتاب')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('views_count')
                    ->label('المشاهدات')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('ruling')
                    ->label('الحكم')
                    ->options([
                        'صحيح' => 'صحيح',
                        'حسن' => 'حسن',
                        'ضعيف' => 'ضعيف',
                    ]),
                
                Tables\Filters\SelectFilter::make('book_id')
                    ->label('الكتاب')
                    ->relationship('book', 'name_ar')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('narrator_id')
                    ->label('الراوي')
                    ->relationship('narrator', 'name_ar')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('number', 'asc');
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHadiths::route('/'),
            'create' => Pages\CreateHadith::route('/create'),
            'edit' => Pages\EditHadith::route('/{record}/edit'),
            'view' => Pages\ViewHadith::route('/{record}'),
        ];
    }
}
```

### 3.4 تخصيص CategoryResource (الشجرية)

**الملف: `app/Filament/Resources/CategoryResource.php`**

```php
namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = 'التصنيفات';
    protected static ?string $modelLabel = 'تصنيف';
    protected static ?string $pluralModelLabel = 'التصنيفات';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('parent_id')
                            ->label('التصنيف الأب')
                            ->relationship('parent', 'name_ar')
                            ->searchable()
                            ->preload()
                            ->placeholder('- تصنيف رئيسي -'),
                        
                        Forms\Components\TextInput::make('name_ar')
                            ->label('الاسم بالعربية')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('name_en')
                            ->label('الاسم بالإنجليزية')
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('description_ar')
                            ->label('الوصف')
                            ->rows(3),
                        
                        Forms\Components\TextInput::make('order_index')
                            ->label('ترتيب العرض')
                            ->numeric()
                            ->default(0),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_ar')
                    ->label('التصنيف')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('parent.name_ar')
                    ->label('التصنيف الأب')
                    ->searchable()
                    ->sortable()
                    ->default('- رئيسي -'),
                
                Tables\Columns\TextColumn::make('depth')
                    ->label('المستوى')
                    ->badge(),
                
                Tables\Columns\TextColumn::make('hadiths_count')
                    ->label('عدد الأحاديث')
                    ->counts('hadiths')
                    ->badge(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('الحالة')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('التصنيف الأب')
                    ->relationship('parent', 'name_ar'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order_index', 'asc');
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
```

### 3.5 إضافة Dashboard Widgets (لوحة معلومات)

```bash
php artisan make:filament-widget StatsOverview
```

**الملف: `app/Filament/Widgets/StatsOverview.php`**

```php
namespace App\Filament\Widgets;

use App\Models\Hadith;
use App\Models\Narrator;
use App\Models\Book;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('إجمالي الأحاديث', Hadith::count())
                ->description('في قاعدة البيانات')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),
            
            Stat::make('الأحاديث الصحيحة', Hadith::where('ruling', 'صحيح')->count())
                ->description('بحكم الألباني')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary'),
            
            Stat::make('عدد الرواة', Narrator::count())
                ->description('في الموسوعة')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),
            
            Stat::make('عدد الكتب', Book::count())
                ->description('الكتب الرئيسية')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('info'),
        ];
    }
}
```

### 3.6 تخصيص Navigation في Filament

**الملف: `app/Providers/Filament/AdminPanelProvider.php`**

```php
public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->path('admin')
        ->login()
        ->colors([
            'primary' => '#D4AF37', // الذهبي الإسلامي
        ])
        ->navigationGroups([
            'المحتوى الأساسي',
            'التصنيفات',
            'المصادر والرواة',
            'الإعدادات',
        ])
        ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
        ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
        ->widgets([
            \App\Filament\Widgets\StatsOverview::class,
        ])
        ->middleware([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
        ])
        ->authMiddleware([
            Authenticate::class,
        ])
        ->spa()
        ->locale('ar')
        ->direction('rtl')
        ->brandName('صحيح الجامع')
        ->brandLogo(asset('images/logo.svg'));
}
```

### 3.7 تعديل Navigation Groups في Resources

في كل Resource، أضف:

```php
// في BookResource
protected static ?string $navigationGroup = 'المحتوى الأساسي';

// في HadithResource
protected static ?string $navigationGroup = 'المحتوى الأساسي';

// في CategoryResource
protected static ?string $navigationGroup = 'التصنيفات';

// في NarratorResource
protected static ?string $navigationGroup = 'المصادر والرواة';

// في SourceResource
protected static ?string $navigationGroup = 'المصادر والرواة';
```

## 📊 النتيجة المتوقعة
- ✅ لوحة تحكم احترافية بـ RTL كامل
- ✅ CRUD كامل لجميع الجداول
- ✅ واجهة سهلة لإدخال الأحاديث
- ✅ Filters و Search متقدم
- ✅ Dashboard بإحصائيات مباشرة
- ✅ Navigation منظم بمجموعات

## ⚠️ نقاط الانتباه
- تأكد من تفعيل SPA Mode للسرعة
- اختبر Form validation على جميع الحقول
- تأكد من عمل العلاقات في Select Fields

---

# 📍 المرحلة 4: معالج البيانات (Parser)

## 🎯 الهدف
بناء أداة ذكية تقرأ الأحاديث من ملف Word وتحللها تلقائياً.

## ✅ المهام

### 4.1 تثبيت حزمة قراءة Word

```bash
composer require phpoffice/phpword
```

### 4.2 إنشاء Parser Command

```bash
php artisan make:command ParseHadithsCommand
```

**الملف: `app/Console/Commands/ParseHadithsCommand.php`**

```php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpWord\IOFactory;
use App\Models\Hadith;
use App\Models\Narrator;
use App\Models\Source;
use Illuminate\Support\Str;

class ParseHadithsCommand extends Command
{
    protected $signature = 'hadiths:parse {file}';
    protected $description = 'Parse hadiths from Word document';

    // خريطة الرموز
    protected $sourceCodes = [
        'خ' => 'خ', 'م' => 'م', 'ق' => 'ق',
        'د' => 'د', 'ت' => 'ت', 'ن' => 'ن',
        'هـ' => 'هـ', '4' => '4', '3' => '3',
        'حم' => 'حم', 'عم' => 'عم', 'ك' => 'ك',
        'خد' => 'خد', 'تخ' => 'تخ', 'حب' => 'حب',
        'طب' => 'طب', 'طس' => 'طس', 'طص' => 'طص',
        'ص' => 'ص', 'ش' => 'ش', 'عب' => 'عب',
        'ع' => 'ع', 'قط' => 'قط', 'فر' => 'فر',
        'حل' => 'حل', 'هب' => 'هب', 'هق' => 'هق',
        'عد' => 'عد', 'عق' => 'عق', 'خط' => 'خط',
    ];

    public function handle()
    {
        $filePath = $this->argument('file');
        
        if (!file_exists($filePath)) {
            $this->error('الملف غير موجود!');
            return 1;
        }

        $this->info('بدء معالجة الملف...');
        
        $phpWord = IOFactory::load($filePath);
        $sections = $phpWord->getSections();
        
        $processedCount = 0;
        
        foreach ($sections as $section) {
            $elements = $section->getElements();
            
            foreach ($elements as $element) {
                if (method_exists($element, 'getText')) {
                    $text = $element->getText();
                    
                    // التحقق من وجود رقم حديث
                    if (preg_match('/\[(\d+)\]/', $text, $matches)) {
                        $this->parseHadith($text);
                        $processedCount++;
                    }
                }
            }
        }
        
        $this->info("تمت معالجة {$processedCount} حديث بنجاح!");
        return 0;
    }

    protected function parseHadith($text)
    {
        // نمط المطابقة:
        // نص الحديث [رقم](حكم)(رموز المصادر)عن الراوي
        $pattern = '/^(.+?)\s*\[(\d+)\]\s*\(([^)]+)\)\s*\(([^)]+)\)\s*عن\s+(.+?)\.$/u';
        
        if (!preg_match($pattern, $text, $matches)) {
            $this->warn("تعذر تحليل: " . Str::limit($text, 50));
            return;
        }

        $hadithText = trim($matches[1]);
        $number = (int)$matches[2];
        $ruling = trim($matches[3]);
        $sourcesStr = trim($matches[4]);
        $narratorName = trim($matches[5]);

        // معالجة الراوي
        $narrator = Narrator::firstOrCreate(
            ['name_ar' => $narratorName],
            ['slug' => Str::slug($narratorName)]
        );

        // إنشاء الحديث
        $hadith = Hadith::updateOrCreate(
            ['number' => $number],
            [
                'text_ar' => $hadithText,
                'slug' => Str::slug($number . '-' . Str::limit($hadithText, 50)),
                'narrator_id' => $narrator->id,
                'ruling' => $ruling,
            ]
        );

        // معالجة المصادر
        $this->parseSources($hadith, $sourcesStr);

        $this->info("✓ تم إضافة الحديث رقم {$number}");
    }

    protected function parseSources($hadith, $sourcesStr)
    {
        // فصل الرموز (مثل: "ق د ن" أو "خ ، م")
        $codes = preg_split('/[\s،,]+/', $sourcesStr, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($codes as $code) {
            $code = trim($code);
            
            if (isset($this->sourceCodes[$code])) {
                $source = Source::where('code', $code)->first();
                
                if ($source) {
                    $hadith->sources()->syncWithoutDetaching([$source->id]);
                }
            }
        }
    }
}
```

### 4.3 استخدام Parser

```bash
# رفع ملف Word إلى storage/app
php artisan hadiths:parse storage/app/sahih-jami.docx
```

### 4.4 (اختياري) واجهة Filament للرفع

```bash
php artisan make:filament-page ImportHadiths
```

**إنشاء صفحة رفع ملفات في Filament:**

```php
namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class ImportHadiths extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static string $view = 'filament.pages.import-hadiths';
    protected static ?string $title = 'استيراد الأحاديث';
    protected static ?string $navigationGroup = 'الإعدادات';

    public $file;

    protected function getFormSchema(): array
    {
        return [
            FileUpload::make('file')
                ->label('ملف Word')
                ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                ->required(),
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();
        
        $filePath = storage_path('app/public/' . $data['file']);
        
        \Artisan::call('hadiths:parse', ['file' => $filePath]);
        
        $this->notify('success', 'تم الاستيراد بنجاح!');
    }
}
```

## 📊 النتيجة المتوقعة
- ✅ أداة تحليل ذكية للأحاديث
- ✅ استيراد تلقائي من Word
- ✅ معالجة الرموز والمصادر
- ✅ ربط تلقائي بالرواة
- ✅ واجهة رفع في Filament (اختياري)

## ⚠️ نقاط الانتباه
- اختبر Parser على 10-20 حديث أولاً
- تأكد من صحة Regex Pattern
- راجع البيانات المُدخلة يدوياً بعد الاستيراد
- احتفظ بنسخة احتياطية قبل الاستيراد الكامل

---

# 📍 المرحلة 5: الواجهة الأمامية الأساسية

## 🎯 الهدف
بناء واجهة مستخدم فخمة ومريحة للعين.

## ✅ المهام

### 5.1 إعداد Routes

**الملف: `routes/web.php`**

```php
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HadithController;
use App\Http\Controllers\NarratorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\SourceController;

// الصفحة الرئيسية
Route::get('/', [HomeController::class, 'index'])->name('home');

// الفهرس
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book:slug}', [BookController::class, 'show'])->name('books.show');

// الحديث
Route::get('/hadith/{hadith:slug}', [HadithController::class, 'show'])->name('hadith.show');

// الرواة
Route::get('/narrators', [NarratorController::class, 'index'])->name('narrators.index');
Route::get('/narrator/{narrator:slug}', [NarratorController::class, 'show'])->name('narrator.show');

// المصادر
Route::get('/sources', [SourceController::class, 'index'])->name('sources.index');

// البحث
Route::get('/search', [HadithController::class, 'search'])->name('search');

// تبديل اللغة
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'en', 'fr', 'ur', 'id', 'tr', 'de'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');