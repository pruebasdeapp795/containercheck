<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        // Inspector
        User::factory()->create([
            'name' => 'Inspector User',
            'email' => 'inspector@example.com',
            'password' => 'password',
            'role' => 'inspector',
        ]);

        // Phases
        $schemaManager = new \App\Services\SchemaManager();

        $phases = [
            ['name' => 'Datos del Conductor', 'table_name' => 'phase_driver', 'order' => 1, 'description' => 'Información del conductor'],
            ['name' => 'Datos del Vehículo', 'table_name' => 'phase_vehicle', 'order' => 2, 'description' => 'Información del vehículo y empresa'],
            ['name' => 'Estado del Contenedor', 'table_name' => 'phase_container', 'order' => 3, 'description' => 'Evaluación física del contenedor'],
            ['name' => 'Personal participante', 'table_name' => 'phase_participant', 'order' => 4, 'description' => 'Firmas y participantes'],
        ];

        foreach ($phases as $phaseData) {
            $phase = \App\Models\Fase::create($phaseData);
            $schemaManager->createPhaseTable($phase->table_name);

            // Seed default fields based on phase
            if ($phase->order === 1) { // Driver
                $fields = [
                    ['label' => 'Nombre del Conductor', 'column_name' => 'driver_name', 'type' => 'text', 'required' => true, 'order' => 1],
                    ['label' => 'Licencia de Conducción', 'column_name' => 'driver_license', 'type' => 'text', 'required' => true, 'order' => 2],
                    ['label' => 'Foto Licencia', 'column_name' => 'driver_license_photo', 'type' => 'photo', 'required' => true, 'description' => 'Tomar foto legible del documento de identidad', 'order' => 3],
                ];
            } elseif ($phase->order === 2) { // Vehicle
                $fields = [
                    ['label' => 'Placa del Vehículo', 'column_name' => 'vehicle_plate', 'type' => 'text', 'required' => true, 'order' => 1],
                    ['label' => 'Empresa Transportadora', 'column_name' => 'transport_company', 'type' => 'text', 'required' => false, 'order' => 2],
                    ['label' => 'Foto Frontal Vehículo', 'column_name' => 'vehicle_front_photo', 'type' => 'photo', 'required' => true, 'description' => 'Foto frontal donde se vea la placa', 'order' => 3],
                ];
            } elseif ($phase->order === 3) { // Container
                $fields = [
                    ['label' => 'Número de Contenedor', 'column_name' => 'container_number', 'type' => 'text', 'required' => true, 'order' => 1],
                    ['label' => 'Estado General', 'column_name' => 'general_condition', 'type' => 'select', 'required' => true, 'options' => json_encode(['Bueno', 'Regular', 'Malo']), 'order' => 2],
                    ['label' => 'Foto Puertas', 'column_name' => 'container_door_photo', 'type' => 'photo', 'required' => true, 'description' => 'Foto de las puertas cerradas y precinto', 'order' => 3],
                    ['label' => 'Foto Interior', 'column_name' => 'container_interior_photo', 'type' => 'photo', 'required' => false, 'description' => 'Foto del interior (vacío)', 'order' => 4],
                ];
            } elseif ($phase->order === 4) { // Participant
                $fields = [
                    ['label' => 'Nombre del Participante', 'column_name' => 'participant_name', 'type' => 'text', 'required' => true, 'order' => 1],
                    ['label' => 'Número de Documento', 'column_name' => 'participant_document', 'type' => 'text', 'required' => true, 'order' => 2],
                ];
            }

            if (isset($fields)) {
                $phase->fields()->createMany($fields);
                foreach ($fields as $f) {
                    $schemaManager->addFieldColumn($phase->table_name, $f['column_name'], $f['type']);
                }
            }
        }
    }
}
