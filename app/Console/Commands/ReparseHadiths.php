<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Hadith;
use App\Models\Source;
use App\Services\HadithParser;
use Illuminate\Console\Command;

class ReparseHadiths extends Command
{
    protected $signature = 'hadiths:reparse {--dry-run : Show what would change without saving}';
    protected $description = 'Re-parse all hadiths raw_text to re-link sources, narrators, and grades';

    public function handle(HadithParser $parser): int
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('🔍 وضع المعاينة — لن يتم حفظ أي تغييرات');
        }

        $hadiths = Hadith::whereNotNull('raw_text')
            ->where('raw_text', '!=', '')
            ->with(['sources', 'narrator'])
            ->get();

        $this->info("📚 عدد الأحاديث المراد تحليلها: {$hadiths->count()}");
        $bar = $this->output->createProgressBar($hadiths->count());

        $updated = 0;
        $newSourcesLinked = 0;
        $details = [];

        foreach ($hadiths as $hadith) {
            $parsed = $parser->parse($hadith->raw_text);

            // Find source IDs from parsed source names
            $sourceIds = [];
            foreach ($parsed['sources'] as $sourceName) {
                $source = Source::where('name', $sourceName)->first();
                if ($source) {
                    $sourceIds[] = $source->id;
                }
            }

            // Also try matching by code directly
            foreach ($parsed['source_codes'] as $code) {
                $source = Source::where('code', $code)->first();
                if ($source && !in_array($source->id, $sourceIds)) {
                    $sourceIds[] = $source->id;
                }
            }

            // Check what's new
            $currentSourceIds = $hadith->sources->pluck('id')->toArray();
            $newIds = array_diff($sourceIds, $currentSourceIds);

            if (!empty($newIds) || count($sourceIds) !== count($currentSourceIds)) {
                $hadithNum = $hadith->number_in_book;
                $newSourceNames = Source::whereIn('id', $newIds)->pluck('name')->toArray();

                $details[] = [
                    'حديث #' . $hadithNum,
                    count($currentSourceIds) . ' → ' . count($sourceIds),
                    implode('، ', $newSourceNames) ?: '-',
                ];

                if (!$isDryRun) {
                    $hadith->sources()->sync($sourceIds);
                    $updated++;
                    $newSourcesLinked += count($newIds);
                } else {
                    $updated++;
                    $newSourcesLinked += count($newIds);
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if (!empty($details)) {
            $this->table(['الحديث', 'المصادر (قبل → بعد)', 'مصادر جديدة'], $details);
        }

        $label = $isDryRun ? 'سيتم تحديث' : 'تم تحديث';
        $this->info("✅ {$label} {$updated} حديث — {$newSourcesLinked} ربط مصدر جديد");

        if ($isDryRun && $updated > 0) {
            $this->warn('⚠️  شغّل الأمر بدون --dry-run لتطبيق التغييرات');
        }

        return Command::SUCCESS;
    }
}
