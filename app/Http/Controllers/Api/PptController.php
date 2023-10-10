<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Font;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\Style\Border;
use \App\Models\Project;
use \App\Models\User;
use \DOMDocument;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Facades\Agent;


class PptController extends Controller
{
    //

    public function createPPt(Request $request)
    {

        try {
            //code...
// dd(storage_path("project/ppt/"));
            $project = Project::whereId($request->id)->with(['bahagianPemilik','rollingPlan','negeri','daerah','RmkObbSdg.strategi','kajianProjects'])->first();
            // dd($project);
            //dd($project->RmkObbSdg->strategi->nama_strategi);
        $objPHPPresentation = new PhpPresentation();

        // Set properties
        $objPHPPresentation->getDocumentProperties()->setCreator('PHPOffice')
            ->setLastModifiedBy('PHPPresentation Team')
            ->setTitle('Sample 01 Title')
            ->setSubject('Sample 01 Subject')
            ->setDescription('Sample 01 Description')
            ->setKeywords('office 2007 openxml libreoffice odt php')
            ->setCategory('Sample Category');

        $objPHPPresentation->removeSlideByIndex(0);

        $objPHPPresentation = $this->slide1($objPHPPresentation,$project);
        $objPHPPresentation = $this->slide2($objPHPPresentation,$project);
        $objPHPPresentation = $this->slide3($objPHPPresentation,$project);
        $objPHPPresentation = $this->slide4($objPHPPresentation,$project);
        $objPHPPresentation = $this->slide5($objPHPPresentation,$project);
        $objPHPPresentation = $this->slide6($objPHPPresentation,$project);
        $objPHPPresentation = $this->slide7($objPHPPresentation,$project);
        $objPHPPresentation = $this->kewanganDetails($objPHPPresentation,$project);

        $oWriterPPTX = IOFactory::createWriter($objPHPPresentation, 'PowerPoint2007');
        $pptname = str_replace("/","_",$project->no_rujukan);
        // $oWriterPPTX->save(storage_path("project\\ppt\\" .$pptname. ".ppt"));
        $oWriterPPTX->save(storage_path("project/ppt/" .$pptname. ".ppt"));

        $ppt=$pptname. ".ppt";
        $imagePath =storage_path("project/ppt/" .$ppt);
        $headers = array('Content-Type'=> '	application/vnd.ms-powerpoint');
        return response()->download($imagePath, $ppt, $headers);

    } catch (\Throwable $th) {
        logger()->error($th->getMessage());

        //------------ error log store and email --------------------
        
        $body = [
            'application_name' => env('APP_NAME'),
            'application_type' => Agent::isPhone(),
            'url' => request()->fullUrl(),
            'error_log' => $th->getMessage(),
            'error_code' => $th->getCode(),
            'ip_address' =>  request()->ip(),
            'user_agent' => request()->userAgent(),
            'email' => env('ERROR_EMAIL'),
        ];

        CallApi($body);

        //------------- end of store and email -----------------------

        return response()->json([
            'code' => '500',
            'status' => 'Failed',
            'error' => $th,
        ]);
    }
        

    }

    private function slide3($objPHPPresentation,$project)
    {
        
        $currentSlide = $this->slideHeadingTemplate($objPHPPresentation,$project,'SLAID A : PENGENALAN PROJEK');

        // Create a shape (table)
        $shape1 = $currentSlide->createTableShape(3);
        $shape1->setHeight(200);
        $shape1->setWidth(945);
        $shape1->setOffsetX(10);
        $shape1->setOffsetY(140);

        $heading = ['MATLAMAT SDG (SDG GOALS)','SASARAN SDG','SDG INDICATOR'];

        // Add row
        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFFFFFFF'))
            ->setEndColor(new Color('FFFFFFFF'));

        foreach($heading as $cols) {
            //add cols
            $oCell1 = $row->nextCell();
            $oCell1->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setMarginBottom(2)
                ->setMarginLeft(5)
                ->setMarginRight(5)
                ->setMarginTop(2);
            $oCell1->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
                ->setStartColor(new Color('FFFFFF'))
                ->setEndColor(new Color('FFFFFF'));
            $oCell1->createTextRun($cols)->getFont()->setBold(true);
            $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(10);
        }


        $rmk_collections = \App\Models\RMKSDGSasaranIndikator::where('permohonan_projek_id',$project->id)->with(['sdg','indikator','sasaran'])->get();

        $distinct_sdg = \App\Models\RMKSDGSasaranIndikator::where('permohonan_projek_id',$project->id)->where('row_status',1)->with('sdg')->distinct()->get('SDG_id');

        foreach($distinct_sdg as $sdg) {
                // Add row
                $row = $shape1->createRow();
                $row->setHeight(20);
                $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
                    ->setRotation(90)
                    ->setStartColor(new Color('FFFFFFFF'))
                    ->setEndColor(new Color('FFFFFFFF'));
                    
                //add cols
                $oCell1 = $row->nextCell();
                $oCell1->getActiveParagraph()->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setMarginBottom(2)
                    ->setMarginLeft(5)
                    ->setMarginRight(5)
                    ->setMarginTop(2);
                $oCell1->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
                    ->setStartColor(new Color('FFFFFF'))
                    ->setEndColor(new Color('FFFFFF'));
                $oCell1->createTextRun($sdg->sdg->nama_sdg)->getFont();
                $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(10);

                $sasarans = \App\Models\RMKSDGSasaranIndikator::where('permohonan_projek_id',$project->id)->where('SDG_id',$sdg->sdg->id)->where('row_status',1)->with('sasaran')->get('Sasaran_id');
                $oCell1 = $row->nextCell();

                foreach($sasarans as $sasaran) {
                    $oCell1->getActiveParagraph()->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setMarginBottom(2)
                        ->setMarginLeft(5)
                        ->setMarginRight(5)
                        ->setMarginTop(2);
                    $oCell1->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
                        ->setStartColor(new Color('FFFFFF'))
                        ->setEndColor(new Color('FFFFFF'));
                    $oCell1->createTextRun("\n" .$sasaran->sasaran->Sasaran)->getFont();
                    $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(10);
                }
                
                
                $oCell1 = $row->nextCell();
                $indikators = \App\Models\RmkSDGIndikator::where('permohonan_projek_id',$project->id)->with('indikator')->where('SDG_id',$sdg->sdg->id)->where('row_status',1)->get('Indikatori_id');
                foreach($indikators as $indikator) {
                    $oCell1->getActiveParagraph()->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setMarginBottom(2)
                        ->setMarginLeft(5)
                        ->setMarginRight(5)
                        ->setMarginTop(2);
                    $oCell1->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
                        ->setStartColor(new Color('FFFFFF'))
                        ->setEndColor(new Color('FFFFFF'));
                    $oCell1->createTextRun("\n" . $indikator->indikator->Indikatori)->getFont();
                    $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(10);
                }
                
            //Log::info($sdg->sdg->nama_sdg);  
        }

        return $objPHPPresentation;
        
    }



