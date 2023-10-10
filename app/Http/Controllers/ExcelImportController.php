<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;
use \App\Models\JenisKategori;



class ExcelImportController extends Controller
{
    public function importView(Request $request){
        return view('importFile');
    }
 
    public function import(Request $request){
        $the_file = $request->file('file'); 
        $spreadsheet = IOFactory::load($the_file->getRealPath()); 
        $sheet        = $spreadsheet->getActiveSheet();
        $row_limit    = $sheet->getHighestDataRow();
        $column_limit = $sheet->getHighestDataColumn();
        
        $row_range    = range( 3, $row_limit );
        $column_range = range( 'F', $column_limit );
        $startcount = 2;
        $data = array(); 
        $result = DB::table('pemantauan_project')->orderBy('id','DESC')->first()->id; //print_r($result);exit;
        $count=$result+1;

        foreach ( $row_range as $row ) {


            $data= [
                'id' => $count,
                'kod_projeck' =>$sheet->getCell( 'R' . $row )->getValue(),
                'kategori_Projek' => 1,
                'bahagian_pemilik' => 15,
                'rolling_plan_code' => 4,
                'rmk' => $sheet->getCell( 'Q' . $row )->getValue(),
                'negeri_id' => 2,
                'daerah_id' =>2,
                'butiran_code'=>1500,
                'nama_projek' =>$sheet->getCell( 'A' . $row )->getValue(),
                'objektif' => $sheet->getCell( 'B' . $row )->getValue(),
                'ringkasan_projek' => $sheet->getCell( 'C' . $row )->getValue(),
                'rasional_projek' => '<ol style="margin-left:-25px "><li>mencegah hakisan</li><li></li></ol>',
                'Faedah' => '<ol style="margin-left:-25px "><li>mencegah hakisan</li><li></li></ol>',
                'tahun' => '2023',
                'kos_projeck' => $sheet->getCell( 'M' . $row )->getValue(),
                'jenis_kategori_code' => $sheet->getCell( 'D' . $row )->getValue(),
                'jenis_sub_kategori_code' => 16,
                'implikasi_projek_tidak_lulus' => '<ol style="margin-left:-25px "><li>hakisan</li><li></li></ol>',
                'bahagian_epu_id' => 1,
                'sektor_utama_id' => 2,
                'sektor_id' => 2,
                'sub_sektor_id' => 2,
                'koridor_pembangunan' => 4,
                'kululusan_khas' => 2,
                'nota_tambahan' => '<ol style="margin-left:-25px "><li></li></ol>',
                'sokongan_upen' => 2,
                'tahun_jangka_mula' => '2023',
                'tahun_jangka_siap' => '2028',
                'tempoh_pelaksanaan'=> '24',
                'kajian' => 2,
                'rujukan_pelan_induk' => 2,
                'status_reka_bantuk' => 0,
                'melibat_pembinaan_fasa' => 1,
                'melibat_pembinaan_fasa_description' => 'fasa description',
                'melibat_pembinaan_fasa_status' => 1,
                'melibat_pembinaan_fasa_tahun' => 2012,
                'kekerapan_banjir_code' => 1,
                'workflow_status' => 1,
                'pernah_dibahasakan' => 1,
                'dibuat_oleh' => 1,
                'status_perlaksanaan' => 26,
                'va_status' => 21,
                've_status' => 21,
                'vr_status' => 21,
                'current_status' => NULL
            ];
        //     print "<pre>";
        // print_r($data); 
        DB::table('pemantauan_project')->insert($data);

            $startcount++;
            $count++;

        }
        
        // print "<pre>";
        // print_r($data); 
        exit;
        DB::table('pemantauan_project')->insert($data);
    }


    // protected function objectveData($sheet,$row)
    // {
    //     $obj = $sheet->getCell( 'B' . $row )->getValue();
    //     if($obj)
    //     {
    //         $new_obj=explode('.,',$obj); 

    //         $dynamicMenu = "<ol style=\'margin-left:-25px\ '>";

    //         foreach($new_obj as $objct)
    //         {
    //             print "obj"; print_r($objct); print "<br>"; 
    //             //$dynamicMenu += "<li>" +  $objct + "</li>";
    //         }
    //         //$dynamicMenu += "</ol>";
    //     }
    // }

    
}
