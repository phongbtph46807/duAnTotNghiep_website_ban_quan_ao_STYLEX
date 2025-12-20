<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoleSalary;

class RoleSalarySeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['role' => 1, 'base_salary' => 15000000],
            ['role' => 2, 'base_salary' => 8000000],
            ['role' => 3, 'base_salary' => 6000000],
            ['role' => 4, 'base_salary' => 5000000],
        ];

        foreach ($roles as $role) {
            RoleSalary::updateOrCreate(
                ['role' => $role['role']],
                ['base_salary' => $role['base_salary']]
            );
        }
    }
}
