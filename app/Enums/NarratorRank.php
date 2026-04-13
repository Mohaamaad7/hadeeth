<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * رتبة الراوي في طبقات الرجال.
 */
enum NarratorRank: string
{
    case Sahabi = 'sahabi';           // صحابي
    case Sahabiyyah = 'sahabiyyah';   // صحابية
    case Tabii = 'tabii';            // تابعي
    case Rawi = 'rawi';              // راوي

    /**
     * الاسم بالعربي.
     */
    public function label(): string
    {
        return match ($this) {
            self::Sahabi => 'صحابي',
            self::Sahabiyyah => 'صحابية',
            self::Tabii => 'تابعي',
            self::Rawi => 'راوي',
        };
    }

    /**
     * هل هذا الراوي صحابي أو صحابية؟
     */
    public function isCompanion(): bool
    {
        return in_array($this, [self::Sahabi, self::Sahabiyyah]);
    }

    /**
     * اللون المرتبط بالرتبة.
     */
    public function color(): string
    {
        return match ($this) {
            self::Sahabi => '#16a34a',      // أخضر
            self::Sahabiyyah => '#059669',   // أخضر زمردي
            self::Tabii => '#2563eb',        // أزرق
            self::Rawi => '#6b7280',         // رمادي
        };
    }

    /**
     * هل يحتاج حكم العلماء؟
     * الصحابة عدول لا يحتاجون حكم.
     */
    public function needsJudgment(): bool
    {
        return !$this->isCompanion();
    }
}
