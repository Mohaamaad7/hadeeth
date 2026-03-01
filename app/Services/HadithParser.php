<?php
declare(strict_types=1);

namespace App\Services;

class HadithParser
{
    /**
     * Source code mapping dictionary.
     */
    private array $sourceMap = [
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
        $narrator = $this->extractNarrator($text);
        $sourceCodes = $this->extractSourceCodes($text);
        $sources = $this->decodeSources($sourceCodes);
        $cleanText = $this->cleanText($text);

        return [
            'number' => $number,
            'grade' => $grade,
            'narrator' => $narrator,
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
     * Extract narrator - text after "عن" in the metadata section.
     * Stops at addition keywords like زاد, ولفظ, etc.
     */
    private function extractNarrator(string $text): ?string
    {
        // Build a regex-safe alternation of addition keywords
        $additionPattern = implode('|', array_map(fn($k) => preg_quote($k, '/'), $this->additionKeywords));

        // Look for عن after source codes section: (codes) عن NAME
        // Stop at: [ or ( or addition keywords or end-of-string or period/dot
        $pattern = '/عن\s+([^\[\(]+?)(?:\s*\[|\s*\(|\s+(?:' . $additionPattern . ')\s|\s*\.\s*$|$)/u';

        if (preg_match($pattern, $text, $matches)) {
            return $this->normalizeNarrator(trim($matches[1]));
        }
        return null;
    }

    /**
     * Normalize narrator name (handle Arabic grammar: Abi/Aba -> Abu).
     */
    private function normalizeNarrator(string $name): string
    {
        $name = trim($name);

        // إزالة النقطة من آخر الاسم إذا وجدت
        $name = rtrim($name, '.');

        // قاعدة ذكية: تحويل "أبي" إلى "أبو" إلا إذا تبعتها "بن" (مثل: أبي بن كعب)
        if (preg_match('/^أبي\s+(?!بن)/u', $name)) {
            $name = preg_replace('/^أبي\s+/u', 'أبو ', $name);
        }

        // تحويل "أبا" إلى "أبو" (حالة النصب)
        if (preg_match('/^أبا\s+/u', $name)) {
            $name = preg_replace('/^أبا\s+/u', 'أبو ', $name);
        }

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

        // Match parentheses containing source codes (letters/symbols, not grade words)
        if (preg_match_all('/\(([^\)]+)\)/u', $text, $matches)) {
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

                // Check for group codes first (entire content)
                if (isset($this->groupExpansion[$match])) {
                    $codes = array_merge($codes, $this->groupExpansion[$match]);
                    continue;
                }

                // Split by spaces or extract individual Arabic/Latin letters
                $parts = preg_split('/\s+/u', $match);
                foreach ($parts as $part) {
                    $part = trim($part);
                    if (empty($part))
                        continue;

                    // Check if it's a group code
                    if (isset($this->groupExpansion[$part])) {
                        $codes = array_merge($codes, $this->groupExpansion[$part]);
                    } elseif (isset($this->sourceMap[$part])) {
                        // Check for multi-char codes (مالك, حم, حب, etc.)
                        $codes[] = $part;
                    } else {
                        // Only split single-char codes if the part is short enough (max 3 chars)
                        // This prevents splitting words like "يعني" into individual chars
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
                    }
                }
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

        // If content has words longer than 4 characters that aren't in our source map
        $parts = preg_split('/\s+/u', $content);
        foreach ($parts as $part) {
            $part = trim($part);
            if (mb_strlen($part, 'UTF-8') > 4 && !isset($this->sourceMap[$part])) {
                return true;
            }
        }

        return false;
    }


    /**
     * Decode source codes to source names.
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
     */
    private function decodeParenthesisContent(string $content): array
    {
        $codes = [];
        $content = trim($content);

        // Check for group codes first (entire content)
        if (isset($this->groupExpansion[$content])) {
            return $this->groupExpansion[$content];
        }

        // Split by spaces
        $parts = preg_split('/\s+/u', $content);
        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part))
                continue;

            if (isset($this->groupExpansion[$part])) {
                $codes = array_merge($codes, $this->groupExpansion[$part]);
            } elseif (isset($this->sourceMap[$part])) {
                $codes[] = $part;
            } else {
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
            }
        }

        return $codes;
    }

    /**
     * Clean text by removing metadata (number, grade, codes, narrator, additions).
     */
    private function cleanText(string $text): string
    {
        // Remove number [123]
        $text = preg_replace('/\[\d+\]/u', '', $text);

        // Remove grade (صحيح), (حسن), etc.
        $text = preg_replace('/\((صحيح|حسن|ضعيف|موضوع)\)/u', '', $text);

        // Remove source codes in parentheses (but keep explanatory ones)
        $text = preg_replace_callback('/\([^\)]+\)/u', function ($match) {
            $content = trim($match[0], '()');
            // Keep explanatory parentheses like (يعني الوحي)
            if ($this->isExplanatoryParenthesis($content)) {
                return $match[0];
            }
            // Remove grade words
            if (preg_match('/^(صحيح|حسن|ضعيف|موضوع)$/u', $content)) {
                return '';
            }
            return '';
        }, $text);

        // Build addition keywords alternation
        $additionPattern = implode('|', array_map(fn($k) => preg_quote($k, '/'), $this->additionKeywords));

        // Remove the additions section entirely: زاد (طب) في آخره: text
        $text = preg_replace('/\s*(?:' . $additionPattern . ')\s*\([^\)]+\).*$/u', '', $text);

        // Remove narrator prefix "عن ..." from the end (after source codes)
        $text = preg_replace('/\s*عن\s+[^\[\(]+$/u', '', $text);

        // Clean up extra spaces
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text);
    }
}
