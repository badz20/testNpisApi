<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\refBahagian;
use \App\Models\BahagianEpuJpm;
use \App\Models\JenisKategori;
use \App\Models\JenisSubKategori;
use \App\Models\SektorUtama;
use \App\Models\Sektor;
use \App\Models\SubSektor;
use \App\Models\Project;
use \App\Models\ProjectRequestUpdateTracker;
use \App\Models\projectLog;
use \App\Models\RollingPlan;
use \App\Models\SkopProject;
use \App\Models\BahagianTerlibat;
use \App\Models\SkopOption;
use \App\Models\SubSkopOption;
use \App\Models\KewanganSkop;
use \App\Models\Outcome;
use \App\Models\OutputPage;
use \App\Models\Brief_projek;
use \App\Models\KalendarModel;
use \App\Models\VEKalendarModel;
use \App\Models\VRKalendarModel;
use \App\Models\PemantauanProject;
use \App\Models\PemantauanFasilitator;
use \App\Models\Maklumat_keewangan;
use \App\Models\PemantauanSkopProjects;
use \App\Models\MaklumatPelakasanaanMakmal;
use \App\Models\vm_tandatangan;
use App\Models\VM\VmButiranFasilitator;
use App\Models\VM\VmObjektif;
use App\Models\VM\VmSkop;
use App\Models\VM\VmOutput;
use App\Models\VM\VmOutcome;
use App\Models\VM\VmMakmalKajianNilai;
use \App\Models\vr_tandatangan;
use App\Models\VM\VmMmpm;
use \App\Models\PengeculianUpdate;
use \App\Models\Penjilidan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Jenssegers\Agent\Facades\Agent;


