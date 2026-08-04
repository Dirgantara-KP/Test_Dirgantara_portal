<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Exports\HasilTestExport;
use App\Models\EventPeserta;
use Maatwebsite\Excel\Facades\Excel;

// Ambil peserta yang sudah SELESAI
$pe = EventPeserta::where('status_pengerjaan', 'selesai')
    ->with(['event', 'peserta'])
    ->first();

if (!$pe) {
    echo "Tidak ada peserta yang selesai.\n";
    exit(1);
}

echo "=== TEST EXPORT UNTUK PESERTA ===\n";
echo "Peserta : {$pe->peserta->nama}\n";
echo "Event   : {$pe->event->nama_event}\n";
echo "event_id: {$pe->event_id} | peserta_id: {$pe->peserta_id}\n\n";

$export = new HasilTestExport($pe->event_id, $pe->peserta_id);

// --- Dump data collection (mapping) ---
echo "=== HEADINGS ===\n";
echo implode(' | ', $export->headings()) . "\n\n";

echo "=== DATA (dari collection + map) ===\n";
foreach ($export->collection() as $row) {
    $mapped = $export->map($row);
    echo implode(' | ', $mapped) . "\n";
}

// --- Generate file ---
$nama = preg_replace('/[^\w\-]+/', '_', $pe->peserta->nama);
$event = preg_replace('/[^\w\-]+/', '_', $pe->event->nama_event);
$filename = 'hasil_test_' . $nama . '_' . $event . '.xlsx';

$path = storage_path('app/private/' . $filename);
Excel::store($export, $filename, 'local');

echo "\n=== FILE TERGENERATE ===\n";
echo "Name : {$filename}\n";
echo "Path : {$path}\n";
echo "Exists: " . (file_exists($path) ? 'YES' : 'NO') . "\n";
echo "Size : " . (file_exists($path) ? round(filesize($path) / 1024, 2) . ' KB' : '-') . "\n";
