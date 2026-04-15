<?php

// boot laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$hadith = App\Models\Hadith::first();

if (!$hadith) {
    die("لا يوجد أي حديث في قاعدة البيانات لاختباره!\n");
}

echo "--- Hadith Original Text ---\n";
echo mb_substr($hadith->content, 0, 100) . "...\n\n";

echo "--- Text used for analyzing (content_searchable) ---\n";
echo mb_substr($hadith->content_searchable ?? $hadith->content, 0, 100) . "...\n\n";

echo "--- Tags before analysis ---\n";
print_r($hadith->tags()->pluck('name')->toArray());

echo "\n--- Running AutoTaggerService ---\n";
$tagger = new App\Services\AutoTaggerService();
$tagger->tagHadith($hadith);

echo "\n--- Tags after analysis ---\n";
// reload the relation to see fresh data
$hadith->load('tags');
print_r($hadith->tags()->pluck('name')->toArray());

echo "\n";
