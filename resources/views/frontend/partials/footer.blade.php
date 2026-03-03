{{-- ╔═══════════════════════════════════════════════════════════════╗ --}}
{{-- ║ Footer — موسوعة الحديث الصحيح ║ --}}
{{-- ║ Unified premium footer across all frontend pages ║ --}}
{{-- ╚═══════════════════════════════════════════════════════════════╝ --}}

<footer class="relative overflow-hidden bg-gradient-to-b from-gray-900 via-gray-900 to-emerald-950 text-white mt-auto">
    {{-- Decorative top border --}}
    <div class="h-1 bg-gradient-to-r from-emerald-400 via-emerald-500 to-blue-500"></div>

    {{-- Background Pattern --}}
    <div class="absolute inset-0 opacity-[0.03]">
        <div class="absolute inset-0"
            style="background-image: url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cpath d=%22M30 0L60 30L30 60L0 30Z%22 fill=%22none%22 stroke=%22%23ffffff%22 stroke-width=%220.5%22/%3E%3C/svg%3E'); background-size: 60px 60px;">
        </div>
    </div>

    {{-- Glow orbs --}}
    <div class="absolute top-0 right-1/4 w-80 h-80 bg-emerald-500/10 rounded-full blur-[120px]"></div>
    <div class="absolute bottom-0 left-1/4 w-60 h-60 bg-blue-500/5 rounded-full blur-[100px]"></div>

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Main Footer Content --}}
        <div class="py-16">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-12">

                {{-- Brand Column --}}
                <div class="md:col-span-5">
                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-500/20">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white tracking-tight">موسوعة الحديث الصحيح</h3>
                            <span class="text-[10px] font-bold text-emerald-400/60 tracking-[0.12em] uppercase">The
                                Authentic Hadith Encyclopedia</span>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6 max-w-sm">
                        مرجعك الرقمي الأول لعلوم الحديث الشريف. قاعدة بيانات ذكية تضم آلاف الأحاديث الصحيحة مع التخريج
                        والشرح وسلاسل الإسناد.
                    </p>
                    <div
                        class="inline-flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/20 rounded-full px-4 py-2">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                        <span class="text-emerald-300 font-bold text-xs">الصحيح.. ولا شيء غير الصحيح</span>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div class="md:col-span-3">
                    <h4 class="text-sm font-black text-white mb-5 flex items-center gap-2">
                        <span class="w-5 h-0.5 bg-emerald-500 rounded-full"></span>
                        تصفح الموسوعة
                    </h4>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('home') }}"
                                class="text-gray-400 hover:text-emerald-400 transition-colors duration-300 text-sm font-medium flex items-center gap-2 group">
                                <i
                                    class="fa-solid fa-chevron-left text-[8px] text-gray-600 group-hover:text-emerald-500 group-hover:-translate-x-1 transition-all"></i>
                                الصفحة الرئيسية
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('books.index') }}"
                                class="text-gray-400 hover:text-emerald-400 transition-colors duration-300 text-sm font-medium flex items-center gap-2 group">
                                <i
                                    class="fa-solid fa-chevron-left text-[8px] text-gray-600 group-hover:text-emerald-500 group-hover:-translate-x-1 transition-all"></i>
                                فهرس الكتب
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('search') }}"
                                class="text-gray-400 hover:text-emerald-400 transition-colors duration-300 text-sm font-medium flex items-center gap-2 group">
                                <i
                                    class="fa-solid fa-chevron-left text-[8px] text-gray-600 group-hover:text-emerald-500 group-hover:-translate-x-1 transition-all"></i>
                                البحث في الأحاديث
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('hadith.random') }}"
                                class="text-gray-400 hover:text-emerald-400 transition-colors duration-300 text-sm font-medium flex items-center gap-2 group">
                                <i
                                    class="fa-solid fa-chevron-left text-[8px] text-gray-600 group-hover:text-emerald-500 group-hover:-translate-x-1 transition-all"></i>
                                حديث عشوائي
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- About & Contact --}}
                <div class="md:col-span-4">
                    <h4 class="text-sm font-black text-white mb-5 flex items-center gap-2">
                        <span class="w-5 h-0.5 bg-emerald-500 rounded-full"></span>
                        عن المشروع
                    </h4>
                    <ul class="space-y-3 mb-6">
                        <li>
                            <a href="{{ route('about') }}"
                                class="text-gray-400 hover:text-emerald-400 transition-colors duration-300 text-sm font-medium flex items-center gap-2 group">
                                <i
                                    class="fa-solid fa-chevron-left text-[8px] text-gray-600 group-hover:text-emerald-500 group-hover:-translate-x-1 transition-all"></i>
                                عن الموسوعة
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('about') }}"
                                class="text-gray-400 hover:text-emerald-400 transition-colors duration-300 text-sm font-medium flex items-center gap-2 group">
                                <i
                                    class="fa-solid fa-chevron-left text-[8px] text-gray-600 group-hover:text-emerald-500 group-hover:-translate-x-1 transition-all"></i>
                                المنهجية العلمية
                            </a>
                        </li>
                    </ul>

                    {{-- Quick Stat --}}
                    <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-emerald-500/20 rounded-xl flex items-center justify-center text-emerald-400">
                                <i class="fa-solid fa-book-quran text-lg"></i>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 font-medium">المصدر الأساسي</div>
                                <div class="text-sm font-bold text-white">صحيح الجامع الصغير وزيادته</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Bar --}}
        <div class="border-t border-white/10 py-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-gray-500 text-xs font-medium">
                    © {{ date('Y') }} جميع الحقوق محفوظة — موسوعة الحديث الصحيح
                </p>
                <div class="flex items-center gap-2 text-gray-400">
                    <span class="text-[10px]">برمجة و تطوير - </span>
                    <i class="fa-solid fa-heart text-[10px] text-emerald-400"></i>
                    <span class="text-[10px]"><a class="text-emerald-300 hover:text-white transition-colors font-bold"
                            href="https://fb.com/mohaamaad" target="_blank">محمد عبد الرازق الهشة</a></span>
                    <span class="text-[10px]">— استضافة <a
                            class="text-emerald-300 hover:text-white transition-colors font-bold"
                            href="https://www.areyada.com" target="_blank">ريادة لنظم المعلومات</a></span>
                </div>
            </div>
        </div>
    </div>
</footer>