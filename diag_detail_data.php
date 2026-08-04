<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HasilJawabanPeserta;

echo "=== Detail Jawaban Peserta (event 11, peserta 1) ===\n";
$rows = HasilJawabanPeserta::with('soal')
    ->where('event_id', 11)
    ->where('peserta_id', 1)
    ->get();

echo "Total baris: " . $rows->count() . "\n\n";

foreach ($rows as $r) {
    $soal = $r->soal;
    echo "kode={$soal->kode_soal} | category={$soal->kategori} | pertanyaan='" . substr($soal->pertanyaan, 0, 50) . "' | is_benar=" . var_export($r->is_benar, true) . "\n";
}

echo "\n=== Semua soal dengan kategori unik (untuk cek pertanyaan 'apa') ===\n";
foreach ($rows as $r) {
    $soal = $r->soal;
    echo "{$soal->kode_soal} | {$soal->kategori} | '{$soal->pertanyaan}'\n";
}
