<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiExtractionService
{
    private string $apiKey;
    private array $models = [
        'gemini-2.5-flash',
        'gemini-2.0-flash',
        'gemma-3-27b-it',
        'gemini-2.5-flash-lite'
    ];

    private string $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', env('GEMINI_API_KEY', ''));
    }

    /**
     * مطابقة ذكية للرواة باستخدام Gemini.
     * يأخذ أسماء الرواة المستخرجة التي لم يتم التعرف عليها في قاعدة البيانات،
     * ويرسلها مع قائمة الرواة الفعلية في DB ليقوم Gemini بمطابقتها ذكياً.
     *
     * @param array $unresolvedNarrators [{hadith_index, narrator_original_name}]
     * @param array $dbNarrators [{id, name, fame_name, alternatives: []}]
     * @return array [narrator_original_name => {id, name, confidence}] 
     */
    public function resolveNarrators(array $unresolvedNarrators, array $dbNarrators): array
    {
        if (empty($this->apiKey) || empty($unresolvedNarrators) || empty($dbNarrators)) {
            return [];
        }

        $systemInstruction = "أنت خبير في أسماء رواة الحديث النبوي الشريف ومعرفة أسمائهم وكناهم وألقابهم.
مهمتك هي مطابقة أسماء رواة مستخرجة من نصوص الأحاديث مع قائمة الرواة الموجودة في قاعدة البيانات.

التزم بالقواعد التالية بصرامة:
1. إذا كان الاسم المستخرج يحتوي على أكثر من راوي ملتصقين (مثل: 'أبي سعيد وأبي هريرة')، قم بفصلهم أولاً ثم طابق كل واحد.
2. 'أبي' و'أبو' و'أبا' هي نفس الشيء (كنية). مثلاً: 'أبي هريرة' = 'أبو هريرة'.
3. 'أبي سعيد' قد يكون 'أبو سعيد الخدري' — ابحث عن أقرب تطابق في القائمة.
4. 'ابن عمر' قد يكون 'عبد الله بن عمر' — ابحث بذكاء.
5. 'ابن مسعود' قد يكون 'عبد الله بن مسعود' وهكذا.
6. إذا لم تجد تطابقاً واضحاً في القائمة، أعد الاسم كما هو مع matched=false.
7. التنسيق المطلوب للرد هو JSON array فقط بدون أي نصوص إضافية أو علامات Markdown.";

        // تحضير قائمة الرواة من DB بصيغة مبسطة
        $dbList = array_map(function ($n) {
            $entry = ['id' => $n['id'], 'name' => $n['name']];
            if (!empty($n['fame_name'])) {
                $entry['fame_name'] = $n['fame_name'];
            }
            if (!empty($n['alternatives'])) {
                $entry['alt_names'] = $n['alternatives'];
            }
            return $entry;
        }, $dbNarrators);

        $promptText = "لدي أسماء رواة مستخرجة من نصوص أحاديث لكن لم يتم التعرف عليها في قاعدة البيانات.\n";
        $promptText .= "أريدك أن تطابق كل اسم مع الراوي الصحيح من قائمة الرواة الموجودة في قاعدة البيانات.\n\n";
        $promptText .= "أسماء الرواة المطلوب مطابقتها:\n";
        $promptText .= json_encode($unresolvedNarrators, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n\n";
        $promptText .= "قائمة الرواة في قاعدة البيانات:\n";
        $promptText .= json_encode($dbList, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n\n";
        $promptText .= "أعد الرد بصيغة JSON Array، كل عنصر يحتوي على:\n";
        $promptText .= "- original_name: الاسم المستخرج الأصلي كما أرسلته لك.\n";
        $promptText .= "- matched: true إذا وجدت تطابقاً، false إذا لم تجد.\n";
        $promptText .= "- resolved_narrators: مصفوفة بالرواة المطابقين (قد يكون أكثر من واحد إذا كان الاسم يحتوي على عدة رواة)، كل عنصر يحتوي على:\n";
        $promptText .= "  - db_id: رقم الراوي من قاعدة البيانات (id).\n";
        $promptText .= "  - db_name: اسم الراوي في قاعدة البيانات.\n";
        $promptText .= "  - reason: سبب المطابقة بالعربي (مثلاً: 'أبي هريرة = أبو هريرة' أو 'أبي سعيد = أبو سعيد الخدري').\n";

        return $this->callGemini($systemInstruction, $promptText, function ($decoded) {
            $results = [];
            foreach ($decoded as $item) {
                if (isset($item['original_name'])) {
                    $results[$item['original_name']] = $item;
                }
            }
            return $results;
        });
    }

    /**
     * مطابقة ذكية للمصادر باستخدام Gemini.
     * يأخذ رموز المصادر غير المعروفة ونصوص الأحاديث الأصلية،
     * ويرسلها مع قائمة المصادر الفعلية في DB ليقوم Gemini بمطابقتها ذكياً.
     *
     * @param array $unresolvedSources [{hadith_index, code, raw_text}]
     * @param array $dbSources [{id, name, code, author, type}]
     * @return array [code => {matched, resolved_sources: [{db_id, db_name, reason}]}]
     */
    public function resolveSources(array $unresolvedSources, array $dbSources): array
    {
        if (empty($this->apiKey) || empty($unresolvedSources) || empty($dbSources)) {
            return [];
        }

        $systemInstruction = "أنت خبير في كتب الحديث النبوي الشريف ومصادره ورموزه المختصرة.
مهمتك هي مطابقة رموز مصادر مستخرجة من نصوص الأحاديث مع قائمة المصادر الموجودة في قاعدة البيانات.

التزم بالقواعد التالية بصرامة:
1. الرموز الشائعة مثل: حم = مسند أحمد، هـ = سنن ابن ماجه، ك = المستدرك للحاكم، خ = صحيح البخاري، م = صحيح مسلم، د = سنن أبي داود، ت = سنن الترمذي، ن = سنن النسائي.
2. الرموز المركبة مثل: ق = البخاري ومسلم، 4 = الأربعة (أبو داود والترمذي والنسائي وابن ماجه)، 3 = الثلاثة (بدون ابن ماجه).
3. المصادر الوصفية مثل 'ابن أبي الدنيا في مكايد الشيطان' — طابقها مع أقرب مصدر في القائمة.
4. 'ابن خزيمة' قد يكون 'صحيح ابن خزيمة' في DB — ابحث بذكاء.
5. إذا كان الرمز المستخرج خطأ في التحليل (مثلاً كلمة من متن الحديث تم اعتبارها رمز مصدر)، أعد matched=false مع سبب 'ليس رمز مصدر'.
6. إذا لم تجد تطابقاً في القائمة، أعد matched=false.
7. انظر إلى النص الأصلي للحديث لتفهم السياق وتحدد المصادر الصحيحة.
8. التنسيق المطلوب للرد هو JSON array فقط بدون أي نصوص إضافية أو علامات Markdown.";

        // تحضير قائمة المصادر من DB بصيغة مبسطة
        $dbList = array_map(function ($s) {
            $entry = ['id' => $s['id'], 'name' => $s['name']];
            if (!empty($s['code'])) {
                $entry['code'] = $s['code'];
            }
            if (!empty($s['author'])) {
                $entry['author'] = $s['author'];
            }
            return $entry;
        }, $dbSources);

        $promptText = "لدي رموز/أسماء مصادر مستخرجة من نصوص أحاديث لكن لم يتم التعرف عليها في قاعدة البيانات.\n";
        $promptText .= "أريدك أن تطابق كل رمز/اسم مع المصدر الصحيح من قائمة المصادر الموجودة في قاعدة البيانات.\n\n";
        $promptText .= "المصادر المطلوب مطابقتها:\n";
        $promptText .= json_encode($unresolvedSources, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n\n";
        $promptText .= "قائمة المصادر في قاعدة البيانات:\n";
        $promptText .= json_encode($dbList, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n\n";
        $promptText .= "أعد الرد بصيغة JSON Array، كل عنصر يحتوي على:\n";
        $promptText .= "- original_code: الرمز/الاسم المستخرج الأصلي كما أرسلته لك.\n";
        $promptText .= "- matched: true إذا وجدت تطابقاً، false إذا لم تجد.\n";
        $promptText .= "- resolved_sources: مصفوفة بالمصادر المطابقة (قد يكون أكثر من واحد إذا كان الرمز يشير لعدة مصادر مثل 'ق' = البخاري ومسلم)، كل عنصر يحتوي على:\n";
        $promptText .= "  - db_id: رقم المصدر من قاعدة البيانات (id).\n";
        $promptText .= "  - db_name: اسم المصدر في قاعدة البيانات.\n";
        $promptText .= "  - reason: سبب المطابقة بالعربي (مثلاً: 'ابن خزيمة = صحيح ابن خزيمة' أو 'ك = المستدرك للحاكم').\n";

        return $this->callGemini($systemInstruction, $promptText, function ($decoded) {
            $results = [];
            foreach ($decoded as $item) {
                if (isset($item['original_code'])) {
                    $results[$item['original_code']] = $item;
                }
            }
            return $results;
        });
    }

    /**
     * Fix incomplete parses using Gemini API with Search Grounding
     * 
     * @param array $failedHadiths Array of hadiths that require fixing
     * @return array Array mapping index to corrected data
     */
    public function fixIncompleteParses(array $failedHadiths): array
    {
        if (empty($this->apiKey) || empty($failedHadiths)) {
            return [];
        }

        // Prepare the payload
        $systemInstruction = "أنت مساعد متخصص في فحص وتخريج الأحاديث النبوية ومعرفة درجاتها ورواتها. 
التزم بالقواعد التالية بصرامة:
1. أنت متصل بمحرك بحث جوجل. يجب عليك البحث عن الحديث للتحقق من درجته (صحيح، حسن، ضعيف، موضوع) إذا كانت غير مذكورة.
2. لا تخمن درجة الحديث أبدًا. يجب أن تعتمد على المصادر في بحثك.
3. إذا وجدت أسماء رواة ملتصقة ببعضها (مثل: عن مالكعننافع)، قم بفصلها إلى مصفوفة نظيفة (مثل: ['مالك', 'نافع']).
4. التنسيق المطلوب للرد هو JSON array فقط يحتوي على الكائنات بالصيغة المحددة لك، بدون أي نصوص إضافية أو علامات Markdown.";

        // Format the input batch
        $batchToProcess = [];
        foreach ($failedHadiths as $item) {
            $batchToProcess[] = [
                'index' => $item['index'],
                'raw_text' => $item['raw'],
                'extracted' => [
                    'grade' => $item['parsed']['grade'] ?? null,
                    'narrators' => $item['parsed']['narrators'] ?? [],
                ],
                'issues' => array_merge($item['errors'] ?? [], $item['warnings'] ?? []),
            ];
        }

        $promptText = "يرجى تصحيح الأحاديث التالية وإرجاع JSON Array يحتوي على التصحيحات فقط.\n";
        $promptText .= "بناء JSON لردك يجب أن يكون مصفوفة (Array) تحتوي على كائنات (Objects)، وكل كائن يحتوي على:\n";
        $promptText .= "- index: رقم الحديث الذي نرسله لك.\n";
        $promptText .= "- number: الرقم المتسلسل للحديث (إن وجد نصاً). اتركه فارغاً إن لم تجده.\n";
        $promptText .= "- grade: درجة الحديث الصحيحة. إذا لم تكن متأكدا، اجعلها فارغة.\n";
        $promptText .= "- narrators: مصفوفة بأسماء الرواة مفصولة ونظيفة.\n";
        $promptText .= "- additions: مصفوفة بأي زيادات في الحديث (خاصة الزيادات المعقدة مثل 'وما بين القوسين زيادة من...'). كل زيادة يجب أن تكون كائن يحتوي على 'source_name' (المصدر) و 'text' (نص الزيادة المستخرج من المتن بناءً على السياق).\n\n";
        $promptText .= "البيانات:\n" . json_encode($batchToProcess, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return $this->callGemini($systemInstruction, $promptText, function ($decoded) {
            $results = [];
            foreach ($decoded as $correction) {
                if (isset($correction['index'])) {
                    $correction['is_ai_corrected'] = true;
                    $results[$correction['index']] = $correction;
                }
            }
            return $results;
        }, useSearch: true);
    }

    /**
     * استدعاء Gemini API مع آلية fallback بين النماذج المتوفرة.
     *
     * @param string $systemInstruction تعليمات النظام
     * @param string $promptText نص البرومبت
     * @param callable $processResult دالة معالجة النتيجة (تستقبل المصفوفة المفكوكة)
     * @param bool $useSearch تفعيل بحث جوجل
     * @return array النتيجة المعالجة
     */
    private function callGemini(string $systemInstruction, string $promptText, callable $processResult, bool $useSearch = false): array
    {
        $payload = [
            'systemInstruction' => [
                'parts' => [
                    ['text' => $systemInstruction]
                ]
            ],
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $promptText]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.1, // Low temperature for more factual responses
            ]
        ];

        if ($useSearch) {
            $payload['tools'] = [
                ['googleSearch' => new \stdClass()]
            ];
        }

        $lastException = null;

        foreach ($this->models as $model) {
            // نسخه من الطلب مخصصة لكل نموذج
            $currentPayload = $payload;

            // عائلة Gemma أحياناً لا تدعم الـ tools
            if (str_contains($model, 'gemma')) {
                unset($currentPayload['tools']);
            }

            try {
                $response = Http::withHeaders([
                    'X-goog-api-key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])->timeout(30)->post($this->apiUrl . $model . ':generateContent', $currentPayload);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                        $jsonResult = $data['candidates'][0]['content']['parts'][0]['text'];
                        $jsonResult = trim($jsonResult);
                        
                        // تنظيف الـ Markdown
                        if (str_starts_with($jsonResult, '```json')) {
                            $jsonResult = preg_replace('/^```json\s*/', '', $jsonResult);
                            $jsonResult = preg_replace('/\s*```$/', '', $jsonResult);
                        } elseif (str_starts_with($jsonResult, '```')) {
                            $jsonResult = preg_replace('/^```\s*/', '', $jsonResult);
                            $jsonResult = preg_replace('/\s*```$/', '', $jsonResult);
                        }

                        $decoded = json_decode($jsonResult, true);
                        
                        if (is_array($decoded)) {
                            return $processResult($decoded);
                        }
                    }

                    // إذا وصلنا هنا، الاستجابة ناجحة لكن التنسيق غير مفهوم
                    $lastException = new \Exception("تعذر فهم إجابة الذكاء الاصطناعي من نموذج {$model}.");
                    continue; // جرب النموذج التالي

                } else {
                    $errorData = $response->json('error');
                    $msg = $errorData['message'] ?? 'خطأ غير معروف في الـ API';
                    
                    if ($response->status() === 429) {
                        $lastException = new \Exception("نموذج {$model} تجاوز الحد المسموح للاستخدام (Quota Exceeded).");
                    } else if ($response->status() === 404) {
                        $lastException = new \Exception("النموذج {$model} غير متوفر.");
                    } else {
                        $lastException = new \Exception("خطأ في نموذج {$model}: " . $msg);
                    }
                    Log::error("Gemini API Error ({$model}): " . $response->body());
                    continue; // جرب النموذج التالي
                }
            } catch (\Exception $e) {
                Log::error("Gemini Extraction Exception ({$model}): " . $e->getMessage());
                $lastException = new \Exception("تعذر الاتصال بالنموذج {$model}: " . $e->getMessage());
                continue; // جرب النموذج التالي
            }
        }

        // إذا استنفدنا كل النماذج وفشلت جميعها
        if ($lastException !== null) {
            throw new \Exception("فشلت جميع النماذج التي تم تجربتها. آخر محاولة: " . $lastException->getMessage());
        }

        return [];
    }
}
