<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RollingPlans extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT rolling_plans ON');
        //DB::table('rolling_plans')->truncate();
        $json = file_get_contents(storage_path('/data/rolling_plans.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['rolling_plans'] as $datum) {
            \App\Models\RollingPlan::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT rolling_plans OFF');
        Schema::enableForeignKeyConstraints();
    }
}
