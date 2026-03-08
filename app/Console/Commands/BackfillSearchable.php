<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Hadith;
use App\Traits\HasDiacriticStripper;
use Illuminate\Console\Command;

class BackfillSearchable extends Command
{
    use HasDiacriticStripper;

    protected $signature = 'hadiths:backfill-searchable';
    protected $description = 'Backfill content_searchable for all hadiths that have it empty';

    public function handle(): int
    {
        $count = Hadith::whereNull('content_searchable')
            ->orWhere('content_searchable', '')
            ->count();

        $this->info("Found {$count} hadiths with empty content_searchable.");

        if ($count === 0) {
            $this->info('Nothing to do!');
            return 0;
        }

        $bar = $this->output->createProgressBar($count);

        Hadith::whereNull('content_searchable')
            ->orWhere('content_searchable', '')
            ->chunkById(500, function ($hadiths) use ($bar) {
                foreach ($hadiths as $hadith) {
                    if (!empty($hadith->content)) {
                        $hadith->content_searchable = $this->stripDiacritics($hadith->content);
                        $hadith->saveQuietly(); // Skip observer to avoid loops
                    }
                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine();
        $this->info('Done! All content_searchable fields have been populated.');

        return 0;
    }
}
