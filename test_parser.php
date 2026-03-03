<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\HadithParser;
use App\Http\Controllers\Dashboard\HadithController;

$parser = app(HadithParser::class);

$text = <<<'EOT'
12- أتانِي جِبْرِيلُ فقال بَشِّرْ أُمَّتَكَ أنَّهُ مَنْ ماتَ لا يُشْرِكُ بالله شَيْئاً دَخَلَ الجَنَّة قُلْتُ يا جِبْرِيلُ وإنْ سَرَقَ وإنْ زَنى قال نَعَمْ قُلْتُ وإنْ سَرَقَ وإنْ زَنى قال نَعَمْ قُلْتُ وإنْ سَرَقَ وإنْ زَنى قال نعم وإنْ شَرِبَ الخَمْرَ. [66] (صحيح) (حم ت ن حب) عن أبي ذر.
13- أحبُّ الأعمال إلى الله إِيمَانٌ بالله ثُمَّ صِلَةُ الرَّحِمِ ثُمَّ الأمْرُ بالمَعْرُوفِ والنَّهْيُ عن المُنْكَرِ وأبْغَضُ الأعْمالِ إلى الله الإشْرَاكُ بالله ثُمَّ قَطيعَةُ الرَّحمِ. [166] (حسن) (ع) عن رجل من خثعم.
14- أخرُجْ فَنادِ في النَّاسِ مَنْ شَهِدَ أنْ لا إلهَ إلاَّ الله وَجَبَتْ لهُ الجَنَّةُ. [229] (صحيح) (ع) عن أبي بكرة.
15- أذِّنْ في النَّاسِ أنَّهُ مَنْ شَهِدَ أنْ لا إله إلاَّ الله وَحْدَهُ لا شَرِيكَ لَهُ مُخْلِصاً دَخَلَ الجَنَّةَ. [851] (صحيح) (البزار ع) عن عمر.
16- اذْهَبْ بِنَعْلَيَّ هاتَيْنِ فَمَنْ لَقِيتَ مِنْ وَراءِ هذا الحائِطِ يَشْهَدُ أنْ لا إله إلاَّ الله مُسْتَيقِناً بِها قَلْبُهُ فَبَشِّرْهُ بالجَنَّةِ. [857] (صحيح) (م) عن أبي هريرة.
17- أسعدُ النّاسِ بِشَفاعَتِي يَوْمَ القِيامَةِ مَنْ قالَ لا إله إلاّ اللَّهُ خالِصاً مُخْلِصاً منْ قَلْبِهِ. [967] (صحيح) (خ) عن أبي هريرة.
EOT;

echo "=== PARSING RESULTS ===\n\n";

$results = $parser->parseMultiple($text);

foreach ($results as $i => $item) {
    $p = $item['parsed'];
    echo "--- Hadith #" . ($i + 1) . " ---\n";
    echo "  Number: " . ($p['number'] ?? 'MISSING') . "\n";
    echo "  Grade:  " . ($p['grade'] ?? 'MISSING') . "\n";
    echo "  Narrator: " . ($p['narrator'] ?? 'MISSING') . "\n";
    echo "  Sources: " . implode(', ', $p['sources'] ?? []) . "\n";
    echo "  Source codes: " . implode(', ', $p['source_codes'] ?? []) . "\n";
    echo "  Text (50 chars): " . mb_substr($p['clean_text'] ?? '', 0, 50) . "...\n";

    if (!empty($p['narrator'])) {
        $controller = app(HadithController::class);
        $method = new ReflectionMethod($controller, 'findNarrator');
        $method->setAccessible(true);
        $narrator = $method->invoke($controller, $p['narrator']);
        if ($narrator) {
            echo "  >> DB FOUND: {$narrator->name} (ID: {$narrator->id})\n";
        } else {
            echo "  >> DB NOT FOUND!\n";
        }
    }
    echo "\n";
}
