<?php

namespace App\Console\Commands;

use App\Models\Hadith;
use App\Models\Source;
use App\Models\Narrator;
use App\Services\HadithParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RebuildHadithChainsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hadeeth:rebuild-chains {--limit= : Limit the number of hadiths to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild hadith chains for existing legacy hadiths using the new HadithParser logic';

    /**
     * Execute the console command.
     */
    public function handle(HadithParser $parser)
    {
        $limit = $this->option('limit');
        $query = Hadith::whereDoesntHave('chains');
        
        if ($limit) {
            $query->limit($limit);
        }
        
        $hadiths = $query->get();
        $total = $hadiths->count();
        
        if ($total === 0) {
            $this->info('No hadiths missing chains found!');
            return;
        }

        $this->info("Found {$total} hadiths to process.");
        $bar = $this->output->createProgressBar($total);
        
        // Cache narrators and sources to minimize DB queries
        $narratorsCache = Narrator::all()->keyBy('name');
        $sourcesCache = Source::all()->keyBy('code');
        $sourcesByName = Source::all()->keyBy('name');

        DB::beginTransaction();
        try {
            foreach ($hadiths as $hadith) {
                if (empty($hadith->raw_text)) {
                    $bar->advance();
                    continue;
                }

                $parsed = $parser->parse($hadith->raw_text);
                
                if (empty($parsed['chains'])) {
                    $bar->advance();
                    continue;
                }

                foreach ($parsed['chains'] as $chainData) {
                    $cSources = [];
                    foreach ($chainData['source_codes'] as $sCode) {
                        $source = $sourcesCache->get($sCode);
                        if (!$source) {
                            // Try description prefix
                            if (str_starts_with($sCode, 'DESC:')) {
                                $desc = substr($sCode, 5);
                                $source = $sourcesByName->get($desc) ?? Source::where('name', 'LIKE', "%{$desc}%")->first();
                            } else {
                                $source = Source::where('name', 'LIKE', "%{$sCode}%")->first();
                            }
                        }
                        if ($source) {
                            $cSources[] = $source;
                        }
                    }

                    $cAttach = [];
                    $pos = 1;
                    foreach ($chainData['narrators'] as $narData) {
                        $name = $narData['name'];
                        $note = $narData['note'];
                        
                        $narrator = $narratorsCache->get($name) ?? Narrator::where('name', 'LIKE', "%{$name}%")->first();
                        
                        if ($narrator) {
                            $cAttach[$narrator->id] = [
                                'position' => $pos++,
                                'transmission_note' => $note,
                            ];
                        }
                    }

                    // Create chains
                    if (!empty($cSources)) {
                        foreach ($cSources as $cS) {
                            $chain = $hadith->chains()->create([
                                'source_id' => $cS->id,
                            ]);
                            if (!empty($cAttach)) {
                                $chain->narrators()->attach($cAttach);
                            }
                        }
                    } else if (!empty($cAttach)) {
                        // Very rare: recognized narrators but completely unrecognized source
                        // We might want to skip or just attach flat. The old system attached flat.
                        // Since we are building chains, we need a source. We skip if no source.
                    }
                }
                
                $bar->advance();
            }
            DB::commit();
            $bar->finish();
            $this->newLine();
            $this->info("Successfully rebuilt chains for {$total} hadiths.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error: " . $e->getMessage());
        }
    }
}
