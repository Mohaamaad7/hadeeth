<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * حكم العلماء على الراوي (الجرح والتعديل).
 * يُستخدم فقط لغير الصحابة.
 */
enum ScholarJudgment: string
{
    case Thiqah = 'thiqah';               // ثقة
    case Saduq = 'saduq';                 // صدوق
    case SaduqYukhti = 'saduq_yukhti';    // صدوق يخطئ
    case Daif = 'daif';                   // ضعيف
    case Matruk = 'matruk';               // متروك
    case Kadhdhab = 'kadhdhab';           // كذاب

    /**
     * الاسم بالعربي.
     */
    public function label(): string
    {
        return match ($this) {
            self::Thiqah => 'ثقة',
            self::Saduq => 'صدوق',
            self::SaduqYukhti => 'صدوق يخطئ',
            self::Daif => 'ضعيف',
            self::Matruk => 'متروك',
            self::Kadhdhab => 'كذاب',
        };
    }

    /**
     * اللون المرتبط بحكم العلماء.
     */
    public function color(): string
    {
        return match ($this) {
            self::Thiqah => '#16a34a',       // أخضر
            self::Saduq => '#2563eb',        // أزرق
            self::SaduqYukhti => '#f59e0b',  // برتقالي
            self::Daif => '#d97706',         // أصفر غامق
            self::Matruk => '#dc2626',       // أحمر
            self::Kadhdhab => '#991b1b',     // أحمر غامق
        };
    }
}