    private function slide4($objPHPPresentation,$project)
    {
        $currentSlide = $this->slideHeadingTemplate($objPHPPresentation,$project,'SLAID A : PENGENALAN PROJEK');

        // Create a shape (table)
        $shape1 = $currentSlide->createTableShape(4);
        $shape1->setHeight(200);
        $shape1->setWidth(945);
        $shape1->setOffsetX(10);
        $shape1->setOffsetY(140);

        $heading = ['RINGKASAN LATAR BELAKANG PROJEK
        (What)','RASIONAL/JUSTIFIKASI KEPERLUAN PROJEK
        (Why)','FAEDAH','IMPLIKASI PROJEK TIDAK DILULUS'];

        // Add row
        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFFFFFFF'))
            ->setEndColor(new Color('FFFFFFFF'));

        foreach($heading as $cols) {
            //add cols
            $oCell1 = $row->nextCell();
            $oCell1->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setMarginBottom(2)
                ->setMarginLeft(5)
                ->setMarginRight(5)
                ->setMarginTop(2);
            $oCell1->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
                ->setStartColor(new Color('FFFFFF'))
                ->setEndColor(new Color('FFFFFF'));
            $oCell1->createTextRun($cols)->getFont()->setBold(true);
            $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(10);
        }



        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFFFFFFF'))
            ->setEndColor(new Color('FFFFFFFF'));
        

        $this->writeListCell($row,$project->ringkasan_projek);
        $this->writeListCell($row,$project->rasional_projek);
        $this->writeListCell($row,$project->Faedah);
        $this->writeListCell($row,$project->implikasi_projek_tidak_lulus);


        return $objPHPPresentation;
        
    }

    private function writeListCell($row,$value)
    {
        $oCell1 = $row->nextCell();
        if($value) {
            $oCell1->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setMarginBottom(2)
                ->setMarginLeft(5)
                ->setMarginRight(5)
                ->setMarginTop(2);
            $oCell1->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
                ->setStartColor(new Color('FFFFFF'))
                ->setEndColor(new Color('FFFFFF'));

            $doc = new DOMDocument();
            $doc->loadHTML($value);
            $doc->saveHTML();
            $items = $doc->getElementsByTagName('li');
            $array_doc = [];
            $counter = 1;
            if(count($items) > 0) //Only if tag1 items are found 
            {
                foreach ($items as $tag1)
                {
                    array_push($array_doc,$counter . '. '.  $tag1->nodeValue);
                    $counter = $counter + 1;
                }
            }
            $oCell1->createTextRun(implode("\n\n", $array_doc))->getFont();
        }
        
        
        // $oCell1->createTextRun($cols)->getFont();
        // $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(10);
    }

    private function writeSkopCell($row,$collection)
    {
        $oCell1 = $row->nextCell();

        $counter = 1;
        if($collection) {

            foreach($collection as $value) {
                $oCell1->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setMarginBottom(2)
                ->setMarginLeft(5)
                ->setMarginRight(5)
                ->setMarginTop(2);
            $oCell1->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
                ->setStartColor(new Color('FFFFFF'))
                ->setEndColor(new Color('FFFFFF'));

            $oCell1->createTextRun("\n\n". $counter . '. ' . $value->skopOptions->skop_name . ' (' . $value->cost . '). ')->getFont();

            $counter = $counter + 1;
            }
            
        }
        
    }


    private function writeOutputCell($row,$collection)
    {
        $oCell1 = $row->nextCell();

        $counter = 1;
        if($collection) {

            foreach($collection as $value) {
                $oCell1->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setMarginBottom(2)
                ->setMarginLeft(5)
                ->setMarginRight(5)
                ->setMarginTop(2);
            $oCell1->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
                ->setStartColor(new Color('FFFFFF'))
                ->setEndColor(new Color('FFFFFF'));
            $temp = $value->unit ? $value->unit->nama_unit:'';
            $oCell1->createTextRun("\n\n". $counter . '. ' . $value->output_proj . ' ' . $value->Kuantiti . ' '. $temp)->getFont();

            $counter = $counter + 1;
            }
            
        }
        
    }

    private function writeOutcomeCell($row,$collection)
    {
        $oCell1 = $row->nextCell();

        $counter = 1;
        if($collection) {

            foreach($collection as $value) {
                $oCell1->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setMarginBottom(2)
                ->setMarginLeft(5)
                ->setMarginRight(5)
                ->setMarginTop(2);
            $oCell1->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
                ->setStartColor(new Color('FFFFFF'))
                ->setEndColor(new Color('FFFFFF'));
                $temp = $value->unit ? $value->unit->nama_unit:'';
            $oCell1->createTextRun("\n\n". $counter . '. ' . $value->Projek_Outcome . ' ' . $value->Kuantiti . ' '. $temp)->getFont();

            $counter = $counter + 1;
            }
            
        }
        
    }

    private function writeKPICell($row,$collection)
    {
        $oCell1 = $row->nextCell();
        $oCell1->setColSpan(4);
        $counter = 1;
        if($collection) {

            foreach($collection as $value) {
                $oCell1->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setMarginBottom(2)
                ->setMarginLeft(5)
                ->setMarginRight(5)
                ->setMarginTop(2);
            $oCell1->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
                ->setStartColor(new Color('FFFFFF'))
                ->setEndColor(new Color('FFFFFF'));
            $temp = $value->OutputUnit ? $value->OutputUnit->nama_unit:''; 
            $oCell1->createTextRun("\n\n". $counter . '. ' . $value->penerangan . ' ' . $value->kuantiti . ' '.$temp )->getFont();

            $counter = $counter + 1;
            }
            
        }
        
    }


    private function slide5($objPHPPresentation,$project)
    {
        $currentSlide = $this->slideHeadingTemplate($objPHPPresentation,$project,'SLAID B : KERANGKA PENILAIAN PERMOHONAN PROJEK');
        
        // Create a shape (table)
        $shape1 = $currentSlide->createTableShape(4);
        $shape1->setHeight(200);
        $shape1->setWidth(945);
        $shape1->setOffsetX(10);
        $shape1->setOffsetY(140);

        $heading = ['OBJEKTIF','SKOP','OUTPUT','OUTCOME'];

        // Add row
        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFFFFFFF'))
            ->setEndColor(new Color('FFFFFFFF'));

        foreach($heading as $cols) {
            //add cols
            $oCell1 = $row->nextCell();
            $oCell1->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setMarginBottom(2)
                ->setMarginLeft(5)
                ->setMarginRight(5)
                ->setMarginTop(2);
            $oCell1->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
                ->setStartColor(new Color('FFFFFF'))
                ->setEndColor(new Color('FFFFFF'));
            $oCell1->createTextRun($cols)->getFont()->setBold(true);
            $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(10);
        }



        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFFFFFFF'))
            ->setEndColor(new Color('FFFFFFFF'));
        

        $output = \App\Models\OutputPage::where('Permohonan_Projek_id',$project->id)->where('row_status',1)->with('unit')->get();
        $outcome = \App\Models\Outcome::where('Permohonan_Projek_id',$project->id)->where('row_status',1)->with('unit')->get();
        $skops = \App\Models\SkopProject::where('project_id',$project->id)->where('row_status',1)->with('skopOptions')->get();

        $this->writeListCell($row,$project->objektif);
        $this->writeSkopCell($row,$skops);
        $this->writeOutputCell($row,$output);
        $this->writeOutcomeCell($row,$outcome);
        
        // Add row
        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFFFFFFF'))
            ->setEndColor(new Color('FFFFFFFF'));

        $oCell1 = $row->nextCell();
        // $oCell1->setcols
        $oCell1->setColSpan(4);
        $oCell1->getActiveParagraph()->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell1->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new Color('FFFFFF'))
            ->setEndColor(new Color('FFFFFF'));
        $oCell1->createTextRun('KPI')->getFont()->setBold(true);
        $oCell1->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


        // Add row
        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFFFFFFF'))
            ->setEndColor(new Color('FFFFFFFF'));

        $kpi = \App\Models\ProjectKpi::where('project_id',$project->id)->where('row_status',1)->get();
        $this->writeKPICell($row,$kpi);

        return $objPHPPresentation;
        
    }

