<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'الموسوعة الرقمية لصحيح الجامع' }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[--color-paper] text-[--color-primary] font-[family-name:--font-arabic]">
    <!-- Header -->
    <header class="bg-[--color-primary] text-[--color-paper] shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[--color-accent] rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-[--color-primary]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold font-[family-name:--font-serif]">صحيح الجامع</h1>
                        <p class="text-sm text-[--color-accent]">الموسوعة الرقمية</p>
                    </div>
                </a>

                <!-- Navigation -->
                <nav class="hidden md:flex gap-6">
                    <a href="/" class="hover:text-[--color-accent] transition-colors">الرئيسية</a>
                    <a href="/search" class="hover:text-[--color-accent] transition-colors">البحث</a>
                    <a href="/books" class="hover:text-[--color-accent] transition-colors">الكتب</a>
                    <a href="/about" class="hover:text-[--color-accent] transition-colors">عن المشروع</a>
                </nav>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenu = !mobileMenu" class="md:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <nav x-show="mobileMenu" x-cloak class="md:hidden mt-4 space-y-2">
                <a href="/" class="block py-2 hover:text-[--color-accent] transition-colors">الرئيسية</a>
                <a href="/search" class="block py-2 hover:text-[--color-accent] transition-colors">البحث</a>
                <a href="/books" class="block py-2 hover:text-[--color-accent] transition-colors">الكتب</a>
                <a href="/about" class="block py-2 hover:text-[--color-accent] transition-colors">عن المشروع</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-[--color-charcoal] text-[--color-paper] mt-16">
        <div class="container mx-auto px-4 py-8">
            <div class="grid md:grid-cols-3 gap-8">
                <!-- About -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-[--color-accent]">عن الموسوعة</h3>
                    <p class="text-sm leading-relaxed">
                        موسوعة رقمية شاملة لصحيح الجامع الصغير، تهدف لتسهيل الوصول إلى الأحاديث النبوية الصحيحة.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-[--color-accent]">روابط سريعة</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/search" class="hover:text-[--color-accent] transition-colors">البحث في الأحاديث</a></li>
                        <li><a href="/books" class="hover:text-[--color-accent] transition-colors">تصفح الكتب</a></li>
                        <li><a href="/about" class="hover:text-[--color-accent] transition-colors">عن المشروع</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-[--color-accent]">تواصل معنا</h3>
                    <p class="text-sm">
                        للملاحظات والاقتراحات:<br>
                        <a href="mailto:info@sahihjami.com" class="text-[--color-accent] hover:underline">info@sahihjami.com</a>
                    </p>
                </div>
            </div>

            <div class="border-t border-[--color-primary] mt-8 pt-6 text-center text-sm">
                <p>&copy; {{ date('Y') }} الموسوعة الرقمية لصحيح الجامع. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('app', () => ({
                mobileMenu: false
            }))
        })
    </script>
</body>
</html>
