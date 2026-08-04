<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HasilJawabanPeserta;
use App\Models\EventPeserta;

echo "=== CALISTA (peserta_id=3, event_id=11) ===\n";
$rows = HasilJawabanPeserta::with(['soal', 'jawabanSoal'])
    ->where('event_id', 11)
    ->where('peserta_id', 3)
    ->get();
foreach ($rows as $r) {
    echo "kode={$r->soal->kode_soal} kategori=[" . ($r->soal->kategori ?? 'KOSONG') . "] is_benar=" . var_export($r->is_benar, true) . "\n";
}

echo "\n=== BUDI (peserta_id=1, event_id=11) ===\n";
$rows = HasilJawabanPeserta::with(['soal', 'jawabanSoal'])
    ->where('event_id', 11)
    ->where('peserta_id', 1)
    ->get();
foreach ($rows as $r) {
    echo "kode={$r->soal->kode_soal} kategori=[" . ($r->soal->kategori ?? 'KOSONG') . "] is_benar=" . var_export($r->is_benar, true) . "\n";
}
