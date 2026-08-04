<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$dir = __DIR__ . '/storage/app/private/import-soal';
$files = glob($dir . '/*.xlsx');
usort($files, fn($a, $b) => filemtime($b) - filemtime($a));

echo 'Jumlah file: ' . count($files) . PHP_EOL;

foreach (array_slice($files, 0, 1) as $file) {
    echo '====================================' . PHP_EOL;
    echo 'FILE: ' . basename($file) . PHP_EOL;
    try {
        $sheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file)->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        $highestCol = $sheet->getHighestColumn();
        echo 'Range: A1:' . $highestCol . $highestRow . PHP_EOL;

        $colNum = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestCol);
        for ($row = 1; $row <= min(3, $highestRow); $row++) {
            $cells = [];
            for ($c = 1; $c <= $colNum; $c++) {
                $ref = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c) . $row;
                $val = $sheet->getCell($ref)->getValue();
                $cells[] = $ref . '=' . (is_null($val) ? 'null' : var_export($val, true));
            }
            echo 'Row ' . $row . ': ' . implode(' | ', $cells) . PHP_EOL;
        }
    } catch (\Exception $e) {
        echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
    }
    echo PHP_EOL;
}


