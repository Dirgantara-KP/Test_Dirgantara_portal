<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TIPE SOAL ===\n";
foreach (\App\Models\TipeSoal::all() as $t) {
    echo "id={$t->id} kode={$t->kode} nama={$t->nama}\n";
}

echo "\n=== SOAL (tipe_soal_id) ===\n";
foreach (\App\Models\Soal::with('tipeSoal')->get() as $s) {
    echo "id={$s->id} kode={$s->kode_soal} tipe_soal_id={$s->tipe_soal_id} tipe={\$s->tipeSoal?->nama} isPg=" . ($s->isPg() ? 'YA' : 'TIDAK') . "\n";
}

echo "\n=== KATA PENGANTAR ===\n";
foreach (\App\Models\KataPengantar::all() as $kp) {
    echo "id={$kp->id} tipe_soal_id={$kp->tipe_soal_id}\n";
}