    private function slide1($objPHPPresentation,$project)
    {

        $user = User::whereId($project->dibuat_oleh)->first();
        // Create slide
        $currentSlide =$objPHPPresentation->createSlide();        

        // Create a shape (drawing)
        $shape = $currentSlide->createDrawingShape();
        $shape->setName('PHPPresentation logo')
            ->setDescription('PHPPresentation logo')
            ->setPath('assets/images/jata.png')
            ->setHeight(140)
            ->setOffsetX(350)
            ->setOffsetY(10);

        // Create a shape (text)
        $Headershape = $currentSlide->createRichTextShape()
            ->setHeight(300)
            ->setWidth(720)
            ->setOffsetX(100)
            ->setOffsetY(150);
        $Headershape->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $textRun = $Headershape->createTextRun('PERMOHONAN PROJEK RMKE-'. $project->rollingPlan->rmk .' ' .$project->rollingPlan->name);
        $textRun->getFont()->setBold(true)
            ->setSize(20);
            // ->setColor(new Color('000000'));

        $Headershape = $currentSlide->createRichTextShape()
            ->setHeight(300)
            ->setWidth(600)
            ->setOffsetX(170)
            ->setOffsetY(210);
        $Headershape->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $textRun = $Headershape->createTextRun('KEMENTERIAN ALAM SEKITAR DAN AIR');
        $textRun->getFont()->setBold(true)
            ->setSize(20);
            // ->setColor(new Color('000000'));

        
        $Bodyshape = $currentSlide->createRichTextShape()
            ->setHeight(250)
            ->setWidth(800)
            ->setOffsetX(80)
            ->setOffsetY(300);
        $Bodyshape->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $textRun = $Bodyshape->createTextRun('TAJUK PROJEK:');
        $textRun->getFont()->setBold(true)
            ->setSize(15);
            // ->setColor(new Color('000000'));


        $Bodyshape = $currentSlide->createRichTextShape()
            ->setHeight(250)
            ->setWidth(900)
            ->setOffsetX(50)
            ->setOffsetY(330);
        $Bodyshape->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $textRun = $Bodyshape->createTextRun($project->nama_projek);
        $textRun->getFont()->setBold(true)
            ->setSize(15);
            // ->setColor(new Color('000000'));


        $kategory = lookupOptionSingle('kategori_project',$project->kategori_Projek);
        // dd($kategory);
        $kategory_value = '';
        if($kategory) {
            $kategory_value = $kategory->value;
        }
        $Bodyshape = $currentSlide->createRichTextShape()
            ->setHeight(250)
            ->setWidth(800)
            ->setOffsetX(80)
            ->setOffsetY(400);
        $Bodyshape->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $textRun = $Bodyshape->createTextRun('KATEGORI PROJEK : ' . $kategory_value);
        $textRun->getFont()->setBold(true)
            ->setSize(15);
            // ->setColor(new Color('000000'));

        $Bodyshape = $currentSlide->createRichTextShape()
            ->setHeight(250)
            ->setWidth(800)
            ->setOffsetX(80)
            ->setOffsetY(450);
        $Bodyshape->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $textRun = $Bodyshape->createTextRun($project->bahagianPemilik->nama_bahagian);
        $textRun->getFont()->setBold(true)
            ->setSize(15);
            // ->setColor(new Color('000000'));


        $Bodyshape = $currentSlide->createRichTextShape()
            ->setHeight(250)
            ->setWidth(800)
            ->setOffsetX(80)
            ->setOffsetY(500);
        $Bodyshape->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $textRun = $Bodyshape->createTextRun('NAMA PEGAWAI IN-CHARGE:  ' . $user->name);
        $textRun->getFont()->setBold(true)
            ->setSize(15);
            // ->setColor(new Color('000000'));


        $Bodyshape = $currentSlide->createRichTextShape()
            ->setHeight(250)
            ->setWidth(800)
            ->setOffsetX(80)
            ->setOffsetY(530);
        $Bodyshape->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $textRun = $Bodyshape->createTextRun('NO.TELEFON: ' . $user->no_telefone);
        $textRun->getFont()->setBold(true)
            ->setSize(15);
            // ->setColor(new Color('000000'));


        $Bodyshape = $currentSlide->createRichTextShape()
            ->setHeight(250)
            ->setWidth(800)
            ->setOffsetX(80)
            ->setOffsetY(560);
        $Bodyshape->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $textRun = $Bodyshape->createTextRun('E-MEL: ' . $user->email);
        $textRun->getFont()->setBold(true)
            ->setSize(15);
            // ->setColor(new Color('000000'));


       return $objPHPPresentation;
    }


