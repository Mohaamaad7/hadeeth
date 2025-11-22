<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\HadithParser;

class HadithParserTest extends TestCase
{
    private HadithParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new HadithParser();
    }

    /**
     * Test parsing a complete hadith text.
     */
    public function test_parse_complete_hadith(): void
    {
        $text = "إنما الأعمال بالنيات [1] (صحيح) (ق) عن عمر بن الخطاب";
        $result = $this->parser->parse($text);

        $this->assertEquals(1, $result['number']);
        $this->assertEquals('صحيح', $result['grade']);
        $this->assertEquals('عمر بن الخطاب', $result['narrator']);
        $this->assertCount(2, $result['sources']); // Bukhari & Muslim
        $this->assertContains('صحيح البخاري', $result['sources']);
        $this->assertContains('صحيح مسلم', $result['sources']);
        $this->assertEquals('إنما الأعمال بالنيات', $result['clean_text']);
    }

    /**
     * Test number extraction.
     */
    public function test_extract_number(): void
    {
        $text = "Some text [123] more text";
        $result = $this->parser->parse($text);
        $this->assertEquals(123, $result['number']);
    }

    /**
     * Test grade extraction.
     */
    public function test_extract_grade(): void
    {
        $text = "Some text (حسن) more text";
        $result = $this->parser->parse($text);
        $this->assertEquals('حسن', $result['grade']);
    }

    /**
     * Test narrator extraction.
     */
    public function test_extract_narrator(): void
    {
        $text = "Some text عن أبي هريرة";
        $result = $this->parser->parse($text);
        $this->assertEquals('أبي هريرة', $result['narrator']);
    }

    /**
     * Test group code expansion - ق (Bukhari & Muslim).
     */
    public function test_group_expansion_q(): void
    {
        $text = "Some text (ق)";
        $result = $this->parser->parse($text);
        $this->assertCount(2, $result['sources']);
        $this->assertContains('صحيح البخاري', $result['sources']);
        $this->assertContains('صحيح مسلم', $result['sources']);
    }

    /**
     * Test group code expansion - 4 (Four Sunan).
     */
    public function test_group_expansion_4(): void
    {
        $text = "Some text (4)";
        $result = $this->parser->parse($text);
        $this->assertCount(4, $result['sources']);
        $this->assertContains('سنن أبي داود', $result['sources']);
        $this->assertContains('سنن الترمذي', $result['sources']);
        $this->assertContains('سنن النسائي', $result['sources']);
        $this->assertContains('سنن ابن ماجه', $result['sources']);
    }

    /**
     * Test group code expansion - 3 (Three Sunan).
     */
    public function test_group_expansion_3(): void
    {
        $text = "Some text (3)";
        $result = $this->parser->parse($text);
        $this->assertCount(3, $result['sources']);
        $this->assertContains('سنن أبي داود', $result['sources']);
        $this->assertContains('سنن الترمذي', $result['sources']);
        $this->assertContains('سنن النسائي', $result['sources']);
    }

    /**
     * Test individual source codes.
     */
    public function test_individual_source_codes(): void
    {
        $text = "Some text (حم د ت)";
        $result = $this->parser->parse($text);
        $this->assertCount(3, $result['sources']);
        $this->assertContains('مسند أحمد', $result['sources']);
        $this->assertContains('سنن أبي داود', $result['sources']);
        $this->assertContains('سنن الترمذي', $result['sources']);
    }

    /**
     * Test clean text extraction.
     */
    public function test_clean_text(): void
    {
        $text = "إنما الأعمال بالنيات [1] (صحيح) (ق) عن عمر بن الخطاب";
        $result = $this->parser->parse($text);
        $this->assertEquals('إنما الأعمال بالنيات', $result['clean_text']);
    }

    /**
     * Test parsing with missing elements.
     */
    public function test_parse_with_missing_elements(): void
    {
        $text = "إنما الأعمال بالنيات";
        $result = $this->parser->parse($text);
        $this->assertNull($result['number']);
        $this->assertNull($result['grade']);
        $this->assertNull($result['narrator']);
        $this->assertEmpty($result['sources']);
        $this->assertEquals('إنما الأعمال بالنيات', $result['clean_text']);
    }
}
