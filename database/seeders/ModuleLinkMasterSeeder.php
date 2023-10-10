<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\Pentadbir_modules;
use \App\Models\ModuleLinkMaster;


class ModuleLinkMasterSeeder extends Seeder
{
   
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ModuleLinkMaster::truncate();

        $modules= Pentadbir_modules::select('id')->get();

        foreach($modules as $module)
        {
            if($module['id']==1){
                $this->InsertPentadbirData(1);
            }

            if($module['id']==2)
            {
                $this->InsertPermohonanData(2);
            }

            if($module['id']==6){
                $this->InsertVmData(6);
            }
        }

    }

    public function InsertPermohonanData($id)
    {
        $created_on = array(
            array(
                "module_id" => $id,
                'link_name' => 'parmohonan-project-dashboard',
                'dibuat_oleh' => 1
            ),
            array(       
                "module_id" => $id,
                'link_name' => 'project-list',
                'dibuat_oleh' => 1
            ),
            array(    
                "module_id" => $id,
                'link_name' => 'daftar-permohonan-project',
                'dibuat_oleh' => 1
            ),
            array(
                "module_id" => $id,
                'link_name' => 'salin-project-list',
                'dibuat_oleh' => 1
            ),
            array(   
                "module_id" => $id,
                'link_name' => 'semak-project-list',
                'dibuat_oleh' => 1
            ),
            array(   
                "module_id" => $id,
                'link_name' => 'janaan_laporan',
                'dibuat_oleh' => 1
            )
        );

        for($i=0;$i<count($created_on);$i++)
        {
            $deisgnation= new ModuleLinkMaster;
            $deisgnation->module_id=$created_on[$i]['module_id'];
            $deisgnation->link_name=$created_on[$i]['link_name'];
            $deisgnation->save();
        }
    }

    public function InsertPentadbirData($id)
    {
        $created_on = array(
            array(
                "module_id" => $id,
                'link_name' => 'home',
                'dibuat_oleh' => 1
            ),
            array(       
                "module_id" => $id,
                'link_name' => 'userlist',
                'dibuat_oleh' => 1
            ),
            array(    
                "module_id" => $id,
                'link_name' => 'pengasahan-pengguna-baharu',
                'dibuat_oleh' => 1
            ),
            array(
                "module_id" => $id,
                'link_name' => 'daftar-pengguna-baharu',
                'dibuat_oleh' => 1
            ),
            array(   
                "module_id" => $id,
                'link_name' => 'master/portal',
                'dibuat_oleh' => 1
            ),
            array(   
                "module_id" => $id,
                'link_name' => 'master',
                'dibuat_oleh' => 1
            ),
            array(   
                "module_id" => $id,
                'link_name' => 'Selenggara_Kod_Projek',
                'dibuat_oleh' => 1
            ),
            array(   
                "module_id" => $id,
                'link_name' => 'selenggara_projek',
                'dibuat_oleh' => 1
            ),
            array(   
                "module_id" => $id,
                'link_name' => 'selenggara_dashboard_analisis',
                'dibuat_oleh' => 1
            ),
            array(   
                "module_id" => $id,
                'link_name' => 'selenggara_map_services',
                'dibuat_oleh' => 1
            ),
            array(   
                "module_id" => $id,
                'link_name' => 'selenggara-pengurusan-peranan',
                'dibuat_oleh' => 1
            ),
            array(   
                "module_id" => $id,
                'link_name' => 'audit-logs',
                'dibuat_oleh' => 1
            ),
        );

        for($i=0;$i<count($created_on);$i++)
        {
            
            $deisgnation= new ModuleLinkMaster;
            $deisgnation->module_id=$created_on[$i]['module_id'];
            $deisgnation->link_name=$created_on[$i]['link_name'];
            $deisgnation->save();
        }
    }

    public function InsertVmData($id)
    {
        $created_on = array(
            array(
                "module_id" => $id,
                'link_name' => 'senarai_makmal_and_mini',
                'dibuat_oleh' => 1
            ),
            array(       
                "module_id" => $id,
                'link_name' => 'fasilitator',
                'dibuat_oleh' => 1
            ),
            array(    
                "module_id" => $id,
                'link_name' => 'KalendarVM',
                'dibuat_oleh' => 1
            ),
            array(    
                "module_id" => $id,
                'link_name' => 'senarai_selasai_makmal',
                'dibuat_oleh' => 1
            )
        );

        for($i=0;$i<count($created_on);$i++)
        {
            $deisgnation= new ModuleLinkMaster;
            $deisgnation->module_id=$created_on[$i]['module_id'];
            $deisgnation->link_name=$created_on[$i]['link_name'];
            $deisgnation->save();
        }
    }
}
