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
            $searchClean = preg_replace('/[+\-><\(\)~*\"@]+/', ' ', $search);
            $searchClean = trim($searchClean);

            if (mb_strlen($searchClean) >= 2 && !is_numeric($search)) {
                // Prefix each word with + to require ALL words (AND mode)
                $words = preg_split('/\s+/', $searchClean, -1, PREG_SPLIT_NO_EMPTY);
                $booleanQuery = implode(' ', array_map(fn($w) => '+' . $w, $words));

                $query->where(function ($q) use ($booleanQuery, $search) {
                    $q->whereRaw(
                        'MATCH(content_searchable) AGAINST(? IN BOOLEAN MODE)',
                        [$booleanQuery]
                    )->orWhere('number_in_book', 'LIKE', "%{$search}%");
                });
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('content', 'LIKE', "%{$search}%")
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
            'number_in_book' => 'required|integer|min:1',
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

            // استخراج المصادر من الـ codes — مطابقة آمنة بدون LIKE خام
            if (!empty($parsed['sources'])) {
                $allSources = Source::all();
                $sourceIds = [];
                foreach ($parsed['sources'] as $sourceName) {
                    // 1️⃣ مطابقة تامة (الحالة الطبيعية — الـ Parser يُرجع الاسم الكامل)
                    $source = $allSources->firstWhere('name', $sourceName);
                    // 2️⃣ Fallback — بحث جزئي آمن في الذاكرة (بدون SQL)
                    if (!$source) {
                        $source = $allSources->first(fn(Source $s) => str_contains($s->name, $sourceName));
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
            'number_in_book' => 'required|integer|min:1',
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
        ]);

        $results = $this->parser->parseMultiple($request->bulk_text);

        $errors = [];
        $warnings = [];
        foreach ($results as $index => $item) {
            $parsed = $item['parsed'];
            $hadithErrors = [];
            $hadithWarnings = [];
            $raw = $item['raw'];
            $snippet = mb_substr($raw, 0, 60, 'UTF-8') . '...';

            if (empty($parsed['number'])) {
                $hadithErrors[] = 'لم يتم العثور على رقم الحديث [xxx]';
            }
            if (empty($parsed['grade'])) {
                $hadithErrors[] = 'لم يتم العثور على الحكم (صحيح/حسن/ضعيف)';
            }

            $narratorsData = [];
            if (empty($parsed['narrators'])) {
                // تحذير فقط — ليس خطأ حرج
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
                        // تحذير — ليس خطأ حرج (يمكن التصحيح inline)
                        $hadithWarnings[] = "راوي غير معروف: «{$narratorName}» — يمكنك تصحيحه أدناه";
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

            if (empty($parsed['sources']) && empty($parsed['source_codes'])) {
                $hadithErrors[] = 'لم يتم العثور على أي مصدر (خ م د ت ...)';
            }
            if (empty($parsed['clean_text'])) {
                $hadithErrors[] = 'لم يتم استخراج نص الحديث';
            }

            // Check for unknown source codes
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
                    'snippet' => $snippet,
                    'errors' => $hadithErrors,
                ];
            }
            if (!empty($hadithWarnings)) {
                $warnings[] = [
                    'index' => $index + 1,
                    'snippet' => $snippet,
                    'warnings' => $hadithWarnings,
                ];
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
            'hadiths.*.number' => 'nullable|integer',
            'hadiths.*.grade' => 'nullable|string',
            'hadiths.*.narrators_data' => 'nullable|string', // JSON encoded array
            'hadiths.*.narrator_ids' => 'nullable|array',
            'hadiths.*.narrator_ids.*' => 'nullable|exists:narrators,id',
            'hadiths.*.sources' => 'nullable|string', // JSON encoded
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
        $allSources = Source::all(); // تحميل مسبق — مطابقة آمنة في الذاكرة

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

            // Attach sources — مطابقة آمنة بدون LIKE خام
            if (!empty($hadithData['sources'])) {
                $sourceNames = json_decode($hadithData['sources'], true);
                if (is_array($sourceNames)) {
                    $sourceIds = [];
                    foreach ($sourceNames as $sourceName) {
                        // 1️⃣ مطابقة تامة أولاً
                        $source = $allSources->firstWhere('name', $sourceName);
                        // 2️⃣ Fallback — بحث جزئي آمن في الذاكرة
                        if (!$source) {
                            $source = $allSources->first(fn(Source $s) => str_contains($s->name, $sourceName));
                        }
                        if ($source && !in_array($source->id, $sourceIds)) {
                            $sourceIds[] = $source->id;
                        }
                    }
                    if (!empty($sourceIds)) {
                        $hadith->sources()->attach($sourceIds);
                    }
                }
            }

            $savedCount++;
        }

        return redirect()
            ->route('dashboard.hadiths.index')
            ->with('success', "تم إدخال {$savedCount} حديث بنجاح وهي بانتظار المراجعة ⏳");
    }
}
