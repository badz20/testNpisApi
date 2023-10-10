<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\PejabatProjek;


class PejabatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $created_on = array(
            array(
                "pajabat_projek" => "Pejabat Lembangan Sungai Muda (PLSM)",
            ),
            array(
                
                "pajabat_projek" => "Pejabat Lembangan Sungai Klang (PLSK)",
            ),
            array(
                
                "pajabat_projek" => "Rancangan-Rancangan Pembangunan Persekutuan (RPP)",
            ),
            array(
                
                "pajabat_projek" => "Rancangan Pengairan Muda (RPM)",
            ),
            array(
                
                "pajabat_projek" => "Unit Pelaksanaan Projek Persekutuan (UPPP) Kelantan",
            ),
            array(
                
                "pajabat_projek" => "Unit Pelaksanaan Projek Persekutuan (UPPP) Johor",
            ),
            array(
                
                "pajabat_projek" => "Unit Pelaksanaan Projek Persekutuan (UPPP) Pahang",
            ),
            array(
                
                "pajabat_projek" => "Unit Pelaksanaan Projek Persekutuan (UPPP) Terengganu",
            ),
            array(
                
                "pajabat_projek" => "Pusat Kawalan SMART (SMART)",
            )
        );

        
            for($i=0;$i<count($created_on);$i++)
            {
            $deisgnation= new PejabatProjek;
            $deisgnation->pajabat_projek=$created_on[$i]['pajabat_projek'];
            $deisgnation->save();
            }
    }
}
