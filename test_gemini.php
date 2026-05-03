<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(\App\Services\GeminiExtractionService::class);
$failedHadiths = [
    [
        'index' => 0,
        'raw' => 'إن الله لا يقبل صلاة أحدكم إذا أحدث حتى يتوضأ',
        'parsed' => ['grade' => null, 'narrators' => [], 'number' => null],
        'errors' => ['لم يتم العثور على الحكم (صحيح/حسن/ضعيف)', 'لم يتم العثور على رقم الحديث [xxx]']
    ]
];

try {
    $result = $service->fixIncompleteParses($failedHadiths);
    print_r($result);
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
