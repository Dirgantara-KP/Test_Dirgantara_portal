<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== SEBELUM ===\n";
foreach (\App\Models\TipeSoal::all() as $t) {
    echo "id={$t->id} kode={$t->kode} nama={$t->nama}\n";
}

// Normalisasi: PG -> pg, E -> esai (jika belum ada esai)
$pg = \App\Models\TipeSoal::whereRaw('LOWER(kode) = ?', ['pg'])->first();
$esai = \App\Models\TipeSoal::whereRaw('LOWER(kode) = ?', ['esai'])->first();

if (! $pg) {
    $pgRow = DB::table('tipe_soals')->where('kode', 'PG')->first();
    if ($pgRow) {
        DB::table('tipe_soals')->where('id', $pgRow->id)->update(['kode' => 'pg']);
        echo "PG -> pg (id={$pgRow->id})\n";
    }
} else {
    echo "pg sudah ada (id={$pg->id})\n";
    $pgRow = DB::table('tipe_soals')->where('kode', 'PG')->first();
    if ($pgRow) {
        // Migrasi semua soal dari PG ke pg
        $soalCount = DB::table('soals')->where('tipe_soal_id', $pgRow->id)->count();
        DB::table('soals')->where('tipe_soal_id', $pgRow->id)->update(['tipe_soal_id' => $pg->id]);
        DB::table('kata_pengantars')->where('tipe_soal_id', $pgRow->id)->update(['tipe_soal_id' => $pg->id]);
        DB::table('tipe_soals')->where('id', $pgRow->id)->delete();
        echo "Merge PG ke pg, {$soalCount} soal dipindahkan.\n";
    }
}

if (! $esai) {
    $eRow = DB::table('tipe_soals')->where('kode', 'E')->first();
    if ($eRow) {
        DB::table('tipe_soals')->where('id', $eRow->id)->update(['kode' => 'esai', 'nama' => 'Esai']);
        echo "E -> esai (id={$eRow->id})\n";
    }
} else {
    echo "esai sudah ada (id={$esai->id})\n";
    $eRow = DB::table('tipe_soals')->where('kode', 'E')->first();
    if ($eRow) {
        $soalCount = DB::table('soals')->where('tipe_soal_id', $eRow->id)->count();
        DB::table('soals')->where('tipe_soal_id', $eRow->id)->update(['tipe_soal_id' => $esai->id]);
        DB::table('kata_pengantars')->where('tipe_soal_id', $eRow->id)->update(['tipe_soal_id' => $esai->id]);
        DB::table('tipe_soals')->where('id', $eRow->id)->delete();
        echo "Merge E ke esai, {$soalCount} soal dipindahkan.\n";
    }
}

echo "\n=== SESUDAH ===\n";
foreach (\App\Models\TipeSoal::all() as $t) {
    echo "id={$t->id} kode={$t->kode} nama={$t->nama}\n";
}

