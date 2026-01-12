<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hadith;
use App\Models\Source;
use App\Models\Narrator;
use App\Models\HadithChain;

class HadithChainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first hadith as example
        $hadith = Hadith::with('sources', 'narrator')->first();
        
        if (!$hadith) {
            $this->command->warn('⚠️  لا توجد أحاديث في قاعدة البيانات. قم بإضافة أحاديث أولاً.');
            return;
        }

        // Create sample narrators if not exist
        $narrators = [
            ['name' => 'الإمام البخاري', 'grade_status' => 'ثقة', 'color_code' => '#16a34a'],
            ['name' => 'عبد الله بن يوسف', 'grade_status' => 'ثقة', 'color_code' => '#16a34a'],
            ['name' => 'مالك بن أنس', 'grade_status' => 'ثقة', 'color_code' => '#16a34a'],
            ['name' => 'نافع', 'grade_status' => 'ثقة', 'color_code' => '#16a34a'],
            ['name' => 'عبد الله بن عمر', 'grade_status' => 'صحابي', 'color_code' => '#0ea5e9'],
        ];

        $createdNarrators = [];
        foreach ($narrators as $narratorData) {
            $narrator = Narrator::firstOrCreate(
                ['name' => $narratorData['name']],
                $narratorData
            );
            $createdNarrators[] = $narrator;
        }

        // Get sources
        $bukhari = Source::where('code', 'خ')->first();
        $muslim = Source::where('code', 'م')->first();

        if (!$bukhari || !$muslim) {
            $this->command->warn('⚠️  لا توجد مصادر (البخاري أو مسلم) في قاعدة البيانات.');
            return;
        }

        // Create chain for Bukhari
        $chainBukhari = HadithChain::create([
            'hadith_id' => $hadith->id,
            'source_id' => $bukhari->id,
            'description' => 'طريق الإمام البخاري في الصحيح',
        ]);

        // Attach narrators to Bukhari chain
        $chainBukhari->narrators()->attach($createdNarrators[0]->id, ['position' => 1, 'role' => 'المصنف']);
        $chainBukhari->narrators()->attach($createdNarrators[1]->id, ['position' => 2, 'role' => null]);
        $chainBukhari->narrators()->attach($createdNarrators[2]->id, ['position' => 3, 'role' => null]);
        $chainBukhari->narrators()->attach($createdNarrators[3]->id, ['position' => 4, 'role' => null]);
        $chainBukhari->narrators()->attach($createdNarrators[4]->id, ['position' => 5, 'role' => 'الصحابي']);

        // Create chain for Muslim (with common narrators)
        $chainMuslim = HadithChain::create([
            'hadith_id' => $hadith->id,
            'source_id' => $muslim->id,
            'description' => 'طريق الإمام مسلم في الصحيح',
        ]);

        // Different first narrator, but same bottom chain
        $muslimNarrator = Narrator::firstOrCreate(
            ['name' => 'الإمام مسلم'],
            ['grade_status' => 'ثقة', 'color_code' => '#16a34a']
        );

        $chainMuslim->narrators()->attach($muslimNarrator->id, ['position' => 1, 'role' => 'المصنف']);
        $chainMuslim->narrators()->attach($createdNarrators[2]->id, ['position' => 2, 'role' => null]); // مالك (نقطة الالتقاء)
        $chainMuslim->narrators()->attach($createdNarrators[3]->id, ['position' => 3, 'role' => null]); // نافع
        $chainMuslim->narrators()->attach($createdNarrators[4]->id, ['position' => 4, 'role' => 'الصحابي']); // ابن عمر

        $this->command->info('✅ تم إنشاء سلاسل إسناد تجريبية للحديث رقم ' . $hadith->id);
        $this->command->info('   - سلسلة البخاري: ' . $chainBukhari->narrators->count() . ' رواة');
        $this->command->info('   - سلسلة مسلم: ' . $chainMuslim->narrators->count() . ' رواة');
        $this->command->info('   - نقطة الالتقاء: ' . $createdNarrators[2]->name);
    }
}
