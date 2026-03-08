<?php
declare(strict_types=1);

namespace App\Services;

class HadithParser
{
    /**
     * Source code mapping dictionary (fallback + merged with DB).
     */
    private array $sourceMap;

    /**
     * Initialize with sources from DB merged with hardcoded fallback.
     */
    public function __construct()
    {
        // Hardcoded fallback map
        $fallback = [
            // المصادر الستة
            'خ' => 'صحيح البخاري',
            'م' => 'صحيح مسلم',
            'د' => 'سنن أبي داود',
            'ت' => 'سنن الترمذي',
            'ن' => 'سنن النسائي',
            'هـ' => 'سنن ابن ماجه',

            // الموطأ
            'مالك' => 'موطأ الإمام مالك',
            'ط' => 'موطأ الإمام مالك',

            // المسانيد
            'حم' => 'مسند أحمد',
            'عم' => 'زوائد عبد الله بن أحمد',
            'ع' => 'مسند أبي يعلى',

            // الحاكم
            'ك' => 'المستدرك للحاكم',

            // البخاري (كتب أخرى)
            'خد' => 'الأدب المفرد للبخاري',
            'تخ' => 'التاريخ الكبير للبخاري',

            // ابن حبان والطبراني
            'حب' => 'صحيح ابن حبان',
            'طب' => 'المعجم الكبير للطبراني',
            'طس' => 'المعجم الأوسط للطبراني',
            'طص' => 'المعجم الصغير للطبراني',

            // السنن والمصنفات
            'ص' => 'سنن سعيد بن منصور',
            'ش' => 'مصنف ابن أبي شيبة',
            'عب' => 'المصنف لعبد الرزاق',
            'قط' => 'سنن الدارقطني',
            'هق' => 'السنن الكبرى للبيهقي',
            'هب' => 'شعب الإيمان للبيهقي',

            // كتب الرجال
            'عد' => 'الكامل لابن عدي',
            'عق' => 'الضعفاء للعقيلي',

            // كتب متنوعة
            'فر' => 'مسند الفردوس للديلمي',
            'حل' => 'حلية الأولياء لأبي نعيم',
            'خط' => 'تاريخ بغداد للخطيب',
        ];

        // Merge with sources from database (DB takes priority)
        try {
            $dbSources = \App\Models\Source::pluck('name', 'code')->toArray();
            $this->sourceMap = array_merge($fallback, $dbSources);
        } catch (\Throwable) {
            // If DB is not available (e.g., during migrations), use fallback only
            $this->sourceMap = $fallback;
        }
    }

    /**
     * Group expansion codes.
     */
    private array $groupExpansion = [
        'ق' => ['خ', 'م'],           // البخاري ومسلم
        '4' => ['د', 'ت', 'ن', 'هـ'], // الأربعة
        '3' => ['د', 'ت', 'ن'],       // الثلاثة (بدون ابن ماجه)
    ];

    /**
     * Keywords that indicate source-specific additions (زيادات).
     * Example: عن عائشة زاد (طب) في آخره: وهو أهونه علي.
     */
    private array $additionKeywords = ['زاد', 'وزاد', 'ولفظ', 'ورواه', 'وعند', 'وفي رواية'];

    /**
     * Normalize source code by collapsing multiple tatweels (ـ) into one.
     * Example: هــ → هـ (fixes ابن ماجه matching)
     */
    private function normalizeSourceCode(string $code): string
    {
        // Collapse multiple consecutive tatweel (U+0640) into one
        return preg_replace('/\x{0640}+/u', "\u{0640}", $code);
    }

    /**
     * Parse hadith text and extract metadata.
     *
     * @param string $text
     * @return array
     */
    public function parse(string $text): array
    {
        $number = $this->extractNumber($text);
        $grade = $this->extractGrade($text);
        $additions = $this->extractAdditions($text);
        $narrators = $this->extractNarrators($text);
        $sourceCodes = $this->extractSourceCodes($text);
        $sources = $this->decodeSources($sourceCodes);
        $cleanText = $this->cleanText($text);

        return [
            'number' => $number,
            'grade' => $grade,
            'narrators' => $narrators, // Now returns an array
            'source_codes' => $sourceCodes,
            'sources' => $sources,
            'additions' => $additions,
            'clean_text' => $cleanText,
        ];
    }

