<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use PhpOffice\PhpSpreadsheet\IOFactory;

$dir = __DIR__ . '/storage/app/private/import-soal';
$files = glob($dir . '/*.xlsx');
usort($files, function ($a, $b) {
    return filemtime($b) - filemtime($a);
});

echo 'Jumlah file: ' . count($files) . PHP_EOL;

foreach (array_slice($files, 0, 2) as $file) {
    echo '==========================================' . PHP_EOL;
    echo 'FILE: ' . basename($file) . PHP_EOL;
    echo 'Tanggal: ' . date('Y-m-d H:i:s', filemtime($file)) . PHP_EOL;

    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        $highestCol = $sheet->getHighestColumn();

        echo 'Range: A1:' . $highestCol . $highestRow . PHP_EOL;

        for ($row = 1; $row <= min(3, $highestRow); $row++) {
            $cells = array();
            $colNum = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestCol);
            for ($c = 1; $c <= $colNum; $c++) {
                $ref = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c) . $row;
                $val = $sheet->getCell($ref)->getValue();
                if (is_null($val)) {
                    $valStr = '(null)';
                } else {
                    $valStr = var_export($val, true);
                }
                $cells[] = $ref . '=' . $valStr;
            }
            echo 'Row ' . $row . ': ' . implode(' | ', $cells) . PHP_EOL;
        }
    } catch (Exception $e) {
        echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
    }
    echo PHP_EOL;
}
</content>

