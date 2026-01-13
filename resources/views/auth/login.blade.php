<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل الدخول - موسوعة الحديث الصحيح</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Cairo:wght@300;400;600;700&display=swap"
        rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            850: '#064e3b',
                            950: '#022c22',
                        },
                        gold: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    },
                    fontFamily: {
                        cairo: ['Cairo', 'sans-serif'],
                        amiri: ['Amiri', 'serif'],
                    },
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #064e3b;
            border-radius: 4px;
        }

        .input-focus-ring:focus-within {
            box-shadow: 0 0 0 2px rgba(6, 78, 59, 0.2);
            border-color: #064e3b;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center relative overflow-hidden">

    <!-- Background -->
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-950 via-emerald-900 to-emerald-950 z-10"></div>
        <div class="absolute inset-0 opacity-20"
            style="background-image: url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cpath d=%22M30 0L60 30L30 60L0 30Z%22 fill=%22none%22 stroke=%22%23ffffff%22 stroke-width=%220.5%22/%3E%3C/svg%3E'); background-size: 60px 60px;">
        </div>
    </div>

    <!-- Decorative Elements -->
    <div
        class="absolute top-0 right-0 w-64 h-64 bg-gold-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 z-0 transform translate-x-1/2 -translate-y-1/2">
    </div>
    <div
        class="absolute bottom-0 left-0 w-64 h-64 bg-emerald-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 z-0 transform -translate-x-1/2 translate-y-1/2">
    </div>

    <!-- Main Card -->
    <div
        class="relative z-20 w-full max-w-4xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row mx-4">

        <!-- Right Side: Login Form -->
        <div class="w-full md:w-1/2 p-8 md:p-12 glass-effect flex flex-col justify-center">

            <!-- Logo Section Mobile Only -->
            <div class="md:hidden flex justify-center mb-6">
                <div class="w-16 h-16 bg-emerald-850 rounded-full flex items-center justify-center text-gold-400">
                    <i data-lucide="book-open" class="w-8 h-8"></i>
                </div>
            </div>

            <div class="text-center md:text-right mb-8">
                <h2 class="text-3xl font-bold text-emerald-950 mb-2">تسجيل الدخول</h2>
                <p class="text-gray-500 text-sm">أهلاً بك مجدداً في موسوعة الحديث الصحيح</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="text-sm text-red-600 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                    <div
                        class="relative input-focus-ring rounded-lg border border-gray-300 transition-all duration-200">
                        <div
                            class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-emerald-600">
                            <i data-lucide="mail" class="w-5 h-5"></i>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            autocomplete="email" autofocus
                            class="block w-full pr-10 pl-3 py-3 rounded-lg focus:outline-none bg-transparent @error('email') border-red-500 @enderror"
                            placeholder="name@example.com">
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-sm font-medium text-gray-700">كلمة المرور</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-sm font-medium text-emerald-600 hover:text-emerald-800 transition-colors">نسيت
                                كلمة المرور؟</a>
                        @endif
                    </div>
                    <div
                        class="relative input-focus-ring rounded-lg border border-gray-300 transition-all duration-200">
                        <div
                            class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-emerald-600">
                            <i data-lucide="lock" class="w-5 h-5"></i>
                        </div>
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                            class="block w-full pr-10 pl-10 py-3 rounded-lg focus:outline-none bg-transparent"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePassword()"
                            class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 hover:text-emerald-600 transition-colors">
                            <i id="eyeIcon" data-lucide="eye" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }}
                        class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded cursor-pointer">
                    <label for="remember" class="mr-2 block text-sm text-gray-700 cursor-pointer select-none">
                        تذكرني على هذا الجهاز
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-emerald-850 hover:bg-emerald-950 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-300 transform hover:-translate-y-0.5">
                    <span>دخول</span>
                    <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
                </button>
            </form>

            <div class="mt-8 text-center">
                <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-emerald-600 transition-colors">
                    <i data-lucide="home" class="w-4 h-4 inline ml-1"></i>
                    العودة للصفحة الرئيسية
                </a>
            </div>
        </div>

        <!-- Left Side: Visual/Branding (Hidden on mobile) -->
        <div
            class="hidden md:flex md:w-1/2 bg-emerald-850 text-white p-12 flex-col justify-between relative overflow-hidden">
            <!-- Decorative Pattern -->
            <div class="absolute inset-0 opacity-10"
                style="background-image: url('data:image/svg+xml,%3Csvg width=%2240%22 height=%2240%22 viewBox=%220 0 40 40%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Ccircle cx=%2220%22 cy=%2220%22 r=%221%22 fill=%22%23ffffff%22/%3E%3C/svg%3E'); background-size: 40px 40px;">
            </div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 text-gold-400 mb-6">
                    <div class="p-2 bg-white/10 rounded-lg backdrop-blur-sm">
                        <i data-lucide="book-open-check" class="w-8 h-8"></i>
                    </div>
                    <span class="text-xl font-bold font-cairo">موسوعة الحديث الصحيح</span>
                </div>
            </div>

            <div class="relative z-10 text-center space-y-6">
                <h1 class="text-4xl font-amiri font-bold leading-tight text-gold-400">
                    من يرد الله به خيراً<br>يفقهه في الدين
                </h1>
                <p class="text-emerald-100 text-lg font-light leading-relaxed">
                    منصتك الموثوقة للبحث في الأحاديث النبوية الشريفة، والتحقق من صحتها، وفهم معانيها بأسلوب عصري وميسر.
                </p>
            </div>

            <div class="relative z-10 flex justify-center gap-4 mt-8">
                <div class="text-center">
                    <span class="block text-2xl font-bold text-gold-400">+٣٠</span>
                    <span class="text-xs text-emerald-200">كتاب رئيسي</span>
                </div>
                <div class="w-px bg-white/20 h-10"></div>
                <div class="text-center">
                    <span class="block text-2xl font-bold text-gold-400">+١٠ آلاف</span>
                    <span class="text-xs text-emerald-200">حديث صحيح</span>
                </div>
            </div>
        </div>

    </div>

    <script>
        // Initialize Icons
        lucide.createIcons();

        // Password Toggle Function
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                eyeIcon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>
</body>

</html>