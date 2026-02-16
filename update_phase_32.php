<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Phase;
use App\Models\Field;

$phase = Phase::find(32);
if ($phase) {
    echo "Updating phase 32: {$phase->name}\n";
    $phase->fields()->delete();

    $fields = [
        ['label' => 'Numero de precinto 01', 'type' => 'text', 'order' => 0],
        ['label' => 'Numero de precinto 02', 'type' => 'text', 'order' => 1],
        ['label' => 'Satelital', 'type' => 'text', 'order' => 2],
        ['label' => 'Cerrado con sellos', 'type' => 'photo', 'order' => 3],
        ['label' => 'Sello por Sello', 'type' => 'photo', 'order' => 4],
    ];

    foreach ($fields as $f) {
        $field = $phase->fields()->create([
            'label' => $f['label'],
            'type' => $f['type'],
            'order' => $f['order'],
            'is_visible' => 1
        ]);
        echo "Created Field ID: {$field->id} | Label: {$field->label}\n";
    }
}
