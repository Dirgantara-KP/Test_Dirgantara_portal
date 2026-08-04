<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== SEBELUM ===\n";
echo "TipeSoal: " . \App\Models\TipeSoal::count() . "\n";
echo "Soal: " . \App\Models\Soal::count() . "\n";
echo "Soal tipe_soal_id=1: " . \App\Models\Soal::where('tipe_soal_id', 1)->count() . "\n";

// Buat tipe_soal pg
$pg = \App\Models\TipeSoal::create(['kode' => 'pg', 'nama' => 'Pilihan Ganda']);
echo "Buat pg id={$pg->id}\n";

// Re-point semua soal yang tipe_soal_id = 1 (hilang) ke pg
$count = DB::table('soals')->where('tipe_soal_id', 1)->update(['tipe_soal_id' => $pg->id]);
echo "Re-point {$count} soal ke pg\n";

// Re-point kata pengantar yang tipe_soal_id = 1 ke pg
$countKp = DB::table('kata_pengantars')->where('tipe_soal_id', 1)->update(['tipe_soal_id' => $pg->id]);
echo "Re-point {$countKp} kata pengantar ke pg\n";

echo "\n=== SESUDAH ===\n";
foreach (\App\Models\TipeSoal::all() as $t) {
    echo "id={$t->id} kode={$t->kode} nama={$t->nama}\n";
}
echo "\nSoal:\n";
foreach (\App\Models\Soal::with('tipeSoal')->get() as $s) {
    $isPg = $s->isPg() ? 'YA' : 'TIDAK';
    echo "id={$s->id} kode={$s->kode_soal} tipe_soal_id={$s->tipe_soal_id} tipe={$s->tipeSoal?->nama} isPg={$isPg}\n";
}
