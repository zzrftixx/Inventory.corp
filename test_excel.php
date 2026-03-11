<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Is zip loaded: " . (extension_loaded('zip') ? 'Yes' : 'No') . "\n";

try {
    $writer = \Spatie\SimpleExcel\SimpleExcelWriter::create('test.xlsx');
    $writer->addRow(['Test' => 'Data']);
    echo "Excel created successfully!\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
