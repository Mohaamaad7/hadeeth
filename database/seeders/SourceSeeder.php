<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    /**
     * Seed standard hadith sources with their codes.
     */
    public function run(): void
    {
        $sources = [
            ['name' => 'صحيح البخاري', 'code' => 'خ', 'type' => 'صحيح'],
            ['name' => 'صحيح مسلم', 'code' => 'م', 'type' => 'صحيح'],
            ['name' => 'سنن أبي داود', 'code' => 'د', 'type' => 'سنن'],
            ['name' => 'سنن الترمذي', 'code' => 'ت', 'type' => 'سنن'],
            ['name' => 'سنن النسائي', 'code' => 'ن', 'type' => 'سنن'],
            ['name' => 'سنن ابن ماجه', 'code' => 'هـ', 'type' => 'سنن'],
            ['name' => 'مسند أحمد', 'code' => 'حم', 'type' => 'مسند'],
            ['name' => 'سنن البيهقي', 'code' => 'هق', 'type' => 'سنن'],
            ['name' => 'مستدرك الحاكم', 'code' => 'ك', 'type' => 'مستدرك'],
        ];

        foreach ($sources as $source) {
            Source::firstOrCreate(
                ['code' => $source['code']],
                $source
            );
        }
    }
}
