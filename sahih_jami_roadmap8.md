```

## 📊 النتيجة المتوقعة
- ✅ بحث فوري وسريع جداً
- ✅ دعم كامل للغة العربية
- ✅ فلاتر متعددة (الحكم، الكتاب، الراوي)
- ✅ Pagination للنتائج
- ✅ تجربة بحث احترافية

## ⚠️ نقاط الانتباه
- Meilisearch يحتاج ذاكرة RAM جيدة (512MB على الأقل)
- احفظ Master Key في مكان آمن
- راقب أداء Meilisearch على الاستضافة
- فهرس البيانات بعد كل تحديث كبير

---

# 📍 المرحلة 7: نظام الترجمة متعدد اللغات

## 🎯 الهدف
بناء نظام ترجمة كامل يدعم 7+ لغات.

## ✅ المهام

### 7.1 إعداد ملفات اللغات

**إنشاء ملفات الترجمة:**
```bash
# إنشاء مجلدات اللغات
mkdir -p lang/ar
mkdir -p lang/en
mkdir -p lang/ur
mkdir -p lang/id
mkdir -p lang/fr
mkdir -p lang/tr
mkdir -p lang/de
```

### 7.2 ملف الترجمة العربي

**الملف: `lang/ar/messages.php`**
```php
<?php

return [
    'home' => 'الرئيسية',
    'index' => 'الفهرس',
    'narrators' => 'الرواة',
    'sources' => 'المصادر',
    'about' => 'عن المشروع',
    'search' => 'بحث',
    'search_placeholder' => 'ابحث في 9000 حديث...',
    'hadith_of_day' => 'حديث اليوم',
    'popular_hadiths' => 'الأكثر بحثاً',
    'top_narrators' => 'أبرز الرواة',
    'browse_index' => 'تصفح الفهرس',
    'ruling' => 'الحكم',
    'narrator' => 'الراوي',
    'sources_text' => 'المصادر',
    'view_details' => 'عرض التفاصيل',
    'copy' => 'نسخ',
    'share' => 'مشاركة',
    'print' => 'طباعة',
    'sahih' => 'صحيح',
    'hasan' => 'حسن',
    'weak' => 'ضعيف',
    'book' => 'الكتاب',
    'chapter' => 'الباب',
    'hadith_number' => 'رقم الحديث',
    'narrated_by' => 'عن',
    'results_found' => 'نتيجة',
    'no_results' => 'لا توجد نتائج',
    'filter_results' => 'تصفية النتائج',
    'apply_filters' => 'تطبيق الفلاتر',
    'reset' => 'إعادة تعيين',
    'all' => 'الكل',
    'total_hadiths' => 'حديث',
    'languages' => 'لغات',
    'all_rights_reserved' => 'جميع الحقوق محفوظة',
];
```

### 7.3 ملف الترجمة الإنجليزي

**الملف: `lang/en/messages.php`**
```php
<?php

return [
    'home' => 'Home',
    'index' => 'Index',
    'narrators' => 'Narrators',
    'sources' => 'Sources',
    'about' => 'About',
    'search' => 'Search',
    'search_placeholder' => 'Search in 9000 hadiths...',
    'hadith_of_day' => 'Hadith of the Day',
    'popular_hadiths' => 'Most Popular',
    'top_narrators' => 'Top Narrators',
    'browse_index' => 'Browse Index',
    'ruling' => 'Ruling',
    'narrator' => 'Narrator',
    'sources_text' => 'Sources',
    'view_details' => 'View Details',
    'copy' => 'Copy',
    'share' => 'Share',
    'print' => 'Print',
    'sahih' => 'Authentic',
    'hasan' => 'Good',
    'weak' => 'Weak',
    'book' => 'Book',
    'chapter' => 'Chapter',
    'hadith_number' => 'Hadith Number',
    'narrated_by' => 'Narrated by',
    'results_found' => 'results found',
    'no_results' => 'No results found',
    'filter_results' => 'Filter Results',
    'apply_filters' => 'Apply Filters',
    'reset' => 'Reset',
    'all' => 'All',
    'total_hadiths' => 'hadiths',
    'languages' => 'languages',
    'all_rights_reserved' => 'All Rights Reserved',
];
```

### 7.4 Middleware للغة

**إنشاء Middleware:**
```bash
php artisan make:middleware SetLocale
```

**الملف: `app/Http/Middleware/SetLocale.php`**
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale', config('app.locale'));
        
        if (in_array($locale, ['ar', 'en', 'ur', 'id', 'fr', 'tr', 'de'])) {
            App::setLocale($locale);
            
            // تعيين اتجاه النص
            $rtlLanguages = ['ar', 'ur'];
            view()->share('direction', in_array($locale, $rtlLanguages) ? 'rtl' : 'ltr');
        }
        
        return $next($request);
    }
}
```

**تسجيل Middleware في `bootstrap/app.php` أو `app/Http/Kernel.php`:**
```php
protected $middlewareGroups = [
    'web' => [
        // ...
        \App\Http\Middleware\SetLocale::class,
    ],
];
```

### 7.5 عرض الترجمات في Blade

**تحديث `resources/views/layouts/app.blade.php`:**
```blade
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $direction ?? 'rtl' }}">
<head>
    <!-- ... -->
</head>
```

**استخدام الترجمات:**
```blade
<a href="{{ route('home') }}">{{ __('messages.home') }}</a>
<input placeholder="{{ __('messages.search_placeholder') }}">
<h2>{{ __('messages.hadith_of_day') }}</h2>
```

### 7.6 نظام ترجمة الأحاديث

**عرض الحديث بلغة المستخدم:**

**تحديث `app/Http/Controllers/HadithController.php`:**
```php
public function show(Hadith $hadith)
{
    $hadith->incrementViews();
    $hadith->load(['narrator', 'book', 'chapter', 'sources']);
    
    // الحصول على الترجمة إذا كانت متوفرة
    $locale = app()->getLocale();
    $translation = null;
    
    if ($locale !== 'ar') {
        $translation = $hadith->translations()
            ->where('locale', $locale)
            ->first();
    }
    
    $relatedHadiths = Hadith::where('narrator_id', $hadith->narrator_id)
        ->where('id', '!=', $hadith->id)
        ->limit(5)
        ->get();
    
    return view('hadith.show', compact('hadith', 'translation', 'relatedHadiths'));
}
```

**تحديث `resources/views/hadith/show.blade.php`:**
```blade
@extends('layouts.app')

@section('content')

<div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">
        
        @include('components.hadith-card', ['hadith' => $hadith])
        
        <!-- Translation if available -->
        @if($translation)
        <div class="mt-8 card border-l-4 border-blue-500">
            <div class="flex items-center mb-4">
                <span class="text-2xl mr-2">🌐</span>
                <h3 class="text-xl font-bold">{{ __('messages.translation') }}</h3>
            </div>
            <p class="text-lg leading-relaxed">{{ $translation->text }}</p>
            @if($translation->translator_name)
            <p class="mt-4 text-sm text-gray-600">
                {{ __('messages.translator') }}: {{ $translation->translator_name }}
            </p>
            @endif
        </div>
        @elseif(app()->getLocale() !== 'ar')
        <div class="mt-8 card bg-yellow-50 dark:bg-yellow-900/20">
            <p class="text-center">
                {{ __('messages.translation_not_available') }}
            </p>
        </div>
        @endif
        
        <!-- Related Hadiths -->
        @if($relatedHadiths->count() > 0)
        <div class="mt-12">
            <h3 class="text-2xl font-bold mb-6">{{ __('messages.related_hadiths') }}</h3>
            <div class="grid md:grid-cols-2 gap-6">
                @foreach($relatedHadiths as $related)
                    @include('components.hadith-card-small', ['hadith' => $related])
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
```

