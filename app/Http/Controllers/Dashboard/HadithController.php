<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Hadith;
use App\Models\Book;
use App\Models\Narrator;
use App\Models\Source;
use App\Services\HadithParser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HadithController extends Controller
{
    public function __construct(
        private HadithParser $parser
    ) {
        $this->middleware('auth');
    }

    /**
     * البحث الذكي عن الراوي.
     * يستخدم استعلام واحد يبحث في: الاسم الكامل، اسم الشهرة، والأسماء البديلة.
     * ثم fallback: تطبيع أبي↔أبو ثنائي الاتجاه، يبدأ بـ، ابن X.
     *
     * @return Narrator|null
     */
    private function findNarrator(string $name): ?Narrator
    {
        $name = trim($name);
        if (empty($name))
            return null;

        // إزالة النقطة الأخيرة إن وجدت
        $name = rtrim($name, '.');

        // 1️⃣ استعلام موحد: الاسم، اسم الشهرة، أو الأسماء البديلة
        $narrator = Narrator::where('name', $name)
            ->orWhere('fame_name', $name)
            ->orWhereHas('alternatives', function ($q) use ($name) {
                $q->where('alternative_name', $name);
            })
            ->first();

        if ($narrator)
            return $narrator;

        // 2️⃣ تطبيع ثنائي الاتجاه: أبي↔أبو + أبا↔أبو
        $variants = [];

        if (str_starts_with($name, 'أبي ') && $name !== 'أبي بن كعب') {
            // أبي → أبو
            $variants[] = 'أبو ' . mb_substr($name, 4);
        } elseif (str_starts_with($name, 'أبو ')) {
            // أبو → أبي (بحث عكسي)
            $variants[] = 'أبي ' . mb_substr($name, 4);
        } elseif (str_starts_with($name, 'أبا ')) {
            // أبا → أبو + أبي
            $variants[] = 'أبو ' . mb_substr($name, 4);
            $variants[] = 'أبي ' . mb_substr($name, 4);
        }

        foreach ($variants as $variant) {
            $narrator = Narrator::where('name', $variant)
                ->orWhere('fame_name', $variant)
                ->orWhereHas('alternatives', function ($q) use ($variant) {
                    $q->where('alternative_name', $variant);
                })
                ->first();

            if ($narrator)
                return $narrator;
        }

        // 3️⃣ الاسم المستخرج أو أحد تحويلاته يبدأ به اسم في DB
        $searchNames = array_merge([$name], $variants);
        foreach ($searchNames as $searchName) {
            $candidates = Narrator::where('name', 'LIKE', "{$searchName} %")->get();
            if ($candidates->count() === 1) {
                return $candidates->first();
            }
        }

        // 4️⃣ حالة "ابن X"
        if (str_starts_with($name, 'ابن ')) {
            $restOfName = mb_substr($name, 4);
            $candidates = Narrator::where('name', 'LIKE', "%بن {$restOfName}%")->get();
            if ($candidates->count() === 1) {
                return $candidates->first();
            }
        }

        return null;
    }

    /**
     * Display a listing of hadiths.
     */
    public function index(Request $request): View
    {
        $query = Hadith::with(['book', 'narrators', 'sources']);

        // Search functionality — FULLTEXT with LIKE fallback
        if ($search = $request->get('search')) {
            // Strip Arabic diacritics (tashkeel) to match content_searchable column
            $searchNoDiacritics = preg_replace('/[\x{064B}-\x{0652}\x{0640}]/u', '', $search);
            $searchClean = preg_replace('/[+\-><\(\)~*\"@]+/', ' ', $searchNoDiacritics);
            $searchClean = trim($searchClean);

            if (mb_strlen($searchClean) >= 2 && !is_numeric($search)) {
                // Prefix each word with + to require ALL words (AND mode)
                $words = preg_split('/\s+/', $searchClean, -1, PREG_SPLIT_NO_EMPTY);
                $booleanQuery = implode(' ', array_map(fn($w) => '+' . $w, $words));

                $query->where(function ($q) use ($booleanQuery, $search, $searchNoDiacritics) {
                    $q->whereRaw(
                        'MATCH(content_searchable) AGAINST(? IN BOOLEAN MODE)',
                        [$booleanQuery]
                    )
                        ->orWhere('content_searchable', 'LIKE', "%{$searchNoDiacritics}%")
                        ->orWhere('number_in_book', 'LIKE', "%{$search}%");
                });
            } else {
                $query->where(function ($q) use ($search, $searchNoDiacritics) {
                    $q->where('content', 'LIKE', "%{$search}%")
                        ->orWhere('content_searchable', 'LIKE', "%{$searchNoDiacritics}%")
                        ->orWhere('number_in_book', 'LIKE', "%{$search}%");
                });
            }
        }

        // Filter by grade
        if ($grade = $request->get('grade')) {
            $query->where('grade', $grade);
        }

        // Hierarchical book filtering
        if ($chapterId = $request->get('chapter_id')) {
            // Sub-chapter selected — filter directly
            $query->where('book_id', $chapterId);
        } elseif ($bookId = $request->get('book_id')) {
            // Main book selected — include the book itself + all its children
            $childIds = Book::where('parent_id', $bookId)->pluck('id')->toArray();
            $allBookIds = array_merge([(int) $bookId], $childIds);
            $query->whereIn('book_id', $allBookIds);
        }

        $hadiths = $query->orderBy('number_in_book', 'asc')
            ->paginate(20)
            ->withQueryString();

        $mainBooks = Book::whereNull('parent_id')->orderBy('sort_order')->get();
        $grades = ['صحيح', 'حسن', 'ضعيف'];

        return view('dashboard.hadiths.index', compact('hadiths', 'mainBooks', 'grades'));
    }

    /**
     * Show the form for creating a new hadith.
     */
    public function create(): View
    {
        $mainBooks = Book::whereNull('parent_id')->orderBy('sort_order')->get();
        $companions = Narrator::companions()->orderBy('name')->get(); // الصحابة فقط
        $narrators = Narrator::orderBy('name')->get(); // جميع الرواة
        $sources = Source::orderBy('name')->get();

        return view('dashboard.hadiths.create', compact('mainBooks', 'companions', 'narrators', 'sources'));
    }

    /**
     * Store a newly created hadith in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'raw_text' => 'nullable|string',
            'content' => 'required|string',
            'explanation' => 'nullable|string',
            'number_in_book' => 'required|string|max:20',
            'grade' => 'required|string|in:صحيح,حسن,ضعيف,موضوع',
            'book_id' => 'required|exists:books,id',
            'narrator_ids' => 'nullable|array',
            'narrator_ids.*' => 'exists:narrators,id',
            'source_ids' => 'nullable|array',
            'source_ids.*' => 'exists:sources,id',
        ]);

        // حفظ النص الأصلي كما ورد (الأمانة العلمية)
        $rawText = $request->input('raw_text');

        // إذا تم إدخال نص خام، استخدم الـ Parser
        if ($request->filled('raw_text')) {
            $parsed = $this->parser->parse($request->raw_text);

            $validated['content'] = $parsed['clean_text'];
            $validated['number_in_book'] = $parsed['number'] ?? $validated['number_in_book'];
            $validated['grade'] = $parsed['grade'] ?? $validated['grade'];

            // البحث عن الرواة إذا تم استخراجهم — بحث ذكي
            if (!empty($parsed['narrators'])) {
                $narratorIds = [];
                $missingNarrators = [];

                foreach ($parsed['narrators'] as $narratorName) {
                    $narrator = $this->findNarrator($narratorName);
                    if ($narrator) {
                        $narratorIds[] = $narrator->id;
                    } else {
                        $missingNarrators[] = $narratorName;
                    }
                }

                if (!empty($missingNarrators)) {
                    $names = implode('، ', $missingNarrators);
                    return redirect()->back()->withInput()->with(
                        'error',
                        "⛔ رواة غير معروفين: «{$names}» — أضفهم من إدارة الرواة أولاً"
                    );
                }

                $validated['narrator_ids'] = $narratorIds;
            }

            // استخراج المصادر — بحث بالرمز أولاً ثم بالاسم
            if (!empty($parsed['sources']) || !empty($parsed['source_codes'])) {
                $allSources = Source::all();
                $sourceIds = [];

                // المصادر العادية (decoded names)
                foreach (($parsed['sources'] ?? []) as $sourceName) {
                    // 1️⃣ بحث بالرمز (code)
                    $source = $allSources->first(fn(Source $s) => $s->code && $s->code === $sourceName);
                    // 2️⃣ بحث بالاسم الكامل
                    if (!$source) {
                        $source = $allSources->firstWhere('name', $sourceName);
                    }
                    // 3️⃣ بحث جزئي في الاسم
                    if (!$source) {
                        $source = $allSources->first(fn(Source $s) => str_contains($s->name, $sourceName));
                    }
                    if ($source && !in_array($source->id, $sourceIds)) {
                        $sourceIds[] = $source->id;
                    }
                }

                // المصادر الوصفية (DESC: prefix)
                foreach (($parsed['source_codes'] ?? []) as $code) {
                    if (!str_starts_with($code, 'DESC:'))
                        continue;
                    $descriptive = substr($code, 5);
                    $source = $allSources->first(fn(Source $s) => str_contains($s->name, $descriptive));
                    if (!$source && preg_match('/^(.+?)\s+في\s+(.+)$/u', $descriptive, $dParts)) {
                        $author = trim($dParts[1]);
                        $book = trim($dParts[2]);
                        $source = $allSources->first(
                            fn(Source $s) =>
                            $s->author && str_contains($s->author, $author) && str_contains($s->name, $book)
                        );
                        if (!$source) {
                            $source = $allSources->first(fn(Source $s) => str_contains($s->name, $book));
                        }
                    }
                    if ($source && !in_array($source->id, $sourceIds)) {
                        $sourceIds[] = $source->id;
                    }
                }

                $validated['source_ids'] = $sourceIds;
            }

            // حفظ الزيادات (additions)
            $validated['additions'] = !empty($parsed['additions']) ? $parsed['additions'] : null;
        }

        $hadith = Hadith::create([
            'content' => $validated['content'],
            'raw_text' => $rawText,
            'explanation' => $validated['explanation'] ?? null,
            'number_in_book' => $validated['number_in_book'],
            'grade' => $validated['grade'],
            'status' => 'pending',
            'book_id' => $validated['book_id'],
            'narrator_id' => !empty($validated['narrator_ids']) ? $validated['narrator_ids'][0] : null, // Legacy support
            'entered_by' => auth()->id(),
            'additions' => $validated['additions'] ?? null,
        ]);

        // Attach narrators (many-to-many)
        if (!empty($validated['narrator_ids'])) {
            $hadith->narrators()->attach($validated['narrator_ids']);
        }

        // Attach sources
        if (!empty($validated['source_ids'])) {
            $hadith->sources()->attach($validated['source_ids']);
        }

        return redirect()
            ->route('dashboard.hadiths.show', $hadith)
            ->with('success', 'تم إدخال الحديث بنجاح وهو بانتظار المراجعة ⏳');
    }

    /**
     * Display the specified hadith.
     */
    public function show(Hadith $hadith): View
    {
        $hadith->load(['book', 'narrators', 'sources']);

        return view('dashboard.hadiths.show', compact('hadith'));
    }

    /**
     * Show the form for editing the specified hadith.
     */
    public function edit(Hadith $hadith): View
    {
        $hadith->load(['sources', 'chains.source', 'chains.narrators', 'book.parent']);
        $mainBooks = Book::whereNull('parent_id')->orderBy('sort_order')->get();
        $companions = Narrator::companions()->orderBy('name')->get(); // الصحابة فقط
        $narrators = Narrator::orderBy('name')->get(); // جميع الرواة
        $sources = Source::orderBy('name')->get();

        return view('dashboard.hadiths.edit', compact('hadith', 'mainBooks', 'companions', 'narrators', 'sources'));
    }

    /**
     * Update the specified hadith in storage.
     */
    public function update(Request $request, Hadith $hadith): RedirectResponse
    {

        $validated = $request->validate([
            'content' => 'required|string',
            'raw_text' => 'nullable|string',
            'explanation' => 'nullable|string',
            'number_in_book' => 'required|string|max:20',
            'grade' => 'required|string|in:صحيح,حسن,ضعيف,موضوع',
            'book_id' => 'required|exists:books,id',
            'narrator_ids' => 'nullable|array',
            'narrator_ids.*' => 'exists:narrators,id',
            'source_ids' => 'nullable|array',
            'source_ids.*' => 'exists:sources,id',
            // الشرح المنظم
            'has_structured_sharh' => 'nullable|boolean',
            'sharh_context' => 'nullable|string',
            'sharh_obstacles' => 'nullable|array',
            'sharh_obstacles.*.title' => 'nullable|string',
            'sharh_obstacles.*.description' => 'nullable|string',
            'sharh_commands' => 'nullable|array',
            'sharh_commands.*.title' => 'nullable|string',
            'sharh_commands.*.ruling' => 'nullable|string',
            'sharh_commands.*.explanation' => 'nullable|string',
            'sharh_conclusion' => 'nullable|string',
            // السلاسل
            'chains' => 'nullable|array',
            'chains.*.source_id' => 'nullable|exists:sources,id',
            'chains.*.description' => 'nullable|string',
            'chains.*.narrators' => 'nullable|array',
            'chains.*.narrators.*.id' => 'nullable|exists:narrators,id',
            'chains.*.narrators.*.role' => 'nullable|string',
        ]);

        // تجهيز بيانات الشرح المنظم
        $sharhData = [];
        if ($request->has_structured_sharh) {
            // تصفية الموانع الفارغة
            $obstacles = collect($validated['sharh_obstacles'] ?? [])
                ->filter(fn($o) => !empty($o['title']) && !empty($o['description']))
                ->values()
                ->toArray();

            // تصفية الأوامر الفارغة
            $commands = collect($validated['sharh_commands'] ?? [])
                ->filter(fn($c) => !empty($c['title']))
                ->values()
                ->toArray();

            $sharhData = [
                'sharh_context' => $validated['sharh_context'] ?? null,
                'sharh_obstacles' => !empty($obstacles) ? $obstacles : null,
                'sharh_commands' => !empty($commands) ? $commands : null,
                'sharh_conclusion' => $validated['sharh_conclusion'] ?? null,
            ];
        } else {
            // إذا لم يتم تفعيل الشرح المنظم، نفرغ الحقول
            $sharhData = [
                'sharh_context' => null,
                'sharh_obstacles' => null,
                'sharh_commands' => null,
                'sharh_conclusion' => null,
            ];
        }

        $hadith->update(array_merge([
            'content' => $validated['content'],
            'raw_text' => $validated['raw_text'] ?? $hadith->raw_text,
            'explanation' => $validated['explanation'] ?? null,
            'number_in_book' => $validated['number_in_book'],
            'grade' => $validated['grade'],
            'book_id' => $validated['book_id'],
            'narrator_id' => !empty($validated['narrator_ids']) ? $validated['narrator_ids'][0] : null,
        ], $sharhData));

        // Sync narrators
        if (isset($validated['narrator_ids'])) {
            $hadith->narrators()->sync($validated['narrator_ids']);
        } else {
            $hadith->narrators()->detach();
        }

        // Sync sources
        if (isset($validated['source_ids'])) {
            $hadith->sources()->sync($validated['source_ids']);
        } else {
            $hadith->sources()->detach();
        }

        // حفظ السلاسل (السلسلة = المصدر + راوٍ واحد على الأقل)
        if (isset($validated['chains'])) {
            // حذف السلاسل القديمة
            $hadith->chains()->delete();

            // إضافة السلاسل الجديدة
            foreach ($validated['chains'] as $chainData) {
                // تخطي السلسلة إذا لم يكن لها source_id
                if (empty($chainData['source_id'])) {
                    continue;
                }

                // تصفية الرواة الفارغة (null)
                $validNarrators = [];
                if (!empty($chainData['narrators'])) {
                    $validNarrators = array_filter($chainData['narrators'], function ($n) {
                        return !empty($n['id']);
                    });
                }

                // تخطي السلسلة إذا لم يكن هناك رواة (السلسلة تبدأ بوجود راوٍ)
                if (empty($validNarrators)) {
                    continue;
                }

                // إنشاء السلسلة
                $chain = $hadith->chains()->create([
                    'source_id' => $chainData['source_id'],
                    'description' => $chainData['description'] ?? null,
                ]);

                // إضافة الرواة مع الترتيب
                $position = 1;
                foreach ($validNarrators as $narratorData) {
                    $chain->narrators()->attach($narratorData['id'], [
                        'position' => $position++,
                        'role' => $narratorData['role'] ?? null,
                    ]);
                }
            }
        }

        return redirect()
            ->route('dashboard.hadiths.show', $hadith)
            ->with('success', 'تم تحديث الحديث والسلاسل بنجاح!');
    }

    /**
     * Remove the specified hadith from storage.
     */
    public function destroy(Hadith $hadith): RedirectResponse
    {
        $hadith->chains()->delete();
        $hadith->sources()->detach();
        $hadith->delete();

        return redirect()
            ->route('dashboard.hadiths.index')
            ->with('success', 'تم حذف الحديث بنجاح!');
    }

    /**
     * Parse raw text and return JSON response.
     */
    public function parseRawText(Request $request)
    {
        $request->validate([
            'raw_text' => 'required|string',
        ]);

        $parsed = $this->parser->parse($request->raw_text);

        return response()->json([
            'success' => true,
            'data' => $parsed,
        ]);
    }

    /**
     * Show bulk import form.
     */
    public function bulkCreate(): View
    {
        $mainBooks = Book::mainBooks()->orderBy('sort_order')->get();

        return view('dashboard.hadiths.bulk-create', compact('mainBooks'));
    }

    /**
     * Preview parsed hadiths from bulk text (AJAX).
     * Returns errors per-hadith if parsing fails.
     * Narrator issues are warnings (non-blocking), not errors.
     */
    public function bulkPreview(Request $request)
    {
        $request->validate([
            'bulk_text' => 'required|string',
            'use_ai' => 'nullable|boolean',
        ]);

        $results = $this->parser->parseMultiple($request->bulk_text);

        // التدخل بالذكاء الاصطناعي لتصحيح النواقص قبل البحث في قاعدة البيانات
        if ($request->boolean('use_ai')) {
            $hadithsFixQueue = [];
            foreach ($results as $index => $item) {
                $parsed = $item['parsed'];
                // نحدد الأحاديث التي تحتاج تصحيح (نقص في الدرجة، الرواة، أو اشتباه في تلاصق الرواة)
                $needsFix = empty($parsed['number']) || empty($parsed['grade']) || empty($parsed['narrators']);
                
                if ($needsFix) {
                    $hadithsFixQueue[] = [
                        'index' => $index,
                        'raw' => $item['raw'],
                        'parsed' => $parsed,
                    ];
                }
            }

            if (!empty($hadithsFixQueue)) {
                try {
                    $aiService = app(\App\Services\GeminiExtractionService::class);
                    $corrections = $aiService->fixIncompleteParses($hadithsFixQueue);

                    foreach ($corrections as $idx => $correction) {
                        if (isset($results[$idx])) {
                            if (!empty($correction['number']) && empty($results[$idx]['parsed']['number'])) {
                                $results[$idx]['parsed']['number'] = $correction['number'];
                                $results[$idx]['parsed']['ai_fixed_number'] = true;
                            }
                            if (!empty($correction['grade']) && empty($results[$idx]['parsed']['grade'])) {
                                $results[$idx]['parsed']['grade'] = $correction['grade'];
                                $results[$idx]['parsed']['ai_fixed_grade'] = true;
                            }
                            if (!empty($correction['narrators']) && is_array($correction['narrators'])) {
                                $results[$idx]['parsed']['narrators'] = $correction['narrators'];
                                $results[$idx]['parsed']['ai_fixed_narrators'] = true;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'تعذر الاتصال بـ Gemini: ' . $e->getMessage(),
                        'errors' => [],
                    ], 422);
                }
            }
        }

        // ===== المرحلة الأولى: البحث العادي في قاعدة البيانات =====
        $errors = [];
        $warnings = [];
        $useAi = $request->boolean('use_ai');

        foreach ($results as $index => $item) {
            $parsed = $item['parsed'];
            $hadithErrors = [];
            $hadithWarnings = [];
            $raw = $item['raw'];
            $snippet = mb_substr($raw, 0, 60, 'UTF-8') . '...';

            if (empty($parsed['number'])) {
                $hadithWarnings[] = 'لم يتم العثور على رقم للحديث';
            }
            if (empty($parsed['grade'])) {
                $hadithErrors[] = 'لم يتم العثور على الحكم (صحيح/حسن/ضعيف)';
            }

            $narratorsData = [];
            if (empty($parsed['narrators'])) {
                $hadithWarnings[] = 'لم يتم العثور على الراوي (عن ...)';
            } else {
                // التحقق من وجود الرواة في قاعدة البيانات
                foreach ($parsed['narrators'] as $narratorName) {
                    $foundNarrator = $this->findNarrator($narratorName);
                    if ($foundNarrator) {
                        $narratorsData[] = [
                            'id' => $foundNarrator->id,
                            'name' => $foundNarrator->name,
                            'found' => true,
                            'original' => $narratorName
                        ];
                    } else {
                        // مبدئياً: غير موجود (سيتم محاولة حلّه بالذكاء الاصطناعي لاحقاً)
                        $narratorsData[] = [
                            'id' => null,
                            'name' => $narratorName,
                            'found' => false,
                            'original' => $narratorName
                        ];
                    }
                }
            }
            $results[$index]['parsed']['narrators_data'] = $narratorsData;

            // ربط المصادر بـ IDs من قاعدة البيانات — بحث بالرمز أولاً ثم بالاسم
            $sourcesData = [];
            if (!empty($parsed['source_codes'])) {
                foreach ($parsed['source_codes'] as $code) {
                    $source = null;

                    // مصدر وصفي مثل "ابن أبي الدنيا في مكايد الشيطان"
                    if (str_starts_with($code, 'DESC:')) {
                        $descriptive = substr($code, 5); // Remove DESC: prefix
                        // بحث بالاسم الكامل أو جزئي
                        $source = Source::where('name', $descriptive)->first();
                        if (!$source) {
                            $source = Source::where('name', 'LIKE', "%{$descriptive}%")->first();
                        }
                        // بحث بتقسيم "المؤلف في الكتاب"
                        if (!$source && preg_match('/^(.+?)\s+في\s+(.+)$/u', $descriptive, $dParts)) {
                            $author = trim($dParts[1]);
                            $book = trim($dParts[2]);
                            $source = Source::where('author', 'LIKE', "%{$author}%")
                                ->where('name', 'LIKE', "%{$book}%")
                                ->first();
                            // أو بحث بالكتاب فقط
                            if (!$source) {
                                $source = Source::where('name', 'LIKE', "%{$book}%")->first();
                            }
                        }
                        $displayCode = $descriptive; // للعرض في التحذير
                    } else {
                        // 1️⃣ بحث بالرمز
                        $source = Source::where('code', $code)->first();
                        // 2️⃣ Fallback: بحث بالاسم
                        if (!$source) {
                            $source = Source::where('name', 'LIKE', "%{$code}%")->first();
                        }
                        $displayCode = $code;
                    }

                    if ($source) {
                        $sourcesData[] = [
                            'id' => $source->id,
                            'name' => $source->name,
                            'code' => $source->code,
                            'found' => true,
                        ];
                    } else {
                        // مبدئياً: غير موجود (سيتم محاولة حلّه بالذكاء الاصطناعي لاحقاً)
                        $sourcesData[] = [
                            'id' => null,
                            'name' => $displayCode,
                            'code' => $code,
                            'found' => false,
                            'original_code' => $code,
                        ];
                    }
                }
            }
            if (empty($sourcesData) && empty($parsed['source_codes'])) {
                $hadithWarnings[] = 'لم يتم العثور على أي مصدر — يمكنك إضافتها يدوياً';
            }
            $results[$index]['parsed']['sources_data'] = $sourcesData;

            if (empty($parsed['clean_text'])) {
                $hadithErrors[] = 'لم يتم استخراج نص الحديث';
            }

            if (!empty($hadithErrors)) {
                $errors[] = [
                    'index' => $index + 1,
                    'snippet' => $snippet,
                    'errors' => $hadithErrors,
                ];
            }
            // نخزن التحذيرات المبدئية لكن بدون تحذيرات الرواة (ستُحَلّ بالذكاء الاصطناعي)
            if (!empty($hadithWarnings)) {
                $warnings[] = [
                    'index' => $index + 1,
                    'snippet' => $snippet,
                    'warnings' => $hadithWarnings,
                ];
            }
        }

        // ===== المرحلة الثانية: مطابقة الرواة بالذكاء الاصطناعي (Gemini) =====
        if ($useAi) {
            // جمع كل الرواة غير المطابقين من جميع الأحاديث
            $unresolvedNarrators = [];
            $unresolvedMap = []; // لتتبع أي حديث ينتمي إليه كل راوي

            foreach ($results as $index => $item) {
                $narratorsData = $item['parsed']['narrators_data'] ?? [];
                foreach ($narratorsData as $ndIdx => $nd) {
                    if (!$nd['found'] && !empty($nd['original'])) {
                        $originalName = $nd['original'];
                        // لا نكرر نفس الاسم في الإرسال لـ Gemini
                        if (!isset($unresolvedNarrators[$originalName])) {
                            $unresolvedNarrators[$originalName] = $originalName;
                        }
                        $unresolvedMap[] = [
                            'hadith_index' => $index,
                            'nd_index' => $ndIdx,
                            'original_name' => $originalName,
                        ];
                    }
                }
            }

            if (!empty($unresolvedNarrators)) {
                try {
                    $aiService = app(\App\Services\GeminiExtractionService::class);

                    // تحضير قائمة الرواة من DB لإرسالها لـ Gemini
                    $dbNarrators = Narrator::with('alternatives')->get()->map(function ($n) {
                        return [
                            'id' => $n->id,
                            'name' => $n->name,
                            'fame_name' => $n->fame_name,
                            'alternatives' => $n->alternatives->pluck('alternative_name')->toArray(),
                        ];
                    })->toArray();

                    $aiResolutions = $aiService->resolveNarrators(
                        array_values($unresolvedNarrators),
                        $dbNarrators
                    );

                    // تطبيق النتائج على كل حديث
                    foreach ($unresolvedMap as $mapping) {
                        $originalName = $mapping['original_name'];
                        $hIdx = $mapping['hadith_index'];
                        $ndIdx = $mapping['nd_index'];

                        if (isset($aiResolutions[$originalName]) && $aiResolutions[$originalName]['matched']) {
                            $resolved = $aiResolutions[$originalName]['resolved_narrators'] ?? [];

                            if (!empty($resolved)) {
                                // استبدال الراوي غير المعروف بالرواة المطابقين من Gemini
                                $newNarratorsData = [];
                                foreach ($resolved as $rNarrator) {
                                    // التحقق من وجود الراوي فعلاً في DB قبل الاعتماد
                                    $verifiedNarrator = Narrator::find($rNarrator['db_id'] ?? null);
                                    if ($verifiedNarrator) {
                                        $newNarratorsData[] = [
                                            'id' => $verifiedNarrator->id,
                                            'name' => $verifiedNarrator->name,
                                            'found' => true,
                                            'original' => $originalName,
                                            'ai_resolved' => true,
                                            'ai_reason' => $rNarrator['reason'] ?? '',
                                        ];
                                    }
                                }

                                if (!empty($newNarratorsData)) {
                                    // استبدال العنصر غير المطابق بالعناصر المطابقة
                                    $currentData = $results[$hIdx]['parsed']['narrators_data'];
                                    array_splice($currentData, $ndIdx, 1, $newNarratorsData);
                                    $results[$hIdx]['parsed']['narrators_data'] = $currentData;
                                    $results[$hIdx]['parsed']['ai_fixed_narrators'] = true;
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // في حالة فشل Gemini، نستمر بالنتائج العادية ونضيف تحذيراً
                    Log::warning('Gemini narrator resolution failed: ' . $e->getMessage());
                }
            }

            // ===== مطابقة المصادر بالذكاء الاصطناعي =====
            $unresolvedSources = [];
            $unresolvedSourceMap = [];

            foreach ($results as $index => $item) {
                $sourcesData = $item['parsed']['sources_data'] ?? [];
                foreach ($sourcesData as $sdIdx => $sd) {
                    if (!$sd['found'] && !empty($sd['original_code'])) {
                        $codeKey = $sd['original_code'];
                        if (!isset($unresolvedSources[$codeKey])) {
                            $unresolvedSources[$codeKey] = [
                                'code' => $codeKey,
                                'display_name' => $sd['name'],
                                'hadith_snippet' => mb_substr($item['raw'], 0, 100, 'UTF-8'),
                            ];
                        }
                        $unresolvedSourceMap[] = [
                            'hadith_index' => $index,
                            'sd_index' => $sdIdx,
                            'original_code' => $codeKey,
                        ];
                    }
                }
            }

            if (!empty($unresolvedSources)) {
                try {
                    $aiService = $aiService ?? app(\App\Services\GeminiExtractionService::class);

                    // تحضير قائمة المصادر من DB
                    $dbSources = Source::all()->map(function ($s) {
                        return [
                            'id' => $s->id,
                            'name' => $s->name,
                            'code' => $s->code,
                            'author' => $s->author,
                            'type' => $s->type,
                        ];
                    })->toArray();

                    $aiSourceResolutions = $aiService->resolveSources(
                        array_values($unresolvedSources),
                        $dbSources
                    );

                    // تطبيق النتائج
                    foreach ($unresolvedSourceMap as $mapping) {
                        $codeKey = $mapping['original_code'];
                        $hIdx = $mapping['hadith_index'];
                        $sdIdx = $mapping['sd_index'];

                        if (isset($aiSourceResolutions[$codeKey]) && $aiSourceResolutions[$codeKey]['matched']) {
                            $resolved = $aiSourceResolutions[$codeKey]['resolved_sources'] ?? [];

                            if (!empty($resolved)) {
                                $newSourcesData = [];
                                foreach ($resolved as $rSource) {
                                    $verifiedSource = Source::find($rSource['db_id'] ?? null);
                                    if ($verifiedSource) {
                                        $newSourcesData[] = [
                                            'id' => $verifiedSource->id,
                                            'name' => $verifiedSource->name,
                                            'code' => $verifiedSource->code,
                                            'found' => true,
                                            'ai_resolved' => true,
                                            'ai_reason' => $rSource['reason'] ?? '',
                                        ];
                                    }
                                }

                                if (!empty($newSourcesData)) {
                                    $currentData = $results[$hIdx]['parsed']['sources_data'];
                                    array_splice($currentData, $sdIdx, 1, $newSourcesData);
                                    $results[$hIdx]['parsed']['sources_data'] = $currentData;
                                    $results[$hIdx]['parsed']['ai_fixed_sources'] = true;
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Gemini source resolution failed: ' . $e->getMessage());
                }
            }

            // إعادة حساب التحذيرات بعد تطبيق نتائج Gemini (الرواة + المصادر)
            $warnings = [];
            foreach ($results as $index => $item) {
                $hadithWarnings = [];
                $raw = $item['raw'];
                $snippet = mb_substr($raw, 0, 60, 'UTF-8') . '...';

                if (empty($item['parsed']['number'])) {
                    $hadithWarnings[] = 'لم يتم العثور على رقم للحديث';
                }

                // تحذيرات الرواة: فقط الرواة الذين لم يتم حلّهم نهائياً
                $narratorsData = $item['parsed']['narrators_data'] ?? [];
                foreach ($narratorsData as $nd) {
                    if (!$nd['found']) {
                        $hadithWarnings[] = "راوي غير معروف: «{$nd['original']}» — يمكنك تصحيحه أدناه";
                    }
                }
                if (empty($narratorsData)) {
                    $hadithWarnings[] = 'لم يتم العثور على الراوي (عن ...)';
                }

                // تحذيرات المصادر: فقط المصادر التي لم يتم حلّها نهائياً
                $sourcesData = $item['parsed']['sources_data'] ?? [];
                $hasUnresolvedSources = false;
                foreach ($sourcesData as $sd) {
                    if (!$sd['found']) {
                        $hadithWarnings[] = "مصدر غير معروف: «{$sd['name']}» — يمكنك تصحيحه أدناه";
                        $hasUnresolvedSources = true;
                    }
                }
                if (empty($sourcesData) && empty($item['parsed']['source_codes'])) {
                    $hadithWarnings[] = 'لم يتم العثور على أي مصدر — يمكنك إضافتها يدوياً';
                }

                if (!empty($hadithWarnings)) {
                    $warnings[] = [
                        'index' => $index + 1,
                        'snippet' => $snippet,
                        'warnings' => $hadithWarnings,
                    ];
                }
            }
        } else {
            // في الوضع العادي (بدون AI)، نضيف تحذيرات الرواة والمصادر غير المعروفين
            $warnings = [];
            foreach ($results as $index => $item) {
                $hadithWarnings = [];
                $raw = $item['raw'];
                $snippet = mb_substr($raw, 0, 60, 'UTF-8') . '...';

                if (empty($item['parsed']['number'])) {
                    $hadithWarnings[] = 'لم يتم العثور على رقم للحديث';
                }

                $narratorsData = $item['parsed']['narrators_data'] ?? [];
                foreach ($narratorsData as $nd) {
                    if (!$nd['found']) {
                        $hadithWarnings[] = "راوي غير معروف: «{$nd['original']}» — يمكنك تصحيحه أدناه";
                    }
                }
                if (empty($narratorsData) && empty($item['parsed']['narrators'])) {
                    $hadithWarnings[] = 'لم يتم العثور على الراوي (عن ...)';
                }

                $sourcesData = $item['parsed']['sources_data'] ?? [];
                foreach ($sourcesData as $sd) {
                    if (!$sd['found']) {
                        $hadithWarnings[] = "مصدر غير معروف: «{$sd['name']}» — يمكنك تصحيحه أدناه";
                    }
                }
                if (empty($sourcesData) && empty($item['parsed']['source_codes'])) {
                    $hadithWarnings[] = 'لم يتم العثور على أي مصدر — يمكنك إضافتها يدوياً';
                }

                if (!empty($hadithWarnings)) {
                    $warnings[] = [
                        'index' => $index + 1,
                        'snippet' => $snippet,
                        'warnings' => $hadithWarnings,
                    ];
                }
            }
        }

        // أخطاء حرجة فقط تمنع المعاينة
        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => 'يوجد ' . count($errors) . ' حديث به مشاكل في التحليل',
                'errors' => $errors,
                'count' => count($results),
                'hadiths' => $results,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'count' => count($results),
            'hadiths' => $results,
            'warnings' => $warnings,
        ]);
    }

    /**
     * Store multiple hadiths from bulk import.
     * Halts entirely if any hadith has parsing issues.
     */
    public function bulkStore(Request $request): RedirectResponse
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'hadiths' => 'required|array|min:1',
            'hadiths.*.raw_text' => 'required|string',
            'hadiths.*.clean_text' => 'required|string',
            'hadiths.*.number' => 'nullable|string|max:20',
            'hadiths.*.grade' => 'nullable|string',
            'hadiths.*.narrators_data' => 'nullable|string', // JSON encoded array
            'hadiths.*.narrator_ids' => 'nullable|array',
            'hadiths.*.narrator_ids.*' => 'nullable|exists:narrators,id',
            'hadiths.*.source_ids' => 'nullable|array',
            'hadiths.*.source_ids.*' => 'nullable|exists:sources,id',
            'hadiths.*.additions' => 'nullable|string', // JSON encoded
        ]);

        // Pre-validate: re-parse each hadith and check for issues
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
                $snippet = mb_substr($hadithData['raw_text'], 0, 60, 'UTF-8') . '...';
                $errors[] = "حديث #" . ($index + 1) . " ({$snippet}): " . implode('، ', $hadithErrors);
            }
        }

        if (!empty($errors)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', '⛔ تم إيقاف الإدخال! يوجد ' . count($errors) . ' حديث به مشاكل:')
                ->with('parsing_errors', $errors);
        }

        $bookId = $request->book_id;
        $savedCount = 0;

        foreach ($request->hadiths as $hadithData) {

            $narratorIdsToAttach = [];

            // استخدام الـ IDs المصححة/المختارة من inline fix (Select2 Multiple)
            if (!empty($hadithData['narrator_ids'])) {
                foreach ($hadithData['narrator_ids'] as $nId) {
                    if (!empty($nId)) {
                        $narratorIdsToAttach[] = (int) $nId;
                    }
                }
            } elseif (!empty($hadithData['narrators_data'])) {
                // استخدام الـ IDs المستخرجة من الـ Parser
                $narratorsData = json_decode($hadithData['narrators_data'], true);
                if (is_array($narratorsData)) {
                    foreach ($narratorsData as $nd) {
                        if (!empty($nd['id'])) {
                            $narratorIdsToAttach[] = (int) $nd['id'];
                        } else if (!empty($nd['original'])) {
                            // fallback search just in case
                            $n = $this->findNarrator($nd['original']);
                            if ($n) {
                                $narratorIdsToAttach[] = $n->id;
                            }
                        }
                    }
                }
            }

            // Create hadith
            $hadith = Hadith::create([
                'content' => $hadithData['clean_text'],
                'raw_text' => $hadithData['raw_text'],
                'number_in_book' => $hadithData['number'] ?? null,
                'grade' => $hadithData['grade'] ?? 'صحيح',
                'status' => 'pending',
                'book_id' => $bookId,
                'narrator_id' => !empty($narratorIdsToAttach) ? $narratorIdsToAttach[0] : null, // Legacy support
                'entered_by' => auth()->id(),
                'additions' => !empty($hadithData['additions']) ? json_decode($hadithData['additions'], true) : null,
            ]);

            // Attach narrators (many-to-many)
            if (!empty($narratorIdsToAttach)) {
                $hadith->narrators()->attach(array_unique($narratorIdsToAttach));
            }

            // Attach sources — IDs مباشرة من Select2
            if (!empty($hadithData['source_ids'])) {
                $sourceIds = array_unique(array_filter(array_map('intval', $hadithData['source_ids'])));
                if (!empty($sourceIds)) {
                    $hadith->sources()->attach($sourceIds);
                }
            }

            $savedCount++;
        }

        return redirect()
            ->route('dashboard.hadiths.index')
            ->with('success', "تم إدخال {$savedCount} حديث بنجاح وهي بانتظار المراجعة ⏳");
    }
}
