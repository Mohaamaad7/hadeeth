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
     * Parse hadith text and extract metadata.
     *
     * @param string $text
     * @return array
     */
    public function parse(string $text): array
    {
        $number = $this->extractNumber($text);
        $grade = $this->extractGrade($text);
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
            'clean_text' => $cleanText,
        ];
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
     * Extract narrator - text after the last occurrence of "عن".
     */
    private function extractNarrator(string $text): ?string
    {
        // Find the last occurrence of "عن" followed by text
        if (preg_match('/عن\s+([^\[\(]+?)(?:\s*\[|\s*\(|$)/u', $text, $matches)) {
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

        // قاعدة ذكية: تحويل "أبي" إلى "أبو" إلا إذا تبعتها "بن" (مثل: أبي بن كعب)
        // لأن "أبي بن ..." تعني غالباً الاسم "أُبيّ" وليس الكنية
        if (preg_match('/^أبي\s+(?!بن)/u', $name)) {
            $name = preg_replace('/^أبي\s+/u', 'أبو ', $name);
        }

        // تحويل "أبا" إلى "أبو" (حالة النصب)
        if (preg_match('/^أبا\s+/u', $name)) {
            $name = preg_replace('/^أبا\s+/u', 'أبو ', $name);
        }

        return $name;
    }

    /**
     * Extract source codes from parentheses (حم د ت).
     */
    private function extractSourceCodes(string $text): array
    {
        $codes = [];

        // Match parentheses containing source codes (letters/symbols, not grade words)
        if (preg_match_all('/\(([^\)]+)\)/u', $text, $matches)) {
            foreach ($matches[1] as $match) {
                // Skip if it's a grade word
                if (preg_match('/^(صحيح|حسن|ضعيف|موضوع)$/u', trim($match))) {
                    continue;
                }

                // Extract individual codes (space-separated or continuous)
                $match = trim($match);

                // Check for group codes first
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
                    } else {
                        // Check for multi-char codes (hm, hk, etc.)
                        if (mb_strlen($part, 'UTF-8') > 1 && isset($this->sourceMap[$part])) {
                            $codes[] = $part;
                        } else {
                            // Split into individual characters for single-letter codes
                            preg_match_all('/./u', $part, $chars);
                            foreach ($chars[0] as $char) {
                                if (isset($this->sourceMap[$char]) || isset($this->groupExpansion[$char])) {
                                    if (isset($this->groupExpansion[$char])) {
                                        $codes = array_merge($codes, $this->groupExpansion[$char]);
                                    } else {
                                        $codes[] = $char;
                                    }
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
     * Clean text by removing metadata (number, grade, codes, narrator prefix).
     */
    private function cleanText(string $text): string
    {
        // Remove number [123]
        $text = preg_replace('/\[\d+\]/u', '', $text);

        // Remove grade (صحيح), (حسن), etc.
        $text = preg_replace('/\((صحيح|حسن|ضعيف|موضوع)\)/u', '', $text);

        // Remove source codes in parentheses (but keep other parentheses)
        $text = preg_replace('/\([^\)]*[a-zأ-ي]+[^\)]*\)/u', '', $text);

        // Remove narrator prefix "عن ..." from the end
        $text = preg_replace('/عن\s+[^\[\(]+$/u', '', $text);

        // Clean up extra spaces
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text);
    }
}
