# Smart Parser Service Report

**Date:** 2025-11-21

## 1. Implemented Files
- `app/Services/HadithParser.php`
- `tests/Unit/HadithParserTest.php`

## 2. Parser Capabilities

### A. Extraction Logic (Regex Patterns)

#### Number Extraction
- **Pattern:** `/\[(\d+)\]/u`
- **Explanation:** Matches digits inside square brackets `[123]`.
- **Unicode:** The `/u` modifier ensures proper handling of mixed Arabic/Latin text.

#### Grade Extraction
- **Pattern:** `/\((صحيح|حسن|ضعيف|موضوع)\)/u`
- **Explanation:** Matches specific grade keywords inside parentheses.
- **Supported Grades:** صحيح (Sahih), حسن (Hasan), ضعيف (Weak), موضوع (Fabricated).

#### Narrator Extraction
- **Pattern:** `/عن\s+([^\[\(]+?)(?:\s*\[|\s*\(|$)/u`
- **Explanation:** 
  - Finds the last occurrence of "عن" (meaning "from/narrated by").
  - Captures text after it until a bracket `[`, parenthesis `(`, or end of string.
  - Uses non-greedy matching to avoid over-capturing.

#### Source Codes Extraction
- **Pattern:** `/\(([^\)]+)\)/u` (matches all parentheses content)
- **Logic:** 
  - Filters out grade words to isolate source codes.
  - Handles both Arabic and Latin letters.
  - Processes multi-character codes (حم, هق, hm, hk).

### B. Source Code Mapping

Dictionary of 16 source codes:
```php
'خ' => 'صحيح البخاري'
'م/m' => 'صحيح مسلم'
'د/d' => 'سنن أبي داود'
'ت/t' => 'سنن الترمذي'
'ن/n' => 'سنن النسائي'
'هـ/h' => 'سنن ابن ماجه'
'حم/hm' => 'مسند أحمد'
'هق/hk' => 'سنن البيهقي'
'ك/k' => 'مستدرك الحاكم'
```

### C. Group Expansion Logic

- **`ق`** → Expands to `['خ', 'م']` (Bukhari & Muslim - The Two Sahihs)
- **`4`** → Expands to `['د', 'ت', 'ن', 'هـ']` (The Four Sunan)
- **`3`** → Expands to `['د', 'ت', 'ن']` (The Three Sunan)

### D. Text Cleaning

The parser removes:
1. Number markers `[123]`
2. Grade markers `(صحيح)`
3. Source codes in parentheses `(ق)` or `(حم د ت)`
4. Narrator prefix "عن ..." from the end
5. Extra whitespace

## 3. Test Results

✅ **10 Tests Passed (32 Assertions)**

Verified test case:
```
Input: "إنما الأعمال بالنيات [1] (صحيح) (ق) عن عمر بن الخطاب"

Output:
- Number: 1
- Grade: صحيح
- Narrator: عمر بن الخطاب
- Sources: 2 (صحيح البخاري, صحيح مسلم)
- Clean Text: "إنما الأعمال بالنيات"
```

## 4. Usage Example

```php
$parser = new HadithParser();
$result = $parser->parse($hadithText);

// Returns:
[
    'number' => 1,
    'grade' => 'صحيح',
    'narrator' => 'عمر بن الخطاب',
    'source_codes' => ['خ', 'م'],
    'sources' => ['صحيح البخاري', 'صحيح مسلم'],
    'clean_text' => 'إنما الأعمال بالنيات'
]
```

---

**Task:** 1.4 The Smart Parser Service — Complete and verified.