class ValueManagementController extends Controller
{
    public function index (Request $request)
    {

        try {
            //code...
            //$users = \App\Models\Brief_projek::with(['kos_projek','bahagian'])->get();
            $user = \App\Models\User::whereId($request->user)->with('bahagian')->first(); 
            Log::info($user->bahagian->acym );
            $query = DB::table('pemantauan_project')
                            ->join('ref_bahagian','ref_bahagian.id', '=','pemantauan_project.bahagian_pemilik')
                            ->join('rolling_plans','rolling_plans.id', '=','pemantauan_project.rolling_plan_code')
                            // ->leftjoin('vm_butirans','vm_butirans.pp_id', '=','pemantauan_project.id')
                            ->leftjoin('status AS A', 'A.status', '=', 'pemantauan_project.status_perlaksanaan')
                            ->leftjoin('status AS B', 'B.status', '=', 'pemantauan_project.va_status')
                            ->leftjoin('status AS C', 'C.status', '=', 'pemantauan_project.ve_status')
                            ->leftjoin('status AS D', 'D.status', '=', 'pemantauan_project.vr_status')
                           ->select('pemantauan_project.*','rolling_plans.rmk','rolling_plans.name as rolling_plan_name','ref_bahagian.nama_bahagian','A.status_name as status_name'
                           ,'B.status_name as va_status_name','C.status_name as ve_status_name','D.status_name as vr_status_name');

                        if($user->bahagian->acym == 'BKOR') {
                            $query->whereIn('pemantauan_project.status_perlaksanaan',['27','29']);
                            $query->orWhere(function($query1) {
                                return $query1->where('pemantauan_project.status_perlaksanaan',30)
                                        ->where('pemantauan_project.penjilidan_status_va','=',null);
                            });
                            // $query->orWhere('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id);
                            $query->orWhere(function($query2) use ($user){
                                return $query2->orWhere('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id)
                                        ->where('pemantauan_project.penjilidan_status_ve','=',null);
                            });
                            $query->orWhere(function($query3) {
                                return $query3->where('pemantauan_project.status_perlaksanaan',36)
                                        ->where('pemantauan_project.penjilidan_status_ve','=',2)
                                        ->orWhere('pemantauan_project.penjilidan_status_ve','=',3);
                            });
                        }
                        else if($user->bahagian->acym == 'BPK') 
                        { 
                            $query->whereIn('pemantauan_project.status_perlaksanaan',['32','34']);
                            $query->orWhere(function($query1) {
                                return $query1->where('pemantauan_project.status_perlaksanaan',35)
                                        ->where('pemantauan_project.penjilidan_status_ve','=',1);
                            });
                            $query->orWhere(function($query2) {
                                return $query2->where('pemantauan_project.status_perlaksanaan',36)
                                        ->where('pemantauan_project.penjilidan_status_ve','=',2)
                                        ->orWhere('pemantauan_project.penjilidan_status_ve','=',3);
                            });
                            $query->orWhere('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id);
                        }
                        else 
                        {             Log::info($user->is_superadmin);

                            if($user->is_superadmin!=1)
                            { 
                                $query->where('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id);
                            }
                            //$query->whereIn('pemantauan_project.status_perlaksanaan',['31','33','36']);
                        }

                        if($request->va==1)
                        {
                            $query->where(function($query1) {
                                return $query1->where('pemantauan_project.kos_projeck','>',50000000)
                                        ->orWhere('pemantauan_project.Is_changed_to_va','=',1);
                            });
                        }
                        else
                        {
                            $query->where('pemantauan_project.kos_projeck','<=',50000000);
                            $query->where('pemantauan_project.Is_changed_to_va','!=',1);
                        }

            
            

            $result = $query->get();
            
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);

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

    public function makmal_list_VE(Request $request){ 
        
        try {
            
            $query = DB::table('pemantauan_project')
                           ->join('ref_bahagian','ref_bahagian.id', '=','pemantauan_project.bahagian_pemilik')
                           ->join('rolling_plans','rolling_plans.id', '=','pemantauan_project.rolling_plan_code')
                        //    ->leftjoin('vm_butirans','vm_butirans.pp_id', '=','pemantauan_project.id')
                           ->leftjoin('status AS A', 'A.status', '=', 'pemantauan_project.status_perlaksanaan')
                            ->leftjoin('status AS B', 'B.status', '=', 'pemantauan_project.va_status')
                            ->leftjoin('status AS C', 'C.status', '=', 'pemantauan_project.ve_status')
                            ->leftjoin('status AS D', 'D.status', '=', 'pemantauan_project.vr_status')
                           ->select('pemantauan_project.*','rolling_plans.rmk','rolling_plans.name as rolling_plan_name','ref_bahagian.nama_bahagian','A.status_name as status_name'
                           ,'B.status_name as va_status_name','C.status_name as ve_status_name','D.status_name as vr_status_name');

            $user = \App\Models\User::where('id',$request->user)->with('bahagian')->first(); //dd($user);
            if($user->bahagian->kod_bahagian == 'BPK') {
                $query->where(function($query1) {
                    return $query1->where('pemantauan_project.status_perlaksanaan','=',35)
                            ->where('pemantauan_project.penjilidan_status_ve','=',2);
                });
                // $query->orWhere(function($query1) {
                //     return $query1->orWhere('pemantauan_project.penjilidan_status_va','=',2)
                //             ->orWhere('pemantauan_project.penjilidan_status_ve','=',1)
                //             ->orWhere('pemantauan_project.penjilidan_status_ve','=',2);
                // });
                $query->orWhere(function($query1) {
                    return $query1->orWhere('pemantauan_project.status_perlaksanaan','=',36)
                            ->where('pemantauan_project.penjilidan_status_ve','!=',3);
                });
            }else { 
                $query->whereIn('pemantauan_project.status_perlaksanaan',['31','33']);
                if($user->is_superadmin!=1)
                {
                    $query->where('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id);
                }
                // $query->orWhere(function($query1) {
                //         return $query1->orWhere('pemantauan_project.penjilidan_status_ve','=',1)
                //                     ->where('pemantauan_project.penjilidan_status_ve','!=',2);
                // });
                    
                // $query->where('pemantauan_project.penjilidan_status_ve','!=',3);
                // $query->where('pemantauan_project.status_perlaksanaan','!=',36);
            }


            
            $result = $query->get();
            
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);

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

    public function makmal_list_VA(Request $request){

        try {
            $user = \App\Models\User::whereId($request->user)->with('bahagian')->first();
            $query = DB::table('pemantauan_project')
                        ->join('ref_bahagian','ref_bahagian.id', '=','pemantauan_project.bahagian_pemilik')
                        ->join('rolling_plans','rolling_plans.id', '=','pemantauan_project.rolling_plan_code')
                        // ->leftjoin('vm_butirans','vm_butirans.pp_id', '=','pemantauan_project.id')
                        ->leftjoin('status AS A', 'A.status', '=', 'pemantauan_project.status_perlaksanaan')
                        ->leftjoin('status AS B', 'B.status', '=', 'pemantauan_project.va_status')
                        ->leftjoin('status AS C', 'C.status', '=', 'pemantauan_project.ve_status')
                        ->leftjoin('status AS D', 'D.status', '=', 'pemantauan_project.vr_status')
                        ->select('pemantauan_project.*','rolling_plans.rmk','rolling_plans.name as rolling_plan_name','ref_bahagian.nama_bahagian','A.status_name as status_name'
                        ,'B.status_name as va_status_name','C.status_name as ve_status_name','D.status_name as vr_status_name');

            // $query->where(function($query1) {
            //     return $query1->where('pemantauan_project.status_perlaksanaan','=',30)
            //             ->where('pemantauan_project.penjilidan_status_va','=',1);
            // });

            // $query->orWhere(function($query4) {
            //     return $query4->orWhere('pemantauan_project.status_perlaksanaan','=',31)
            //             ->where('pemantauan_project.penjilidan_status_va','=',2);
            // });

            $query->where(function($query2) {
                return $query2->where('pemantauan_project.kos_projeck','>',50000000)
                        ->orWhere('pemantauan_project.Is_changed_to_va','=',1);
            });

            if($user->bahagian->acym == 'BKOR') {
                // $query->where(function($query1) {
                //     return $query1->where('pemantauan_project.status_perlaksanaan','=',30)
                //             ->where('pemantauan_project.penjilidan_status_va','=',1);
                // });
    
                // $query->orWhere(function($query4) {
                //     return $query4->orWhere('pemantauan_project.status_perlaksanaan','=',31)
                //             ->where('pemantauan_project.penjilidan_status_va','=',2);
                // });
                // $query->orWhere(function($query3) {
                //     return $query3->where('pemantauan_project.status_perlaksanaan','=',35)
                //             ->where('pemantauan_project.penjilidan_status_va','=',1);
                // });
                $query->where('pemantauan_project.penjilidan_status_va','=',1);
            }else
            {
                if($user->is_superadmin!=1)
                {
                    $query->where('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id);
                } 
                $query->where('pemantauan_project.penjilidan_status_va','=',1);
            }


            $result = $query->get();
            
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);

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

    public function makmal_list_VR(Request $request){
        try {
            $query = DB::table('pemantauan_project')
                    ->join('ref_bahagian','ref_bahagian.id', '=','pemantauan_project.bahagian_pemilik')
                    ->join('rolling_plans','rolling_plans.id', '=','pemantauan_project.rolling_plan_code')
                    // ->leftjoin('vm_butirans','vm_butirans.pp_id', '=','pemantauan_project.id')
                    ->leftjoin('status AS A', 'A.status', '=', 'pemantauan_project.status_perlaksanaan')
                    ->leftjoin('status AS B', 'B.status', '=', 'pemantauan_project.va_status')
                    ->leftjoin('status AS C', 'C.status', '=', 'pemantauan_project.ve_status')
                    ->leftjoin('status AS D', 'D.status', '=', 'pemantauan_project.vr_status')
                    ->select('pemantauan_project.*','rolling_plans.rmk','rolling_plans.name as rolling_plan_name','ref_bahagian.nama_bahagian','A.status_name as status_name'
                    ,'B.status_name as va_status_name','C.status_name as ve_status_name','D.status_name as vr_status_name');
                    
            $user = \App\Models\User::whereId($request->user)->with('bahagian')->first(); 
            if($user->bahagian->acym == 'BPK' || $user->bahagian->acym=='BKOR') {
                    $query->where('pemantauan_project.status_perlaksanaan',36);
                    $query->orWhere('pemantauan_project.penjilidan_status_ve',3);
            }
            else
            {
                $query->where('pemantauan_project.status_perlaksanaan',36);
                if($user->is_superadmin!=1)
                {
                    $query->where('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id);
                }
            }

            $result = $query->get();


            
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);

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
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function makmal_list_mini_va(Request $request){
        try {
            $user = \App\Models\User::whereId($request->user)->with('bahagian')->first();
            Log::info($user);
            $query = DB::table('pemantauan_project')
                        ->join('ref_bahagian','ref_bahagian.id', '=','pemantauan_project.bahagian_pemilik')
                        ->join('rolling_plans','rolling_plans.id', '=','pemantauan_project.rolling_plan_code')
                        // ->leftjoin('vm_butirans','vm_butirans.pp_id', '=','pemantauan_project.id')
                        ->leftjoin('status AS A', 'A.status', '=', 'pemantauan_project.status_perlaksanaan')
                        ->leftjoin('status AS B', 'B.status', '=', 'pemantauan_project.va_status')
                        ->leftjoin('status AS C', 'C.status', '=', 'pemantauan_project.ve_status')
                        ->leftjoin('status AS D', 'D.status', '=', 'pemantauan_project.vr_status')
                        ->select('pemantauan_project.*','rolling_plans.rmk','rolling_plans.name as rolling_plan_name','ref_bahagian.nama_bahagian','A.status_name as status_name'
                    ,'B.status_name as va_status_name','C.status_name as ve_status_name','D.status_name as vr_status_name');

            $query->where('pemantauan_project.kos_projeck','<=',50000000);
            $query->where('pemantauan_project.Is_changed_to_va','!=',1);
            $query->where('pemantauan_project.status_perlaksanaan',41);
            if($user->bahagian->acym!='BPK' || $user->bahagian->acym!='BKOR')
            {
            }
            else
            {
                $query->where('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id);
            }
            $result = $query->get();
            
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);

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
                'error' => $th->getMessage(),
            ]);
        }
    }

    
    public function FilterbrifProjectMakmalMini(Request $request)
    {
        try {
            // dd($request->user);

                $query = DB::table('pemantauan_project')
                            ->join('ref_bahagian','ref_bahagian.id', '=','pemantauan_project.bahagian_pemilik')
                            ->join('rolling_plans','rolling_plans.id', '=','pemantauan_project.rolling_plan_code')
                            // ->leftjoin('vm_butirans','vm_butirans.pp_id', '=','pemantauan_project.id')
                            ->leftjoin('status AS A', 'A.status', '=', 'pemantauan_project.status_perlaksanaan')
                            ->leftjoin('status AS B', 'B.status', '=', 'pemantauan_project.va_status')
                            ->leftjoin('status AS C', 'C.status', '=', 'pemantauan_project.ve_status')
                            ->leftjoin('status AS D', 'D.status', '=', 'pemantauan_project.vr_status')
                           ->select('pemantauan_project.*','rolling_plans.rmk','rolling_plans.name as rolling_plan_name','ref_bahagian.nama_bahagian','A.status_name as status_name'
                           ,'B.status_name as va_status_name','C.status_name as ve_status_name','D.status_name as vr_status_name')                         
                            ->where('pemantauan_project.kos_projeck','<=',50000000)  
                            ->where('pemantauan_project.Is_changed_to_va','!=',1)                        
                            ->where(function($query) use ($request){
                                if($request->rolling_plan)
                                {
                                        $query->where('rolling_plan_code','=', $request->rolling_plan);
                                }
                                if($request->nama_project)
                                {
                                        $query->where('nama_projek','like','%'.$request->nama_project.'%');
                                }
                                if($request->kod_project)
                                {
                                        $query->where('pemantauan_project.kod_projeck','like','%'.$request->kod_project.'%');
                                }
                                
                                if($request->status)
                                {
                                    $query->orWhere(function($query1) use ($request){
                                        return $query1->orWhere('pemantauan_project.va_status',$request->status)
                                                ->orWhere('pemantauan_project.ve_status','=',$request->status)
                                                ->orWhere('pemantauan_project.vr_status','=',$request->status);
                                    });
                                    
                                        // $query->where('va_status','=', $request->status);
                                        // $query->orWhere('ve_status','=', $request->status);
                                        // $query->orWhere('vr_status','=', $request->status);

                                }
                                if($request->tahun)
                                {
                                        $query->where('pemantauan_project.tahun','=', $request->tahun);
                                }
                                if($request->bahagian)
                                {
                                        $query->where('pemantauan_project.bahagian_pemilik','=', $request->bahagian);
                                }

                                $user = \App\Models\User::whereId($request->user)->with('bahagian')->first();
                                if($user->bahagian->acym == 'BKOR') {
                                    $query->whereIn('pemantauan_project.status_perlaksanaan',['27','29']);
                                    $query->orWhere(function($query1) {
                                        return $query1->where('pemantauan_project.status_perlaksanaan',30)
                                                ->where('pemantauan_project.penjilidan_status_va','=',null);
                                    });
                                    $query->orWhere(function($query2) use ($user){
                                        return $query2->orWhere('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id)
                                                ->where('pemantauan_project.penjilidan_status_ve','=',null);
                                    });
                                }
                                else if($user->bahagian->acym == 'BPK') 
                                { 
                                    $query->whereIn('pemantauan_project.status_perlaksanaan',['32','34']);
                                    $query->orWhere(function($query1) {
                                        return $query1->where('pemantauan_project.status_perlaksanaan',35)
                                                ->where('pemantauan_project.penjilidan_status_ve','=',1);
                                    });
                                    $query->orWhere(function($query2) {
                                        return $query2->where('pemantauan_project.status_perlaksanaan',36)
                                                ->where('pemantauan_project.penjilidan_status_ve','=',2);
                                    });
                                    $query->orWhere('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id);
                                }
                                else 
                                {
                                    if($user->is_superadmin!=1)
                                    {
                                        $query->where('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id);
                                    }
                                    //$query->whereIn('pemantauan_project.status_perlaksanaan',['31','33','36']);
                                }

                                $query->where('pemantauan_project.kos_projeck','<=',50000000);
                                $query->where('pemantauan_project.Is_changed_to_va','!=',1);
                                         
                          })->get();
            
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $query,
            ]);

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
                'error' => $th->getMessage(),
            ]);
        }

    }

    public function FilterbrifProjectMakmal(Request $request)
    {
        try {

                $query = DB::table('pemantauan_project')
                            ->join('ref_bahagian','ref_bahagian.id', '=','pemantauan_project.bahagian_pemilik')
                            ->join('rolling_plans','rolling_plans.id', '=','pemantauan_project.rolling_plan_code')
                            // ->leftjoin('vm_butirans','vm_butirans.pp_id', '=','pemantauan_project.id')
                            ->leftjoin('status AS A', 'A.status', '=', 'pemantauan_project.status_perlaksanaan')
                            ->leftjoin('status AS B', 'B.status', '=', 'pemantauan_project.va_status')
                            ->leftjoin('status AS C', 'C.status', '=', 'pemantauan_project.ve_status')
                            ->leftjoin('status AS D', 'D.status', '=', 'pemantauan_project.vr_status')
                           ->select('pemantauan_project.*','rolling_plans.rmk','rolling_plans.name as rolling_plan_name','ref_bahagian.nama_bahagian','A.status_name as status_name'
                           ,'B.status_name as va_status_name','C.status_name as ve_status_name','D.status_name as vr_status_name')
                            
                            ->where(function($query) use ($request){
                                        if($request->rolling_plan)
                                        {
                                            $query->where('rolling_plan_code','=', $request->rolling_plan);
                                        }
                                        if($request->nama_project)
                                        {
                                                $query->where('pemantauan_project.nama_projek','like','%'.$request->nama_project.'%');
                                        }
                                        if($request->kod_project)
                                        {
                                            $query->where('pemantauan_project.kod_projeck','like','%'.$request->kod_project.'%');
                                        }
                                        if($request->status)
                                        {
                                            $query->orWhere(function($query1) use ($request){
                                                return $query1->orWhere('pemantauan_project.va_status',$request->status)
                                                        ->orWhere('pemantauan_project.ve_status','=',$request->status)
                                                        ->orWhere('pemantauan_project.vr_status','=',$request->status);
                                            });
                                        }
                                        if($request->tahun)
                                        {
                                                $query->where('pemantauan_project.tahun','=', $request->tahun);
                                        }
                                        if($request->bahagian)
                                        {
                                                $query->where('pemantauan_project.bahagian_pemilik','=', $request->bahagian);
                                        }
                                        $user = \App\Models\User::whereId($request->user)->with('bahagian')->first();
                                        if($user->bahagian->acym == 'BKOR') {
                                            $query->whereIn('pemantauan_project.status_perlaksanaan',['27','29']);
                                            $query->orWhere(function($query1) {
                                                return $query1->where('pemantauan_project.status_perlaksanaan',30)
                                                        ->where('pemantauan_project.penjilidan_status_va','=',null);
                                            });
                                            // $query->orWhere('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id);
                                            $query->orWhere(function($query2) use ($user){
                                                return $query2->orWhere('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id)
                                                        ->where('pemantauan_project.penjilidan_status_ve','=',null);
                                            });
                                            $query->orWhere(function($query3) {
                                                return $query3->where('pemantauan_project.status_perlaksanaan',36)
                                                        ->where('pemantauan_project.penjilidan_status_ve','=',2)
                                                        ->orWhere('pemantauan_project.penjilidan_status_ve','=',3);
                                            });
                                        }
                                        else if($user->bahagian->acym == 'BPK') 
                                        { 
                                            $query->whereIn('pemantauan_project.status_perlaksanaan',['32','34']);
                                            $query->orWhere(function($query1) {
                                                return $query1->where('pemantauan_project.status_perlaksanaan',35)
                                                        ->where('pemantauan_project.penjilidan_status_ve','=',1);
                                            });
                                            $query->orWhere(function($query2) {
                                                return $query2->where('pemantauan_project.status_perlaksanaan',36)
                                                        ->where('pemantauan_project.penjilidan_status_ve','=',2)
                                                        ->orWhere('pemantauan_project.penjilidan_status_ve','=',3);
                                            });
                                            $query->orWhere('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id);
                                        }
                                        else 
                                        {
                                            if($user->is_superadmin!=1)
                                            {
                                                $query->where('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id);
                                                //$query->whereIn('pemantauan_project.status_perlaksanaan',['31','33','36']);
                                            }
                                        }

                                        $query->where(function($query1) {
                                            return $query1->where('pemantauan_project.kos_projeck','>',50000000)
                                                    ->orWhere('pemantauan_project.Is_changed_to_va','=',1);
                                        });

                                  })->get();
                           
            
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $query,
            ]);

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
                'error' => $th->getMessage(),
            ]);
        }
        
    }

    public function FilterbrifProjectMakmalVa(Request $request){
        try {
            $query = DB::table('pemantauan_project')
            ->join('ref_bahagian','ref_bahagian.id', '=','pemantauan_project.bahagian_pemilik')
            ->join('rolling_plans','rolling_plans.id', '=','pemantauan_project.rolling_plan_code')
            // ->leftjoin('vm_butirans','vm_butirans.pp_id', '=','pemantauan_project.id')
            ->leftjoin('status AS A', 'A.status', '=', 'pemantauan_project.status_perlaksanaan')
            ->leftjoin('status AS B', 'B.status', '=', 'pemantauan_project.va_status')
            ->leftjoin('status AS C', 'C.status', '=', 'pemantauan_project.ve_status')
            ->leftjoin('status AS D', 'D.status', '=', 'pemantauan_project.vr_status')
            ->select('pemantauan_project.*','rolling_plans.rmk','rolling_plans.name as rolling_plan_name','ref_bahagian.nama_bahagian','A.status_name as status_name'
            ,'B.status_name as va_status_name','C.status_name as ve_status_name','D.status_name as vr_status_name')
            ->where(function($query) use ($request){
                if($request->rolling_plan)
                {
                    $query->where('rolling_plan_code','=', $request->rolling_plan);
                }
                if($request->nama_project)
                {
                    $query->where('nama_projek','like','%'.$request->nama_project.'%');
                }
                if($request->kod_project)
                {
                    $query->where('pemantauan_project.kod_projeck','like','%'.$request->kod_project.'%');
                }
                if($request->status)
                {
                                            $query->orWhere(function($query1) use ($request){
                                                return $query1->orWhere('pemantauan_project.va_status',$request->status)
                                                        ->orWhere('pemantauan_project.ve_status','=',$request->status)
                                                        ->orWhere('pemantauan_project.vr_status','=',$request->status);
                                            });
                }
                if($request->tahun)
                {
                        $query->where('pemantauan_project.tahun','=', $request->tahun);
                }
                if($request->bahagian)
                {
                                        $query->where('pemantauan_project.bahagian_pemilik','=', $request->bahagian);
                }

                $query->where(function($query2) {
                    return $query2->where('pemantauan_project.kos_projeck','>',50000000)
                            ->orWhere('pemantauan_project.Is_changed_to_va','=',1);
                });

                $user = \App\Models\User::whereId($request->user)->with('bahagian')->first();                 
                //Log::info($user->bahagian_id);

                if($user->bahagian->acym == 'BKOR') {
                    // $query->where(function($query1) {
                    //     return $query1->where('pemantauan_project.status_perlaksanaan','=',30)
                    //             ->where('pemantauan_project.penjilidan_status_va','=',1);
                    // });
        
                    // $query->orWhere(function($query4) {
                    //     return $query4->orWhere('pemantauan_project.status_perlaksanaan','=',31)
                    //             ->where('pemantauan_project.penjilidan_status_va','=',2);
                    // });
                    // $query->orWhere(function($query3) {
                    //     return $query3->where('pemantauan_project.status_perlaksanaan','=',35)
                    //             ->where('pemantauan_project.penjilidan_status_va','=',1);
                    // });
                    $query->where('pemantauan_project.penjilidan_status_va','=',1);
                }else
                {
                    if($user->is_superadmin!=1)
                    {
                        $query->where('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id);
                    }
                    $query->where('pemantauan_project.penjilidan_status_va','=',1); 
                }

          })->get();
            
                        

                       
        
        return response()->json([
            'code' => '200',
            'status' => 'Success',
            'data' => $query,
        ]);

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
            'error' => $th->getMessage(),
        ]);
    }

    }

    public function FilterbrifProjectMakmalVr(Request $request){

        try {
            $query = DB::table('pemantauan_project')
                    ->join('ref_bahagian','ref_bahagian.id', '=','pemantauan_project.bahagian_pemilik')
                    ->join('rolling_plans','rolling_plans.id', '=','pemantauan_project.rolling_plan_code')
                    // ->leftjoin('vm_butirans','vm_butirans.pp_id', '=','pemantauan_project.id')
                    ->leftjoin('status AS A', 'A.status', '=', 'pemantauan_project.status_perlaksanaan')
                    ->leftjoin('status AS B', 'B.status', '=', 'pemantauan_project.va_status')
                    ->leftjoin('status AS C', 'C.status', '=', 'pemantauan_project.ve_status')
                    ->leftjoin('status AS D', 'D.status', '=', 'pemantauan_project.vr_status')
                    ->select('pemantauan_project.*','rolling_plans.rmk','rolling_plans.name as rolling_plan_name','ref_bahagian.nama_bahagian','A.status_name as status_name'
                    ,'B.status_name as va_status_name','C.status_name as ve_status_name','D.status_name as vr_status_name')
                           ->where(function($query) use ($request){
                                    if($request->rolling_plan)
                                    {
                                        $query->where('pemantauan_project.rolling_plan_code','=', $request->rolling_plan);
                                    }
                                    if($request->nama_project)
                                    {
                                        $query->where('pemantauan_project.nama_projek','like','%'.$request->nama_project.'%');
                                    }
                                    if($request->kod_project)
                                    {
                                        $query->where('pemantauan_project.kod_projeck','like','%'.$request->kod_project.'%');
                                    }
                                    if($request->status)
                                    {
                                        $query->orWhere(function($query1) use ($request){
                                            return $query1->orWhere('pemantauan_project.va_status',$request->status)
                                                    ->orWhere('pemantauan_project.ve_status','=',$request->status)
                                                    ->orWhere('pemantauan_project.vr_status','=',$request->status);
                                        });
                                    }     
                                    if($request->tahun)
                                    {
                                            $query->where('pemantauan_project.tahun','=', $request->tahun);
                                    }
                                    if($request->bahagian)
                                    {
                                        $query->where('pemantauan_project.bahagian_pemilik','=', $request->bahagian);
                                    }
                                    $user = \App\Models\User::whereId($request->user)->with('bahagian')->first();
                                    if($user->bahagian->acym=='BPK' || $user->bahagian->acym=='BKOR')
                                    {
                                        $query->where('pemantauan_project.status_perlaksanaan',36);
                                        $query->orWhere('pemantauan_project.penjilidan_status_ve',3);
                                    }
                                    else
                                    {
                                        $query->where('pemantauan_project.status_perlaksanaan',36);
                                        if($user->is_superadmin!=1)
                                        {
                                            $query->where('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id);
                                        }
                                    }
                            })->get();
            
            
        
        return response()->json([
            'code' => '200',
            'status' => 'Success',
            'data' => $query,
        ]);

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
            'error' => $th->getMessage(),
        ]);
    }
        
    }
    public function FilterbrifProjectMakmalVr1(Request $request){
        try {
           // $user = \App\Models\User::whereId($request->user)->with('bahagian')->first();
            $query = DB::table('pemantauan_project')
                    ->join('ref_bahagian','ref_bahagian.id', '=','pemantauan_project.bahagian_pemilik')
                    ->join('rolling_plans','rolling_plans.id', '=','pemantauan_project.rolling_plan_code')
                    // ->leftjoin('vm_butirans','vm_butirans.pp_id', '=','pemantauan_project.id')
                    ->leftjoin('status AS A', 'A.status', '=', 'pemantauan_project.status_perlaksanaan')
                    ->leftjoin('status AS B', 'B.status', '=', 'pemantauan_project.va_status')
                    ->leftjoin('status AS C', 'C.status', '=', 'pemantauan_project.ve_status')
                    ->leftjoin('status AS D', 'D.status', '=', 'pemantauan_project.vr_status')
                    ->select('pemantauan_project.*','rolling_plans.rmk','rolling_plans.name as rolling_plan_name','ref_bahagian.nama_bahagian','A.status_name as status_name'
                    ,'B.status_name as va_status_name','C.status_name as ve_status_name','D.status_name as vr_status_name')
                     ->where(function($query) use ($request){
                                    if($request->rolling_plan)
                                    {
                                        $query->where('pemantauan_project.rolling_plan_code','=', $request->rolling_plan);
                                    }
                                    if($request->nama_project)
                                    {
                                        $query->where('pemantauan_project.nama_projek','like','%'.$request->nama_project.'%');
                                    }
                                    if($request->kod_project)
                                    {
                                        $query->where('pemantauan_project.kod_projeck','like','%'.$request->kod_project.'%');
                                    }
                                    if($request->status)
                                    {
                                        $query->orWhere(function($query1) use ($request){
                                            return $query1->orWhere('pemantauan_project.va_status',$request->status)
                                                    ->orWhere('pemantauan_project.ve_status','=',$request->status)
                                                    ->orWhere('pemantauan_project.vr_status','=',$request->status);
                                        });
                                    }   
                                    if($request->tahun)
                                    {
                                            $query->where('pemantauan_project.tahun','=', $request->tahun);
                                    }
                                    if($request->bahagian)
                                    {
                                        $query->where('pemantauan_project.bahagian_pemilik','=', $request->bahagian);
                                    }
                                    $user = \App\Models\User::whereId($request->user)->with('bahagian')->first();
                                    if($user->bahagian->acym=='BPK' || $user->bahagian->acym=='BKOR')
                                    {
                                        $query->where('pemantauan_project.status_perlaksanaan',36);
                                        $query->orWhere('pemantauan_project.penjilidan_status_ve',3);
                                    }
                                    else
                                    {
                                        $query->where('pemantauan_project.status_perlaksanaan',36);
                                        if($user->is_superadmin!=1)
                                        {
                                            $query->where('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id);
                                        }
                                    }                                    //$query->where('pemantauan_project.penjilidan_status_ve','>=',2);

                                    
                              })->get();


                                // if($user->bahagian->acym == 'BPK') {
                                //     $result = $query->get();
                                // }
                                // else
                                // {
                                //     $result = [];
                                // }     
                                
                                $result = $query->get();

                       
        
        return response()->json([
            'code' => '200',
            'status' => 'Success',
            'data' => $query,
        ]);

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
            'error' => $th->getMessage(),
        ]);
    }

    }
    public function FilterbrifProjectMakmalVe(Request $request){

        try {
            $query = DB::table('pemantauan_project')
                    ->join('ref_bahagian','ref_bahagian.id', '=','pemantauan_project.bahagian_pemilik')
                    ->join('rolling_plans','rolling_plans.id', '=','pemantauan_project.rolling_plan_code')
                    // ->leftjoin('vm_butirans','vm_butirans.pp_id', '=','pemantauan_project.id')
                    ->leftjoin('status AS A', 'A.status', '=', 'pemantauan_project.status_perlaksanaan')
                    ->leftjoin('status AS B', 'B.status', '=', 'pemantauan_project.va_status')
                    ->leftjoin('status AS C', 'C.status', '=', 'pemantauan_project.ve_status')
                    ->leftjoin('status AS D', 'D.status', '=', 'pemantauan_project.vr_status')
                    ->select('pemantauan_project.*','rolling_plans.rmk','rolling_plans.name as rolling_plan_name','ref_bahagian.nama_bahagian','A.status_name as status_name'
                    ,'B.status_name as va_status_name','C.status_name as ve_status_name','D.status_name as vr_status_name')
                           ->where(function($query) use ($request){
                            if($request->rolling_plan)
                            {
                                $query->where('rolling_plan_code','=', $request->rolling_plan);
                            }
                            if($request->nama_project)
                            {
                                $query->where('nama_projek','like','%'.$request->nama_project.'%');
                            }
                            if($request->kod_project)
                            {
                                $query->where('pemantauan_project.kod_projeck','like','%'.$request->kod_project.'%');
                            }
                            if($request->status)
                            {
                                $query->orWhere(function($query1) use ($request){
                                    return $query1->orWhere('pemantauan_project.va_status',$request->status)
                                            ->orWhere('pemantauan_project.ve_status','=',$request->status)
                                            ->orWhere('pemantauan_project.vr_status','=',$request->status);
                                });
                            }    
                            if($request->tahun)
                            {
                                    $query->where('pemantauan_project.tahun','=', $request->tahun);
                            }
                            if($request->bahagian)
                            {
                                        $query->where('pemantauan_project.bahagian_pemilik','=', $request->bahagian);
                            }
                            $user = \App\Models\User::whereId($request->user)->with('bahagian')->first(); 

                            if($user->bahagian->kod_bahagian == 'BPK') {
                                $query->where(function($query1) {
                                    return $query1->where('pemantauan_project.status_perlaksanaan','=',35)
                                            ->where('pemantauan_project.penjilidan_status_ve','=',2);
                                });
                    
                                $query->orWhere(function($query1) {
                                    return $query1->orWhere('pemantauan_project.status_perlaksanaan','=',36)
                                            ->where('pemantauan_project.penjilidan_status_ve','!=',3);
                                });
                            }else { 
                                $query->whereIn('pemantauan_project.status_perlaksanaan',['31','33']);
                                if($user->is_superadmin!=1)
                                {
                                    $query->where('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id);
                                }
                            }
                                                        

                      })->get();
            
            
        
        return response()->json([
            'code' => '200',
            'status' => 'Success',
            'data' => $query,
        ]);

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
            'error' => $th->getMessage(),
        ]);
    }
        
    }

    public function FilterbrifProjectMakmalMiniVA(Request $request){

       
        try {
            // dd($request->user);

                $query = DB::table('pemantauan_project')
                            ->join('ref_bahagian','ref_bahagian.id', '=','pemantauan_project.bahagian_pemilik')
                            ->join('rolling_plans','rolling_plans.id', '=','pemantauan_project.rolling_plan_code')
                            // ->leftjoin('vm_butirans','vm_butirans.pp_id', '=','pemantauan_project.id')
                            ->leftjoin('status AS A', 'A.status', '=', 'pemantauan_project.status_perlaksanaan')
                            ->leftjoin('status AS B', 'B.status', '=', 'pemantauan_project.va_status')
                            ->leftjoin('status AS C', 'C.status', '=', 'pemantauan_project.ve_status')
                            ->leftjoin('status AS D', 'D.status', '=', 'pemantauan_project.vr_status')
                           ->select('pemantauan_project.*','rolling_plans.rmk','rolling_plans.name as rolling_plan_name','ref_bahagian.nama_bahagian','A.status_name as status_name'
                           ,'B.status_name as va_status_name','C.status_name as ve_status_name','D.status_name as vr_status_name')                         
                            ->where('pemantauan_project.kos_projeck','<=',50000000)  
                            ->where('pemantauan_project.Is_changed_to_va','!=',1)                        
                            ->where(function($query) use ($request){
                                if($request->rolling_plan)
                                {
                                    $query->where('rolling_plan_code','=', $request->rolling_plan);
                                }
                                if($request->nama_project)
                                {
                                    $query->where('nama_projek','like','%'.$request->nama_project.'%');
                                }
                                if($request->kod_project)
                                {
                                    $query->where('pemantauan_project.kod_projeck','like','%'.$request->kod_project.'%');
                                }
                                if($request->status)
                                {
                                    $query->orWhere(function($query1) use ($request){
                                        return $query1->orWhere('pemantauan_project.va_status',$request->status)
                                                ->orWhere('pemantauan_project.ve_status','=',$request->status)
                                                ->orWhere('pemantauan_project.vr_status','=',$request->status);
                                    });
                                }   
                                if($request->tahun)
                                {
                                        $query->where('pemantauan_project.tahun','=', $request->tahun);
                                }
                                if($request->bahagian)
                                {
                                        $query->where('pemantauan_project.bahagian_pemilik','=', $request->bahagian);
                                }

                                $user = \App\Models\User::whereId($request->user)->with('bahagian')->first();
                                if($user->bahagian->acym=='BPK' || $user->bahagian->acym=='BKOR')
                                {
                                    $query->where(function($query1) {
                                        return $query1->where('pemantauan_project.kos_projeck','<=',50000000)
                                        ->where('pemantauan_project.Is_changed_to_va','!=',1)
                                        ->where('pemantauan_project.status_perlaksanaan','=',41);
                                    });
                                }
                                else
                                {
                                    $query->where(function($query1) {
                                        return $query1->where('pemantauan_project.kos_projeck','<=',50000000)
                                        ->where('pemantauan_project.Is_changed_to_va','!=',1)
                                        ->where('pemantauan_project.status_perlaksanaan','=',41);
                                    });
                                    if($user->is_superadmin!=1)
                                    {
                                        $query->where('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id);
                                    }
                                }
                          })->get();
            
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $query,
            ]);

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
                'error' => $th->getMessage(),
            ]);
        }
    }

    

    public function storeKalenderData(Request $request){
        // dd($request->toArray());

        if($request->type=='VE')
        {
            $kalender = VEKalendarModel::where('pp_id',$request->id)
                             ->where('kategori',$request->kategori)
                             ->where('row_status',1)->first();
            if($kalender){

                $kalender->row_status=0;
                $kalender->dikemaskini_oleh = $request->user_id;
                $kalender->dikemaskini_pada = Carbon::now()->format('Y-m-d H:i:s');
                $kalender->update();
            }
            $data = VEKalendarModel::create([                    
                'pp_id' => $request->id,
                'kategori' => $request->kategori,
                'tarikh_mula'=>$request->Tarikh_Mula,
                'tarikh_tamat'=>$request->Tarikh_Tamat,
                'row_status' => 1,
                'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'dibuat_oleh' => $request->user_id,
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            $ve_data= PemantauanProject::where('id',$request->id)->first();
                $ve_data->ve_status = 22;
                $ve_data->current_status = 22;
                $ve_data->update();
        }
        else if($request->type == 'VR') {

            $kalender = VRKalendarModel::where('pp_id',$request->id)
                             ->where('kategori',$request->kategori)
                             ->where('row_status',1)->first();
            if($kalender){

                $kalender->row_status=0;
                $kalender->dikemaskini_oleh = $request->user_id;
                $kalender->dikemaskini_pada = Carbon::now()->format('Y-m-d H:i:s');
                $kalender->update();
            }
            $data = VRKalendarModel::create([                    
                'pp_id' => $request->id,
                'kategori' => $request->kategori,
                'tarikh_mula'=>$request->Tarikh_Mula,
                'tarikh_tamat'=>$request->Tarikh_Tamat,
                'row_status' => 1,
                'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'dibuat_oleh' => $request->user_id,
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            $vr_data= PemantauanProject::where('id',$request->id)->first();
                $vr_data->vr_status = 22;
                $vr_data->current_status = 22;
                $vr_data->update();
        }
        else
        {
            // dd($request);
            // if($request->projeck_id==''){
                $kalender = KalendarModel::where('pp_id',$request->id)
                                ->where('kategori',$request->kategori)
                                ->where('row_status',1)->first();
                if($kalender){

                    $kalender->row_status=0;
                    $kalender->dikemaskini_oleh = $request->user_id;
                    $kalender->dikemaskini_pada = Carbon::now()->format('Y-m-d H:i:s');
                    $kalender->update();
                }
                $data = KalendarModel::create([                    
                    'pp_id' => $request->id,
                    'kategori' => $request->kategori,
                    'tarikh_mula'=>$request->Tarikh_Mula,
                    'tarikh_tamat'=>$request->Tarikh_Tamat,
                    'row_status' => 1,
                    'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                    'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                    'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    'dibuat_oleh' => $request->user_id,
                    'dikemaskini_oleh' => $request->user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);

                $va_data= PemantauanProject::where('id',$request->id)->first();
                $va_data->va_status = 22;
                $va_data->current_status = 22;
                $va_data->update();
            // }
            // else{
            //     $kalender = KalendarModel::where('pp_id',$request->id)
            //     ->where('kategori',$request->kategori)
            //     ->where('row_status',1)->first();
            //     if($kalender){
            //         $kalender->row_status=0;
            //         $kalender->dikemaskini_oleh = $request->user_id;
            //         $kalender->dikemaskini_pada = Carbon::now()->format('Y-m-d H:i:s');
            //         $kalender->update();
            //     }
            //     $data = KalendarModel::where('id', $request->projeck_id)->update([                    
            //     'pp_id' => $request->id,
            //     'kategori' => $request->kategori,
            //     'tarikh_mula'=>$request->Tarikh_Mula,
            //     'tarikh_tamat'=>$request->Tarikh_Tamat,
            //     'row_status' => 1,
            //     'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
            //     'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
            //     'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            //     'dibuat_oleh' => $request->user_id,
            //     'dikemaskini_oleh' => $request->user_id,
            //     'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            //     ]);

            //     $va_data= PemantauanProject::where('id',$request->id)->first();
            //     $va_data->va_status = 22;
            //     $va_data->current_status = 22;
            //     $va_data->update();
                
            // }
        }         

        return response()->json([
            'code' => '200',
            'status' => 'Success',
            'kategori' => $request->kategori
        ]);
    }



    public function kalenderData(Request $request,$key,$type,$user_id,$user_type){
        // dd($key);

        $user = \App\Models\User::whereId($user_id)->with('bahagian')->first(); 

        if($user_type==4 || $user->is_superadmin==1)
        {
            $isAdmin = false;
        }
        else
        {
            $isAdmin = true; 
        }


                if($type=='Mini_VA')
                {
                    $data= DB::table('vm_perancagan_makmal')
                                            ->leftJoin('pemantauan_project', 'pemantauan_project.id', '=', 'vm_perancagan_makmal.pp_id')
                                            ->when($isAdmin, function ($query) use($user){
                                                return $query->where('pemantauan_project.bahagian_pemilik',$user->bahagian_id);
                                            })
                                            ->where('vm_perancagan_makmal.row_status',1)
                                            ->where(function($query) {
                                                        $query->where('pemantauan_project.kos_projeck','<=',50000000)
                                                        ->orWhere('pemantauan_project.Is_changed_to_va','!=',1);
                                            })->get();

                    $data2 =   DB::table('pemantauan_project')
                                                ->when($isAdmin, function ($query) use($user){
                                                    return $query->where('pemantauan_project.bahagian_pemilik',$user->bahagian_id);
                                                })
                                                ->where(function($query1) {
                                                    $query1->where('pemantauan_project.kos_projeck','<=',50000000)
                                                        ->orWhere('pemantauan_project.Is_changed_to_va','!=',1);
                                                })->get();
                    $data3 =   DB::table('vm_perancagan_makmal')
                                            ->leftJoin('pemantauan_project', 'pemantauan_project.id', '=', 'vm_perancagan_makmal.pp_id')
                                            ->when($isAdmin, function ($query) use($user){
                                                return $query->where('pemantauan_project.bahagian_pemilik',$user->bahagian_id);
                                            })
                                            ->whereYear('vm_perancagan_makmal.created_at', date('Y'))->where('vm_perancagan_makmal.row_status',1)
                                            ->where(function($query2) {
                                                $query2->where('pemantauan_project.kos_projeck','<=',50000000)
                                                        ->orWhere('pemantauan_project.Is_changed_to_va','!=',1);
                                            })->get();
                    $data4 =   DB::table('vm_perancagan_makmal')
                                            ->leftJoin('pemantauan_project', 'pemantauan_project.id', '=', 'vm_perancagan_makmal.pp_id')
                                            ->when($isAdmin, function ($query) use($user){
                                                return $query->where('pemantauan_project.bahagian_pemilik',$user->bahagian_id);
                                            })
                                            ->whereDate('vm_perancagan_makmal.tarikh_mula', date('Y-m-d'))->where('vm_perancagan_makmal.row_status',1)
                                            ->where(function($query3) {
                                                $query3->where('pemantauan_project.kos_projeck','<=',50000000)
                                                        ->orWhere('pemantauan_project.Is_changed_to_va','!=',1);
                                            })->get();
                    $data5 =   DB::table('vm_perancagan_makmal')
                                            ->leftJoin('pemantauan_project', 'pemantauan_project.id', '=', 'vm_perancagan_makmal.pp_id')
                                            ->when($isAdmin, function ($query) use($user){
                                                return $query->where('pemantauan_project.bahagian_pemilik',$user->bahagian_id);
                                            })
                                            ->where('vm_perancagan_makmal.row_status',1)
                                            ->where(function($query4) {
                                                $query4->where('pemantauan_project.kos_projeck','<=',50000000)
                                                        ->orWhere('pemantauan_project.Is_changed_to_va','!=',1);
                                            })->get();

                    // To select only month data for each project
                    $months = DB::table('vm_perancagan_makmal')
                        ->leftJoin('pemantauan_project', 'pemantauan_project.id', '=', 'vm_perancagan_makmal.pp_id')
                        ->when($isAdmin, function ($query) use ($user) {
                            return $query->where('pemantauan_project.bahagian_pemilik', $user->bahagian_id);
                        })
                        ->whereYear('vm_perancagan_makmal.created_at', date('Y'))
                        ->where('vm_perancagan_makmal.row_status', 1)
                        ->where(function ($query2) {
                            $query2->where('pemantauan_project.kos_projeck', '<=', 50000000)
                                ->orWhere('pemantauan_project.Is_changed_to_va', '!=', 1);
                        })
                        ->selectRaw('MONTH(tarikh_mula) as month_mula, MONTH(tarikh_tamat) as month_tamat, YEAR(vm_perancagan_makmal.created_at) as created_at_year')
                        ->distinct()
                        ->get();
                }
                else if($type=='VE')
                {
                    $data= DB::table('ve_perancagan_makmal')
                                            ->leftJoin('pemantauan_project', 'pemantauan_project.id', '=', 've_perancagan_makmal.pp_id')
                                            ->when($isAdmin, function ($query) use($user){
                                                return $query->where('pemantauan_project.bahagian_pemilik',$user->bahagian_id);
                                            })
                                            ->where('ve_perancagan_makmal.row_status',1)
                                            ->where(function($query) {
                                                        $query->where('pemantauan_project.kos_projeck','>',50000000)
                                                        ->orWhere('pemantauan_project.Is_changed_to_va','=',1);
                                            })->get();

                    $data2 =   DB::table('pemantauan_project')
                                                ->when($isAdmin, function ($query) use($user){
                                                    return $query->where('pemantauan_project.bahagian_pemilik',$user->bahagian_id);
                                                })
                                                ->where(function($query1) {
                                                    $query1->where('pemantauan_project.kos_projeck','>',50000000)
                                                    ->orWhere('pemantauan_project.Is_changed_to_va','=',1);
                                                })->get();
                    $data3 =   DB::table('ve_perancagan_makmal')
                                            ->leftJoin('pemantauan_project', 'pemantauan_project.id', '=', 've_perancagan_makmal.pp_id')
                                            ->when($isAdmin, function ($query) use($user){
                                                return $query->where('pemantauan_project.bahagian_pemilik',$user->bahagian_id);
                                            })
                                            ->whereYear('ve_perancagan_makmal.created_at', date('Y'))->where('ve_perancagan_makmal.row_status',1)
                                            ->where(function($query2) {
                                                $query2->where('pemantauan_project.kos_projeck','>',50000000)
                                                ->orWhere('pemantauan_project.Is_changed_to_va','=',1);
                                            })->get();
                    $data4 =   DB::table('ve_perancagan_makmal')
                                            ->leftJoin('pemantauan_project', 'pemantauan_project.id', '=', 've_perancagan_makmal.pp_id')
                                            ->when($isAdmin, function ($query) use($user){
                                                return $query->where('pemantauan_project.bahagian_pemilik',$user->bahagian_id);
                                            })
                                            ->whereDate('ve_perancagan_makmal.tarikh_mula', date('Y-m-d'))->where('ve_perancagan_makmal.row_status',1)
                                            ->where(function($query3) {
                                                $query3->where('pemantauan_project.kos_projeck','>',50000000)
                                                ->orWhere('pemantauan_project.Is_changed_to_va','=',1);
                                            })->get();
                    $data5 =   DB::table('ve_perancagan_makmal')
                                            ->leftJoin('pemantauan_project', 'pemantauan_project.id', '=', 've_perancagan_makmal.pp_id')
                                            ->when($isAdmin, function ($query) use($user){
                                                return $query->where('pemantauan_project.bahagian_pemilik',$user->bahagian_id);
                                            })
                                            ->where('ve_perancagan_makmal.row_status',1)
                                            ->where(function($query4) {
                                                $query4->where('pemantauan_project.kos_projeck','>',50000000)
                                                ->orWhere('pemantauan_project.Is_changed_to_va','=',1);
                                            })->get();
                                            
                    // To select only month data for each project
                    $months =   DB::table('ve_perancagan_makmal')
                                            ->leftJoin('pemantauan_project', 'pemantauan_project.id', '=', 've_perancagan_makmal.pp_id')
                                            ->when($isAdmin, function ($query) use($user){
                                                return $query->where('pemantauan_project.bahagian_pemilik',$user->bahagian_id);
                                            })
                                            ->whereYear('ve_perancagan_makmal.created_at', date('Y'))->where('ve_perancagan_makmal.row_status',1)
                                            ->where(function($query2) {
                                                $query2->where('pemantauan_project.kos_projeck','>',50000000)
                                                ->orWhere('pemantauan_project.Is_changed_to_va','=',1);
                                            })
                                            ->selectRaw('MONTH(tarikh_mula) as month_mula, MONTH(tarikh_tamat) as month_tamat, YEAR(ve_perancagan_makmal.created_at) as created_at_year')
                                            ->distinct()
                                            ->get();

                }
                else if($type=='VR')
                {
                    $data= DB::table('vr_perancagan_makmal')
                                            ->leftJoin('pemantauan_project', 'pemantauan_project.id', '=', 'vr_perancagan_makmal.pp_id')
                                            ->when($isAdmin, function ($query) use($user){
                                                return $query->where('pemantauan_project.bahagian_pemilik',$user->bahagian_id);
                                            })
                                            ->where('vr_perancagan_makmal.row_status',1)
                                            ->where(function($query) {
                                                        $query->where('pemantauan_project.kos_projeck','>',50000000)
                                                        ->orWhere('pemantauan_project.Is_changed_to_va','=',1);
                                            })->get();

                    $data2 =   DB::table('pemantauan_project')
                                                ->when($isAdmin, function ($query) use($user){
                                                    return $query->where('pemantauan_project.bahagian_pemilik',$user->bahagian_id);
                                                })
                                                ->where(function($query1) {
                                                    $query1->where('pemantauan_project.kos_projeck','>',50000000)
                                                    ->orWhere('pemantauan_project.Is_changed_to_va','=',1);
                                                })->get();
                    $data3 =   DB::table('vr_perancagan_makmal')
                                            ->leftJoin('pemantauan_project', 'pemantauan_project.id', '=', 'vr_perancagan_makmal.pp_id')
                                            ->when($isAdmin, function ($query) use($user){
                                                return $query->where('pemantauan_project.bahagian_pemilik',$user->bahagian_id);
                                            })
                                            ->whereYear('vr_perancagan_makmal.created_at', date('Y'))->where('vr_perancagan_makmal.row_status',1)
                                            ->where(function($query2) {
                                                $query2->where('pemantauan_project.kos_projeck','>',50000000)
                                                ->orWhere('pemantauan_project.Is_changed_to_va','=',1);
                                            })->get();
                    $data4 =   DB::table('vr_perancagan_makmal')
                                            ->leftJoin('pemantauan_project', 'pemantauan_project.id', '=', 'vr_perancagan_makmal.pp_id')
                                            ->when($isAdmin, function ($query) use($user){
                                                return $query->where('pemantauan_project.bahagian_pemilik',$user->bahagian_id);
                                            })
                                            ->whereDate('vr_perancagan_makmal.tarikh_mula', date('Y-m-d'))->where('vr_perancagan_makmal.row_status',1)
                                            ->where(function($query3) {
                                                $query3->where('pemantauan_project.kos_projeck','>',50000000)
                                                ->orWhere('pemantauan_project.Is_changed_to_va','=',1);
                                            })->get();
                    $data5 =   DB::table('vr_perancagan_makmal')
                                            ->leftJoin('pemantauan_project', 'pemantauan_project.id', '=', 'vr_perancagan_makmal.pp_id')
                                            ->when($isAdmin, function ($query) use($user){
                                                return $query->where('pemantauan_project.bahagian_pemilik',$user->bahagian_id);
                                            })
                                            ->where('vr_perancagan_makmal.row_status',1)
                                            ->where(function($query4) {
                                                $query4->where('pemantauan_project.kos_projeck','>',50000000)
                                                ->orWhere('pemantauan_project.Is_changed_to_va','=',1);
                                            })->get();

                    // To select only month data for each project
                    $months =   DB::table('vr_perancagan_makmal')
                                            ->leftJoin('pemantauan_project', 'pemantauan_project.id', '=', 'vr_perancagan_makmal.pp_id')
                                            ->when($isAdmin, function ($query) use($user){
                                                return $query->where('pemantauan_project.bahagian_pemilik',$user->bahagian_id);
                                            })
                                            ->whereYear('vr_perancagan_makmal.created_at', date('Y'))->where('vr_perancagan_makmal.row_status',1)
                                            ->where(function($query2) {
                                                $query2->where('pemantauan_project.kos_projeck','>',50000000)
                                                ->orWhere('pemantauan_project.Is_changed_to_va','=',1);
                                            })
                                            ->selectRaw('MONTH(tarikh_mula) as month_mula, MONTH(tarikh_tamat) as month_tamat, YEAR(vr_perancagan_makmal.created_at) as created_at_year')
                                            ->distinct()
                                            ->get();

                }
                else
                {
                    $data= DB::table('vm_perancagan_makmal')
                                            ->leftJoin('pemantauan_project', 'pemantauan_project.id', '=', 'vm_perancagan_makmal.pp_id')
                                            ->when($isAdmin, function ($query) use($user){
                                                return $query->where('pemantauan_project.bahagian_pemilik',$user->bahagian_id);
                                            })
                                            ->where('vm_perancagan_makmal.row_status',1)
                                            ->where(function($query) {
                                                        $query->where('pemantauan_project.kos_projeck','>',50000000)
                                                        ->orWhere('pemantauan_project.Is_changed_to_va','=',1);
                                            })->get();

                    $data2 =   DB::table('pemantauan_project')
                                                ->when($isAdmin, function ($query) use($user){
                                                    return $query->where('pemantauan_project.bahagian_pemilik',$user->bahagian_id);
                                                })
                                                ->where(function($query1) {
                                                    $query1->where('pemantauan_project.kos_projeck','>',50000000)
                                                    ->orWhere('pemantauan_project.Is_changed_to_va','=',1);
                                                })->get();
                    $data3 =   DB::table('vm_perancagan_makmal')
                                            ->leftJoin('pemantauan_project', 'pemantauan_project.id', '=', 'vm_perancagan_makmal.pp_id')
                                            ->when($isAdmin, function ($query) use($user){
                                                return $query->where('pemantauan_project.bahagian_pemilik',$user->bahagian_id);
                                            })
                                            ->whereYear('vm_perancagan_makmal.created_at', date('Y'))->where('vm_perancagan_makmal.row_status',1)
                                            ->where(function($query2) {
                                                $query2->where('pemantauan_project.kos_projeck','>',50000000)
                                                ->orWhere('pemantauan_project.Is_changed_to_va','=',1);
                                            })->get();
                    $data4 =   DB::table('vm_perancagan_makmal')
                                            ->leftJoin('pemantauan_project', 'pemantauan_project.id', '=', 'vm_perancagan_makmal.pp_id')
                                            ->when($isAdmin, function ($query) use($user){
                                                return $query->where('pemantauan_project.bahagian_pemilik',$user->bahagian_id);
                                            })
                                            ->whereDate('vm_perancagan_makmal.tarikh_mula', date('Y-m-d'))->where('vm_perancagan_makmal.row_status',1)
                                            ->where(function($query3) {
                                                $query3->where('pemantauan_project.kos_projeck','>',50000000)
                                                ->orWhere('pemantauan_project.Is_changed_to_va','=',1);
                                            })->get();
                    $data5 =   DB::table('vm_perancagan_makmal')
                                            ->leftJoin('pemantauan_project', 'pemantauan_project.id', '=', 'vm_perancagan_makmal.pp_id')
                                            ->when($isAdmin, function ($query) use($user){
                                                return $query->where('pemantauan_project.bahagian_pemilik',$user->bahagian_id);
                                            })
                                            ->where('vm_perancagan_makmal.row_status',1)
                                            ->where(function($query4) {
                                                $query4->where('pemantauan_project.kos_projeck','>',50000000)
                                                ->orWhere('pemantauan_project.Is_changed_to_va','=',1);
                                            })->get();

                    // To select only month data for each project
                    $months = DB::table('vm_perancagan_makmal')
                                            ->leftJoin('pemantauan_project', 'pemantauan_project.id', '=', 'vm_perancagan_makmal.pp_id')
                                            ->when($isAdmin, function ($query) use($user){
                                                return $query->where('pemantauan_project.bahagian_pemilik',$user->bahagian_id);
                                            })
                                            ->whereYear('vm_perancagan_makmal.created_at', date('Y'))->where('vm_perancagan_makmal.row_status',1)
                                            ->where(function($query2) {
                                                $query2->where('pemantauan_project.kos_projeck','>',50000000)
                                                ->orWhere('pemantauan_project.Is_changed_to_va','=',1);
                                            })
                                            ->selectRaw('MONTH(tarikh_mula) as month_mula, MONTH(tarikh_tamat) as month_tamat, YEAR(vm_perancagan_makmal.created_at) as created_at_year')
                                            ->distinct()
                                            ->get();
                }

                $uniqueMonths = collect($months)->pluck('month_mula')->merge(collect($months)->pluck('month_tamat'))->unique();
                $years = $months->pluck('created_at_year')->unique();
                
        return response()->json([
            'code' => '200',
            'status' => 'Success',
            'data' => $data,
            'data2'=>$data2,
            'data3'=>$data3,
            'data4'=>$data4,
            'data5'=>$data5,
            'months'=>$months,
            'uniqueMonths'=>$uniqueMonths,
            'years'=>$years,
        ]);
    }

    public function kalenderDataDetails(Request $request){
        // dd($key);
        

        if($request->type=='VE')
        {
            $data = VEKalendarModel::where('pp_id',$request->id)
                             ->where('kategori',$request->kategori)
                             ->where('row_status',1)->first();

        }
        else if($request->type=='VR')
        {
            $data = VRKalendarModel::where('pp_id',$request->id)
                             ->where('kategori',$request->kategori)
                             ->where('row_status',1)->first();

        }
        else
        {
            $data = KalendarModel::where('pp_id',$request->id)
                             ->where('kategori',$request->kategori)
                             ->where('row_status',1)->first();
        }
        
        return response()->json([
            'code' => '200',
            'status' => 'Success',
            'data' => $data
        ]);
    }
    public function brifProjectDetails ($id)
    {
        try {
 
                $data['result'] = DB::table('pemantauan_project')
                           ->join('ref_bahagian','ref_bahagian.id', '=','pemantauan_project.bahagian_pemilik')
                           ->join('ref_jenis_kategori','ref_jenis_kategori.id', '=','pemantauan_project.jenis_kategori_code')
                           ->join('status','status.status', '=','pemantauan_project.status_perlaksanaan')
                           ->select('pemantauan_project.*','ref_bahagian.nama_bahagian','ref_jenis_kategori.name as jenis_kategori_name','status.status_name')
                           ->where('pemantauan_project.id','=',$id)->first();

                $data['output'] = DB::table('pemantauan_output')->where('pemantauan_output.pp_id','=',$id)->get();
                $data['outcome'] = DB::table('pemantauan_outcome')->where('pemantauan_outcome.pp_id','=',$id)->get();
                $data['kos'] = DB::table('pemantauan_kewangan_deails')
                                 ->join('REF_Komponen','REF_Komponen.id', '=','pemantauan_kewangan_deails.Komponen_id')
                                 ->where('pp_id','=',$id)->first();

                $data['kpi'] = DB::table('pemantauan_kpi')
                                    ->join('REF_Unit','REF_Unit.id', '=','pemantauan_kpi.unit')
                                    ->where('pemantauan_kpi.pp_id','=',$id)->get();


                $data['lokasi'] = DB::table('pemantauan_negeri_lokas')
                           ->join('ref_negeri','ref_negeri.id', '=','pemantauan_negeri_lokas.negeri_id')
                           ->join('ref_parliment','ref_parliment.id', '=','pemantauan_negeri_lokas.parlimen_id')
                           ->join('ref_daerah','ref_daerah.id', '=','pemantauan_negeri_lokas.daerah_id')
                           ->join('ref_dun','ref_dun.id', '=','pemantauan_negeri_lokas.dun_id')
                           ->select('ref_dun.nama_dun','ref_negeri.nama_negeri','ref_daerah.nama_daerah','ref_parliment.nama_parlimen')
                           ->where('pemantauan_negeri_lokas.pp_id','=',$id)->get();
                
                $data['skop_project'] = PemantauanSkopProjects::where('pp_id',$id)->with(['pemantauansubskopProjects','pemantauanskopOptions'])->get();
                $data['sub_skops'] = SubSkopOption::where('is_hidden', '!=', 1)->get();
                $data['units'] = \App\Models\Units::where('IsActive','=',1)->get();


            
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
            ]);

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

    public function getFasilitatorList(){

        try {
            $new_bayaran='Fasilitator';
            $result = PemantauanFasilitator::query()->with(
                    [
                        'newfasilitator' => function ($query) use ($new_bayaran) {
                            $query->where('fasilitator_type', '=', $new_bayaran);
                        },
                        'bahagian','jabatan','jawatan','gredJawatan','fasilitator'
                    ])
                    ->where('pemantauan_fasilitator.IsActive','=',1)
                    ->orderBy('updated_at','DESC')
                    ->get();
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);

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

    public function getFasilitatorListById($id){

        try {

            $result['data'] = DB::table('pemantauan_fasilitator')
                        ->leftjoin('ref_jawatan','ref_jawatan.id', '=','pemantauan_fasilitator.jawatan_id')
                        ->leftjoin('ref_bahagian','ref_bahagian.id', '=','pemantauan_fasilitator.bahagian_id')
                        ->select('pemantauan_fasilitator.*','ref_jawatan.nama_jawatan','ref_bahagian.nama_bahagian')
                        ->where('pemantauan_fasilitator.id','=',$id)->get();
        
            $result['count'] = VmButiranFasilitator::where('fasilitator_id',$id)->count(); // Replace 'whereNotNull' with appropriate conditions if needed
            $result['ketua_count'] = VmButiranFasilitator::where('fasilitator_type','Ketua Fasilitator')->where('fasilitator_id',$id)->count(); 
            $result['fasi_count'] = VmButiranFasilitator::where('fasilitator_type','Fasilitator')->where('fasilitator_id',$id)->count(); 

            $result['table_data'] = DB::table('vm_butiran_fasilitators')
                        ->leftjoin('vm_butirans','vm_butirans.id', '=','vm_butiran_fasilitators.butiran_id')
                        ->leftjoin('pemantauan_project','pemantauan_project.id', '=','vm_butirans.pp_id')
                        ->leftjoin('rolling_plans','rolling_plans.id', '=','pemantauan_project.rolling_plan_code')
                        ->select('pemantauan_project.*','rolling_plans.rmk','rolling_plans.name as rolling_plan_name','vm_butirans.*','vm_butiran_fasilitators.*')
                        ->where('vm_butiran_fasilitators.fasilitator_id','=',$id)->get();
            
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);

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

    public function addFasilitator(Request $request){

        try {

            $data=$request->toArray();
            $fasilitator= new PemantauanFasilitator;
            $fasilitator->nama_fasilitator = $request->fasilitator_name;
            // $fasilitator->tugas            = $request->tugas;
            // $fasilitator->tugas_id         = $request->tugas_id;
            $fasilitator->bahagian_id      = $request->bahagian;
            $fasilitator->jawatan_id       = $request->jawatan;
            $fasilitator->gred_id          = $request->gred;
            $fasilitator->fasilitator_type = $request->fasilitator_type;
            $fasilitator->jabatan_id       = $request->jabatan;
            $fasilitator->dibuat_oleh      = $request->user_id;
            $fasilitator->dikemaskini_oleh = $request->user_id;
            $fasilitator->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
            $fasilitator->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
            $fasilitator->save();
            
            return response()->json([
                'code' => '200',
                'status' => 'Success'
            ]);

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

    public function updateFasilitator(Request $request){

        try {

            $data=$request->toArray();
            $fasilitator= PemantauanFasilitator::where('id',$request->id)->first();
            $fasilitator->nama_fasilitator = $request->fasilitator_name;
            // $fasilitator->tugas            = $request->tugas;
            // $fasilitator->tugas_id         = $request->tugas_id;
            $fasilitator->bahagian_id      = $request->bahagian;
            $fasilitator->jawatan_id       = $request->jawatan;
            $fasilitator->gred_id          = $request->gred;
            $fasilitator->fasilitator_type = $request->fasilitator_type;
            $fasilitator->jabatan_id       = $request->jabatan;
            $fasilitator->dibuat_oleh      = $request->user_id;
            $fasilitator->dikemaskini_oleh = $request->user_id;
            $fasilitator->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
            $fasilitator->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
            $fasilitator->update();
            
            return response()->json([
                'code' => '200',
                'status' => 'Success'
            ]);

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

    public function setAsMakmalVa(Request $request){

        try {

            $va_data= PemantauanProject::where('id',$request->id)->first();
            $va_data->Is_changed_to_va = 1;
            $va_data->dikemaskini_oleh = $request->user_id;
            $va_data->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
            $va_data->update();
            
            return response()->json([
                'code' => '200',
                'status' => 'Success'
            ]);

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

    public function pelakasanan(Request $request){
        // dd($request);
        try {
            // dd($terima_file_name);
            $result_data = MaklumatPelakasanaanMakmal::with('media')->where('vm_type', $request->type)->where('pp_id', $request->pp_id)->first();
            if ($result_data) {

                $terima_file_name=$request->file('terima_file_name')->getClientOriginalName();
                $result_data->tarikh_terima = $request->tarikh_terima;
                $result_data->terima_file_name = $terima_file_name;
                $result_data->update();

                if($request->file('terima_file_name')) {
                    $result_data->clearMediaCollection('terima_file_name');
                    $result_data
                    ->addMedia($request->file('terima_file_name'))
                    ->toMediaCollection('terima_file_name');
                }
            }
            else {

                $kemuka_file_name=$request->file('kemuka_file_name')->getClientOriginalName();
                $result = MaklumatPelakasanaanMakmal::Create(
                    [
                        'pp_id'=>$request->pp_id,
                        'tarikh_kemuka' => $request->tarikh_kemuka,
                        // 'tarikh_terima' => $request->tarikh_terima,
                        'kemuka_file_name' => $kemuka_file_name,
                        //'terima_file_name' => $terima_file_name,
                        'dibuat_oleh' => $request->id,
                        'dikemaskini_oleh' => $request->id,
                        'vm_type' => $request->type,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s')
                    ]
                    );
                
                $data = MaklumatPelakasanaanMakmal::with('media')->where('id',$result['id'])->first();

                if($request->file('kemuka_file_name')) {
                    $data->clearMediaCollection('kemuka_file_name');
                    $data->addMedia($request->file('kemuka_file_name'))
                         ->toMediaCollection('kemuka_file_name');
                }

            }
           

                // $va_data= PemantauanProject::where('id',$request->pp_id)->first();
                // $va_data->va_status = 36;
                // $va_data->update();


            //print_r($result_data);exit;


            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data'=> $result_data
            ]);
        }catch (\Throwable $th) {
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
    public function maklumat_pelakasanaan_makmal(Request $request){
        try {

            $result =MaklumatPelakasanaanMakmal::where('pp_id',$request->id)->where('row_status',1)->orderBy('id', 'DESC')->get(); 
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);

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
    
    public function update_status_perlaksanaan(Request $request){
        try {
        
        if($request->update_status_perlaksanaan==35)
        {
            $result=DB::table('pemantauan_project')->where('id', $request->kod)->update(array('status_perlaksanaan' => $request->update_status_perlaksanaan,'ve_status' => 36,'current_status'=>36,'penjilidan_status_ve'=>2));
        }
        else
        {
            $result=DB::table('pemantauan_project')->where('id', $request->kod)->update(array('status_perlaksanaan' => $request->update_status_perlaksanaan,'va_status' => 36,'current_status'=>36));
        }
               
        
        return response()->json([
            'code' => '200',
            'status' => 'Success',
            'data' => $result,
        ]);
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

    public function tandatanganData(Request $request){
        // dd($request);
        try {
            $terima_file_name=$request->file('terima_file_name')->getClientOriginalName();
            // dd($terima_file_name);
            $result = vm_tandatangan::Create(
            [
                'pp_id'=>$request->pp_id,
                'kategori_tandatangan' => $request->kategori_tandatangan,
                'tarikh_tandatangan' => $request->tarikh_tandatangan,
                'tandatangan_file_name' => $terima_file_name,
                'dibuat_oleh' => $request->id,
                'dikemaskini_oleh' => $request->id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s')
            ]
            );

            // $va_data= PemantauanProject::where('id',$request->pp_id)->first();
            //     $va_data->va_status = 36;
            //     $va_data->current_status =0;
            //     $va_data->update();

            $result_data = vm_tandatangan::with('media')->where('id',$result['id'])->first();

            //print_r($result_data);exit;

            if($request->file('kemuka_file_name')) {
                $result_data->clearMediaCollection('kemuka_file_name');
                $result_data
                ->addMedia($request->file('kemuka_file_name'))
                ->toMediaCollection('kemuka_file_name');
            }

            if($request->file('terima_file_name')) {
                $result_data->clearMediaCollection('terima_file_name');
                $result_data
                ->addMedia($request->file('terima_file_name'))
                ->toMediaCollection('terima_file_name');
            }

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data'=> $result_data
            ]);
        }catch (\Throwable $th) {
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


    public function va_tandatanganData(Request $request){

        try {

            $project = PemantauanProject::whereId($request->id)->first();
            $result =vm_tandatangan::where('row_status',1)->where('pp_id',$request->id)->orderBy('id', 'DESC')->get(); 
            $result_data = vm_tandatangan::with('media')->where('pp_id',$request->id)->first();
            if($result_data)
            {
                $noc_media =  $result_data->getFirstMedia('noc_file');
            }
            else
            {
                $noc_media='';
            }

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
                'noc_file' => $noc_media,
                'project' => $project
            ]);

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

    public function getProjectData ($id)
    {
        try {
 
                $data['result'] = DB::table('pemantauan_project')
                           ->join('ref_bahagian','ref_bahagian.id', '=','pemantauan_project.bahagian_pemilik')
                           ->join('ref_jenis_kategori','ref_jenis_kategori.id', '=','pemantauan_project.jenis_kategori_code')
                           ->join('status','status.status', '=','pemantauan_project.status_perlaksanaan')
                           ->select('pemantauan_project.*','ref_bahagian.nama_bahagian','ref_jenis_kategori.name as jenis_kategori_name','status.status_name')
                           ->where('pemantauan_project.id','=',$id)->first();

                $data['output'] = DB::table('pemantauan_output')->where('pemantauan_output.pp_id','=',$id)->get();
                $data['outcome'] = DB::table('pemantauan_outcome')->where('pemantauan_outcome.pp_id','=',$id)->get();
                $data['kos'] = DB::table('pemantauan_kewangan_deails')
                                 ->join('REF_Komponen','REF_Komponen.id', '=','pemantauan_kewangan_deails.Komponen_id')
                                 ->where('pp_id','=',$id)->first();
                
                $data['skop_project'] = PemantauanSkopProjects::where('pp_id',$id)->with(['pemantauansubskopProjects','pemantauanskopOptions'])->get();
                $data['sub_skops'] = SubSkopOption::where('is_hidden', '!=', 1)->get();

                $data['vm_objectif'] = VmObjektif::where('type','VA')->where('pp_id',$id)->get(); 
                $data['vm_skop'] = VmSkop::where('type','VA')->where('pp_id',$id)->get();
                $data['vm_output'] = VmOutput::where('type','VA')->where('pp_id',$id)->get();
                $data['vm_outcome'] = VmOutcome::where('type','VA')->where('pp_id',$id)->get();
                $data['units'] = \App\Models\Units::where('IsActive','=',1)->get();


            
                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'data'=> $data
                ]);
            }catch (\Throwable $th) {
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
                    'error' => $th->getMessage(),
                ]);
            }
        }

        public function VRtandatanganData(Request $request){
        try {
            $terima_file_name=$request->file('terima_file_name')->getClientOriginalName();
            // dd($request->toArray());
            $result = vr_tandatangan::Create(
            [
                'pp_id'=>$request->pp_id,
                'jenis_jabatan'=>$request->jps,
                'kategori_tandatangan' => $request->kategori_tandatangan,
                'tarikh_tandatangan' => $request->tarikh_tandatangan,
                'tandatangan_file_name' => $terima_file_name,
                'dibuat_oleh' => $request->id,
                'dikemaskini_oleh' => $request->id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s')
            ]
            );

            $result_data = vr_tandatangan::with('media')->where('id',$result['id'])->first();

            //print_r($result_data);exit;

            if($request->file('kemuka_file_name')) {
                $result_data->clearMediaCollection('kemuka_file_name');
                $result_data
                ->addMedia($request->file('kemuka_file_name'))
                ->toMediaCollection('kemuka_file_name');
            }

            if($request->file('terima_file_name')) {
                $result_data->clearMediaCollection('terima_file_name');
                $result_data
                ->addMedia($request->file('terima_file_name'))
                ->toMediaCollection('terima_file_name');
            }

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data'=> $result_data
            ]);
        }catch (\Throwable $th) {
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
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function GetPengeculianData(Request $request){
        try {

            $result =PengeculianUpdate::where('row_status',1)->where('pp_id',$request->id)->where('type','=',$request->type)->orderBy('id', 'DESC')->get(); 
            
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);

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


    public function VRformData(Request $request){
        // dd($request);
        try { 
            $validator = Validator::make($request->all(),[
                'Cadangan_Pra_Makmal' => ['required', 'string', 'max:255'],
                'Pra_Makmal_Sebenar' => ['required', 'string', 'max:255'],
                'keputusan_mesyuarat' => ['required', 'string', 'max:255'],
                'surat_jemputan' => ['required', 'max:5000','mimes:pdf,png'],
                'minit_mesyuarat' => ['required', 'max:5000','mimes:pdf,png'],
            ]);

            if(!$validator->fails()) {      
                if($request->id) {
                    $data = VmMmpm::where('id', $request->id)->update([
                        'pra_makmal_sebenar' => $request->Pra_Makmal_Sebenar,
                        'keputusan_mesyuarat' => $request->keputusan_mesyuarat,
                        'sjm_file_name' => $request->file('surat_jemputan')->getClientOriginalName(),
                        'mm_file_name' => $request->file('minit_mesyuarat')->getClientOriginalName(),
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'type' => $request->type,
                    ]);
                }else {               
                    
                    
                $data = VmMmpm::create([       
                        'pp_id' => $request->pp_id,
                        'cadangan_pra_makmal' => $request->Cadangan_Pra_Makmal,
                        'pra_makmal_sebenar' => $request->Pra_Makmal_Sebenar,
                        'keputusan_mesyuarat' => $request->keputusan_mesyuarat,
                        'sjm_file_name' => $request->file('surat_jemputan')->getClientOriginalName(),
                        'mm_file_name' => $request->file('minit_mesyuarat')->getClientOriginalName(),
                        'row_status' => 1,
                        'is_hidden' => 0,                    
                        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'type' => $request->type,
                    ]);
                }

                $vr_data= PemantauanProject::where('id',$request->pp_id)->first();
                    $vr_data->vr_status = 24;
                    $vr_data->current_status = 24;
                    $vr_data->update();
                // dd($request->all());

                if($request->id) {
                    $data = VmMmpm::where('id', $request->id)->first();
                }

                if($request->file('surat_jemputan')) {
                    $data->clearMediaCollection('surat_jemputan_mesyuarat');
                    $data->addMedia($request->file('surat_jemputan'))
                              ->toMediaCollection('surat_jemputan_mesyuarat', 'vm_mmpm');
                }

                if($request->file('minit_mesyuarat')){
                    $data->clearMediaCollection('minit_mesyuara');
                    $data->addMedia($request->file('minit_mesyuarat'))
                              ->toMediaCollection('minit_mesyuara','vm_mmpm');
                }

                return response()->json([
                    'code' => '200',
                    'status' => 'Sucess',
                    'data' => $data,
                ]);
            }else {                
                return response()->json([
                    'code' => '422',
                    'status' => 'Unprocessable Entity',
                    'data' => $validator->errors(),
                ]);
            }
           
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
            ]);

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

                'error' => $th->getMessage(),
            ]);
        }
    }
    public function previewPengeculianfile(Request $request ,Media $mediaItem){
        $id = $request->id;
        $doc = PengeculianUpdate::with('media')->where('id','=',$id)->where('type','=',$request->type)->first();
        $mediaItem = $doc->getFirstMedia('surat_lampiran');
        return response()->download($mediaItem->getPath(), $mediaItem->file_name);
    }

    public function selesaiUpdate(Request $request)
    {
        if($request->type=='VA')
        {
            $status = '30';

            PemantauanProject::where('id', $request->pp_id)->update([
                'status_perlaksanaan' => 36,'current_status' => '0', 'va_status' => '23', 've_status' => '23', 'vr_status' => '23'
            ]);

        }
        else if($request->type=='VR')
        {
            $status = '36';

            PemantauanProject::where('id', $request->pp_id)->update([
                'status_perlaksanaan' => 36,'current_status' => '0', 'vr_status' => '23'
            ]);

        }else{
            $status = '36';

            PemantauanProject::where('id', $request->pp_id)->update([
                'status_perlaksanaan' => 35,'current_status' => '0', 've_status' => '23', 'vr_status' => '23'
            ]);
        }

        $data = VmMakmalKajianNilai::create([
            'pp_id' => $request->pp_id,
            'kos_selepas_makmal' => '0.00',
            'pengecualian' => 1,
            'status' => $status,
            'laporan_file_name' => 'NULL',
            'row_status' => 1,
            'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
            'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            'dibuat_oleh' => $request->user_id,
            'dikemaskini_oleh' => $request->user_id,
            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            'type' => $request->type,
        ]);

         // $va_data= PemantauanProject::where('id',$request->pp_id)->first();
            // $va_data->va_status = 23;
            // $va_data->current_status = 23;
            // $va_data->update();

    }

    public function PengeculianUpdate(Request $request){

        try {
            
            $surat_lampiran=$request->file('surat_lampiran')->getClientOriginalName();
            $result = PengeculianUpdate::Create(
            [
                'pp_id'=>$request->pp_id,
                'pengecualian'=>$request->pengecualian,
                'pengeculian_khas' => $request->pengeculian_khas,
                'surat_lampiran' => $surat_lampiran,
                'type' => $request->type,
                'dibuat_oleh' => $request->user_id,
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s')
            ]
            );

            $result_data = PengeculianUpdate::with('media')->where('id',$result['id'])->where('type','=',$request->type)->first();

            // $va_data= PemantauanProject::where('id',$request->pp_id)->first();
            // $va_data->va_status = 23;
            // $va_data->current_status = 23;
            // $va_data->update();


            if($request->file('surat_lampiran')) {
                $result_data->clearMediaCollection('surat_lampiran');
                $result_data
                ->addMedia($request->file('surat_lampiran'))
                ->toMediaCollection('surat_lampiran');
            }

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data'=> $result_data
            ]);
        }catch (\Throwable $th) {
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
                'error' => $th->getMessage(),
            ]);
        }

    }

    public function mmpms_vr($id){
        try {

            $result =VmMmpm::where('pp_id', $id)->where('row_status',1)->where('type','VR')->orderBy('id', 'DESC')->get(); 
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);

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

    public function selesai(Request $request){
        // dd($request);
        try{
            $result=PemantauanProject::where('id', $request->pp_id)->update([
                    'status_perlaksanaan' => $request->status,
                    'current_status' => 36,
                    'vr_status' =>36
                ]);

            $result_data=PemantauanProject::where('id', $request->pp_id)->first();


        return response()->json([
            'code' => '200',
            'status' => 'Success',
            'data' => $result_data,
        ]);
       }
       catch (\Throwable $th) {
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
            'error' => $th->getMessage(),
        ]);
      }
    }

    public function NocUpdate(Request $request){
        // dd($request);
        try{
            $result=PemantauanProject::where('id', $request->pp_id)->update(['noc_status' => $request->status]);

        return response()->json([
            'code' => '200',
            'status' => 'Success',
            'data' => $result,
        ]);
       }
       catch (\Throwable $th) {
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
            'error' => $th->getMessage(),
        ]);
      }
    }

    public function vr_tandatanganData($kod){
        try{
            $result['datas']=vr_tandatangan::where('pp_id', $kod)->get();
            $result['first']=vr_tandatangan::where('pp_id', $kod)->first();

        return response()->json([
            'code' => '200',
            'status' => 'Success',
            'data' => $result,
        ]);
       }
       catch (\Throwable $th) {
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
            'error' => $th->getMessage(),
        ]);
      }
    }

    public function updatePenjidianData(Request $request){
        try {
               if($request->type=='VA')
               {
                  if($request->update_status==1)
                  {
                    $status=30;
                    $va_status=32;
                    $current_status=32;
                  }
                  else
                  {
                    $status=31;
                    $va_status=36;
                    $current_status=36;
                  }
                $result=DB::table('pemantauan_project')->where('id', $request->kod)->update(array('penjilidan_status_va' => $request->update_status,'status_perlaksanaan'=>$status,'va_status'=>$va_status,'current_status' => $current_status));
               }
               else
               {
                if($request->update_status==3)
                  {
                    $result=DB::table('pemantauan_project')->where('id', $request->kod)->update(array('penjilidan_status_ve' => $request->update_status,'status_perlaksanaan'=>36,'ve_status'=>36,'current_status' => 36));
                  }
                  else
                  {
                    $result=DB::table('pemantauan_project')->where('id', $request->kod)->update(array('penjilidan_status_ve' => $request->update_status,'ve_status'=>32,'current_status' => 32));
                  }
               }
                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'data' => $result,
                ]);
                } catch (\Throwable $th) {
                logger()->error($th->getMessage());

                return response()->json([
                    'code' => '500',
                    'status' => 'Failed',
                    'error' => $th,
                ]);
            }
    }

        public function addPenjidianData(Request $request){

            try {

                if($request->kemukakan_file)
                {
                    $tarikh=$request->tarikh_kemukakan;
                    $kemuka_file_name=$request->kemukakan_file;
                    $peranan = 'Kemukakan';
                    $file_name='kemukakan_file';
                    $this->createPenjidianData($tarikh,$kemuka_file_name,$peranan,$request,$file_name);

                }

                if($request->terima_file)
                {
                    $tarikh=$request->tarikh_terima;
                    $kemuka_file_name=$request->terima_file;
                    $peranan = 'Terima';
                    $file_name='terima_file';
                    $this->createPenjidianData($tarikh,$kemuka_file_name,$peranan,$request,$file_name);
                }

                if($request->edaran_file)
                {
                    $tarikh=$request->tarikh_edaran;
                    $kemuka_file_name=$request->edaran_file;
                    $peranan = 'Edaran';
                    $file_name='edaran_file';
                    $this->createPenjidianData($tarikh,$kemuka_file_name,$peranan,$request,$file_name);
                }
                    
                
                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                ]);
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

        public function createPenjidianData($tarikh,$kemuka_file_name,$peranan,$request,$file_name)
        { 
            $data = Penjilidan::create([       
                'pp_id' => $request->pp_id,
                'tarikh' => $tarikh,
                'peranan' => $peranan,
                'type' => $request->type,
                'penjilidan_file' => $kemuka_file_name->getClientOriginalName(),
                'lampiran' => '',
                'row_status' => 1,
                'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'dibuat_oleh' => $request->user_id,
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            $result_data = Penjilidan::with('media')->where('id',$data['id'])->first();

            if($request->file($file_name)) {
                $result_data->clearMediaCollection($peranan);
                $result_data
                ->addMedia($request->file($file_name))
                ->toMediaCollection($peranan);
            }

            return $data;
        }

        public function getPenjidianData(Request $request){

            try {
                   
    
                    $data = Penjilidan::where('pp_id',$request->pp_id)->where('type',$request->type)->get();

                    return response()->json([
                        'code' => '200',
                        'status' => 'Success',
                        'data' => $data,
                    ]);
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

        public function tandakan_update(Request $request)
        {      

            $result_data = vm_tandatangan::with('media')->where('pp_id',$request->pp_id)->first(); //print_r($result_data);exit();


            if($result_data)
            {
                if($request->file('noc_file')) { 
                    $result_data->clearMediaCollection('noc_file');
                    $result_data
                    ->addMedia($request->file('noc_file'))
                    ->toMediaCollection('noc_file');
                }

            }
            

            return $result_data;
        }
}
?>