### 7.7 واجهة إضافة ترجمات في Filament

**إنشاء Resource للترجمات:**
```bash
php artisan make:filament-resource HadithTranslation
```

**الملف: `app/Filament/Resources/HadithTranslationResource.php`**
```php
namespace App\Filament\Resources;

use App\Filament\Resources\HadithTranslationResource\Pages;
use App\Models\HadithTranslation;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;

class HadithTranslationResource extends Resource
{
    protected static ?string $model = HadithTranslation::class;
    protected static ?string $navigationIcon = 'heroicon-o-language';
    protected static ?string $navigationLabel = 'الترجمات';
    protected static ?string $navigationGroup = 'المحتوى الأساسي';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('hadith_id')
                    ->label('الحديث')
                    ->relationship('hadith', 'number')
                    ->searchable()
                    ->required(),
                
                Forms\Components\Select::make('locale')
                    ->label('اللغة')
                    ->options([
                        'en' => 'English',
                        'ur' => 'اردو',
                        'id' => 'Bahasa Indonesia',
                        'fr' => 'Français',
                        'tr' => 'Türkçe',
                        'de' => 'Deutsch',
                    ])
                    ->required(),
                
                Forms\Components\Textarea::make('text')
                    ->label('النص المترجم')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),
                
                Forms\Components\TextInput::make('translator_name')
                    ->label('اسم المترجم'),
                
                Forms\Components\Toggle::make('is_verified')
                    ->label('مراجع علمياً')
                    ->default(false),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hadith.number')
                    ->label('رقم الحديث')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('locale')
                    ->label('اللغة')
                    ->badge(),
                
                Tables\Columns\TextColumn::make('text')
                    ->label('النص')
                    ->limit(60),
                
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('مراجع')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('locale')
                    ->label('اللغة')
                    ->options([
                        'en' => 'English',
                        'ur' => 'اردو',
                        'id' => 'Indonesia',
                        'fr' => 'Français',
                        'tr' => 'Türkçe',
                    ]),
            ]);
    }
}
```

## 📊 النتيجة المتوقعة
- ✅ دعم 7 لغات حية
- ✅ تبديل سلس بين اللغات
- ✅ RTL/LTR تلقائي
- ✅ عرض ترجمات الأحاديث
- ✅ واجهة إدارة الترجمات في Filament
- ✅ جاهزية لإضافة لغات جديدة

## ⚠️ نقاط الانتباه
- أكمل ملفات اللغات لجميع الصفحات
- راجع جودة الترجمات علمياً
- تأكد من عمل RTL/LTR في جميع الصفحات
- اختبر الخطوط لجميع اللغات

---

# 📍 المرحلة 8: التحسين والأداء

## 🎯 الهدف
تحسين سرعة الموقع وتجربة المستخدم.

## ✅ المهام

### 8.1 Caching Strategy

**الملف: `app/Http/Controllers/HomeController.php`**
```php
use Illuminate\Support\Facades\Cache;

public function index()
{
    $data = Cache::remember('homepage_data', 3600, function () {
        return [
            'totalHadiths' => Hadith::count(),
            'totalNarrators' => Narrator::count(),
            'totalBooks' => Book::count(),
            'totalSources' => 30,
            'hadithOfDay' => Hadith::inRandomOrder()->first(),
            'popularHadiths' => Hadith::popular(5)->get(),
            'topNarrators' => Narrator::topNarrators(6)->get(),
        ];
    });
    
    return view('home', $data);
}
```

### 8.2 Eager Loading

**تحسين Queries في جميع Controllers:**
```php
// قبل
$hadiths = Hadith::all();
foreach($hadiths as $hadith) {
    echo $hadith->narrator->name_ar; // N+1 Problem
}

// بعد
$hadiths = Hadith::with(['narrator', 'book', 'sources'])->get();
foreach($hadiths as $hadith) {
    echo $hadith->narrator->name_ar; // One Query
}
```

### 8.3 Database Indexing

**إضافة Indexes مهمة:**
```php
// في Migration جديد
Schema::table('hadiths', function (Blueprint $table) {
    $table->index(['book_id', 'chapter_id']);
    $table->index('narrator_id');
    $table->index('ruling');
    $table->index('views_count');
});
```

### 8.4 Image Optimization

**تثبيت حزمة تحسين الصور:**
```bash
composer require intervention/image
```

**إعداد معالج الصور (إذا أضفت صور الرواة لاحقاً):**
```php
use Intervention\Image\ImageManager;

$image = ImageManager::imagick()->read('photo.jpg');
$image->scale(width: 300);
$image->save('thumbnail.jpg');
```

### 8.5 Asset Optimization

**الملف: `vite.config.js`**
```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        minify: 'terser',
        cssMinify: true,
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': ['alpinejs'],
                }
            }
        }
    }
});
```

### 8.6 Response Caching

**تثبيت حزمة Response Cache:**
```bash
composer require spatie/laravel-responsecache
```

**النشر:**
```bash
php artisan vendor:publish --provider="Spatie\ResponseCache\ResponseCacheServiceProvider"
```

**إضافة Middleware في Routes:**
```php
Route::middleware('cacheResponse:3600')->group(function () {
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/books', [BookController::class, 'index']);
    // ... الصفحات الثابتة
});
```

### 8.7 Lazy Loading للصور

**في Blade Templates:**
```blade
<img src="{{ asset('images/logo.png') }}" 
     alt="Logo" 
     loading="lazy"
     class="...">
```

### 8.8 CDN للملفات الثابتة

**في `.env`:**
```env
ASSET_URL=https://cdn.yourdomain.com
```

### 8.9 Compression

**في `.htaccess` (إذا كنت تستخدم Apache):**
```apache
# Enable Gzip Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>

# Browser Caching
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/pdf "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

### 8.10 Queue للعمليات الثقيلة

**إعداد Queue:**
```env
QUEUE_CONNECTION=database
```

```bash
php artisan queue:table
php artisan migrate
```

**مثال: إرسال إشعارات أو معالجة بيانات:**
```php
// في Controller
dispatch(new ProcessHadithImport($file));

// Job
php artisan make:job ProcessHadithImport
```

## 📊 النتيجة المتوقعة
- ✅ تحميل الصفحة الرئيسية في أقل من 2 ثانية
- ✅ تقليل عدد Queries بنسبة 70%
- ✅ Caching فعال
- ✅ ضغط الملفات
- ✅ تجربة مستخدم سلسة

## ⚠️ نقاط الانتباه
- راقب استهلاك الذاكرة
- امسح Cache بعد التحديثات (`php artisan cache:clear`)
- اختبر الأداء بأدوات مثل Google PageSpeed Insights
- استخدم `php artisan optimize` قبل النشر

---

# 📍 المرحلة 9: الصفحات الإضافية

## 🎯 الهدف
إكمال باقي صفحات الموقع.

## ✅ المهام

### 9.1 صفحة الفهرس (Books Index)

**Controller:**
```php
// app/Http/Controllers/BookController.php
public function index()
{
    $books = Book::active()
        ->withCount('hadiths')
        ->with('chapters')
        ->orderBy('order_index')
        ->get();
    
    return view('books.index', compact('books'));
}

