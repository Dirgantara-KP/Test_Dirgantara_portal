<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Soal;

// Isi kategori untuk soal-soal demo supaya tabel breakdown kompetensi & chart terlihat bermakna
$kategori = [
    '001A' => 'K3LH',
    '001B' => 'K3LH',
    '001C' => 'APD',
    '003A' => 'K3LH',
    '003C' => 'Risk Management',
    '004A' => 'APD',
    '004C' => 'Risk Management',
];

foreach ($kategori as $kode => $kat) {
    $soal = Soal::where('kode_soal', $kode)->first();
    if ($soal) {
        $soal->update(['kategori' => $kat]);
        echo "Updated {$kode} -> {$kat}\n";
    }
}

echo "\nDone. Verifikasi:\n";
foreach (Soal::all() as $s) {
    echo "kode={$s->kode_soal} kategori=[" . ($s->kategori ?? 'KOSONG') . "]\n";
}
