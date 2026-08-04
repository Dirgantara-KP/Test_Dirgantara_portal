<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use PhpOffice\PhpSpreadsheet\IOFactory;

$path = __DIR__ . '/storage/app/private/hasil_test_Budi_Santoso_TES_HC_TW1.xlsx';
if (!file_exists($path)) {
    echo "File tidak ditemukan: {$path}\n";
    exit(1);
}

$reader = IOFactory::createReader('Xlsx');
$reader->setIncludeCharts(true);
$spreadsheet = $reader->load($path);
$sheet = $spreadsheet->getActiveSheet();

echo "=== ISI SEL (kolom A-F) ===\n";
foreach ($sheet->getRowIterator() as $row) {
    $rowIndex = $row->getRowIndex();
    $cells = [];
    foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $col) {
        $cells[] = $sheet->getCell($col . $rowIndex)->getValue();
    }
    if (count(array_filter($cells)) == 0) {
        continue;
    }
    echo "R{$rowIndex}: " . implode(' | ', $cells) . "\n";
}

echo "\n=== STYLE HEADER (baris 1) ===\n";
$font = $sheet->getStyle('A1')->getFont();
echo "A1 bold: " . ($font->getBold() ? 'YES' : 'NO') . "\n";

echo "\n=== STYLE SCORE=0 (baris detail) ===\n";
for ($r = 2; $r <= 4; $r++) {
    $fill = $sheet->getStyle('A' . $r)->getFill();
    echo "A{$r}: fill={$fill->getFillType()} color={$fill->getStartColor()->getARGB()} E={$sheet->getCell('E' . $r)->getValue()}\n";
}

echo "\n=== BREAKDOWN ACCURACY FILL (kolom D) ===\n";
// Asumsi: detail = 4 baris (header+3), summary = 1+5+2, breakdown header = +2
// breakdown data mulai di baris 4+3+6+2+2 = 17
foreach ([16, 17, 18] as $r) {
    $d = $sheet->getCell('D' . $r)->getValue();
    if ($d === null || $d === '') {
        continue;
    }
    $fill = $sheet->getStyle('D' . $r)->getFill();
    echo "D{$r} ({$d}): type={$fill->getFillType()} color={$fill->getStartColor()->getARGB()}\n";
}

echo "\n=== CHART ===\n";
$charts = $sheet->getChartCollection();
if (count($charts) > 0) {
    foreach ($charts as $chart) {
        echo "Chart Name: " . $chart->getName() . "\n";
        echo "Chart Title: " . $chart->getTitle()->getCaptionText() . "\n";
        echo "TopLeft: " . $chart->getTopLeftCell() . "\n";
        echo "BottomRight: " . $chart->getBottomRightCell() . "\n";
        $ax = $chart->getChartAxisX();
        echo "  X-axis min: " . var_export($ax->getAxisOptionsProperty('minimum'), true) . "\n";
        echo "  X-axis max: " . var_export($ax->getAxisOptionsProperty('maximum'), true) . "\n";
        echo "  X-axis majorUnit: " . var_export($ax->getAxisOptionsProperty('major_unit'), true) . "\n";
        foreach ($chart->getPlotArea()->getPlotGroup() as $series) {
            foreach ($series->getPlotValues() as $v) {
                $layout = $v->getLabelLayout();
                echo "  Series label showVal: " . ($layout ? var_export($layout->getShowVal(), true) : 'null') . "\n";
            }
        }
    }
} else {
    echo "TIDAK ADA CHART\n";
}
