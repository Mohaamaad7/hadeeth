<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * أنواع الأسماء البديلة للرواة.
 */
enum AlternativeNameType: string
{
    case Misspelling = 'misspelling';  // خطأ نساخ (تشابه رسم الحروف)
    case Variation = 'variation';    // تهجئة بديلة (عبدالله ↔ عبد الله)
    case Title = 'title';        // لقب (أمير المؤمنين)
    case Kunya = 'kunya';        // كنية (أبو فلان)

    /**
     * الاسم بالعربي
     */
    public function label(): string
    {
        return match ($this) {
            self::Misspelling => 'خطأ نساخ',
            self::Variation => 'تهجئة بديلة',
            self::Title => 'لقب',
            self::Kunya => 'كنية',
        };
    }
}
