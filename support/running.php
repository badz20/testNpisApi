<?php

use App\Models\RunningNumber;
use App\Models\Project;
use Illuminate\Support\Str;

if (! function_exists('generate_running_number')) {
    function generate_running_number($type)
    {
        $type = strtoupper($type);
        
        $number = 1;
        $year = date('Y');
        if (! RunningNumber::where('type', $type)->where('year', $year)->exists()) {
            RunningNumber::create([
                'type' => $type,
                'number' => $number,
                'year' => $year,
            ]);
        }

        $running_number = RunningNumber::where('type', $type)->where('year', $year)->first();
        $running_number->number++;
        $running_number->save();
        $number = $running_number->number;
        $number = leading_zero($number);

        // BKOR/2022/001
        $value = $type . '/' . date('Y'). '/' . $number;

        return $value;
    }
}

if (! function_exists('generate_project_number')) {
    function generate_project_number($id)
    {

        $project = Project::whereId($id)->with(['negeri','bahagianPemilik','bahagianPemilik.kementerian','bahagianPemilik.jabatan','lokasi.negeri'])->first();
        $type = 'P_' . strtoupper($project->bahagianPemilik->acym);
        
        $number = 1;
        $year = date('Y');
        if (! RunningNumber::where('type', $type)->where('year', $year)->exists()) {
            RunningNumber::create([
                'type' => $type,
                'number' => $number,
                'year' => $year,
            ]);
        }

        $running_number = RunningNumber::where('type', $type)->where('year', $year)->first();
        $running_number->number++;
        $running_number->save();
        $number = $running_number->number;
        $number = leading_zero($number,2);

        // BKOR/2022/001
        //P{{kementerian_code}}  {{butiran code}} {{code jabatan}}{{code negeri}} {{code bahagian}}{{running number}}

        //$value = 'P' . $project->bahagianPemilik->kementerian->kod_kementerian . ' ' . $project->butiran_code . ' '.  $project->bahagianPemilik->jabatan->kod_jabatan . $project->lokasi->negeri->kod_negeri . ' ' .$project->bahagianPemilik->kod_bahagian . $number;
        $kod =  $project->butiran_code . ' '.  $project->bahagianPemilik->jabatan->kod_jabatan . $project->lokasi->negeri->kod_negeri . ' ' .$project->bahagianPemilik->kod_bahagian . $number;
        $value=array('kod_baharu'=>$project->bahagianPemilik->kementerian->kod_kementerian,'kod'=>$kod);
        return $value;
    }
}

if (! function_exists('leading_zero')) {
    function leading_zero($number, $leading_by = 3)
    {
        return str_pad($number, $leading_by, '0', STR_PAD_LEFT);
    }
}