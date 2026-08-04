<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 0);
$sheet->setCellValue('A2', '0');
$sheet->setCellValue('A3', 1);

$tmp = __DIR__ . '/storage/app/private/diag_zero_test.xlsx';
$writer = new Xlsx($spreadsheet);
$writer->save($tmp);

// Read back
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
$sp = $reader->load($tmp);
$sh = $sp->getActiveSheet();
echo "A1 (int 0): " . var_export($sh->getCell('A1')->getValue(), true) . "\n";
echo "A2 (string '0'): " . var_export($sh->getCell('A2')->getValue(), true) . "\n";
echo "A3 (int 1): " . var_export($sh->getCell('A3')->getValue(), true) . "\n";

echo "\n=== Bandingkan dengan data export maatwebsite ===\n";
$e = new App\Exports\HasilTestExport(11, 1);
foreach ($e->collection() as $row) {
    $m = $e->map($row);
    echo "score=" . var_export($m[4], true) . " type=" . gettype($m[4]) . "\n";
}
