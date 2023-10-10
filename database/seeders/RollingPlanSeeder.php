<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;

class RollingPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Schema::disableForeignKeyConstraints();
        foreach ($this->master() as $datum) {
            //foreach ($this->data() as $datum) {
            $master = \App\Models\RollingPlan::updateOrCreate([                
                'name' => $datum['name'],
                'rmk' => $datum['rmk'],
                'is_active' => $datum['is_active'],
                'is_selectable' => $datum['is_selectable'],
                'dikemaskini_oleh' => $datum['dikemaskini_oleh'],
                'dibuat_oleh' => $datum['dibuat_oleh'],
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }

    public function master()
    {
        return [
            [
                'name' => 'RP1 (2021-2022)',
                'rmk' => '12',
                'is_active' => 1,
                'is_selectable' => 0,
                'dikemaskini_oleh' => 1,
                'dibuat_oleh' => 1,
            ],
            [
                'name' => 'RP2 (2022-2023)',
                'rmk' => '12',
                'is_active' => 1,
                'is_selectable' => 0,
                'dikemaskini_oleh' => 1,
                'dibuat_oleh' => 1,
            ],
            [
                'name' => 'RP3 (2023-2024)',
                'rmk' => '12',
                'is_active' => 1,
                'is_selectable' => 0,
                'dikemaskini_oleh' => 1,
                'dibuat_oleh' => 1,
            ],
            [
                'name' => 'RP4 (2024-2025)',
                'rmk' => '12',
                'is_active' => 1,
                'is_selectable' => 1,
                'dikemaskini_oleh' => 1,
                'dibuat_oleh' => 1,
            ],
            [
                'name' => 'RP4 (2025)',
                'rmk' => '12',
                'is_active' => 1,
                'is_selectable' => 1,
                'dikemaskini_oleh' => 1,
                'dibuat_oleh' => 1,
            ],
            [
                'name' => 'RP1 (2026-2027)',
                'rmk' => '13',
                'is_active' => 0,
                'is_selectable' => 1,
                'dikemaskini_oleh' => 1,
                'dibuat_oleh' => 1,
            ],
            [
                'name' => 'RP2 (2027-2028)',
                'rmk' => '13',
                'is_active' => 0,
                'is_selectable' => 1,
                'dikemaskini_oleh' => 1,
                'dibuat_oleh' => 1,
            ],
            [
                'name' => 'RP3 (2028-2029)',
                'rmk' => '13',
                'is_active' => 0,
                'is_selectable' => 1,
                'dikemaskini_oleh' => 1,
                'dibuat_oleh' => 1,
            ],
            [
                'name' => 'RP4 (2029-2030)',
                'rmk' => '13',
                'is_active' => 0,
                'is_selectable' => 1,
                'dikemaskini_oleh' => 1,
                'dibuat_oleh' => 1,
            ],
            [
                'name' => 'RP4 (2030)',
                'rmk' => '13',
                'is_active' => 0,
                'is_selectable' => 1,
                'dikemaskini_oleh' => 1,
                'dibuat_oleh' => 1,
            ],
        ];
    }
}
