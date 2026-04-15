<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Hadith;
use App\Services\AutoTaggerService;

class GenerateHadithTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hadeeth:generate-tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'مسح جميع الأحاديث في قاعدة البيانات وربطها بالمواضيع المناسبة تلقائياً';

    /**
     * Execute the console command.
     */
    public function handle(AutoTaggerService $tagger)
    {
        $this->info("بدأ عملية تحليل وتوليد المواضيع (Tags) لجميع الأحاديث آلياً...");
        
        $total = Hadith::count();
        
        if ($total === 0) {
            $this->warn('لا يوجد أحاديث في قاعدة البيانات!');
            return;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        // مسح قاعدة الوسوم بالكامل للبدء على نظافة (بدون التأثير على الـ foreign keys)
        \Illuminate\Support\Facades\DB::table('taggables')->delete();
        \Illuminate\Support\Facades\DB::table('tags')->delete();

        // استخدام Chunk لعدم استهلاك رامات السيرفر عند التعامل مع آلاف الأحاديث
        Hadith::chunk(200, function ($hadiths) use ($tagger, $bar) {
            foreach ($hadiths as $hadith) {
                // تمرير الحديث للمحرك ليقوم بتحليله ووضع الوسوم
                $tagger->tagHadith($hadith);
                $bar->advance();
            }
        });

        $bar->finish();
        
        $this->newLine(2);
        $this->info("تم الانتهاء بنجاح! تم فحص وتوسيم {$total} حديث باحترافية.");
    }
}