public function show(Book $book)
{
    $book->load(['chapters' => function($query) {
        $query->active()->orderBy('order_index');
    }]);
    
    return view('books.show', compact('book'));
}
```

**View: `resources/views/books/index.blade.php`**
```blade
@extends('layouts.app')

@section('content')

<div class="container mx-auto px-4 py-12">
    <h1 class="text-4xl font-bold font-arabic text-center mb-12">
        {{ __('messages.index') }}
    </h1>
    
    <div class="space-y-6 max-w-5xl mx-auto">
        @foreach($books as $book)
        <div class="card" x-data="{ open: false }">
            <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <span class="text-4xl">📕</span>
                    <div>
                        <h3 class="text-2xl font-bold">{{ $book->name_ar }}</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ $book->hadiths_count }} {{ __('messages.hadith') }}
                        </p>
                    </div>
                </div>
                <svg class="w-6 h-6 transform transition-transform" :class="{'rotate-180': open}">
                    <path d="M19 9l-7 7-7-7" stroke="currentColor" fill="none"/>
                </svg>
            </div>
            
            <div x-show="open" x-collapse class="mt-6 pr-16 space-y-3">
                @foreach($book->chapters as $chapter)
                <a href="{{ route('books.show', $book->slug) }}#chapter-{{ $chapter->id }}" 
                   class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <div class="flex items-center justify-between">
                        <span class="font-medium">{{ $chapter->name_ar }}</span>
                        <span class="text-sm text-gray-500">
                            {{ $chapter->start_hadith_number }} - {{ $chapter->end_hadith_number }}
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection
```

### 9.2 صفحة الرواة

**Controller:**
```php
// app/Http/Controllers/NarratorController.php
public function index()
{
    $narrators = Narrator::withCount('hadiths')
        ->orderBy('hadiths_count', 'desc')
        ->paginate(30);
    
    return view('narrators.index', compact('narrators'));
}

public function show(Narrator $narrator)
{
    $narrator->load('hadiths');
    $hadiths = $narrator->hadiths()->paginate(20);
    
    return view('narrators.show', compact('narrator', 'hadiths'));
}
```

### 9.3 صفحة المصادر

**Controller:**
```php
// app/Http/Controllers/SourceController.php
public function index()
{
    $sources = Source::ordered()->get();
    
    return view('sources.index', compact('sources'));
}
```

### 9.4 صفحة "عن المشروع"

**Route:**
```php
Route::view('/about', 'about')->name('about');
```

**View: `resources/views/about.blade.php`**
```blade
@extends('layouts.app')

@section('content')

<div class="container mx-auto px-4 py-12 max-w-4xl prose dark:prose-invert">
    <h1>عن المشروع</h1>
    
    <h2>الرؤية</h2>
    <p>الموسوعة الرقمية لصحيح الجامع الصغير هي مشروع علمي تقني يهدف إلى...</p>
    
    <h2>منهجية الإمام الألباني</h2>
    <p>اعتمد الإمام الألباني - رحمه الله - في تصحيح الأحاديث على...</p>
    
    <h2>دليل الرموز</h2>
    <table>
        <thead>
            <tr>
                <th>الرمز</th>
                <th>المصدر</th>
                <th>الاسم الكامل</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>خ</td><td>البخاري</td><td>الجامع الصحيح</td></tr>
            <tr><td>م</td><td>مسلم</td><td>الصحيح</td></tr>
            <!-- ... باقي الرموز -->
        </tbody>
    </table>
</div>

@endsection
```

### 9.5 Sitemap Generator

**تثبيت الحزمة:**
```bash
composer require spatie/laravel-sitemap
```

**إنشاء Command:**
```bash
php artisan make:command GenerateSitemap
```

**الملف: `app/Console/Commands/GenerateSitemap.php`**
```php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Hadith;
use App\Models\Book;
use App\Models\Narrator;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap';

    public function handle()
    {
        $sitemap = Sitemap::create();
        
        // الصفحات الثابتة
        $sitemap->add(Url::create(route('home'))->setPriority(1.0));
        $sitemap->add(Url::create(route('books.index'))->setPriority(0.9));
        $sitemap->add(Url::create(route('narrators.index'))->setPriority(0.8));
        
        // الأحاديث
        Hadith::all()->each(function (Hadith $hadith) use ($sitemap) {
            $sitemap->add(
                Url::create(route('hadith.show', $hadith->slug))
                    ->setLastModificationDate($hadith->updated_at)
                    ->setPriority(0.7)
            );
        });
        
        // الكتب
        Book::all()->each(function (Book $book) use ($sitemap) {
            $sitemap->add(
                Url::create(route('books.show', $book->slug))
                    ->setPriority(0.6)
            );
        });
        
        $sitemap->writeToFile(public_path('sitemap.xml'));
        
        $this->info('Sitemap generated successfully!');
    }
}
```

**تشغيل:**
```bash
php artisan sitemap:generate
```

## 📊 النتيجة المتوقعة
- ✅ فهرس تفاعلي كامل
- ✅ صفحات الرواة والمصادر
- ✅ صفحة "عن المشروع"
- ✅ Sitemap للـ SEO
- ✅ جميع الصفحات متناسقة

## ⚠️ نقاط الانتباه
- اختبر جميع الروابط
- تأكد من عمل Breadcrumbs
- راجع المحتوى لغوياً
- جدول الـ Sitemap بشكل دوري

---

# 📍 المرحلة 10: الاختبار والإطلاق

## 🎯 الهدف
اختبار شامل ونشر آمن للموقع.

## ✅ المهام

### 10.1 Checklist النهائي

**الوظائف:**
```
☐ البحث يعمل بشكل صحيح في جميع الحالات
☐ الفلاتر تعمل
☐ Pagination يعمل
☐ جميع الروابط صحيحة
☐ تبديل اللغات يعمل
☐ Dark Mode يعمل
☐ الأزرار (نسخ، مشاركة، طباعة) تعمل
☐ Responsive على جميع الأجهزة
```

**الأداء:**
```
☐ Page Load < 3 ثوان
☐ Caching يعمل
☐ Images محسّنة
☐ Lazy Loading يعمل
☐ No N+1 Queries
☐ Database Indexes فعالة
```

**الأمان:**
```
☐ CSRF Protection مفعل
☐ XSS Prevention
☐ SQL Injection Prevention
☐ Rate Limiting للـ API
☐ HTTPS مفعل
☐ Backup Strategy جاهزة
```

**SEO:**
```
☐ Meta Tags في جميع الصفحات
☐ Sitemap.xml موجود
☐ robots.txt محدّث
☐ Structured Data (Schema.org)
☐ Open Graph Tags
☐ Canonical URLs
```

### 10.2 Testing Strategy

**Unit Tests:**
```bash
php artisan make:test HadithTest --unit
```

**مثال: `tests/Unit/HadithTest.php`**
```php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Hadith;
use App\Models\Narrator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HadithTest extends TestCase
{
    use RefreshDatabase;

    public function test_hadith_belongs_to_narrator()
    {
        $narrator = Narrator::factory()->create();
        $hadith = Hadith::factory()->create(['narrator_id' => $narrator->id]);
        
        $this->assertInstanceOf(Narrator::class, $hadith->narrator);
        $this->assertEquals($narrator->id, $hadith->narrator->id);
    }
    
    public function test_hadith_increments_views()
    {
        $hadith = Hadith::factory()->create(['views_count' => 0]);
        
        $hadith->incrementViews();
        
        $this->assertEquals(1, $hadith->fresh()->views_count);
    }
}
```

**Feature Tests:**
```bash
php artisan make:test HomePageTest
```

**مثال: `tests/Feature/HomePageTest.php`**
```php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_successfully()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('صحيح الجامع');
    }
    
    public function test_search_returns_results()
    {
        $response = $this->get('/search?q=الصلاة');
        
        $response->assertStatus(200);
        $response->assertViewHas('results');
    }
}
```

**تشغيل الاختبارات:**
```bash
php artisan test
```

### 10.3 Performance Testing

**استخدام Laravel Telescope:**
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

**زيارة:**
```
http://your-domain.test/telescope
```

**مراقبة:**
- Queries (عدد ووقت التنفيذ)
- Requests (زمن الاستجابة)
- Cache Hits/Misses
- Exceptions

### 10.4 Load Testing

**استخدام Apache Bench:**
```bash
# اختبار 100 طلب، 10 متزامنين
ab -n 100 -c 10 https://yourdomain.com/
```

**أو استخدام K6:**
```bash
# تثبيت k6
brew install k6  # Mac
# أو
sudo apt install k6  # Linux

