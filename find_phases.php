<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Phase;

$phases = Phase::with('fields')->get();

foreach ($phases as $phase) {
    if (str_contains(strtolower($phase->name), 'sello') || str_contains(strtolower($phase->name), 'precinto')) {
        echo "Phase ID: {$phase->id} | Name: {$phase->name}\n";
        foreach ($phase->fields as $field) {
            echo "  Field ID: {$field->id} | Label: {$field->label} | Type: {$field->type}\n";
        }
    }
}