    private function slide2($objPHPPresentation,$project)
    {
        $currentSlide =$objPHPPresentation->createSlide();


        $shape = $currentSlide->createTableShape(1);
        $shape->setHeight(200);
        $shape->setWidth(930);
        $shape->setOffsetX(10);
        $shape->setOffsetY(10);

        // Add row Title
        $row = $shape->createRow();
        // $row->setHeight(50);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFFFFFFF'))
            ->setEndColor(new Color('FFFFFFFF'));
        $cell = $row->nextCell();
        
        $cell->createTextRun('SLAID A : PENGENALAN PROJEK')->getFont()->setBold(true)->setSize(12);
        $cell->getBorders()->getLeft()->setLineWidth(2)
            ->setLineStyle(Border::LINE_SINGLE)
            ->setColor(new Color('0000FF'));
        $cell->getBorders()->getRight()->setLineWidth(2)
            ->setLineStyle(Border::LINE_SINGLE)
            ->setColor(new Color('0000FF'));
        $cell->getBorders()->getTop()->setLineWidth(2)
            ->setLineStyle(Border::LINE_SINGLE)
            ->setColor(new Color('0000FF'));
            //->setDashStyle(Border::DASH_DASH);
        $cell->getBorders()->getBottom()->setLineWidth()
            ->setLineStyle(Border::LINE_SINGLE)
            ->setColor(new Color('0000FF'));
            //->setDashStyle(Border::DASH_DASH);
        $cell->getActiveParagraph()->getAlignment()
            ->setMarginLeft(10);


        // Create a shape (table)
        $shape1 = $currentSlide->createTableShape(3);
        $shape1->setHeight(200);
        $shape1->setWidth(930);
        $shape1->setOffsetX(10);
        $shape1->setOffsetY(60);


        // Add row
        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFFFFFFF'))
            ->setEndColor(new Color('FFFFFFFF'));
        $oCell1 = $row->nextCell();
        $oCell1->setWidth(200);
        $oCell1->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell1->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell1->createTextRun('TAJUK PROJEK')->getFont()->setBold(true);
        $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(20);
        $oCell2 = $row->nextCell();
        $oCell2->setWidth(610);
        $oCell2->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell2->createTextRun($project->nama_projek)->getFont()->setBold(true);
        $oCell3 = $row->nextCell();
        $oCell3->setWidth(120);
        $oCell3->createTextRun('')->getFont()->setBold(true);





         // Create a shape (table)
         $shape1 = $currentSlide->createTableShape(5);
         $shape1->setHeight(200);
         $shape1->setWidth(930);
         $shape1->setOffsetX(10);
         $shape1->setOffsetY(95);
 
 
         // Add row
         $row = $shape1->createRow();
         $row->setHeight(20);
         $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
             ->setRotation(90)
             ->setStartColor(new Color('ADD8E6'))
             ->setEndColor(new Color('ADD8E6'));
         $oCell1 = $row->nextCell();
         $oCell1->setWidth(400);
         $oCell1->getActiveParagraph()->getAlignment()
             ->setMarginBottom(2)
             ->setMarginLeft(5)
             ->setMarginRight(5)
             ->setMarginTop(2);
         $oCell1->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
             ->setStartColor(new Color('ADD8E6'))
             ->setEndColor(new Color('ADD8E6'));
         $oCell1->createTextRun('BIDANG KEUTAMAAN RANCANGAN MALAYSIA Ke 12 (RMKe-12)')->getFont()->setBold(true);
         $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(20);
         $oCell2 = $row->nextCell();
         $oCell2->setWidth(200);
         $oCell2->getActiveParagraph()->getAlignment()
             ->setMarginBottom(2)
             ->setMarginLeft(5)
             ->setMarginRight(5)
             ->setMarginTop(2);
         $oCell2->createTextRun('STRATEGI BIDANG KEUTAMAAN RMKe-12')->getFont()->setBold(true);
         $oCell3 = $row->nextCell();
         $oCell3->setWidth(105);
         $oCell3->createTextRun('KATEGORI PROJEK')->getFont()->setBold(true);
         $oCell4 = $row->nextCell();
         $oCell4->setWidth(105);
         $oCell4->createTextRun('CREATIVITY INDEX (CI)')->getFont()->setBold(true);
         $oCell5 = $row->nextCell();
         $oCell5->setWidth(120);
         $oCell5->createTextRun('SDG GOALS')->getFont()->setBold(true);

         $rmk_collections = \App\Models\RMKSDGSasaranIndikator::where('permohonan_projek_id',$project->id)->with(['sdg'])->get();
         $sdgGoals = [];
         foreach($rmk_collections as $rmk){
             if($rmk->row_status == 1) {
                array_push($sdgGoals,$rmk->sdg->kod_sdg);
             }
            
         }
         // Add row
         
         $row = $shape1->createRow();
         $row->setHeight(20);
         $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
             ->setRotation(90)
             ->setStartColor(new Color('FFFFFF'))
             ->setEndColor(new Color('FFFFFF'));
         $oCell1 = $row->nextCell();
         $oCell1->setWidth(400);
         $oCell1->getActiveParagraph()->getAlignment()
             ->setMarginBottom(2)
             ->setMarginLeft(5)
             ->setMarginRight(5)
             ->setMarginTop(2)
             ->setHorizontal(Alignment::HORIZONTAL_CENTER);
         $oCell1->createTextRun($project->RmkObbSdg ? $project->RmkObbSdg->Bidang_Keutamaan : '' )->getFont();
         $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(20);
         $oCell2 = $row->nextCell();
         $oCell2->setWidth(200);
         $oCell2->getActiveParagraph()->getAlignment()
             ->setMarginBottom(2)
             ->setMarginLeft(5)
             ->setMarginRight(5)
             ->setMarginTop(2)
             ->setHorizontal(Alignment::HORIZONTAL_CENTER);
         $oCell2->createTextRun($project->RmkObbSdg ? $project->RmkObbSdg->strategi->nama_strategi : '')->getFont();
         $oCell3 = $row->nextCell();
         $oCell3->setWidth(105);
         $oCell3->getActiveParagraph()->getAlignment()
             ->setMarginBottom(2)
             ->setMarginLeft(5)
             ->setMarginRight(5)
             ->setMarginTop(2)
             ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $kategory = lookupOptionSingle('kategori_project',$project->kategori_Projek);
        $kategory_value = '';
        if($kategory) {
            $kategory_value = $kategory->value;
        }
         $oCell3->createTextRun($kategory_value)->getFont();
         $oCell4 = $row->nextCell();
         $oCell4->setWidth(105);
         $oCell4->getActiveParagraph()->getAlignment()
             ->setMarginBottom(2)
             ->setMarginLeft(5)
             ->setMarginRight(5)
             ->setMarginTop(2)
             ->setHorizontal(Alignment::HORIZONTAL_CENTER);
         $oCell4->createTextRun('16.80')->getFont();
         $oCell5 = $row->nextCell();
         $oCell5->setWidth(120);
         $oCell5->getActiveParagraph()->getAlignment()
             ->setMarginBottom(2)
             ->setMarginLeft(5)
             ->setMarginRight(5)
             ->setMarginTop(2)
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
         $oCell5->createTextRun(implode(",", $sdgGoals))->getFont();


        // Create a shape (table)
        $shape1 = $currentSlide->createTableShape(4);
        $shape1->setHeight(200);
        $shape1->setWidth(930);
        $shape1->setOffsetX(10);
        $shape1->setOffsetY(215);

        // Add row
        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell1 = $row->nextCell();
        $oCell1->setWidth(200);
        $oCell1->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $oCell1->createTextRun('ANGGARAN KEPERLUAN DE')->getFont()->setBold(true);
        $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(20);
        $oCell2 = $row->nextCell();
        $oCell2->setWidth(200);
        $oCell2->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $oCell2->createTextRun('TEMPOH PROJEK')->getFont()->setBold(true);
        $oCell2->getActiveParagraph()->getAlignment()->setMarginLeft(20);
        $oCell3 = $row->nextCell();
        $oCell3->setWidth(200);
        $oCell3->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $oCell3->createTextRun('CADANGAN KERJASAMA')->getFont()->setBold(true);
        $oCell3->getActiveParagraph()->getAlignment()->setMarginLeft(20);
        $oCell4 = $row->nextCell();
        $oCell4->setWidth(330);
        $oCell4->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $oCell4->createTextRun('NOTA TAMBAHAN (KELULUSAN KHAS SEKIRANYA ADA)')->getFont()->setBold(true);
        $oCell4->getActiveParagraph()->getAlignment()->setMarginLeft(20);



        // Create a shape (table)
        $shape1 = $currentSlide->createTableShape(1);
        $shape1->setHeight(200);
        $shape1->setWidth(400);
        $shape1->setOffsetX(10);
        $shape1->setOffsetY(255);

        // Add row
        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFFFFF'))
            ->setEndColor(new Color('FFFFFF'));
        $oCell1 = $row->nextCell();
        $oCell1->setWidth(200);
        $oCell1->getActiveParagraph()->getAlignment()
            // ->setMarginBottom(2)
            // ->setMarginLeft(1)
            // ->setMarginRight(1)
            // ->setMarginTop(2)
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $oCell1->createTextRun('Kos keseluruhan: RM195,000,000.00
        RMKe-11: -
        RMKe-12: RM46,927,089.40
        RMKe-13: RM148,072,910.60')->getFont();
        $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(20);



        // Create a shape (table)
        $shape1 = $currentSlide->createTableShape(2);
        $shape1->setHeight(200);
        $shape1->setWidth(200);
        $shape1->setOffsetX(210);
        $shape1->setOffsetY(255);



        // Add row
        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFFFFF'))
            ->setEndColor(new Color('FFFFFF'));
        $oCell1 = $row->nextCell();
        $oCell1->setWidth(100);
        $oCell1->getActiveParagraph()->getAlignment()
            // ->setMarginBottom(2)
            // ->setMarginLeft(1)
            // ->setMarginRight(1)
            // ->setMarginTop(2)
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $oCell1->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
        ->setStartColor(new Color('ADD8E6'))
        ->setEndColor(new Color('ADD8E6'));
        $oCell1->createTextRun('Tempoh Pelaksanaan')->getFont();
        $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(3);
        $oCell2 = $row->nextCell();
        $oCell2->setWidth(100);
        $oCell2->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2)
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $oCell2->createTextRun($project->tempoh_pelaksanaan . ' Bulan')->getFont()->setBold(true);
        $oCell2->getActiveParagraph()->getAlignment()->setMarginLeft(20);

        // Add row
        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFFFFF'))
            ->setEndColor(new Color('FFFFFF'));
        $oCell1 = $row->nextCell();
        $oCell1->setWidth(100);
        $oCell1->getActiveParagraph()->getAlignment()
            // ->setMarginBottom(2)
            // ->setMarginLeft(1)
            // ->setMarginRight(1)
            // ->setMarginTop(2)
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $oCell1->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
        ->setStartColor(new Color('ADD8E6'))
        ->setEndColor(new Color('ADD8E6'));
        $oCell1->createTextRun('Tahun Mula')->getFont();
        $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(3);
        $oCell2 = $row->nextCell();
        $oCell2->setWidth(100);
        $oCell2->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $oCell2->createTextRun($project->tahun_jangka_mula)->getFont()->setBold(true);
        $oCell2->getActiveParagraph()->getAlignment()->setMarginLeft(20);

        // Add row
        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFFFFF'))
            ->setEndColor(new Color('FFFFFF'));
        $oCell1 = $row->nextCell();
        $oCell1->setWidth(100);
        $oCell1->getActiveParagraph()->getAlignment()
            // ->setMarginBottom(2)
            // ->setMarginLeft(1)
            // ->setMarginRight(1)
            // ->setMarginTop(2)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $oCell1->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
        ->setStartColor(new Color('ADD8E6'))
        ->setEndColor(new Color('ADD8E6'));
        $oCell1->createTextRun('Tahun Jangka Siap')->getFont();
        $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(3);
        $oCell2 = $row->nextCell();
        $oCell2->setWidth(100);
        $oCell2->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $oCell2->createTextRun($project->tahun_jangka_siap)->getFont()->setBold(true);
        $oCell2->getActiveParagraph()->getAlignment()->setMarginLeft(20);





        // Create a shape (table)
        $shape1 = $currentSlide->createTableShape(2);
        $shape1->setHeight(200);
        $shape1->setWidth(400);
        $shape1->setOffsetX(10);
        $shape1->setOffsetY(385);

        // Add row
        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell1 = $row->nextCell();
        $oCell1->setWidth(200);
        $oCell1->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $oCell1->createTextRun('Permohonan RP3 (2023): RM80,000.00')->getFont();
        $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(20);
        $oCell2 = $row->nextCell();
        $oCell2->setWidth(200);
        $oCell2->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $oCell2->createTextRun('Status Pelan Induk / Kajian: (jika berkaitan)')->getFont();
        $oCell2->getActiveParagraph()->getAlignment()->setMarginLeft(20);




        // Create a shape (table)
        $shape1 = $currentSlide->createTableShape(2);
        $shape1->setHeight(200);
        $shape1->setWidth(200);
        $shape1->setOffsetX(10);
        $shape1->setOffsetY(425);

        $lokasi = \App\Models\ProjectNegeriLokas::where('permohonan_Projek_id',$project->id)
                    ->where('row_status',1)
                    ->with(['negeri','daerah','parlimen','dun'])
                    ->first();
        // Add row
        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell1 = $row->nextCell();
        $oCell1->setWidth(100);
        $oCell1->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $oCell1->createTextRun('Negeri')->getFont();
        $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(20);
        $oCell2 = $row->nextCell();
        $oCell2->setWidth(100);
        $oCell2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFFFFF'))
            ->setEndColor(new Color('FFFFFF'));
        $oCell2->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        if($lokasi) {
            $oCell2->createTextRun($lokasi->negeri ? $lokasi->negeri->nama_negeri : '')->getFont();
        }
        
        $oCell2->getActiveParagraph()->getAlignment()->setMarginLeft(20);

        // Add row
        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell1 = $row->nextCell();
        $oCell1->setWidth(100);
        $oCell1->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $oCell1->createTextRun('Daerah')->getFont();
        $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(20);
        $oCell2 = $row->nextCell();
        $oCell2->setWidth(100);
        $oCell2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFFFFF'))
            ->setEndColor(new Color('FFFFFF'));
        $oCell2->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        if($lokasi) {
            $oCell2->createTextRun($lokasi->daerah ? $lokasi->daerah->nama_daerah : '')->getFont();
        }
        $oCell2->getActiveParagraph()->getAlignment()->setMarginLeft(20);

        // Add row
        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell1 = $row->nextCell();
        $oCell1->setWidth(100);
        $oCell1->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $oCell1->createTextRun('Parlimen')->getFont();
        $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(20);
        $oCell2 = $row->nextCell();
        $oCell2->setWidth(100);
        $oCell2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFFFFF'))
            ->setEndColor(new Color('FFFFFF'));
        $oCell2->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        if($lokasi) {
            $oCell2->createTextRun($lokasi->parlimen_id ? $lokasi->parlimen->nama_parlimen : '')->getFont();
        }
        $oCell2->getActiveParagraph()->getAlignment()->setMarginLeft(20);

        // Add row
        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell1 = $row->nextCell();
        $oCell1->setWidth(100);
        $oCell1->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $oCell1->createTextRun('DUN')->getFont();
        $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(20);
        $oCell2 = $row->nextCell();
        $oCell2->setWidth(100);
        $oCell2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFFFFF'))
            ->setEndColor(new Color('FFFFFF'));
        $oCell2->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        if($lokasi) {
            $oCell2->createTextRun($lokasi->dun_id ? $lokasi->dun->nama_dun : '')->getFont();
        }
        $oCell2->getActiveParagraph()->getAlignment()->setMarginLeft(20);

         // Create a shape (table)
         $shape1 = $currentSlide->createTableShape(1);
         $shape1->setHeight(200);
         $shape1->setWidth(200);
         $shape1->setOffsetX(210);
         $shape1->setOffsetY(425);
 
         // Add row
         $row = $shape1->createRow();
         $row->setHeight(20);
         $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
             ->setRotation(90)
             ->setStartColor(new Color('FFFFFF'))
             ->setEndColor(new Color('FFFFFF'));
         $oCell1 = $row->nextCell();
         $oCell1->setWidth(200);
         $oCell1->getActiveParagraph()->getAlignment()
             ->setMarginBottom(2)
             ->setMarginLeft(5)
             ->setMarginRight(5)
             ->setMarginTop(2)
             ->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $kajian_array = [];
        $kajian_counter = 1;
         foreach($project->kajianProjects as $kajian) {
             if($kajian->kategori_hakisan) {
                array_push($kajian_array,$kajian_counter .'. Natonal Coastal Erosion Study (' .$kajian->tahun_siap_terkini . ')');
             }else {
                array_push($kajian_array,$kajian_counter . '. ' .$kajian->nama_laporan . ' (' .$kajian->tahun_siap_terkini . ')');
             }
             $kajian_counter = $kajian_counter + 1;
         }

         switch ($project->kajian) {
             case '0':
                # code...
                $oCell1->createTextRun('Tidak')->getFont();
                break;

             case '1':
                 # code...
                 $oCell1->createTextRun(implode("\n", $kajian_array))->getFont();
                 break;

            case '2':
                # code...
                $oCell1->createTextRun('Tidak Berkaitan')->getFont();
                break;
             
             default:
                 # code...
                 $oCell1->createTextRun('')->getFont();
                 break;
         }
         
        $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(10);

         // Create a shape (table)
         $shape1 = $currentSlide->createTableShape(1);
         $shape1->setHeight(200);
         $shape1->setWidth(200);
         $shape1->setOffsetX(410);
         $shape1->setOffsetY(255);
 
         // Add row
         $row = $shape1->createRow();
         $row->setHeight(20);
         $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
             ->setRotation(90)
             ->setStartColor(new Color('FFFFFF'))
             ->setEndColor(new Color('FFFFFF'));
         $oCell1 = $row->nextCell();
         $oCell1->setWidth(200);
         $oCell1->getActiveParagraph()->getAlignment()
             ->setMarginBottom(2)
             ->setMarginLeft(5)
             ->setMarginRight(5)
             ->setMarginTop(2)
             ->setHorizontal(Alignment::HORIZONTAL_LEFT);
         $oCell1->createTextRun('')->getFont();
         $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(10);




         // Create a shape (table)
         $shape1 = $currentSlide->createTableShape(1);
         $shape1->setHeight(200);
         $shape1->setWidth(330);
         $shape1->setOffsetX(610);
         $shape1->setOffsetY(255);
 
         // Add row
         $row = $shape1->createRow();
         $row->setHeight(20);
         $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
             ->setRotation(90)
             ->setStartColor(new Color('FFFFFF'))
             ->setEndColor(new Color('FFFFFF'));
         $oCell1 = $row->nextCell();
         $oCell1->setWidth(330);
         
         $oCell1->getActiveParagraph()->getAlignment()
             ->setMarginBottom(2)
             ->setMarginLeft(5)
             ->setMarginRight(5)
             ->setMarginTop(2)
             ->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $doc = new DOMDocument();
        $doc->loadHTML($project->nota_tambahan);
        $doc->saveHTML();
        $items = $doc->getElementsByTagName('li');
        $array_doc = [];
        $counter = 1;
        if(count($items) > 0) //Only if tag1 items are found 
        {
            foreach ($items as $tag1)
            {
                array_push($array_doc,$counter . '. '.  $tag1->nodeValue);
                 $counter = $counter + 1;
            }
        }

        switch ($project->kululusan_khas) {
            case '0':
               # code...
               $oCell1->createTextRun('Tidak')->getFont();
               break;

            case '1':
                # code...
                $oCell1->createTextRun(implode("\n", $array_doc))->getFont();
                break;

           case '2':
               # code...
               $oCell1->createTextRun('Tidak Berkaitan')->getFont();
               break;
            
            default:
                # code...
                $oCell1->createTextRun('')->getFont();
                break;
        }

        



        return $objPHPPresentation;

    }

    
    private function slide6($objPHPPresentation,$project)
    {
        $currentSlide = $this->slideHeadingTemplate($objPHPPresentation,$project,'TIMELINE PELAKSANAAN PROJEK (PERINCIAN)');

        $total_cols = ($project->tempoh_pelaksanaan / 12)  + 3;
        $start_year = $project->tahun_jangka_mula;
        $cols_headings = ['BIL','SKOP','KOS (RM)'];

        $total_years = $project->tempoh_pelaksanaan / 12;
        for ($x = 1; $x <= $total_years; $x++) {
            array_push($cols_headings,$start_year);
            $start_year = $start_year + 1;
            } 
// dd($total_cols,$cols_headings);
        // Create a shape (table)
        $shape1 = $currentSlide->createTableShape($total_cols);
        $shape1->setHeight(200);
        $shape1->setWidth(945);
        $shape1->setOffsetX(10);
        $shape1->setOffsetY(160);

        // Add row
        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));

        foreach($cols_headings as $headings){
            $oCell = $row->nextCell();
            // $oCell8->setWidth(100);
            $oCell->getActiveParagraph()->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setMarginBottom(2)
                ->setMarginLeft(5)
                ->setMarginRight(5)
                ->setMarginTop(2);
            $oCell->createTextRun($headings)->getFont()->setBold(true);
        }

        $details = \App\Models\KewanganSkopSilling::where('permohonan_projek_id',$project->id)->where('row_status',1)->with('skop.skopOptions')->get()->toArray();
        $row_counter = 1;
        $sum = [
            'jumlah' => 0,
            'siling_yr1' => 0,
            'siling_yr2' => 0,
            'siling_yr3' => 0,
            'siling_yr4' => 0,
            'siling_yr5' => 0,
            'siling_yr6' => 0,
            'siling_yr7' => 0,
            'siling_yr8' => 0,
            'siling_yr9' => 0,
            'siling_yr1' => 0,
            'siling_yr10' => 0,
        ];
        foreach($details as $siling) {
            // Add row

            $row = $shape1->createRow();
            $row->setHeight(20);
            $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
                ->setRotation(90)
                ->setStartColor(new Color('FFFFFFFF'))
                ->setEndColor(new Color('FFFFFFFF'));

            $oCell = $row->nextCell();
            // $oCell8->setWidth(100);
            $oCell->getActiveParagraph()->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setMarginBottom(2)
                ->setMarginLeft(5)
                ->setMarginRight(5)
                ->setMarginTop(2);
            $oCell->createTextRun($row_counter)->getFont();

            $oCell = $row->nextCell();
            // $oCell8->setWidth(100);
            $oCell->getActiveParagraph()->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setMarginBottom(2)
                ->setMarginLeft(5)
                ->setMarginRight(5)
                ->setMarginTop(2);
            if(array_key_exists('skop_options',$siling['skop'])) {
                $oCell->createTextRun($siling['skop']['skop_options']['skop_name'])->getFont();
            }else {
                $oCell->createTextRun('')->getFont();
            }
            
            $oCell = $row->nextCell();
            // $oCell8->setWidth(100);
            $oCell->getActiveParagraph()->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setMarginBottom(2)
                ->setMarginLeft(5)
                ->setMarginRight(5)
                ->setMarginTop(2);
            $oCell->createTextRun(number_format($siling['jumlah_kos'],2))->getFont();
            $sum['jumlah'] = $sum['jumlah'] + $siling['jumlah_kos'];
            for ($x = 1; $x <= $total_years; $x++) {
                    $col_name = 'siling_yr' . $x;
                    $oCell = $row->nextCell();
                    // $oCell8->setWidth(100);
                    $oCell->getActiveParagraph()->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setMarginBottom(2)
                        ->setMarginLeft(5)
                        ->setMarginRight(5)
                        ->setMarginTop(2);
                    $oCell->createTextRun(number_format($siling[$col_name],2) )->getFont();
                    $sum[$col_name] = $sum[$col_name] + $siling[$col_name];
                } 
                $row_counter = $row_counter + 1;
        }

         // Add row
         $row = $shape1->createRow();
         $row->setHeight(20);
         $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
             ->setRotation(90)
             ->setStartColor(new Color('ADD8E6'))
             ->setEndColor(new Color('ADD8E6'));

        $oCell = $row->nextCell();
        $oCell->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell->createTextRun('')->getFont();

        $oCell = $row->nextCell();
        $oCell->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell->createTextRun('JUMLAH (RM)')->getFont()->setBold(true);

        $oCell = $row->nextCell();
        $oCell->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell->createTextRun(number_format($sum['jumlah']))->getFont()->setBold(true);
        for ($x = 1; $x <= $total_years; $x++) {
            $col_name = 'siling_yr' . $x;
            $oCell = $row->nextCell();
            // $oCell8->setWidth(100);
            $oCell->getActiveParagraph()->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setMarginBottom(2)
                ->setMarginLeft(5)
                ->setMarginRight(5)
                ->setMarginTop(2);
            $oCell->createTextRun(number_format($sum[$col_name],2) )->getFont()->setBold(true);
        } 

        return $objPHPPresentation;
    }



    private function slide7($objPHPPresentation,$project)
    {
        
        $currentSlide = $this->slideHeadingTemplate($objPHPPresentation,$project,'TIMELINE PELAKSANAAN PROJEK (PERINCIAN)');

        $total_cols = ($project->tempoh_pelaksanaan / 12)  + 3;
        $start_year = $project->tahun_jangka_mula;
        $cols_headings = ['BIL','SKOP','KOS (RM)'];

        $total_years = $project->tempoh_pelaksanaan / 12;
        for ($x = 1; $x <= $total_years; $x++) {
            array_push($cols_headings,$start_year);
            $start_year = $start_year + 1;
            } 
// dd($total_cols,$cols_headings);
        // Create a shape (table)
        $shape1 = $currentSlide->createTableShape($total_cols);
        $shape1->setHeight(200);
        $shape1->setWidth(945);
        $shape1->setOffsetX(10);
        $shape1->setOffsetY(160);

        // Add row
        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));

        foreach($cols_headings as $headings){
            $oCell = $row->nextCell();
            // $oCell8->setWidth(100);
            $oCell->getActiveParagraph()->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setMarginBottom(2)
                ->setMarginLeft(5)
                ->setMarginRight(5)
                ->setMarginTop(2);
            $oCell->createTextRun($headings)->getFont()->setBold(true);
        }
        $const_width = 945;
        $width_size =  $const_width / count($cols_headings);
        $details = \App\Models\KewanganBayaranSukuTahunan::where('permohonan_projek_id',$project->id)->where('row_status',1)->with('skop')->get()->toArray();
        $row_counter = 1;

        $shape1 = $currentSlide->createTableShape(($total_years * 4) + 3);
        $shape1->setHeight(200);
        $shape1->setWidth(945);
        $shape1->setOffsetX(10);
        $shape1->setOffsetY(200);

        foreach($details as $siling) {
            // Add row
            $row = $shape1->createRow();
            $row->setHeight(20);
            $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
                ->setRotation(90)
                ->setStartColor(new Color('FFFFFFFF'))
                ->setEndColor(new Color('FFFFFFFF'));

            $oCell = $row->nextCell();
            $oCell->setWidth($width_size);
            $oCell->getActiveParagraph()->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setMarginBottom(2)
                ->setMarginLeft(5)
                ->setMarginRight(5)
                ->setMarginTop(2);
            $oCell->createTextRun($row_counter)->getFont();

            $oCell = $row->nextCell();
            $oCell->setWidth($width_size);
            $oCell->getActiveParagraph()->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setMarginBottom(2)
                ->setMarginLeft(5)
                ->setMarginRight(5)
                ->setMarginTop(2);
            if(array_key_exists('skop_name',$siling['skop'])) {
                $oCell->createTextRun($siling['skop']['skop_name'])->getFont();
            }else {
                $oCell->createTextRun('')->getFont();
            }
            // $oCell->createTextRun($siling['skop']['skop_name'])->getFont();

            $oCell = $row->nextCell();
            $oCell->setWidth($width_size);
            $oCell->getActiveParagraph()->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setMarginBottom(2)
                ->setMarginLeft(5)
                ->setMarginRight(5)
                ->setMarginTop(2);
            $oCell->createTextRun(number_format(0,2))->getFont();
            // $oCell->createTextRun(number_format($siling['jumlah_kos'],2))->getFont();
            // $sum['jumlah'] = $sum['jumlah'] + $siling['jumlah_kos'];
            $counter = 1;
            
            for ($x = 1; $x <= $total_years; $x++) {
                $year_width_size = ($width_size / 4 ) + 0.45;
                for ($y = 1; $y <= 4; $y++) {
                    $col_name = 'yr1_quarters' . $counter;
                    $oCell = $row->nextCell();
                    $oCell->setWidth($year_width_size);
                    $oCell->getActiveParagraph()->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setMarginBottom(2)
                        ->setMarginLeft(5)
                        ->setMarginRight(5)
                        ->setMarginTop(2);
                    $value = '-';
                    if($siling[$col_name] == 1) {
                        $value = '√';
                    }
                    $oCell->createTextRun($value)->getFont();
                    // $sum[$col_name] = $sum[$col_name] + $siling[$col_name];

                    $counter = $counter + 1;
                }
                    
                } 
                $row_counter = $row_counter + 1;
        }
        
        $shape1 = $currentSlide->createTableShape($total_cols);
        $shape1->setHeight(200);
        $shape1->setWidth(945);
        $shape1->setOffsetX(10);
        $shape1->setOffsetY(560);

        // Add row        
        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFFFFFFF'))
            ->setEndColor(new Color('FFFFFFFF'));

        $oCell = $row->nextCell();
        // $oCell8->setWidth(100);
        $oCell->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell->createTextRun('')->getFont();

        $oCell = $row->nextCell();
        // $oCell8->setWidth(100);
        $oCell->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell->createTextRun('JUMLAH (RM)')->getFont();

        $oCell = $row->nextCell();
        // $oCell8->setWidth(100);
        $oCell->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell->createTextRun(number_format(0,2))->getFont();
        
        for ($x = 1; $x <= $total_years; $x++) {
                // $col_name = 'siling_yr' . $x;
                $oCell = $row->nextCell();
                // $oCell8->setWidth(100);
                $oCell->getActiveParagraph()->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setMarginBottom(2)
                    ->setMarginLeft(5)
                    ->setMarginRight(5)
                    ->setMarginTop(2);
                $oCell->createTextRun(number_format(0,2) )->getFont();
                // $sum[$col_name] = $sum[$col_name] + $siling[$col_name];
            } 
            // $row_counter = $row_counter + 1;
            
        return $objPHPPresentation;
    }

    private function kewanganDetails($objPHPPresentation,$project)
    {
        $skopDetails = \App\Models\SkopProject::where('project_id',$project->id)
        ->with(['skopOptions'])
        ->with(['subskopProjects.subSkopOptions' => function ($query) {
            $query->where('row_status', 1)->get();
        }])
        ->with(['subskopProjects.subsubskopProjects' => function ($query) {
            $query->where('row_status', 1)->get();
        }])
        ->get();

        // foreach($skopDetails as $skop) {
        //     // dump($skop->skopOptions->skop_name,$skop->cost);
        //     dump($skop->skopOptions->skop_name);
        //     foreach($skop->subskopProjects as $subSkop){
        //         dump($subSkop->subSkopOptions->sub_skop_name);
        //         if($subSkop->subsubskopProjects->count() > 0) {
        //             // dump($subSkop->subsubskopProjects->count());
        //             foreach($subSkop->subsubskopProjects as $subsubskop) {
        //                 dump($subsubskop->nama_componen);
        //             }
                    
        //         }
                
        //     }

        // }

        $rowCount = 1;
        $recordCounter = 0;
        $currentSlide = $this->slideHeadingTemplate($objPHPPresentation,$project,'PECAHAN KOS PELAKSANAAN PROJEK (PERINCIAN)');
        $shape1 = $this->kewanganHeading($objPHPPresentation,$project,$currentSlide);
        $total_cost = 0;

        foreach($skopDetails as $skop) {
            // dump($skop->skopOptions->skop_name,$skop->cost);
            $recordCounter = $recordCounter + 1;
            if($rowCount == 7) {
                $this->subtotal($shape1,'Sub Total Carried Forward (RM)',$total_cost);
                $currentSlide = $this->slideHeadingTemplate($objPHPPresentation,$project,'PECAHAN KOS PELAKSANAAN PROJEK (PERINCIAN)');
                $shape1 = $this->kewanganHeading($objPHPPresentation,$project,$currentSlide);
                $this->subtotal($shape1,'Sub Total Brought Forward (RM)',$total_cost);
                $rowCount = 1;

            }
            $this->kewanganRowDetails( $shape1,$skop->skopOptions->skop_name,$skop->cost,'','','','',$recordCounter);
            $total_cost = $total_cost + $skop->cost;
            
            $rowCount = $rowCount +1;
            // dump($skop->skopOptions->skop_name);
            $recordsubCounter = 0;
            
            foreach($skop->subskopProjects as $subSkop){
                $recordsubCounter = $recordsubCounter + 1;
                if($rowCount == 7) {
                    $this->subtotal($shape1,'Sub Total Carried Forward (RM)',$total_cost);
                    $currentSlide = $this->slideHeadingTemplate($objPHPPresentation,$project,'PECAHAN KOS PELAKSANAAN PROJEK (PERINCIAN)');
                    $shape1 = $this->kewanganHeading($objPHPPresentation,$project,$currentSlide);
                    $this->subtotal($shape1,'Sub Total Brought Forward (RM)',$total_cost);
                    $rowCount = 1;
    
                }
                $this->kewanganRowDetails( $shape1,$subSkop->subSkopOptions->sub_skop_name,$subSkop->jumlahkos,$subSkop->Kuantiti,$subSkop->units,$subSkop->Kos,$subSkop->Catatan, $recordCounter .'.' .$recordsubCounter);
                
                $rowCount = $rowCount +1;
                // dump($subSkop->subSkopOptions->sub_skop_name);
                if($subSkop->subsubskopProjects->count() > 0) {
                    // dump($subSkop->subsubskopProjects->count());
                    $recordsubsubCounter = 0;
                    
                    foreach($subSkop->subsubskopProjects as $subsubskop) {
                        $recordsubsubCounter = $recordsubsubCounter + 1;
                        if($rowCount == 7) {
                            $this->subtotal($shape1,'Sub Total Carried Forward (RM)',$total_cost);
                            $currentSlide = $this->slideHeadingTemplate($objPHPPresentation,$project,'PECAHAN KOS PELAKSANAAN PROJEK (PERINCIAN)');
                            $shape1 = $this->kewanganHeading($objPHPPresentation,$project,$currentSlide);
                            $this->subtotal($shape1,'Sub Total Brought Forward (RM)',$total_cost);
                            $rowCount = 1;
            
                        }
                        $this->kewanganRowDetails( $shape1,$subsubskop->nama_componen,$subSkop->jumlahkos,$subSkop->Kuantiti,$subSkop->units,$subSkop->Kos,$subSkop->Catatan,$recordCounter .'.' .$recordsubCounter  . '.'. $recordsubsubCounter);
                        
                        $rowCount = $rowCount +1;
                        // dump($subsubskop->nama_componen);
                    }
                    
                }
                
            }

        }
        $this->subtotal($shape1,'TOTAL (RM)',$total_cost);

        return $objPHPPresentation;
    }

    private function kewanganRowDetails($shape1,$heading,$jumlah,$kuantiti,$unit,$kos_unit,$catatan,$counter)
    {
// Add row
        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFFFFFFF'))
            ->setEndColor(new Color('FFFFFFFF'));
        $oCell1 = $row->nextCell();
        $oCell1->getActiveParagraph()->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell1->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell1->createTextRun($counter)->getFont()->setBold(true);
        $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(20);
        $oCell2 = $row->nextCell();
        $oCell2->getActiveParagraph()->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell2->createTextRun($heading)->getFont()->setBold(true);
        $oCell2->getActiveParagraph()->getAlignment()->setMarginLeft(20);

        $oCell3 = $row->nextCell();
        $oCell3->getActiveParagraph()->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell3->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell3->createTextRun($jumlah)->getFont()->setBold(true);
        $oCell3->getActiveParagraph()->getAlignment()->setMarginLeft(20);

        $oCell4 = $row->nextCell();
        $oCell4->getActiveParagraph()->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell4->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell4->createTextRun($kuantiti)->getFont()->setBold(true);
        $oCell4->getActiveParagraph()->getAlignment()->setMarginLeft(20);

        $oCell5 = $row->nextCell();
        $oCell5->getActiveParagraph()->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell5->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell5->createTextRun($unit)->getFont()->setBold(true);
        $oCell5->getActiveParagraph()->getAlignment()->setMarginLeft(20);

        $oCell6 = $row->nextCell();
        $oCell6->getActiveParagraph()->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell6->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell6->createTextRun($kos_unit)->getFont()->setBold(true);
        $oCell6->getActiveParagraph()->getAlignment()->setMarginLeft(20);

        $oCell7 = $row->nextCell();
        $oCell7->getActiveParagraph()->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell7->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell7->createTextRun($catatan ? $catatan: '')->getFont()->setBold(true);
        $oCell7->getActiveParagraph()->getAlignment()->setMarginLeft(20);
    }


    private function subtotal($shape1,$heading,$jumlah)
    {
        // Add row
        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFFFFFFF'))
            ->setEndColor(new Color('FFFFFFFF'));
        $oCell1 = $row->nextCell();
        $oCell1->getActiveParagraph()->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell1->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell1->createTextRun('')->getFont()->setBold(true);
        $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(20);
        $oCell2 = $row->nextCell();
        $oCell2->getActiveParagraph()->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell2->createTextRun($heading)->getFont()->setBold(true);
        $oCell2->getActiveParagraph()->getAlignment()->setMarginLeft(20);

        $oCell3 = $row->nextCell();
        $oCell3->getActiveParagraph()->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell3->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell3->createTextRun($jumlah)->getFont()->setBold(true);
        $oCell3->getActiveParagraph()->getAlignment()->setMarginLeft(20);

    }


    private function kewanganHeading($objPHPPresentation,$project, $currentSlide)
    {
        // Create a shape (table)
        $shape1 = $currentSlide->createTableShape(7);
        $shape1->setHeight(200);
        $shape1->setWidth(945);
        $shape1->setOffsetX(10);
        $shape1->setOffsetY(110);


        // Add row
        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFFFFFFF'))
            ->setEndColor(new Color('FFFFFFFF'));
        $oCell1 = $row->nextCell();
        
        $oCell1->getActiveParagraph()->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell1->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell1->createTextRun('BIL')->getFont()->setBold(true);
        $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(20);
        $oCell2 = $row->nextCell();
        $oCell2->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell2->createTextRun('SKOP & KOMPONEN')->getFont()->setBold(true);
        $oCell3 = $row->nextCell();
        $oCell3->getActiveParagraph()->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell3->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell3->createTextRun('JUMLAH KOS (RM)')->getFont()->setBold(true);
        $oCell4 = $row->nextCell();
        $oCell4->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell4->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell4->createTextRun('Kuantiti')->getFont()->setBold(true);
        $oCell5 = $row->nextCell();
        $oCell5->getActiveParagraph()->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell5->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell5->createTextRun('Unit/ Pekali (m/m2/%/etc)')->getFont()->setBold(true);
        $oCell6 = $row->nextCell();
        $oCell6->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell6->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell6->createTextRun('Kos/unit (RM)')->getFont()->setBold(true);
        $oCell7 = $row->nextCell();
        $oCell7->getActiveParagraph()->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell7->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell7->createTextRun('Catatan')->getFont()->setBold(true);

        return $shape1;
    }

    private function slideHeadingTemplate($objPHPPresentation,$project,$mainHeading)
    {
        $currentSlide =$objPHPPresentation->createSlide();


        $shape = $currentSlide->createTableShape(1);
        $shape->setHeight(200);
        $shape->setWidth(945);
        $shape->setOffsetX(10);
        $shape->setOffsetY(10);

         // Add row Title
         $row = $shape->createRow();
         // $row->setHeight(50);
         $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
             ->setRotation(90)
             ->setStartColor(new Color('FFFFFFFF'))
             ->setEndColor(new Color('FFFFFFFF'));
         $cell = $row->nextCell();
         
         $cell->createTextRun($mainHeading)->getFont()->setBold(true)->setSize(12);
         $cell->getBorders()->getLeft()->setLineWidth(2)
             ->setLineStyle(Border::LINE_SINGLE)
             ->setColor(new Color('0000FF'));
         $cell->getBorders()->getRight()->setLineWidth(2)
             ->setLineStyle(Border::LINE_SINGLE)
             ->setColor(new Color('0000FF'));
         $cell->getBorders()->getTop()->setLineWidth(2)
             ->setLineStyle(Border::LINE_SINGLE)
             ->setColor(new Color('0000FF'));
             //->setDashStyle(Border::DASH_DASH);
         $cell->getBorders()->getBottom()->setLineWidth()
             ->setLineStyle(Border::LINE_SINGLE)
             ->setColor(new Color('0000FF'));
             //->setDashStyle(Border::DASH_DASH);
         $cell->getActiveParagraph()->getAlignment()
             ->setMarginLeft(10);


        // Create a shape (table)
        $shape1 = $currentSlide->createTableShape(8);
        $shape1->setHeight(200);
        $shape1->setWidth(945);
        $shape1->setOffsetX(10);
        $shape1->setOffsetY(60);


        // Add row
        $row = $shape1->createRow();
        $row->setHeight(20);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFFFFFFF'))
            ->setEndColor(new Color('FFFFFFFF'));
        $oCell1 = $row->nextCell();
        $oCell1->setWidth(100);
        $oCell1->getActiveParagraph()->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell1->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell1->createTextRun('TAJUK PROJEK')->getFont()->setBold(true);
        $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(20);
        $oCell2 = $row->nextCell();
        $oCell2->setWidth(250);
        $oCell2->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell2->createTextRun($project->nama_projek)->getFont()->setBold(true);
        $oCell3 = $row->nextCell();
        $oCell3->setWidth(100);
        $oCell3->getActiveParagraph()->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell3->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell3->createTextRun('TEMPOH PELAKSANAAN')->getFont()->setBold(true);
        $oCell4 = $row->nextCell();
        $oCell4->setWidth(100);
        $oCell4->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell4->createTextRun($project->tempoh_pelaksanaan . ' Bulan ' . '( ' . $project->tahun_jangka_mula. '-' .$project->tahun_jangka_siap. ' )')->getFont()->setBold(true);
        $oCell5 = $row->nextCell();
        $oCell5->setWidth(100);
        $oCell5->getActiveParagraph()->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell5->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell5->createTextRun('KOS PROJEK')->getFont()->setBold(true);
        $oCell6 = $row->nextCell();
        $oCell6->setWidth(100);
        $oCell6->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell6->createTextRun('')->getFont()->setBold(true);
        $oCell7 = $row->nextCell();
        $oCell7->setWidth(100);
        $oCell7->getActiveParagraph()->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell7->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new Color('ADD8E6'))
            ->setEndColor(new Color('ADD8E6'));
        $oCell7->createTextRun('SILING RP3')->getFont()->setBold(true);
        $oCell8 = $row->nextCell();
        $oCell8->setWidth(100);
        $oCell8->getActiveParagraph()->getAlignment()
            ->setMarginBottom(2)
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(2);
        $oCell8->createTextRun('')->getFont()->setBold(true);

        return $currentSlide;
    }
}