# ملف الاختبار: load-test.js
import http from 'k6/http';
import { sleep } from 'k6';

export let options = {
    vus: 10,
    duration: '30s',
};

export default function () {
    http.get('https://yourdomain.com');
    sleep(1);
}

# تشغيل
k6 run load-test.js
```

### 10.5 SEO Optimization

**إضافة Meta Tags في Layout:**
```blade
<!-- resources/views/layouts/app.blade.php -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta Tags -->
    <title>@yield('title', 'صحيح الجامع الصغير - الموسوعة الرقمية')</title>
    <meta name="description" content="@yield('meta_description', 'الموسوعة الرقمية لصحيح الجامع الصغير بتحقيق الإمام الألباني - 9000 حديث صحيح')">
    <meta name="keywords" content="صحيح الجامع, الألباني, أحاديث, حديث نبوي, إسلام">
    <meta name="author" content="Your Name">
    
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', 'صحيح الجامع الصغير')">
    <meta property="og:description" content="@yield('og_description', 'الموسوعة الرقمية لصحيح الجامع الصغير')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.jpg'))">
    <meta property="og:url" content="{{ url()->current() }}">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', 'صحيح الجامع الصغير')">
    <meta name="twitter:description" content="@yield('twitter_description', 'الموسوعة الرقمية')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/og-image.jpg'))">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
