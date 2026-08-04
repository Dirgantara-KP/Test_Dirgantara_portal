<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HasilJawabanPeserta;
use PhpOffice\PhpSpreadsheet\IOFactory;

// 1) Cek nilai is_benar mentah dari DB
echo "=== DB is_benar (Budi, event 11) ===\n";
foreach (HasilJawabanPeserta::where('event_id', 11)->where('peserta_id', 1)->get() as $r) {
    $soal = $r->soal;
    echo "kode={$soal->kode_soal} is_benar=" . var_export($r->is_benar, true) . " type=" . gettype($r->is_benar) . "\n";
}

echo "\n=== Cek Isi File Excel ===\n";
$path = __DIR__ . '/storage/app/private/hasil_test_Budi_Santoso_TES_HC_TW1.xlsx';
$reader = IOFactory::createReader('Xlsx');
$reader->setIncludeCharts(true);
$spreadsheet = $reader->load($path);
$sheet = $spreadsheet->getActiveSheet();

echo "E4 raw value: " . var_export($sheet->getCell('E4')->getValue(), true) . "\n";
echo "E4 formatted: " . var_export($sheet->getCell('E4')->getFormattedValue(), true) . "\n";

echo "\n=== Fill per baris (kolom A) ===\n";
for ($r = 1; $r <= 4; $r++) {
    $fill = $sheet->getStyle('A' . $r)->getFill();
    echo "R{$r}: type={$fill->getFillType()} color={$fill->getStartColor()->getARGB()} E={$sheet->getCell('E' . $r)->getValue()}\n";
}
