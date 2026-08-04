<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use PhpOffice\PhpSpreadsheet\IOFactory;

$path = __DIR__ . '/storage/app/private/hasil_test_Budi_Santoso_TES_HC_TW1.xlsx';
$reader = IOFactory::createReader('Xlsx');
$reader->setIncludeCharts(true);
$spreadsheet = $reader->load($path);
$sheet = $spreadsheet->getActiveSheet();

echo "=== Detail row E value & fill ===\n";
for ($r = 1; $r <= 6; $r++) {
    $val = $sheet->getCell('E' . $r)->getValue();
    $formatted = $sheet->getCell('E' . $r)->getFormattedValue();
    $fill = $sheet->getStyle('A' . $r)->getFill();
    echo "R{$r}: E raw=" . var_export($val, true) . " formatted=" . var_export($formatted, true)
        . " fillType=" . $fill->getFillType() . " color=" . $fill->getStartColor()->getARGB() . "\n";
}

echo "\n=== Apakah ada data lain di kolom A-E rows 5-6 (harus kosong) ===\n";
for ($r = 5; $r <= 6; $r++) {
    $cells = [];
    foreach (['A', 'B', 'C', 'D', 'E'] as $col) {
        $cells[] = $sheet->getCell($col . $r)->getValue();
    }
    echo "R{$r}: " . implode(' | ', $cells) . "\n";
}
