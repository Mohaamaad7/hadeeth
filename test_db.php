<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$codes = ['حم', 'د', 'ن', 'ك'];

echo "DB Code lookup:\n";
foreach ($codes as $code) {
    $s = \App\Models\Source::where('code', $code)->first();
    echo "$code => " . ($s ? "Found ({$s->name})" : "NOT FOUND") . "\n";
}

$names = ['مسند أحمد', 'سنن أبي داود', 'سنن النسائي', 'المستدرك للحاكم'];
echo "\nDB Name lookup:\n";
foreach ($names as $name) {
    $s = \App\Models\Source::where('name', 'LIKE', "%$name%")->first();
    echo "$name => " . ($s ? "Found ({$s->name}, code: {$s->code})" : "NOT FOUND") . "\n";
}
