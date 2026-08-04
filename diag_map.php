<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Exports\HasilTestExport;

$e = new HasilTestExport(11, 1);

echo "=== MAP OUTPUT ===\n";
foreach ($e->collection() as $row) {
    $m = $e->map($row);
    echo "score=" . var_export($m[4], true) . " type=" . gettype($m[4]) . "\n";
}

echo "\n=== KOMPETENSI (kolom index 5) ===\n";
foreach ($e->collection() as $row) {
    $m = $e->map($row);
    echo "kompetensi=" . var_export($m[5], true) . "\n";
}
