<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['usuario' => 'admin'],
            [
                'name' => 'Administrador',
                'email' => 'admin@containercheck.com',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
            ]
        );

        // Control Riego
        User::updateOrCreate(
            ['usuario' => 'control_riesgo'],
            [
                'name' => 'Control Riego',
                'email' => 'controlriesgo@containercheck.com',
                'password' => Hash::make('12345678'),
                'role' => 'control_riesgo',
            ]
        );

        // Personal
        User::updateOrCreate(
            ['cedula' => '10203040'], // Use a realistic cedula
            [
                'name' => 'Usuario Personal',
                'email' => 'personal@containercheck.com',
                'password' => Hash::make('12345678'),
                'role' => 'personal',
                'usuario' => 'personal',
            ]
        );

        // Ensure all existing users have the generic password if needed
        // But usually seeders are for initial data.
        // If the user wants ALL users to have 12345678:
        User::where('role', 'personal')->update(['password' => Hash::make('12345678')]);
    }
}
