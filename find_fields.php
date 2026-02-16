<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Field;

$fields = Field::where('label', 'like', '%precinto%')
    ->orWhere('label', 'like', '%satelital%')
    ->get();

foreach ($fields as $field) {
    echo "ID: {$field->id} | Label: {$field->label} | Type: {$field->type}\n";
}
