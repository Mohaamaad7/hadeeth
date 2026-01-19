<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    
    <title><?php echo $__env->yieldContent('title', 'موسوعة الحديث الصحيح - محرك بحث الأحاديث النبوية الشريفة'); ?></title>
    <meta name="title" content="<?php echo $__env->yieldContent('title', 'موسوعة الحديث الصحيح - محرك بحث الأحاديث النبوية الشريفة'); ?>">
    <meta name="description"
        content="<?php echo $__env->yieldContent('meta_description', 'موسوعة شاملة للأحاديث النبوية الصحيحة مع التخريج والشرح وسلاسل الإسناد. ابحث في آلاف الأحاديث من صحيح البخاري ومسلم والسنن.'); ?>">
    <meta name="keywords"
        content="<?php echo $__env->yieldContent('meta_keywords', 'حديث, أحاديث, الحديث النبوي, صحيح البخاري, صحيح مسلم, السنة النبوية, الأحاديث الصحيحة, موسوعة الحديث'); ?>">
    <meta name="author" content="موسوعة الحديث الصحيح">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo e(url()->current()); ?>">
    <meta name="language" content="Arabic">
    <meta name="revisit-after" content="1 days">

    
    <link rel="alternate" hreflang="ar" href="<?php echo e(url()->current()); ?>">
    <link rel="alternate" hreflang="x-default" href="<?php echo e(url()->current()); ?>">

    
    <meta property="og:type" content="<?php echo $__env->yieldContent('og_type', 'website'); ?>">
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">
    <meta property="og:title" content="<?php echo $__env->yieldContent('og_title', 'موسوعة الحديث الصحيح'); ?>">
    <meta property="og:description"
        content="<?php echo $__env->yieldContent('og_description', 'موسوعة شاملة للأحاديث النبوية الصحيحة مع التخريج والشرح'); ?>">
    <meta property="og:image" content="<?php echo $__env->yieldContent('og_image', asset('images/og-default.png')); ?>">
    <meta property="og:locale" content="ar_AR">
    <meta property="og:site_name" content="موسوعة الحديث الصحيح">

    
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?php echo e(url()->current()); ?>">
    <meta name="twitter:title" content="<?php echo $__env->yieldContent('twitter_title', 'موسوعة الحديث الصحيح'); ?>">
    <meta name="twitter:description" content="<?php echo $__env->yieldContent('twitter_description', 'موسوعة شاملة للأحاديث النبوية الصحيحة'); ?>">
    <meta name="twitter:image" content="<?php echo $__env->yieldContent('twitter_image', asset('images/og-default.png')); ?>">

    
    <link rel="icon" type="image/png" href="<?php echo e(asset('favicon.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('apple-touch-icon.png')); ?>">

    
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-8PD0HL5BVE"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'G-8PD0HL5BVE');
    </script>

    
    <?php echo $__env->yieldPushContent('structured_data'); ?>

    
    <script src="https://cdn.tailwindcss.com"></script>

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&family=Scheherazade+New:wght@400;700&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                            950: '#022c22',
                        },
                        gold: {
                            50: '#FCFBF4',
                            100: '#F9F1D8',
                            200: '#F0E0AA',
                            300: '#E8D49F',
                            400: '#DEC075',
                            500: '#D4AF37',
                            600: '#B4942D',
                            700: '#8C7322',
                            800: '#5C4D1A',
                        },
                    },
                    fontFamily: {
                        tajawal: ['Tajawal', 'sans-serif'],
                        scheherazade: ['Scheherazade New', 'serif'],
                        ibm: ['IBM Plex Sans Arabic', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #fafcfd;
        }

        /* نمط هندسي حديث */
        .modern-islamic-bg {
            background-color: #fafcfd;
            background-image:
                radial-gradient(#10b98115 1px, transparent 1px),
                linear-gradient(to right, #f0f9ff 0%, #ffffff 100%);
            background-size: 40px 40px, 100% 100%;
        }

        .hero-pattern {
            position: relative;
            background: linear-gradient(135deg, #064e3b 0%, #1e40af 100%);
            overflow: hidden;
        }

        /* SVG Line Art Pattern */
        .hero-pattern::before {
            content: "";
            position: absolute;
            inset: 0;
            opacity: 0.15;
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M50 0 L100 50 L50 100 L0 50 Z M0 0 L100 100 M100 0 L0 100' stroke='%23ffffff' fill='none' stroke-width='0.5'/%3E%3C/svg%3E");
            background-size: 150px 150px;
            mask-image: radial-gradient(circle at center, black, transparent 80%);
        }

        .search-container {
            backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.95);
        }

        .floating-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .floating-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px -10px rgba(16, 185, 129, 0.15);
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #10b981;
            border-radius: 4px;
        }

        .nav-pill {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-pill:hover {
            transform: translateY(-2px);
            background: linear-gradient(to bottom right, #fff, #ecfdf5);
            border-color: #10b981;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
        }

        .hadith-frame {
            border: 1px solid #a7f3d0;
            box-shadow: 0 0 0 4px #FAFAF9, 0 0 0 5px #a7f3d0;
            position: relative;
        }

        .hadith-frame::before {
            content: "❝";
            position: absolute;
            top: -20px;
            right: 20px;
            font-size: 80px;
            color: #6ee7b7;
            font-family: serif;
            line-height: 1;
            opacity: 0.5;
        }

        .ornament-corner {
            position: absolute;
            width: 30px;
            height: 30px;
            border-style: solid;
            border-color: #10b981;
            opacity: 0.6;
        }

        .top-left {
            top: 10px;
            left: 10px;
            border-width: 2px 0 0 2px;
        }

        .top-right {
            top: 10px;
            right: 10px;
            border-width: 2px 2px 0 0;
        }

        .bottom-left {
            bottom: 10px;
            left: 10px;
            border-width: 0 0 2px 2px;
        }

        .bottom-right {
            bottom: 10px;
            right: 10px;
            border-width: 0 2px 2px 0;
        }

        .timeline-node {
            position: relative;
            padding-right: 2rem;
            border-right: 2px solid #a7f3d0;
        }

        .timeline-node::before {
            content: '';
            position: absolute;
            right: -6px;
            top: 6px;
            width: 10px;
            height: 10px;
            background: #fff;
            border: 2px solid #10b981;
            border-radius: 50%;
        }

        .timeline-node:last-child {
            border-right: 2px solid transparent;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-up {
            animation: fadeIn 0.8s ease-out forwards;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInDown 1.5s ease-out;
        }

        .search-box-shadow {
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.1), 0 2px 4px -1px rgba(16, 185, 129, 0.06);
            transition: all 0.3s ease;
        }

        .search-box-shadow:hover,
        .search-box-shadow:focus-within {
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.2), 0 4px 6px -2px rgba(16, 185, 129, 0.1);
            border-color: #10b981;
        }
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body class="font-tajawal text-gray-800 min-h-screen flex flex-col modern-islamic-bg">

    <?php echo $__env->yieldContent('content'); ?>

    <script>
        // Mobile Menu Logic
        const mobileBtn = document.getElementById('mobile-menu-btn');
        const closeBtn = document.getElementById('close-menu');
        const mobileMenu = document.getElementById('mobile-menu');

        function toggleMenu() {
            if (mobileMenu) mobileMenu.classList.toggle('translate-x-full');
        }

        if (mobileBtn) mobileBtn.addEventListener('click', toggleMenu);
        if (closeBtn) closeBtn.addEventListener('click', toggleMenu);
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html><?php /**PATH C:\server\www\hadeeth\resources\views/layouts/frontend.blade.php ENDPATH**/ ?>