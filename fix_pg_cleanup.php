<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== SEBELUM ===\n";
foreach (DB::table('tipe_soals')->get() as $t) {
    echo "id={$t->id} kode={$t->kode} nama={$t->nama}\n";
}

// Cari semua tipe pg
$pgRows = DB::table('tipe_soals')->whereRaw('LOWER(kode) = ?', ['pg'])->orderBy('id')->get();
echo "\nTotal tipe pg: " . $pgRows->count() . "\n";

if ($pgRows->count() > 1) {
    // Set first (terkecil id) sebagai kanonik
    $keep = $pgRows->first();
    $removeIds = $pgRows->slice(1)->pluck('id')->toArray();

    echo "Kananik pg id={$keep->id}\n";
    echo "Hapus pg id: " . implode(', ', $removeIds) . "\n";

    // Re-point soals yang mereferensikan tipe pg yang dihapus ke tipe kanonik
    foreach ($removeIds as $removeId) {
        $countSoal = DB::table('soals')->where('tipe_soal_id', $removeId)->count();
        DB::table('soals')->where('tipe_soal_id', $removeId)->update(['tipe_soal_id' => $keep->id]);
        echo "  Re-point {$countSoal} soal dari tipe_soal_id={$removeId} ke {$keep->id}\n";

        $countKp = DB::table('kata_pengantars')->where('tipe_soal_id', $removeId)->count();
        DB::table('kata_pengantars')->where('tipe_soal_id', $removeId)->update(['tipe_soal_id' => $keep->id]);
        echo "  Re-point {$countKp} kata pengantar dari tipe_soal_id={$removeId} ke {$keep->id}\n";

        // Hapus tipe duplikat
        DB::table('tipe_soals')->where('id', $removeId)->delete();
        echo "  Hapus tipe_soal id={$removeId}\n";
    }
} else {
    echo "Tidak ada duplikat pg, tidak perlu diubah.\n";
}

// Pastikan hanya ada 1 pg dan 1 esai
$pg = DB::table('tipe_soals')->whereRaw('LOWER(kode) = ?', ['pg'])->first();
$esai = DB::table('tipe_soals')->whereRaw('LOWER(kode) = ?', ['esai'])->first();

if (! $pg) {
    $pg = DB::table('tipe_soals')->insertGetId(['kode' => 'pg', 'nama' => 'Pilihan Ganda', 'created_at' => now(), 'updated_at' => now()]);
    echo "\nBuat tipe pg id={$pg}\n";
}

if (! $esai) {
    $esai = DB::table('tipe_soals')->insertGetId(['kode' => 'esai', 'nama' => 'Esai', 'created_at' => now(), 'updated_at' => now()]);
    echo "\nBuat tipe esai id={$esai}\n";
}

echo "\n=== SESUDAH ===\n";
foreach (DB::table('tipe_soals')->get() as $t) {
    echo "id={$t->id} kode={$t->kode} nama={$t->nama}\n";
}

echo "\n=== SOAL ===\n";
foreach (DB::table('soals')->get() as $s) {
    $tipe = DB::table('tipe_soals')->where('id', $s->tipe_soal_id)->first();
    $jml = DB::table('jawaban_soals')->where('soal_id', $s->id)->count();
    echo "id={$s->id} kode={$s->kode_soal} tipe_soal_id={$s->tipe_soal_id} tipe={$tipe?->kode} jml_jawaban={$jml}\n";
}