```

**Structured Data (Schema.org):**
```blade
<!-- في صفحة الحديث -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "حديث رقم {{ $hadith->number }}",
    "description": "{{ Str::limit($hadith->text_ar, 160) }}",
    "author": {
        "@type": "Person",
        "name": "{{ $hadith->narrator->name_ar }}"
    },
    "datePublished": "{{ $hadith->created_at->toIso8601String() }}",
    "dateModified": "{{ $hadith->updated_at->toIso8601String() }}"
}
</script>
```

**robots.txt:**
```
User-agent: *
Allow: /
Disallow: /admin
Disallow: /admin/*

Sitemap: https://yourdomain.com/sitemap.xml
```

### 10.6 Backup Strategy

**تثبيت Laravel Backup:**
```bash
composer require spatie/laravel-backup
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
```

**إعداد `.env`:**
```env
BACKUP_DISK=s3  # أو local
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
```

**إعداد `config/backup.php`:**
```php
'backup' => [
    'name' => 'sahih-jami',
    
    'source' => [
        'files' => [
            'include' => [
                base_path(),
            ],
            'exclude' => [
                base_path('vendor'),
                base_path('node_modules'),
            ],
        ],
        
        'databases' => ['mysql'],
    ],
    
    'destination' => [
        'disks' => ['s3'],
    ],
],
```

**جدولة النسخ الاحتياطي:**
```php
// في app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('backup:clean')->daily()->at('01:00');
    $schedule->command('backup:run')->daily()->at('02:00');
    $schedule->command('sitemap:generate')->weekly();
}
```

**تشغيل:**
```bash
php artisan backup:run
```

### 10.7 Monitoring & Logging

**تفعيل Logging:**
```env
LOG_CHANNEL=stack
LOG_LEVEL=debug  # في التطوير
LOG_LEVEL=error  # في الإنتاج
```

**إضافة Error Tracking (Sentry):**
```bash
composer require sentry/sentry-laravel
php artisan vendor:publish --provider="Sentry\Laravel\ServiceProvider"
```

**في `.env`:**
```env
SENTRY_LARAVEL_DSN=your-sentry-dsn
SENTRY_TRACES_SAMPLE_RATE=1.0
```

### 10.8 Deployment Checklist

**قبل النشر:**
```bash
# 1. تحديث Dependencies
composer install --optimize-autoloader --no-dev

# 2. Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 3. Build Assets
npm run build

# 4. تشغيل Migrations
php artisan migrate --force

# 5. فهرسة البحث
php artisan scout:import "App\Models\Hadith"

# 6. Generate Sitemap
php artisan sitemap:generate

# 7. Clear Cache
php artisan cache:clear
php artisan route:clear
php artisan config:clear
```

**إعدادات الاستضافة المشتركة:**

1. **رفع الملفات:**
   - رفع جميع الملفات ماعدا `public/` إلى مجلد خارج `public_html`
   - رفع محتويات `public/` إلى `public_html`

2. **تعديل `index.php`:**
```php
// في public_html/index.php
require __DIR__.'/../laravel/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';
```

3. **`.htaccess`:**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

4. **إعداد Cron Job:**
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

5. **إعداد Queue Worker:**
```bash
# إنشاء Supervisor Config
[program:sahih-jami-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=your-user
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/worker.log
```

### 10.9 Post-Launch Monitoring

**مراقبة الأداء:**
- Google PageSpeed Insights
- GTmetrix
- Pingdom

**Analytics:**
```bash
# Google Analytics
# أضف في <head>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-XXXXXXXXXX');
</script>
```

**Uptime Monitoring:**
- استخدام خدمات مثل UptimeRobot أو Pingdom
- تنبيهات عند توقف الموقع

### 10.10 Documentation

**إنشاء README.md:**
```markdown
# صحيح الجامع الصغير - الموسوعة الرقمية

## متطلبات التشغيل
- PHP 8.2+
- MySQL 8.0+
- Composer
- Node.js & NPM

## التثبيت
```bash
git clone repository-url
cd sahih-jami
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

## الميزات
- 9000 حديث صحيح
- 7 لغات
- بحث متقدم
- ...

## الصيانة
```bash
# نسخ احتياطي
php artisan backup:run

# تحديث البحث
php artisan scout:import "App\Models\Hadith"
```

## الدعم
للدعم، تواصل على: support@example.com
```

## 📊 النتيجة المتوقعة
- ✅ موقع مختبر بالكامل
- ✅ أداء ممتاز (>90 في PageSpeed)
- ✅ SEO محسّن
- ✅ Backup تلقائي
- ✅ Monitoring فعال
- ✅ Documentation كامل
- ✅ جاهز للإطلاق الرسمي

## ⚠️ نقاط الانتباه
- اختبر على بيئة مشابهة للإنتاج أولاً
- احتفظ بنسخة احتياطية قبل كل تحديث
- راقب Logs بشكل يومي في الأسبوع الأول
- جهز خطة للتعامل مع الأخطاء الطارئة

---

# 🎉 خاتمة خارطة الطريق

## ملخص المراحل

| المرحلة | المدة المتوقعة | الأولوية |
|---------|----------------|----------|
| 1. البنية التحتية | 1 أسبوع | 🔴 عالية جداً |
| 2. قاعدة البيانات | 1 أسبوع | 🔴 عالية جداً |
| 3. لوحة التحكم | 2 أسبوع | 🔴 عالية جداً |
| 4. معالج البيانات | 1 أسبوع | 🟠 عالية |
| 5. الواجهة الأمامية | 2 أسبوع | 🟠 عالية |
| 6. محرك البحث | 1 أسبوع | 🟠 عالية |
| 7. نظام الترجمة | 1 أسبوع | 🟡 متوسطة |
| 8. التحسين والأداء | 1 أسبوع | 🟡 متوسطة |
| 9. الصفحات الإضافية | 1 أسبوع | 🟢 منخفضة |
| 10. الاختبار والإطلاق | 1 أسبوع | 🔴 عالية جداً |

**إجمالي المدة:** 12 أسبوع (3 أشهر)

---

## نصائح عامة للتنفيذ

### 1. **ابدأ بالـ MVP (Minimum Viable Product)**
لا تحاول إكمال كل شيء دفعة واحدة. ركز على:
- الأحاديث + البحث الأساسي
- لوحة تحكم بسيطة
- واجهة نظيفة
ثم أضف الميزات الإضافية تدريجياً.

### 2. **استخدم Git بفعالية**
```bash
# Branch لكل ميزة
git checkout -b feature/hadith-search
git commit -m "feat: add search functionality"
git push origin feature/hadith-search
```

### 3. **وثّق كل شيء**
- اكتب تعليقات في الكود
- سجل القرارات المهمة
- احتفظ بملف CHANGELOG.md

### 4. **اختبر باستمرار**
لا تنتظر حتى النهاية للاختبار. اختبر كل ميزة فور الانتهاء منها.

### 5. **راجع الأداء دورياً**
استخدم Laravel Debugbar في التطوير لمراقبة:
- عدد الـ Queries
- زمن التنفيذ
- استهلاك الذاكرة

### 6. **خطط للمستقبل**
احتفظ بقائمة "Future Features":
- تطبيق موبايل
- API عامة
- نظام المستخدمين والمفضلة
- تعليقات العلماء على الأحاديث
- ربط مع مشاريع أخرى

---

## الموارد المفيدة

### Documentation:
- [Laravel 12 Docs](https://laravel.com/docs/12.x)
- [Filament 4 Docs](https://filamentphp.com/docs)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Meilisearch Docs](https://www.meilisearch.com/docs)

### Tools:
- **Laravel Herd** - بيئة تطوير محلية سريعة
- **TablePlus** - إدارة قواعد البيانات
- **Postman** - اختبار API
- **Figma** - تصميم UI/UX

### Communities:
- [Laravel Discord](https://discord.gg/laravel)
- [Filament Discord](https://discord.gg/filamentphp)
- [Laracasts Forum](https://laracasts.com/discuss)

---

## Checklist النهائي قبل الإطلاق

```
الوظائف الأساسية:
☐ إدخال جميع الـ 9000 حديث
☐ ربط جميع الرواة والمصادر
☐ اختبار البحث في 100+ سيناريو
☐ التأكد من دقة البيانات (مراجعة عشوائية 100 حديث)

التصميم:
☐ Responsive على (iPhone, iPad, Desktop)
☐ Dark Mode يعمل في جميع الصفحات
☐ الخطوط تظهر بشكل صحيح
☐ الألوان متناسقة

الأداء:
☐ Page Load < 3 ثوان
☐ No N+1 Queries
☐ Caching فعال
☐ Images محسّنة

الأمان:
☐ HTTPS مفعل
☐ Environment Variables آمنة
☐ Backup Strategy جاهزة
☐ Error Logging مفعل

SEO:
☐ Meta Tags في جميع الصفحات
☐ Sitemap.xml موجود
☐ robots.txt صحيح
☐ Open Graph Tags

الإطلاق:
☐ Domain Name جاهز
☐ Hosting جاهز
☐ SSL Certificate مثبت
☐ Email للإشعارات مُعد
☐ Analytics مفعل
☐ Monitoring مفعل
```

---

## 🚀 كلمة أخيرة

هذا المشروع طموح ومميز، وسيكون إضافة قيمة للمحتوى الإسلامي الرقمي. 

**تذكر:**
- **الجودة أهم من السرعة** - خذ وقتك في كل مرحلة
- **المراجعة العلمية أساسية** - تأكد من دقة البيانات
- **تجربة المستخدم هي الأساس** - اختبر من منظور المستخدم العادي
- **التوثيق يوفر وقتاً لاحقاً** - لا تهمله

**بالتوفيق في رحلة البناء! 🌟**

---

## 📞 الدعم الفني

إذا واجهت أي عقبات خلال التنفيذ:
1. راجع Documentation الرسمي أولاً
2. ابحث في GitHub Issues
3. اسأل في Communities
4. لا تتردد في العودة للحوار معي لأي استفسارات

**نسأل الله التوفيق والسداد** 🤲```

### 5.2 إنشاء Controllers

```bash
php artisan make:controller HomeController
php artisan make:controller HadithController
php artisan make:controller NarratorController
php artisan make:controller BookController
php artisan make:controller SourceController
```

### 5.3 HomeController - الصفحة الرئيسية

**الملف: `app/Http/Controllers/HomeController.php`**

```php
namespace App\Http\Controllers;

use App\Models\Hadith;
use App\Models\Narrator;
use App\Models\Book;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $data = [
            'totalHadiths' => Hadith::count(),
            'totalNarrators' => Narrator::count(),
            'totalBooks' => Book::count(),
            'totalSources' => 30, // أو من قاعدة البيانات
            
            // حديث اليوم (عشوائي)
            'hadithOfDay' => Hadith::inRandomOrder()->first(),
            
            // الأكثر بحثاً
            'popularHadiths' => Hadith::popular(5)->get(),
            
            // أبرز الرواة
            'topNarrators' => Narrator::topNarrators(6)->get(),
        ];
        
        return view('home', $data);
    }
}
```

### 5.4 HadithController

**الملف: `app/Http/Controllers/HadithController.php`**

```php
namespace App\Http\Controllers;

use App\Models\Hadith;
use Illuminate\Http\Request;

class HadithController extends Controller
{
    public function show(Hadith $hadith)
    {
        // زيادة عدد المشاهدات
        $hadith->incrementViews();
        
        // تحميل العلاقات
        $hadith->load(['narrator', 'book', 'chapter', 'sources', 'translations']);
        
        // أحاديث ذات صلة (نفس الراوي)
        $relatedHadiths = Hadith::where('narrator_id', $hadith->narrator_id)
            ->where('id', '!=', $hadith->id)
            ->limit(5)
            ->get();
        
        return view('hadith.show', compact('hadith', 'relatedHadiths'));
    }
    
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        if (empty($query)) {
            return redirect()->route('home');
        }
        
        // البحث باستخدام Scout
        $results = Hadith::search($query)->paginate(20);
        
        return view('search', compact('results', 'query'));
    }
}
```

### 5.5 Layout الأساسي

**الملف: `resources/views/layouts/app.blade.php`**

```blade
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'صحيح الجامع الصغير')</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="@yield('meta_description', 'الموسوعة الرقمية لصحيح الجامع الصغير بتحقيق الإمام الألباني')">
    <meta name="keywords" content="صحيح الجامع, الألباني, أحاديث, حديث, إسلام">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="bg-light-cream dark:bg-dark-brown text-gray-900 dark:text-gray-100 transition-colors duration-300">
    
    <!-- Header/Navigation -->
    @include('layouts.header')
    
    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>
    
    <!-- Footer -->
    @include('layouts.footer')
    
    @stack('scripts')
</body>
</html>
```

### 5.6 Header Component

**الملف: `resources/views/layouts/header.blade.php`**

```blade
<header class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-50 transition-colors duration-300">
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-3">
                <div class="text-2xl">🕌</div>
                <span class="text-xl font-bold font-arabic text-islamic-gold">صحيح الجامع</span>
            </a>
            
            <!-- Search Bar (Desktop) -->
            <div class="hidden md:block flex-1 max-w-2xl mx-8">
                <form action="{{ route('search') }}" method="GET" class="relative">
                    <input 
                        type="text" 
                        name="q" 
                        placeholder="ابحث في 9000 حديث..."
                        class="w-full px-6 py-3 rounded-full border-2 border-gray-200 dark:border-gray-700 focus:border-islamic-gold focus:ring-2 focus:ring-islamic-gold/20 dark:bg-gray-900 transition-all"
                    >
                    <button type="submit" class="absolute left-4 top-1/2 -translate-y-1/2 text-islamic-gold">
                        🔍
                    </button>
                </form>
            </div>
            
            <!-- Navigation Links -->
            <nav class="hidden lg:flex items-center space-x-6 space-x-reverse">
                <a href="{{ route('home') }}" class="nav-link">الرئيسية</a>
                <a href="{{ route('books.index') }}" class="nav-link">الفهرس</a>
                <a href="{{ route('narrators.index') }}" class="nav-link">الرواة</a>
                <a href="{{ route('sources.index') }}" class="nav-link">المصادر</a>
                
                <!-- Language Switcher -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center space-x-2 nav-link">
                        <span>🌐</span>
                        <span>{{ strtoupper(app()->getLocale()) }}</span>
                    </button>
                    
                    <div x-show="open" @click.away="open = false" 
                         class="absolute left-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-xl py-2">
                        <a href="{{ route('lang.switch', 'ar') }}" class="dropdown-item">🇸🇦 العربية</a>
                        <a href="{{ route('lang.switch', 'en') }}" class="dropdown-item">🇬🇧 English</a>
                        <a href="{{ route('lang.switch', 'ur') }}" class="dropdown-item">🇵🇰 اردو</a>
                        <a href="{{ route('lang.switch', 'id') }}" class="dropdown-item">🇮🇩 Indonesia</a>
                        <a href="{{ route('lang.switch', 'fr') }}" class="dropdown-item">🇫🇷 Français</a>
                        <a href="{{ route('lang.switch', 'tr') }}" class="dropdown-item">🇹🇷 Türkçe</a>
                    </div>
                </div>
                
                <!-- Dark Mode Toggle -->
                <button id="theme-toggle" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <span class="dark:hidden">🌙</span>
                    <span class="hidden dark:inline">☀️</span>
                </button>
            </nav>
            
            <!-- Mobile Menu Button -->
            <button id="mobile-menu-btn" class="lg:hidden p-2">
                ☰
            </button>
        </div>
        
        <!-- Mobile Search -->
        <div class="md:hidden mt-4">
            <form action="{{ route('search') }}" method="GET" class="relative">
                <input 
                    type="text" 
                    name="q" 
                    placeholder="ابحث..."
                    class="w-full px-6 py-3 rounded-full border-2 border-gray-200 dark:border-gray-700 focus:border-islamic-gold dark:bg-gray-900"
                >
            </form>
        </div>
    </div>
</header>

<style>
.nav-link {
    @apply text-gray-700 dark:text-gray-300 hover:text-islamic-gold transition-colors font-medium;
}

.dropdown-item {
    @apply block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors;
}
</style>
```

### 5.7 الصفحة الرئيسية

**الملف: `resources/views/home.blade.php`**

```blade
@extends('layouts.app')

@section('title', 'الموسوعة الرقمية لصحيح الجامع الصغير')

@section('content')

<!-- Hero Section -->
<section class="bg-gradient-to-br from-islamic-gold/10 to-transparent py-20">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-5xl md:text-6xl font-bold font-arabic text-dark-brown dark:text-light-cream mb-6">
            الموسوعة الرقمية
            <span class="block text-islamic-gold mt-2">لصحيح الجامع الصغير</span>
        </h1>
        
        <p class="text-xl text-gray-600 dark:text-gray-400 mb-8 max-w-2xl mx-auto">
            9000 حديث صحيح بتحقيق الإمام الألباني - رحمه الله
        </p>
        
        <!-- Search Bar Large -->
        <div class="max-w-3xl mx-auto mb-12">
            <form action="{{ route('search') }}" method="GET" class="relative">
                <input 
                    type="text" 
                    name="q" 
                    placeholder="ابحث في الأحاديث بالكلمات أو الموضوع أو الراوي..."
                    class="w-full px-8 py-5 text-lg rounded-2xl border-2 border-gray-200 dark:border-gray-700 focus:border-islamic-gold focus:ring-4 focus:ring-islamic-gold/20 dark:bg-gray-900 shadow-xl transition-all"
                    autofocus
                >
                <button type="submit" class="absolute left-6 top-1/2 -translate-y-1/2 bg-islamic-gold text-white px-8 py-3 rounded-xl hover:bg-islamic-gold/90 transition-colors">
                    بحث
                </button>
            </form>
        </div>
        
        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                <div class="text-4xl font-bold text-islamic-gold">{{ number_format($totalHadiths) }}</div>
                <div class="text-gray-600 dark:text-gray-400 mt-2">حديث</div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                <div class="text-4xl font-bold text-islamic-gold">{{ $totalNarrators }}+</div>
                <div class="text-gray-600 dark:text-gray-400 mt-2">راوٍ</div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                <div class="text-4xl font-bold text-islamic-gold">7</div>
                <div class="text-gray-600 dark:text-gray-400 mt-2">لغات</div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                <div class="text-4xl font-bold text-islamic-gold">{{ $totalSources }}</div>
                <div class="text-gray-600 dark:text-gray-400 mt-2">مصدر</div>
            </div>
        </div>
    </div>
</section>

<!-- Hadith of the Day -->
@if($hadithOfDay)
<section class="py-16 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold font-arabic text-center mb-12 text-islamic-gold">
            ⭐ حديث اليوم
        </h2>
        
        <div class="max-w-4xl mx-auto">
            @include('components.hadith-card', ['hadith' => $hadithOfDay])
        </div>
    </div>
</section>
@endif

<!-- Popular Hadiths -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold font-arabic text-center mb-12">
            📚 الأكثر بحثاً
        </h2>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($popularHadiths as $hadith)
                @include('components.hadith-card-small', ['hadith' => $hadith])
            @endforeach
        </div>
    </div>
</section>

<!-- Top Narrators -->
<section class="py-16 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold font-arabic text-center mb-12 text-islamic-gold">
            👤 أبرز الرواة
        </h2>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            @foreach($topNarrators as $narrator)
            <a href="{{ route('narrator.show', $narrator->slug) }}" 
               class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 p-6 rounded-xl text-center hover:shadow-xl transition-all hover:-translate-y-1">
                <div class="text-4xl mb-3">👤</div>
                <div class="font-bold text-lg mb-2">{{ $narrator->name_ar }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">{{ $narrator->hadiths_count }} حديث</div>
            </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Quick Links -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-3 gap-8">
            <a href="{{ route('books.index') }}" 
               class="group bg-gradient-to-br from-islamic-gold/10 to-transparent p-8 rounded-2xl hover:shadow-2xl transition-all hover:-translate-y-2">
                <div class="text-5xl mb-4">📖</div>
                <h3 class="text-2xl font-bold mb-3 group-hover:text-islamic-gold transition-colors">تصفح الفهرس</h3>
                <p class="text-gray-600 dark:text-gray-400">استعرض الأحاديث حسب الكتب والأبواب</p>
            </a>
            
            <a href="{{ route('narrators.index') }}" 
               class="group bg-gradient-to-br from-islamic-gold/10 to-transparent p-8 rounded-2xl hover:shadow-2xl transition-all hover:-translate-y-2">
                <div class="text-5xl mb-4">👥</div>
                <h3 class="text-2xl font-bold mb-3 group-hover:text-islamic-gold transition-colors">الرواة</h3>
                <p class="text-gray-600 dark:text-gray-400">تعرف على رواة الأحاديث وأحاديثهم</p>
            </a>
            
            <a href="{{ route('sources.index') }}" 
               class="group bg-gradient-to-br from-islamic-gold/10 to-transparent p-8 rounded-2xl hover:shadow-2xl transition-all hover:-translate-y-2">
                <div class="text-5xl mb-4">📚</div>
                <h3 class="text-2xl font-bold mb-3 group-hover:text-islamic-gold transition-colors">المصادر</h3>
                <p class="text-gray-600 dark:text-gray-400">مصادر التخريج الحديثية</p>
            </a>
        </div>
    </div>
</section>

@endsection
```

### 5.8 Hadith Card Component

**الملف: `resources/views/components/hadith-card.blade.php`**

```blade
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 hover:shadow-2xl transition-all border-t-4 border-islamic-gold">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3 space-x-reverse">
            <span class="bg-islamic-gold text-white px-4 py-2 rounded-full font-bold">
                #{{ $hadith->number }}
            </span>
            <span class="px-4 py-2 rounded-full text-sm font-medium
                {{ $hadith->ruling === 'صحيح' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                {{ $hadith->ruling === 'حسن' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                {{ $hadith->ruling === 'ضعيف' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
            ">
                {{ $hadith->ruling }}
            </span>
        </div>
        
        <div class="text-gray-500 dark:text-gray-400 text-sm">
            👁️ {{ number_format($hadith->views_count) }}
        </div>
    </div>
    
    <!-- Hadith Text -->
    <div class="mb-6">
        <p class="text-xl leading-relaxed font-arabic text-gray-800 dark:text-gray-200">
            " {{ $hadith->text_ar }} "
        </p>
    </div>
    
    <!-- Narrator -->
    <div class="mb-4">
        <a href="{{ route('narrator.show', $hadith->narrator->slug) }}" 
           class="inline-flex items-center space-x-2 space-x-reverse text-islamic-gold hover:underline">
            <span>📖</span>
            <span class="font-medium">عن {{ $hadith->narrator->name_ar }}</span>
        </a>
    </div>
    
    <!-- Sources -->
    @if($hadith->sources->count() > 0)
    <div class="mb-6">
        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">المصادر:</div>
        <div class="flex flex-wrap gap-2">
            @foreach($hadith->sources as $source)
            <span class="px-3 py-1 rounded-full text-sm font-medium text-white" 
                  style="background-color: {{ $source->color }}">
                {{ $source->name_ar }}
            </span>
            @endforeach
        </div>
    </div>
    @endif
    
    <!-- Actions -->
    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
        <a href="{{ route('hadith.show', $hadith->slug) }}" 
           class="text-islamic-gold hover:underline font-medium">
            عرض التفاصيل ←
        </a>
        
        <div class="flex items-center space-x-3 space-x-reverse">
            <button onclick="copyHadith({{ $hadith->id }})" 
                    class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
                    title="نسخ">
                📋
            </button>
            <button onclick="shareHadith({{ $hadith->id }})" 
                    class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
                    title="مشاركة">
                🔗
            </button>
            <button onclick="printHadith({{ $hadith->id }})" 
                    class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
                    title="طباعة">
                🖨️
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyHadith(id) {
    // Copy to clipboard logic
    alert('تم النسخ!');
}

function shareHadith(id) {
    // Share logic
    if (navigator.share) {
        navigator.share({
            title: 'حديث من صحيح الجامع',
            url: window.location.href
        });
    }
}

function printHadith(id) {
    window.print();
}
</script>
@endpush
```

### 5.9 إضافة Dark Mode Toggle

**الملف: `resources/js/app.js`**

```javascript
import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Dark Mode Toggle
const themeToggle = document.getElementById('theme-toggle');
const html = document.documentElement;

// Check for saved theme preference or default to 'light'
const currentTheme = localStorage.getItem('theme') || 'light';
html.classList.toggle('dark', currentTheme === 'dark');

themeToggle?.addEventListener('click', () => {
    html.classList.toggle('dark');
    const newTheme = html.classList.contains('dark') ? 'dark' : 'light';
    localStorage.setItem('theme', newTheme);
});
```

### 5.10 تخصيص Tailwind

**الملف: `resources/css/app.css`**

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

@layer base {
    body {
        font-family: 'Cairo', sans-serif;
    }
    
    .font-arabic {
        font-family: 'Amiri', serif;
    }
}

@layer components {
    .btn-primary {
        @apply bg-islamic-gold text-white px-6 py-3 rounded-xl font-medium hover:bg-islamic-gold/90 transition-all shadow-lg hover:shadow-xl;
    }
    
    .card {
        @apply bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all;
    }
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 10px;
}

::-webkit-scrollbar-track {
    @apply bg-gray-100 dark:bg-gray-900;
}

::-webkit-scrollbar-thumb {
    @apply bg-islamic-gold rounded-full;
}

::-webkit-scrollbar-thumb:hover {
    @apply bg-islamic-gold/80;
}

/* Print Styles */
@media print {
    header, footer, .no-print {
        display: none !important;
    }
    
    body {
        @apply text-black bg-white;
    }
}
```

## 📊 النتيجة المتوقعة
- ✅ واجهة رئيسية جذابة مع Hero Section
- ✅ بطاقات أحاديث فخمة
- ✅ Dark Mode كامل
- ✅ Responsive تماماً
- ✅ أزرار النسخ والمشاركة والطباعة
- ✅ Navigation سلس
- ✅ Typography عربي أنيق

## ⚠️ نقاط الانتباه
- اختبر على جميع الأجهزة (موبايل، تابلت، ديسكتوب)
- تأكد من سرعة تحميل الخطوط
- راجع التباينات اللونية للقراءة المريحة
- اختبر Dark Mode في جميع الصفحات

---

# 📍 المرحلة 6: محرك البحث (Laravel Scout + Meilisearch)

## 🎯 الهدف
بناء محرك بحث سريع وذكي يدعم البحث بالعربية.

## ✅ المهام

### 6.1 تثبيت وإعداد Meilisearch

**تثبيت Meilisearch:**
```bash
# على الاستضافة، استخدم Docker أو التثبيت المباشر
# محلياً، استخدم:
curl -L https://install.meilisearch.com | sh
./meilisearch

