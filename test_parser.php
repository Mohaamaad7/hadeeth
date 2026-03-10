<?php
require 'vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$parser = app(\App\Services\HadithParser::class);
$text = "240- إنّ الشَّيْطانَ يأْتِي أحَدَكُمْ فيقولُ منْ خَلَقَكَ فيقولُ اللَّهُ فيقولُ فَمَنْ خَلَقَ اللَّهَ فإذا وَجَدَ أحدُكُمْ ذلِكَ فَلْيَقُلْ آمنْتُ باللَّهِ وَرُسْلِهِ فإن ذلِكَ يَذْهَبُ عَنْهُ. [1657] (صحيح) (ابن أبي الدنيا في مكايد الشيطان) عن عائشة.";

$result = $parser->parse($text);
print_r($result);
