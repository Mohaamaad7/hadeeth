<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$text = "1951- فِي كُلِّ سَائِمَةِ إِبِلٍ فِي أَرْبَعِينَ بِنْتُ لَبُونٍ لاَ يُفَرَّقُ إِبِلٌ عَنْ حِسَابِهَا مَنْ أَعْطَاهَا مُؤْتَجراً بِهَا فَلَهُ أَجْرُهَا وَمَنْ مَنَعَهَا فَإِنَّا آخِذُوهَا وَشَطْرَ مَالِهِ عَزْمَةً مِنْ عَزَمَاتِ رَبِّنَا عَزَّ وَجَلَّ لَيْسَ لِمحَمَّدٍ وَلاَ لآلِ مُحَمَّدٍ مِنْهَا شَيْءٌ. [4265] (حسن) (حم د ن ك) عن معاوية بن قرة.";

$parser = new App\Services\HadithParser();
$result = $parser->parse($text);
echo "--- PARSE RESULT ---\n";
print_r($result);
echo "--- SOURCE MAP KEYS ---\n";
$reflection = new ReflectionClass($parser);
$property = $reflection->getProperty('sourceMap');
$property->setAccessible(true);
$map = $property->getValue($parser);
echo implode(', ', array_keys($map)) . "\n";