# أو عبر Docker:
docker run -p 7700:7700 -v $(pwd)/meili_data:/meili_data getmeili/meilisearch
```

**إعداد `.env`:**
```env
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=your-master-key
```

### 6.2 إعداد Scout في Hadith Model

**إضافة إلى `app/Models/Hadith.php`:**
```php
use Laravel\Scout\Searchable;

class Hadith extends Model
{
    use Searchable;

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'text_ar' => $this->text_ar,
            'narrator_name' => $this->narrator->name_ar,
            'book_name' => $this->book?->name_ar,
            'ruling' => $this->ruling,
        ];
    }

    /**
     * Get the index name for the model.
     */
    public function searchableAs(): string
    {
        return 'hadiths_index';
    }
}
```

### 6.3 فهرسة البيانات

```bash
# فهرسة جميع الأحاديث
php artisan scout:import "App\Models\Hadith"

# حذف الفهرس وإعادة إنشائه
php artisan scout:flush "App\Models\Hadith"
php artisan scout:import "App\Models\Hadith"
```

### 6.4 تحسين HadithController للبحث

**تحديث `app/Http/Controllers/HadithController.php`:**
```php
public function search(Request $request)
{
    $query = $request->input('q');
    $filters = [
        'ruling' => $request->input('ruling'),
        'book_id' => $request->input('book_id'),
        'narrator_id' => $request->input('narrator_id'),
    ];
    
    if (empty($query) && empty(array_filter($filters))) {
        return redirect()->route('home');
    }
    
    // البحث الأساسي
    $searchQuery = Hadith::search($query);
    
    // تطبيق الفلاتر
    if ($filters['ruling']) {
        $searchQuery->where('ruling', $filters['ruling']);
    }
    
    if ($filters['book_id']) {
        $searchQuery->where('book_id', $filters['book_id']);
    }
    
    if ($filters['narrator_id']) {
        $searchQuery->where('narrator_id', $filters['narrator_id']);
    }
    
    $results = $searchQuery->paginate(20);
    
    // بيانات إضافية للفلاتر
    $books = Book::active()->orderBy('order_index')->get();
    $narrators = Narrator::topNarrators(50)->get();
    
    return view('search', compact('results', 'query', 'filters', 'books', 'narrators'));
}
```

### 6.5 صفحة البحث المتقدم

**الملف: `resources/views/search.blade.php`**

```blade
@extends('layouts.app')