    /**
     * Parse multiple hadiths from a block of text.
     * Each hadith starts with a sequence number followed by a dash: "3- ..."
     *
     * @param string $bulkText
     * @return array<int, array{raw: string, parsed: array}>
     */
    public function parseMultiple(string $bulkText): array
    {
        $results = [];

        // Normalize line endings
        $bulkText = str_replace(["\r\n", "\r"], "\n", $bulkText);

        // Split by the pattern: number followed by dash at the start of a line
        // Pattern: start-of-line, optional whitespace, digits, dash, space
        $segments = preg_split('/(?=^\s*\d+\s*[-–]\s)/um', $bulkText, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($segments as $segment) {
            $segment = trim($segment);
            if (empty($segment))
                continue;

            // Remove the leading sequence number (e.g., "3- " or "3 - ")
            $rawHadith = preg_replace('/^\d+\s*[-–]\s*/u', '', $segment);
            $rawHadith = trim($rawHadith);

            if (empty($rawHadith))
                continue;

            // Parse each individual hadith
            $parsed = $this->parse($rawHadith);

            $results[] = [
                'raw' => $rawHadith,
                'parsed' => $parsed,
            ];
        }

        return $results;
    }

    /**
     * Extract hadith number from square brackets [123].
     */
    private function extractNumber(string $text): ?int
    {
        if (preg_match('/\[(\d+)\]/u', $text, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    /**
     * Extract grade from parentheses (صحيح) or (حسن).
     */
    private function extractGrade(string $text): ?string
    {
        // Match Arabic words in parentheses (excluding source codes)
        if (preg_match('/\((صحيح|حسن|ضعيف|موضوع)\)/u', $text, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Extract narrators - text after "عن" in the metadata section ONLY.
     * Returns an array of narrators.
     * 
     * المشكلة: كلمة "عن" تظهر في متن الحديث (مثل: النهي عن المنكر)
     * الحل: الراوي يأتي دائماً بعد [رقم_الصفحة] (الحكم) (المصادر) عن الراوي
     *       لذلك نبحث عن "عن" فقط في القسم الذي يلي [رقم_الصفحة]
     */
    private function extractNarrators(string $text): array
    {
        // 1. أوجد قسم البيانات الوصفية (بعد [رقم الصفحة])
        // هذا يفصل متن الحديث عن معلومات التخريج
        $metadataSection = $text;
        if (preg_match('/\[\d+\]/u', $text, $matches, PREG_OFFSET_CAPTURE)) {
            $metadataSection = substr($text, $matches[0][1]);
        }

        $narratorsStr = null;

        // 2. في قسم البيانات الوصفية، ابحث عن "عن" بعد آخر قوس ) (أي بعد المصادر)
        $lastParenPos = mb_strrpos($metadataSection, ')');
        if ($lastParenPos !== false) {
            $afterLastParen = mb_substr($metadataSection, $lastParenPos + 1);

            // ابحث عن "عن NARRATOR" — توقف عند: نقطة نهائية أو قوس جديد أو كلمة زيادة
            $additionPattern = implode('|', array_map(fn($k) => preg_quote($k, '/'), $this->additionKeywords));

            $pattern = '/عن\s+(.+?)(?:\s*\(|\s+(?:' . $additionPattern . ')\s|\s*\.\s*$|$)/u';

            if (preg_match($pattern, $afterLastParen, $narratorMatch)) {
                $narratorsStr = trim($narratorMatch[1]);
            }
        }

        // 3. Fallback: ابحث عن "عن" في كل قسم البيانات الوصفية (بعد [رقم])
        if (!$narratorsStr) {
            $additionPattern = implode('|', array_map(fn($k) => preg_quote($k, '/'), $this->additionKeywords));
            $pattern = '/عن\s+([^\[\(]+?)(?:\s*\[|\s*\(|\s+(?:' . $additionPattern . ')\s|\s*\.\s*$|$)/u';

            if (preg_match($pattern, $metadataSection, $matches)) {
                $narratorsStr = trim($matches[1]);
            }
        }

        if (!$narratorsStr)
            return [];

        // تقسيم النص إذا كان يحتوي على أكثر من راوي ("عن فلان وعن فلان" أو "عن فلان وفلان")
        $narrators = [];
        // تقسيم بكلمة "وعن" أولاً
        $parts = preg_split('/(?:\s+وعن\s+|\s+و\s+)/u', $narratorsStr);
        foreach ($parts as $part) {
            $cleaned = $this->normalizeNarrator($part);
            if (!empty($cleaned)) {
                $narrators[] = $cleaned;
            }
        }

        return $narrators;
    }

    /**
     * Normalize narrator name — basic cleanup only.
     * لا نحول أبي→أبو هنا — findNarrator يتولى البحث الثنائي.
     */
    private function normalizeNarrator(string $name): string
    {
        $name = trim($name);

        // إزالة النقطة من آخر الاسم إذا وجدت
        $name = rtrim($name, '.');

        return trim($name);
    }

    /**
     * Extract source-specific additions (زيادات).
     * Pattern: زاد (source_code) في آخره: text
     * Pattern: زاد (source_code): text
     * Pattern: ولفظ (source_code): text
     *
     * @return array<array{source_code: string, source_name: string, text: string}>
     */
    private function extractAdditions(string $text): array
    {
        $additions = [];

        // Build keyword alternation
        $keywords = implode('|', array_map(fn($k) => preg_quote($k, '/'), $this->additionKeywords));

        // Pattern: keyword (source_code) optional-context: addition_text
        // Examples:
        //   زاد (طب) في آخره: وهو أهونه علي.
        //   زاد (حم): بالمعروف.
        //   ولفظ (م): كذا وكذا.
        $pattern = '/(?:' . $keywords . ')\s*\(([^\)]+)\)\s*(?:[^:]*:\s*)?(.+?)(?=\s*(?:' . $keywords . ')\s*\(|\s*$)/u';

        if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $codeRaw = trim($match[1]);
                $additionText = trim($match[2]);

                // Remove trailing period
                $additionText = rtrim($additionText, '.');

                if (empty($additionText)) {
                    continue;
                }

                // Decode the source code
                $sourceCodes = $this->decodeParenthesisContent($codeRaw);
                $sourceNames = $this->decodeSources($sourceCodes);

                $additions[] = [
                    'source_code' => $codeRaw,
                    'source_name' => !empty($sourceNames) ? implode('، ', $sourceNames) : $codeRaw,
                    'text' => $additionText,
                ];
            }
        }

        return $additions;
    }

    /**
     * Extract source codes from parentheses (حم د ت).
     * Intelligently skips explanatory parentheses like (يعني الوحي).
     */
    private function extractSourceCodes(string $text): array
    {
        $codes = [];

        // 1. إيجاد قسم البيانات الوصفية (بعد [رقم الصفحة]) لتجنب أقواس الآيات داخل المتن
        $metadataSection = $text;
        if (preg_match('/\[\d+\]/u', $text, $matches, PREG_OFFSET_CAPTURE)) {
            $metadataSection = substr($text, $matches[0][1]);
        }

        // Match parentheses containing source codes in metadata section only
        if (preg_match_all('/\(([^\)]+)\)/u', $metadataSection, $matches)) {
            foreach ($matches[1] as $match) {
                $match = trim($match);

                // Skip if it's a grade word
                if (preg_match('/^(صحيح|حسن|ضعيف|موضوع)$/u', $match)) {
                    continue;
                }

                // Skip explanatory parentheses (contain long words > 4 chars that aren't source codes)
                if ($this->isExplanatoryParenthesis($match)) {
                    continue;
                }

                $parenthesisCodes = $this->decodeParenthesisContent($match);
                $codes = array_merge($codes, $parenthesisCodes);
            }
        }

        return array_unique($codes);
    }

    /**
     * Check if parenthesis content is explanatory text (not source codes).
     * Examples: (يعني الوحي), (أي الملائكة), etc.
     */
    private function isExplanatoryParenthesis(string $content): bool
    {
        // Common explanatory words that indicate this is not source codes
        $explanatoryPatterns = [
            '/يعني/u',
            '/أي\s/u',
            '/أى\s/u',
            '/يريد/u',
            '/قال/u',
            '/قوله/u',
        ];

        foreach ($explanatoryPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        // Check if ANY part is recognized as a source code or group
        // By running the decoding function and checking if it found anything
        $codes = $this->decodeParenthesisContent($content);
        return empty($codes);
    }


    /**
     * Decode sources to original names
     */
    private function decodeSources(array $codes): array
    {
        $sources = [];
        foreach ($codes as $code) {
            if (isset($this->sourceMap[$code])) {
                $sources[] = $this->sourceMap[$code];
            }
        }
        return array_unique($sources);
    }

    /**
     * Decode content inside parentheses into source codes.
     * Shared helper used by both extractSourceCodes and extractAdditions.
     * Uses Greedy Matching to find grouped words like "ابن سعد" before splitting.
     */
    private function decodeParenthesisContent(string $content): array
    {
        $codes = [];
        $content = $this->normalizeSourceCode(trim($content));

        // Check for group codes first (entire content)
        if (isset($this->groupExpansion[$content])) {
            return $this->groupExpansion[$content];
        }

        // Split by spaces
        $parts = preg_split('/\s+/u', $content, -1, PREG_SPLIT_NO_EMPTY);
        $count = count($parts);

        for ($i = 0; $i < $count; ) {
            $matched = false;

            // Try to match longest sequence of words starting from $i
            for ($len = $count - $i; $len > 0; $len--) {
                $phrase = implode(' ', array_slice($parts, $i, $len));

                if (isset($this->groupExpansion[$phrase])) {
                    $codes = array_merge($codes, $this->groupExpansion[$phrase]);
                    $i += $len;
                    $matched = true;
                    break;
                } elseif (isset($this->sourceMap[$phrase])) {
                    $codes[] = $phrase;
                    $i += $len;
                    $matched = true;
                    break;
                }
            }

            if (!$matched) {
                // No multi-word or single-word match found for parts[$i]
                $part = $parts[$i];

                // Fallback: split short unknown parts into letters
                if (mb_strlen($part, 'UTF-8') <= 3) {
                    preg_match_all('/./u', $part, $chars);
                    foreach ($chars[0] as $char) {
                        if (isset($this->groupExpansion[$char])) {
                            $codes = array_merge($codes, $this->groupExpansion[$char]);
                        } elseif (isset($this->sourceMap[$char])) {
                            $codes[] = $char;
                        }
                    }
                }

                $i++;
            }
        }

        return $codes;
    }

    /**
     * Clean text by removing metadata (number, grade, codes, narrator, additions).
     */
    private function cleanText(string $text): string
    {
        // أوجد بداية قسم البيانات الوصفية [رقم الصفحة]
        // كل شيء من [رقم] وحتى نهاية النص هو metadata ويُحذف
        $cleanedText = $text;

        if (preg_match('/\[\d+\]/u', $text, $matches, PREG_OFFSET_CAPTURE)) {
            // احتفظ فقط بمتن الحديث (قبل [رقم الصفحة])
            $cleanedText = substr($text, 0, $matches[0][1]);
        } else {
            // Fallback: لا يوجد [رقم] — حذف يدوي
            // Remove number [123]
            $cleanedText = preg_replace('/\[\d+\]/u', '', $cleanedText);

            // Remove grade (صحيح), (حسن), etc.
            $cleanedText = preg_replace('/\((صحيح|حسن|ضعيف|موضوع)\)/u', '', $cleanedText);

            // Remove source codes in parentheses (but keep explanatory ones)
            $cleanedText = preg_replace_callback('/\([^\)]+\)/u', function ($match) {
                $content = trim($match[0], '()');
                if ($this->isExplanatoryParenthesis($content)) {
                    return $match[0];
                }
                return '';
            }, $cleanedText);

            // Build addition keywords alternation
            $additionPattern = implode('|', array_map(fn($k) => preg_quote($k, '/'), $this->additionKeywords));

            // Remove the additions section
            $cleanedText = preg_replace('/\s*(?:' . $additionPattern . ')\s*\([^\)]+\).*$/u', '', $cleanedText);

            // Remove narrator prefix "عن ..." from the end
            $cleanedText = preg_replace('/\s*عن\s+[^\[\(]+$/u', '', $cleanedText);
        }

        // Remove leading hadith number: "4934-" or "10-"
        $cleanedText = preg_replace('/^\d+[\-\–]\s*/u', '', $cleanedText);

        // Clean up extra spaces
        $cleanedText = preg_replace('/\s+/u', ' ', $cleanedText);

        return trim($cleanedText, " \t\n\r\0\x0B.");
    }
}