@section('title', 'نتائج البحث')

@section('content')

<div class="container mx-auto px-4 py-12">
    
    <!-- Search Header -->
    <div class="mb-12">
        <h1 class="text-4xl font-bold font-arabic mb-4">
            نتائج البحث
            @if($query)
                <span class="text-islamic-gold">عن: "{{ $query }}"</span>
            @endif
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            وُجد {{ $results->total() }} نتيجة
        </p>
    </div>
    
    <div class="grid lg:grid-cols-4 gap-8">
        
        <!-- Filters Sidebar -->
        <div class="lg:col-span-1">
            <div class="card sticky top-24">
                <h3 class="text-xl font-bold mb-4">تصفية النتائج</h3>
                
                <form action="{{ route('search') }}" method="GET">
                    <input type="hidden" name="q" value="{{ $query }}">
                    
                    <!-- Ruling Filter -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2">الحكم</label>
                        <select name="ruling" class="w-full px-4 py-2 rounded-lg border dark:bg-gray-900">
                            <option value="">الكل</option>
                            <option value="صحيح" {{ $filters['ruling'] === 'صحيح' ? 'selected' : '' }}>صحيح</option>
                            <option value="حسن" {{ $filters['ruling'] === 'حسن' ? 'selected' : '' }}>حسن</option>
                            <option value="ضعيف" {{ $filters['ruling'] === 'ضعيف' ? 'selected' : '' }}>ضعيف</option>
                        </select>
                    </div>
                    
                    <!-- Book Filter -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2">الكتاب</label>
                        <select name="book_id" class="w-full px-4 py-2 rounded-lg border dark:bg-gray-900">
                            <option value="">الكل</option>
                            @foreach($books as $book)
                                <option value="{{ $book->id }}" {{ $filters['book_id'] == $book->id ? 'selected' : '' }}>
                                    {{ $book->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Narrator Filter -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-2">الراوي</label>
                        <select name="narrator_id" class="w-full px-4 py-2 rounded-lg border dark:bg-gray-900">
                            <option value="">الكل</option>
                            @foreach($narrators as $narrator)
                                <option value="{{ $narrator->id }}" {{ $filters['narrator_id'] == $narrator->id ? 'selected' : '' }}>
                                    {{ $narrator->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-primary w-full">
                        تطبيق الفلاتر
                    </button>
                    
                    <a href="{{ route('search', ['q' => $query]) }}" class="block text-center mt-3 text-sm text-gray-600 hover:text-islamic-gold">
                        إعادة تعيين
                    </a>
                </form>
            </div>
        </div>
        
        <!-- Results -->
        <div class="lg:col-span-3">
            @if($results->count() > 0)
                <div class="space-y-6">
                    @foreach($results as $hadith)
                        @include('components.hadith-card-small', ['hadith' => $hadith])
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-12">
                    {{ $results->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-2xl font-bold mb-2">لا توجد نتائج</h3>
                    <p class="text-gray-600 dark:text-gray-400">جرّب كلمات بحث مختلفة</p>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection# 🗺️ خارطة الطريق الكاملة - الموسوعة الرقمية لصحيح الجامع الصغير

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