<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use \App\Models\User;
use \App\Models\tempUser;
use \App\Models\UserPeranan;
use \App\Models\mapService;
use \App\Models\Pentadbir_modules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use \App\Notifications\UserRegistrattion;
use \App\Notifications\UserApproval;
use \App\Notifications\UserRejection;
use \App\Notifications\ChangePassword;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Http;
use Jenssegers\Agent\Facades\Agent;


class UserController extends Controller
{
    public function mapsService(){

        try{
            $data=mapService::with(['Module'=> function ($query) {
                $query->select('id', 'modul_name');
            }])->where('row_status','=',1)->get();
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());            
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
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function mapServiceData(Request $request){
        try{
            $data=$request->toArray();
            $mapService= new mapService;
            $mapService->modul_id=$data["module_id"];
            $mapService->nama_servis=$data["nama_servis"];
            $mapService->pautan_api=$data["pautan_nama"];
            $mapService->server=$data["server"];
            $mapService->save();
            return response()->json([
                'code' => '200',
                'status' => 'Success',
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());            
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
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function mapServiceEdit(Request $request){
        try{
            $data=$request->toArray();
            $modul_id=$data['modul_id'];
            $modul_list=Pentadbir_modules::all();
            $modulData=compact('modul_list');
            $data=mapService::where('id','=',$modul_id)->with(['Module'=> function ($query) {
                $query->select('id', 'modul_name');
            }])->get();
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => [$data,$modulData],
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());            
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
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }

    }

    public function mapsupdate(Request $request){
        try{
            $data=$request->toArray();
            $update_id=$data['update_id'];
            $module_id=$data['module_id'];
            $pautan=$data['pautan'];
            $edit_nama_servis=$data['edit_nama_servis'];
            $edit_server=$data['edit_server'];
            $mapsupdate=mapService::find($update_id);
            $mapsupdate->modul_id=$module_id;
            $mapsupdate->pautan_api=$pautan;
            $mapsupdate->nama_servis=$edit_nama_servis;
            $mapsupdate->server=$edit_server;
            $mapsupdate->dibuat_oleh=$request->user_id;
            $mapsupdate->dikemaskini_oleh=$request->user_id;
            $mapsupdate->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
            $mapsupdate->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
            $mapsupdate->save();
            if($mapsupdate->save()=='true'){
                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'data' => $data,
                ]);
            }
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());            
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
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }
    
    //
    public function getUsers(Request $request)
    {
        try {
            $isJps = 2;            
            if($request->isJps){
                $isJps = 1;
            }
            $users = \App\Models\User::
            with(['jawatan'=> function ($query) {
                $query->select('id', 'nama_jawatan');
            }])
            ->with(['jabatan'=> function ($query) {
                $query->select('id', 'nama_jabatan');
            }])
            ->with(['agensi'=> function ($query) {
                $query->select('id', 'nama_agensi');
            }])
            ->with(['bahagian'=> function ($query) {
                $query->select('id', 'nama_bahagian');
            }])
            ->with(['daerah'=> function ($query) {
                $query->select('id', 'nama_daerah');
            }])
            ->with(['gredJawatan'=> function ($query) {
                $query->select('id', 'nama_gred');
            }])
            ->with(['jenisPengguna'=> function ($query) {
                $query->select('id', 'nama_jenis_pengguna');
            }])
            ->with(['negeri'=> function ($query) {
                $query->select('id', 'nama_negeri');
            }])
            ->with(['updatedBy'])
            ->where('jenis_pengguna_id', $isJps)            
            ->where('row_status', 1)
            ->orderBy('id','DESC')
            ->get();
            
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $users,
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

    public function getTempUsers(Request $request)
    {
        try {
            $isJps = 2;            
            if($request->isJps){
                $isJps = 1;
            }
            $users = \App\Models\tempUser::
            with(['jawatan'=> function ($query) {
                $query->select('id', 'nama_jawatan');
            }])
            ->with(['jabatan'=> function ($query) {
                $query->select('id', 'nama_jabatan');
            }])
            ->with(['agensi'=> function ($query) {
                $query->select('id', 'nama_agensi');
            }])
            ->with(['bahagian'=> function ($query) {
                $query->select('id', 'nama_bahagian');
            }])
            ->with(['daerah'=> function ($query) {
                $query->select('id', 'nama_daerah');
            }])
            ->with(['gredJawatan'=> function ($query) {
                $query->select('id', 'nama_gred');
            }])
            ->with(['jenisPengguna'=> function ($query) {
                $query->select('id', 'nama_jenis_pengguna');
            }])
            ->with(['negeri'=> function ($query) {
                $query->select('id', 'nama_negeri');
            }])
            ->with(['updatedBy'])
            ->where('row_status', 1)
            ->with('media')
            ->orderBy('id','DESC');

            if($request->isJps=='3')
            {
                $users->where('status_pengguna_id', 2);   
            }
            else
            {
                $users->where('jenis_pengguna_id', $isJps);  
                $users->where('status_pengguna_id','!=', 2);             
            }
            $result=$users->get();
            
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());            
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
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function getAllTempUsers(Request $request)
    {
        try {            
            $users = \App\Models\tempUser::
            with(['jawatan'=> function ($query) {
                $query->select('id', 'nama_jawatan');
            }])
            ->with(['jabatan'=> function ($query) {
                $query->select('id', 'nama_jabatan');
            }])
            ->with(['agensi'=> function ($query) {
                $query->select('id', 'nama_agensi');
            }])
            ->with(['bahagian'=> function ($query) {
                $query->select('id', 'nama_bahagian');
            }])
            ->with(['daerah'=> function ($query) {
                $query->select('id', 'nama_daerah');
            }])
            ->with(['gredJawatan'=> function ($query) {
                $query->select('id', 'nama_gred');
            }])
            ->with(['jenisPengguna'=> function ($query) {
                $query->select('id', 'nama_jenis_pengguna');
            }])
            ->with(['negeri'=> function ($query) {
                $query->select('id', 'nama_negeri');
            }])       
            ->where('status_pengguna_id','!=', 2)     
            ->where('row_status', 1)
            ->with('media')
            ->orderBy('id','DESC')
            ->get();
            
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $users,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());            
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
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function createUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'nama' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'no_kod_penganalan' => ['required', 'string',  'max:255', 'unique:users,no_ic'],
                //'password' => ['required', 'string', 'min:8', 'confirmed'],
                'kategori' => ['required', 'integer', 'max:255'],
                //'no_telefon' => ['required', 'string', 'max:255'],
                'jawatan' => ['required', 'string', 'max:255'],
                // 'jabatan' => ['required', 'string', 'max:255'],
                'gred' => ['required', 'string', 'max:255'],
                // 'kementerian' => ['required', 'integer', 'max:255'],
                //'bahagian' => ['required', 'integer', 'max:255'],
                //'negeri' => ['required', 'integer', 'max:255'],
                //'daerah' => ['required', 'integer', 'max:255']
            ]
            ,[
                'no_kod_penganalan.unique' => 'No. Kad pengenalan telah digunakan',
                'email.unique' => 'Emel telah digunakan'
            ]);
            if(!$validator->fails()) {                
                $password = Str::random(10);
                $user = $this->createData($request->all(),$password); 

                SetUserType($user['id']);

                // $userDocumentPath = $request->file('documents')->store('user/documents/');
                // $userProfilePath = $request->file('profile_image')->store('user/profile/');
                // $user->gambar_profil = $userProfilePath;
                // $user->document_path = $userDocumentPath;
                // $user->save();

                //----------------- registration log -----------------------------------------------------------------
                $userdata = \App\Models\User::where('id',$user['id'])->with(['jawatan','jabatan','bahagian','kementerian'])->first();         
                $logged_user = \App\Models\User::where('id',$request->loged_user_id)->with(['jawatan'])->first();  
            
                $data=[
                                'user_id'=>$user['id'],
                                'user_ic_no'=>$userdata['no_ic'],
                                'user_jawatan'=>$userdata['jawatan']['nama_jawatan'],
                                'user_name'=>$userdata['name'],
                                'updated_by_user_id'=>$request->loged_user_id,
                                'updated_by_user_ic_no'=>$logged_user['no_ic'],
                                'updated_by_user_jawatan'=>$logged_user['jawatan']['nama_jawatan'],
                                'updated_by_user_name'=>$logged_user['name'],
                                'action_taken'=>'DAFTAR PENGGUNA - Pengguna Dalaman',
                                'created_on'=>Carbon::now()->format('Y-m-d H:i:s'),
                                'created_at'=>Carbon::now()->format('Y-m-d H:i:s'),
                                'updated_at'=>Carbon::now()->format('Y-m-d H:i:s')
                        ];
                DB::connection(env('DB_CONNECTION_AUDIT'))->table('registration_log')->insert($data);                

                //---------------------ends-------------------------------------------------------------------------------
                
                if($request->file('documents')) {
                    $user
                    ->addMedia($request->file('documents'))
                    ->toMediaCollection('document');
                }

                if($request->file('profile_image')){
                    $user
                    ->addMedia($request->file('profile_image'))
                    ->toMediaCollection('profile_image');
                }
                
                
                $user->notify(new userApproval($password, $user));
                return response()->json([
                    'code' => '200',
                    'status' => 'Sucess',
                    'data' => $user,
                ]);
            }else {                
                return response()->json([
                    'code' => '422',
                    'status' => 'Unprocessable Entity',
                    'data' => $validator->errors(),
                ]);
            }

        } catch (\Throwable $th) {
            logger()->error($th->getMessage());            
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
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }       
    }

    public function createTempUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'nama' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:temp_users','unique:users'],
                'no_kod_penganalan' => ['required', 'string',  'max:255', 'unique:temp_users,no_ic','unique:users,no_ic'],
                //'password' => ['required', 'string', 'min:8', 'confirmed'],
                'kategori' => ['required', 'integer', 'max:255'],
                'no_telefon' => ['required', 'string', 'max:255'],
                'jawatan' => ['required', 'string', 'max:255'],
                // 'jabatan' => ['required', 'string', 'max:255'],
                'gred' => ['required', 'string', 'max:255'],
                // 'kementerian' => ['required', 'integer', 'max:255'],
                // 'bahagian' => ['required', 'integer', 'max:255'],
                // 'negeri' => ['required', 'integer', 'max:255'],
                // 'daerah' => ['required', 'integer', 'max:255']
            ], [
                'no_kod_penganalan.unique' => 'No. Kad pengenalan telah digunakan',
                'email.unique' => 'Emel telah digunakan'
            ]);
            if(!$validator->fails()) {

                $password = Str::random(10);
                $user = $this->createTempData($request->all(),$password);

                // $userDocumentPath = $request->file('documents')->store('user/documents/');
                // $userProfilePath = $request->file('profile_image')->store('user/profile/');
                // $user->gambar_profil = $userProfilePath;
                // $user->document_path = $userDocumentPath;
                // $user->save();

                //----------------- registration log -----------------------------------------------------------------
                $userdata = \App\Models\tempUser::where('id',$user['id'])->with(['jawatan','jabatan','bahagian','kementerian'])->first();  
                
                $notification_data=[
                                        'user_id'=>$user['id'],
                                        'notification_type'=>1,
                                        'notification_sub_type'=>'create user outside',
                                        'notification'=>$userdata['name'].' '.'didaftarkan di laman web ini dengan Ic_no 12:'.$userdata['no_ic'],
                                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                                        'dibuat_oleh' => $user['id'],
                                        'dikemaskini_oleh' => $user['id'],
                                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                                    ];
                
                DB::connection(env('DB_CONNECTION'))->table('notification')->insert($notification_data);                

                $data=[
                                'user_id'=>$user['id'],
                                'user_ic_no'=>$userdata['no_ic'],
                                'user_jawatan'=>$userdata['jawatan']['nama_jawatan'],
                                'user_name'=>$userdata['name'],
                                'updated_by_user_id'=>$user['id'],
                                'updated_by_user_ic_no'=>$userdata['no_ic'],
                                'updated_by_user_jawatan'=>$userdata['jawatan']['nama_jawatan'],
                                'updated_by_user_name'=>$userdata['name'],
                                'action_taken'=>'DAFTAR PENGGUNA - Pengguna Luar',
                                'created_on'=>Carbon::now()->format('Y-m-d H:i:s'),
                                'created_at'=>Carbon::now()->format('Y-m-d H:i:s'),
                                'updated_at'=>Carbon::now()->format('Y-m-d H:i:s')
                        ];
                DB::connection(env('DB_CONNECTION_AUDIT'))->table('registration_log')->insert($data);                

                //---------------------ends-------------------------------------------------------------------------------

                if($request->file('documents')) {
                    $user
                    ->addMedia($request->file('documents'))
                    ->toMediaCollection('document');
                }

                if($request->file('profile_image')){
                    $user
                    ->addMedia($request->file('profile_image'))
                    ->toMediaCollection('profile_image');
                }                

                $user->notify(new UserRegistrattion($password,$userdata));
                return response()->json([
                    'code' => '200',
                    'status' => 'Sucess',
                    'data' => $user,
                ]);
            }else {                
                return response()->json([
                    'code' => '422',
                    'status' => 'Unprocessable Entity',
                    'data' => $validator->errors(),
                ]);
            }

        } catch (\Throwable $th) {
            logger()->error($th->getMessage());            
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
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }       

    }

    public function updateUser(Request $request)
    { 
        try {
            $validator = Validator::make($request->all(),[
                'id' => ['required', 'string', 'max:255'],
                'nama' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'no_kod_penganalan' => ['required', 'string',  'max:255'],
                //'password' => ['required', 'string', 'min:8', 'confirmed'],
                'kategori' => ['required', 'integer', 'max:255'],
                'no_telefon' => ['required', 'string', 'max:255'],
                'jawatan' => ['required', 'string', 'max:255'],
                // 'jabatan' => ['required', 'string', 'max:255'],
                'gred' => ['required', 'string', 'max:255'],
                // 'kementerian' => ['required', 'integer', 'max:255'],
                // 'bahagian' => ['required', 'integer', 'max:255'],
                // 'negeri' => ['required', 'integer', 'max:255'],
                // 'daerah' => ['required', 'integer', 'max:255']
            ]);
            if(!$validator->fails()) {   
                // print_r($request->all());exit;   
                $user_email=User::where('email',$request->email)->where('id','!=', $request->id)->first();      
                if($user_email)
                {
                    $email[]="E-mel telah diambil";
                    return response()->json([
                        'code' => '422',
                        'status' => 'Unprocessable Entity',
                        'data' => array('email'=>$email),
                    ]);
                }   

                $user_no_ic=User::where('no_ic',$request->no_kod_penganalan)->where('id','!=', $request->id)->first();      
                if($user_no_ic)
                {
                    $no_kod_penganalan[]="No_kod_penganalan telah pun diambil";
                    return response()->json([
                        'code' => '422',
                        'status' => 'Unprocessable Entity',
                        'data' => array('no_kod_penganalan'=>$no_kod_penganalan),
                    ]);
                }   

                $user = $this->updateUserData($request->all(),$request->id);
                
                if($request->previous_checked != $request->current_checked)
                {
                    \App\Models\UserPeranan::where('user_id',$request->id)->update(['row_status' => '0']);
                }
                
                $user_data=User::where('id',$request->id)->first();      

                if($request->file('documents')) {
                    $user_data->clearMediaCollection('documents');
                    $user_data->addMedia($request->file('documents'))
                              ->toMediaCollection('document');
                }

                if($request->file('profile_image')){
                    $user_data->clearMediaCollection('profile_image');
                    $user_data->addMedia($request->file('profile_image'))
                              ->toMediaCollection('profile_image');
                }
                

                SetUserType($request->id);
                
                //----------------- registration log -----------------------------------------------------------------
                    $user = \App\Models\User::where('id',$request->id)->with(['jawatan'])->first();         
                    $logged_user = \App\Models\User::where('id',$request->loged_user_id)->with(['jawatan'])->first();  
                
                    $data=[
                                    'user_id'=>$request->id,
                                    'user_ic_no'=>$user['no_ic'],
                                    'user_jawatan'=>$user['jawatan']['nama_jawatan'],
                                    'user_name'=>$user['name'],
                                    'updated_by_user_id'=>$request->loged_user_id,
                                    'updated_by_user_ic_no'=>$logged_user['no_ic'],
                                    'updated_by_user_jawatan'=>$logged_user['jawatan']['nama_jawatan'],
                                    'updated_by_user_name'=>$logged_user['name'],
                                    'action_taken'=>'PROFIL PENGGUNA - Maklumat pengguna dikemas kini',
                                    'created_on'=>Carbon::now()->format('Y-m-d H:i:s'),
                                    'created_at'=>Carbon::now()->format('Y-m-d H:i:s'),
                                    'updated_at'=>Carbon::now()->format('Y-m-d H:i:s')
                            ];
                    DB::connection(env('DB_CONNECTION_AUDIT'))->table('registration_log')->insert($data);                

                //---------------------ends-------------------------------------------------------------------------------
                
                return response()->json([
                    'code' => '200',
                    'status' => 'Sucess',
                    'data' => $user,
                ]);
            }else {                
                return response()->json([
                    'code' => '422',
                    'status' => 'Unprocessable Entity',
                    'data' => $validator->errors(),
                ]);
            }

        } catch (\Throwable $th) {
            logger()->error($th->getMessage());            
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
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }       
    }

    public function userByEmailIc($email,$ic)
    {
        try {
            //code...
            $user = \App\Models\User::where('email',$email)->where('no_ic',$ic)->first();

            if($user){
                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'data' => 'user exist',
                ]);
            } else {
                return response()->json([
                    'code' => '422',
                    'status' => 'Success',
                    'data' => 'user does not exist',
                ]);
            }
            
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());            
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
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }   
    }

    public function userDetails($id)
    {
        try {
            $user = \App\Models\User::whereId($id)
                ->with(['jawatan'=> function ($query) {
                    $query->select('id', 'nama_jawatan');
                }])
                ->with(['jabatan'=> function ($query) {
                    $query->select('id', 'nama_jabatan');
                }])
                ->with(['agensi'=> function ($query) {
                    $query->select('id', 'nama_agensi');
                }])
                ->with(['bahagian'=> function ($query) {
                    $query->select('id', 'nama_bahagian');
                }])
                ->with(['daerah'=> function ($query) {
                    $query->select('id', 'nama_daerah');
                }])
                ->with(['gredJawatan'=> function ($query) {
                    $query->select('id', 'nama_gred');
                }])
                ->with(['jenisPengguna'=> function ($query) {
                    $query->select('id', 'nama_jenis_pengguna');
                }])
                ->with(['negeri'=> function ($query) {
                    $query->select('id', 'nama_negeri');
                }])->first();            
            $data['user'] = $user;
            $data['profile_url'] = $user->getMedia('profile_image');
            $data['document_path'] = $user->getMedia('document');
            $data['peranan'] = \App\Models\UserPeranan::where('user_id',$id)->where('row_status',1)->get();
            $data['gred'] = \App\Models\refGredJawatan::where('is_hidden','!=',1)->where('row_status','=',1)->with('updatedBy')->get();
            $data['negeri'] = \App\Models\refNegeri::where('is_hidden','!=',1)->where('row_status','=',1)->with('updatedBy')->get();
            $data['jawatan'] = \App\Models\refJawatan::where('is_hidden','!=',1)->where('row_status','=',1)->with('updatedBy')->get();
            $data['pejabat'] = \App\Models\PejabatProjek::where('IsActive','=',1)->get();
            return response()->json([
                'code' => '200',
                'status' => 'Sucess',
                'data' => $data
            ]);

        } catch (\Throwable $th) {
            logger()->error($th->getMessage());            
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
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function userTempDetails($id)
    {
        try {            
            $user = \App\Models\tempUser::whereId($id)
                ->with(['jawatan'=> function ($query) {
                    $query->select('id', 'nama_jawatan');
                }])
                ->with(['jabatan'=> function ($query) {
                    $query->select('id', 'nama_jabatan');
                }])
                ->with(['agensi'=> function ($query) {
                    $query->select('id', 'nama_agensi');
                }])
                ->with(['bahagian'=> function ($query) {
                    $query->select('id', 'nama_bahagian');
                }])
                ->with(['daerah'=> function ($query) {
                    $query->select('id', 'nama_daerah');
                }])
                ->with(['gredJawatan'=> function ($query) {
                    $query->select('id', 'nama_gred');
                }])
                ->with(['jenisPengguna'=> function ($query) {
                    $query->select('id', 'nama_jenis_pengguna');
                }])
                ->with(['negeri'=> function ($query) {
                    $query->select('id', 'nama_negeri');
                }])->first();            
            
            $data['user'] = $user;
            $data['profile_url'] = $user->getMedia('profile_image');
            $data['document_path'] = $user->getMedia('document');
            $data['peranan'] = \App\Models\UserPeranan::where('user_id',$id)->where('row_status',1)->get();
            $data['gred'] = \App\Models\refGredJawatan::where('is_hidden','!=',1)->where('row_status','=',1)->with('updatedBy')->get();
            $data['negeri'] = \App\Models\refNegeri::where('is_hidden','!=',1)->where('row_status','=',1)->with('updatedBy')->get();
            $data['jawatan'] = \App\Models\refJawatan::where('is_hidden','!=',1)->where('row_status','=',1)->with('updatedBy')->get();
            $data['pejabat'] = \App\Models\PejabatProjek::where('IsActive','=',1)->get();

            return response()->json([
                'code' => '200',
                'status' => 'Sucess',
                'data' => $data,
            ]);

        } catch (\Throwable $th) {
            logger()->error($th->getMessage());            
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
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function firstResetUpdate(Request $request)
    {
        //
        Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6'])->validate();
        
        $user = Auth::user();
        
        $user->password = Hash::make($request->password);

        $user->setRememberToken(Str::random(60));

        $user->first_time = 0;

        $user->save();

        //event(new PasswordReset($user));

        Auth::guard()->login($user);
        
        return redirect()->route('home');
    }    

    /**
     * Approve user registration
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userApproval(Request $request)
    {
        //

        try {

            $id = $request->id;
            $tempUser = \App\Models\tempUser::whereId($id)->first();

            $documentItem = $tempUser->getMedia('document')->first();
            $profileItem = $tempUser->getMedia('profile_image')->first();
            
            
            
            $password = Str::random(10);
            $userdata = User::create([
                'name' => $tempUser->name,
                'email' => $tempUser->email,
                'password' => Hash::make($password),
                'no_ic' => $tempUser->no_ic,          
                'jenis_pengguna_id' => $tempUser->jenis_pengguna_id,
                'no_telefon' => $tempUser->no_telefon,
                'jawatan_id' => $tempUser->jawatan_id,
                'jabatan_id' => $tempUser->jabatan_id,
                'gred_jawatan_id' => $tempUser->gred_jawatan_id,
                'kementerian_id' => $tempUser->kementerian_id,
                'pajabat_id' => $tempUser->pajabat_id,
                'bahagian_id' => $tempUser->bahagian_id,
                'negeri_id' => $tempUser->negeri_id,
                'daerah_id' => $tempUser->daerah_id,
                'catatan' => $tempUser->catatan,
                'first_time' => 1,
                'status_pengguna_id' => 1,
                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'dibuat_oleh' => $request->loged_user_id,
                'dikemaskini_oleh' => $request->loged_user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            SetUserType($userdata['id']);


            if($documentItem){
                $documentItem->copy($userdata, 'document');
            }

            if($profileItem) {
                $profileItem->copy($userdata, 'profile_image');
            }

             //----------------- registration log -----------------------------------------------------------------
             $user = \App\Models\User::where('id',$userdata['id'])->with(['jawatan'])->first(); 
             $logged_user = \App\Models\User::where('id',$request->loged_user_id)->with(['jawatan'])->first(); //print_r($logged_user);exit;

             $data=[
                 'user_id'=>$id,
                 'user_ic_no'=>$user['no_ic'],
                 'user_jawatan'=>$user['jawatan']['nama_jawatan'],
                 'user_name'=>$user['name'],
                 'updated_by_user_id'=>$request->loged_user_id,
                 'updated_by_user_ic_no'=>$logged_user['no_ic'],
                 'updated_by_user_jawatan'=>$logged_user['jawatan']['nama_jawatan'],
                 'updated_by_user_name'=>$logged_user['name'],
                 'action_taken'=>$request->action,
                 'created_on'=>Carbon::now()->format('Y-m-d H:i:s'),
                 'created_at'=>Carbon::now()->format('Y-m-d H:i:s'),
                 'updated_at'=>Carbon::now()->format('Y-m-d H:i:s')
             ];
             DB::connection(env('DB_CONNECTION_AUDIT'))->table('registration_log')->insert($data);
            //---------------------ends-------------------------------------------------------------------------------

            
           $tempUser->delete();

            $user->notify(new UserApproval($password,$user));
             
            return response()->json([
                'code' => '200',
                'status' => 'Sucess',
                'data' => $user,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());            
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
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
        session()->flash('message', 'Approval for User Berjaya.'); 

        return redirect('home');

    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'no_kod_penganalan' => ['required', 'string',  'max:255'],
            //'password' => ['required', 'string', 'min:8', 'confirmed'],
            'kategori' => ['required', 'integer', 'max:255'],
            'no_telefon' => ['required', 'string', 'max:255'],
            'jawatan' => ['required', 'string', 'max:255'],
            'jabatan' => ['required', 'string', 'max:255'],
            'gred' => ['required', 'string', 'max:255'],
            //'kementerian' => ['required', 'integer', 'max:255'],
            'bahagian' => ['required', 'integer', 'max:255'],
            'negeri' => ['required', 'integer', 'max:255'],
            'daerah' => ['required', 'integer', 'max:255']
        ]
        , [
            'email.unique' => 'Emel telah digunakan'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function createData(array $data,$password)
    {       // print_r($data);exit;
        return User::create([
            'name' => $data['nama'],
            'email' => $data['email'],
            'password' => Hash::make($password),
            'no_ic' => $data['no_kod_penganalan'],            
            'jenis_pengguna_id' => $data['kategori'],
            'first_time' => 1,
            'no_telefon' => $data['no_telefon'],
            'jawatan_id' => $data['jawatan'],
            'jabatan_id' => $data['jabatan'],
            'gred_jawatan_id' => $data['gred'],
            'kementerian_id' => $data['kementerian'],
            'pajabat_id' => $data['pajabat'],
            'bahagian_id' => $data['bahagian'],
            'negeri_id' => $data['negeri'],
            'daerah_id' => $data['daerah'],
            'catatan' => $data['catatan'],
            'status_pengguna_id' => 1,
            'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }

    protected function updateUserData(array $data, $id)
    {        
        return User::where('id', $id)->update([
            'name' => $data['nama'],
            'email' => $data['email'],            
            'no_ic' => $data['no_kod_penganalan'],            
            'jenis_pengguna_id' => $data['kategori'],
            //'first_time' => 1,
            'no_telefon' => $data['no_telefon'],
            'jawatan_id' => $data['jawatan'],
            'jabatan_id' => $data['jabatan'],
            'gred_jawatan_id' => $data['gred'],
            'kementerian_id' => $data['kementerian'],
            'pajabat_id' => $data['pajabat'],
            'bahagian_id' => $data['bahagian'],
            'negeri_id' => $data['negeri'],
            'daerah_id' => $data['daerah'],
            'catatan' => $data['catatan'],
            'status_pengguna_id' => $data['status_pengguna_id'],
            //'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function createTempData(array $data,$password)
    {  
        return tempUser::create([
            'name' => $data['nama'],
            'email' => $data['email'],
            'password' => Hash::make($password),
            'no_ic' => $data['no_kod_penganalan'],            
            'jenis_pengguna_id' => $data['kategori'],
            'no_telefon' => $data['no_telefon'],
            'jawatan_id' => $data['jawatan'],
            'jabatan_id' => $data['jabatan'],
            'gred_jawatan_id' => $data['gred'],
            'kementerian_id' => $data['kementerian'],
            'pajabat_id' => $data['pajabat'],
            'bahagian_id' => $data['bahagian'],
            'negeri_id' => $data['negeri'],
            'daerah_id' => $data['daerah'],
            'catatan' => $data['catatan'],
            'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function updateRejectionDetails(Request $request)
    {  
        try {
                $user = $this->updateTempUserRejectionData($request->all(),$request->id);  
                
                //----------------- registration log -----------------------------------------------------------------
                $userdata = \App\Models\tempUser::where('id',$request->id)->with(['jawatan'])->first(); 
                $logged_user = \App\Models\User::where('id',$request->loged_user_id)->with(['jawatan'])->first();// print_r($logged_user);exit;

                $regi_data=[
                    'user_id'=>$request->id,
                    'user_ic_no'=>$userdata['no_ic'],
                    'user_jawatan'=>$userdata['jawatan']['nama_jawatan'],
                    'user_name'=>$userdata['name'],
                    'updated_by_user_id'=>$request->loged_user_id,
                    'updated_by_user_ic_no'=>$logged_user['no_ic'],
                    'updated_by_user_jawatan'=>$logged_user['jawatan']['nama_jawatan'],
                    'updated_by_user_name'=>$logged_user['name'],
                    'action_taken'=>$request->action,
                    'created_on'=>Carbon::now()->format('Y-m-d H:i:s'),
                    'created_at'=>Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at'=>Carbon::now()->format('Y-m-d H:i:s')
                ];
                DB::connection(env('DB_CONNECTION_AUDIT'))->table('registration_log')->insert($regi_data);
                //---------------------ends-------------------------------------------------------------------------------

                return response()->json([
                    'code' => '200',
                    'status' => 'Sucess',
                    'data' => $user,
                ]);

            } catch (\Throwable $th) {
                logger()->error($th->getMessage());            
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
                return response()->json([
                    'code' => '500',
                    'status' => 'Failed',
                    'error' => $th,
                ]);
            }
    }    
        
        protected function updateTempUserRejectionData(array $data, $id)
        {  
            $user=tempUser::where('id',$id)->first();

            if($data['type']=='update')
            { 

                if($data['count']==0)
                {
                    tempUser::where('id', $id)->update([
                        'count' => $data['count']+1,
                        'alasan_penolakan1' => $data['comment'], 
                        'dikemaskini_oleh' => $data['loged_user_id'],
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]); 
                    $userData = [
                        'name' => $user['name'],
                        'status' => 1,
                        'comment' => $data['comment'],
                        'Url' => env('EMAIL_REDIRECT_URL').'user/temp-user-update/'.$id
                    ];
                    $user->notify(new UserRejection($userData));
                }
                else if($data['count']==1)
                {
                    tempUser::where('id', $id)->update([
                        'count' => $data['count']+1,
                        'alasan_penolakan2' => $data['comment'], 
                        'dikemaskini_oleh' => $data['loged_user_id'],
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]); 
                    $userData1 = [
                        'name' => $user['name'],
                        'status' => 1,
                        'comment' => $data['comment'],
                        'Url' => env('EMAIL_REDIRECT_URL').'user/temp-user-update/'.$id
                    ]; 
                    $user->notify(new UserRejection($userData1));
                }
                else
                {
                    tempUser::where('id', $id)->update([
                        'count' => $data['count']+1,
                        'alasan_penolakan3' => $data['comment'], 
                        'dikemaskini_oleh' => $data['loged_user_id'],
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]); 
                    $userData2 = [
                        'name' => $user['name'],
                        'status' => 1,
                        'comment' => $data['comment'],
                        'Url' => env('EMAIL_REDIRECT_URL').'user/temp-user-update/'.$id
                    ]; 
                    $user->notify(new UserRejection($userData2));
                }
                return $data['count']+1;

            } 
            else
            {
                tempUser::where('id', $id)->update([
                    'alasan_penolakan_permanet' => $data['comment'], 
                    'status_pengguna_id' => 2,
                    'dikemaskini_oleh' => $data['loged_user_id'],
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);

                $userData = [
                    'status' => 2,
                    'comment' => $data['comment']
                ];
                $user->notify(new UserRejection($userData));

            }
        }

        public function updateTempUser(Request $request)
        { 
            try {
                $validator = Validator::make($request->all(),[
                    'id' => ['required', 'string', 'max:255'],
                    'nama' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                    'no_kod_penganalan' => ['required', 'string',  'max:255', 'unique:users,no_ic'],
                    //'password' => ['required', 'string', 'min:8', 'confirmed'],
                    'kategori' => ['required', 'integer', 'max:255'],
                    'no_telefon' => ['required', 'string', 'max:255'],
                    'jawatan' => ['required', 'string', 'max:255'],
                    // 'jabatan' => ['required', 'string', 'max:255'],
                    // 'gred' => ['required', 'string', 'max:255'],
                     'kementerian' => ['required', 'integer', 'max:255'],
                    // 'bahagian' => ['required', 'integer', 'max:255'],
                    // 'negeri' => ['required', 'integer', 'max:255'],
                    // 'daerah' => ['required', 'integer', 'max:255']
                ], 
                [
                    'no_kod_penganalan.unique' => 'No. Kad pengenalan telah digunakan',
                    'email.unique' => 'Emel telah digunakan'
                ]);
                if(!$validator->fails()) {                                
                    $this->updateTempUserData($request->all(),$request->id);

                    //----------------- registration log -----------------------------------------------------------------
                    $userdata = \App\Models\tempUser::where('id',$request->id)->with(['jawatan'])->first(); 

                    $data=[
                        'user_id'=>$request->id,
                        'user_ic_no'=>$userdata['no_ic'],
                        'user_jawatan'=>$userdata['jawatan']['nama_jawatan'],
                        'user_name'=>$userdata['name'],
                        'updated_by_user_id'=>$request->id,
                        'updated_by_user_ic_no'=>$userdata['no_ic'],
                        'updated_by_user_jawatan'=>$userdata['jawatan']['nama_jawatan'],
                        'updated_by_user_name'=>$userdata['name'],
                        'action_taken'=>"KEMASKINI DATA SEMENTARA - Kemaskini data pengguna yang ditolak",
                        'created_on'=>Carbon::now()->format('Y-m-d H:i:s'),
                        'created_at'=>Carbon::now()->format('Y-m-d H:i:s'),
                        'updated_at'=>Carbon::now()->format('Y-m-d H:i:s')
                    ];
                    DB::connection(env('DB_CONNECTION_AUDIT'))->table('registration_log')->insert($data);
                    //---------------------ends-------------------------------------------------------------------------------

                    $user=tempUser::where('id',$request->id)->first();
                    
                    if($request->file('documents')) {
                        $user->clearMediaCollection('document');
                        $user
                        ->addMedia($request->file('documents'))
                        ->toMediaCollection('document');
                    }
                    return response()->json([
                        'code' => '200',
                        'status' => 'Sucess',
                        'data' => $user,
                    ]);
                }else {                
                    return response()->json([
                        'code' => '422',
                        'status' => 'Unprocessable Entity',
                        'data' => $validator->errors(),
                    ]);
                }
    
            } catch (\Throwable $th) {
                logger()->error($th->getMessage());            
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
                return response()->json([
                    'code' => '500',
                    'status' => 'Failed',
                    'error' => $th,
                ]);
            }     
    
        }

        protected function updateTempUserData(array $data, $id)
        {   
            return tempUser::where('id', $id)->update([
                'name' => $data['nama'],
                'email' => $data['email'],            
                'no_ic' => $data['no_kod_penganalan'],            
                'jenis_pengguna_id' => $data['kategori'],
                //'first_time' => 1,
                'no_telefon' => $data['no_telefon'],
                'jawatan_id' => $data['jawatan'],
                'jabatan_id' => $data['jabatan'],
                'gred_jawatan_id' => $data['gred'],
                'kementerian_id' => $data['kementerian'],
                'pajabat_id' => $data['pajabat'],
                'bahagian_id' => $data['bahagian'],
                'negeri_id' => $data['negeri'],
                'daerah_id' => $data['daerah'],
                'catatan' => $data['catatan'],
                'UpdateCounter' => $data['UpdateCounter'],
                //'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'dikemaskini_oleh' => $id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
        }

        public function jawatanName(Request $request){
            try{
                    $id=$request->toArray();
                    // var_dump($id["id"]);
                    // exit();
                    $userImage = \App\Models\User::where('id',$id["id"])->first();
                    $jawatanName=User::where('id',$id["id"])->with(['jawatan'])->first();
                    $profileItem = $userImage->getMedia('profile_image')->first();
                    return response()->json([
                        'code' => '200',
                        'status' => 'Sucess',
                        'data' => $jawatanName,
                        'profilePic' => $profileItem,
                    ]);
                } catch (\Throwable $th) {
                    logger()->error($th->getMessage());            
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
                    return response()->json([
                        'code' => '500',
                        'status' => 'Failed',
                        'error' => $th,
                    ]);
                }
            
        }

        public function activeUser(){
            try{
                $activeUser = \App\Models\User::whereNotNull('two_factor_secret')->where('updated_at','>',Carbon::now()->subHours(6)->toDateTimeString())->with(['jawatan'])->get();
                foreach($activeUser as $user) {
                    $user['profileImage'] = $user->profileImageUrl();
                }

                // print_r($activeUser->toArray());
                // exit();
                // $activeUser = \App\Models\User::whereNotNull('two_factor_secret')->whereTime('updated_at','<',Carbon::now())->with(['jawatan'])->get();
                return response()->json([
                    'code' => '200',    
                    'status' => 'Sucess',
                    'activelist' => $activeUser,
                ]);
            } catch (\Throwable $th) {
                logger()->error($th->getMessage());            
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
                return response()->json([
                    'code' => '500',
                    'status' => 'Failed',
                    'error' => $th,
                ]);
            }

        }

        public function ActivateUser(Request $request){
            try{
                $data=$request->toArray();
                // print_r($data);
                // exit();
                $activate_id=$data['id'];
                $value=$data['value'];
                $activate_user=User::find($activate_id);
                $activate_user->dikemaskini_oleh=$request->loged_user_id;
                $activate_user->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s'); 
                $activate_user->status_pengguna_id=$value;        
                $activate_user->update();
                if($activate_user->update()=='true'){

                    //----------------- registration log -----------------------------------------------------------------
                    $user = \App\Models\User::where('id',$activate_id)->with(['jawatan'])->first();
                    $logged_user = \App\Models\User::where('id',$request->loged_user_id)->with(['jawatan'])->first();

                    $data=[
                        'user_id'=>$activate_id,
                        'user_ic_no'=>$user['no_ic'],
                        'user_jawatan'=>$user['jawatan']['nama_jawatan'],
                        'user_name'=>$user['name'],
                        'updated_by_user_id'=>$request->loged_user_id,
                        'updated_by_user_ic_no'=>$logged_user['no_ic'],
                        'updated_by_user_jawatan'=>$logged_user['jawatan']['nama_jawatan'],
                        'updated_by_user_name'=>$logged_user['name'],
                        'action_taken'=>$request->action,
                        'created_on'=>Carbon::now()->format('Y-m-d H:i:s'),
                        'created_at'=>Carbon::now()->format('Y-m-d H:i:s'),
                        'updated_at'=>Carbon::now()->format('Y-m-d H:i:s')
                    ];
                    DB::connection(env('DB_CONNECTION_AUDIT'))->table('registration_log')->insert($data);
                    //---------------------ends-------------------------------------------------------------------------------

                    return response()->json([
                        'code' => '200',
                        'status' => 'Success',
                        'data' => $data,
                    ]);
                }
            } catch (\Throwable $th) {
                logger()->error($th->getMessage());            
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
                return response()->json([
                    'code' => '500',
                    'status' => 'Failed',
                    'error' => $th,
                ]);
            }

        }

        public function deActivateUser(Request $request){
            try{
                    $data=$request->toArray();
                    // print_r($data);
                    // exit();
                    $activate_id=$data['id'];
                    $value=$data['value'];
                    $deactivate_user=User::find($activate_id);
                    $deactivate_user->dikemaskini_oleh=$request->loged_user_id;
                    $deactivate_user->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s'); 
                    $deactivate_user->status_pengguna_id=$value;        
                    $deactivate_user->update();
                    if($deactivate_user->update()=='true'){
                        //----------------- registration log -----------------------------------------------------------------
                        $user = \App\Models\User::where('id',$activate_id)->with(['jawatan'])->first();
                        $logged_user = \App\Models\User::where('id',$request->loged_user_id)->with(['jawatan'])->first();

                        $data=[
                            'user_id'=>$activate_id,
                            'user_ic_no'=>$user['no_ic'],
                            'user_jawatan'=>$user['jawatan']['nama_jawatan'],
                            'user_name'=>$user['name'],
                            'updated_by_user_id'=>$request->loged_user_id,
                            'updated_by_user_ic_no'=>$logged_user['no_ic'],
                            'updated_by_user_jawatan'=>$logged_user['jawatan']['nama_jawatan'],
                            'updated_by_user_name'=>$logged_user['name'],
                            'action_taken'=>$request->action,
                            'created_on'=>Carbon::now()->format('Y-m-d H:i:s'),
                            'created_at'=>Carbon::now()->format('Y-m-d H:i:s'),
                            'updated_at'=>Carbon::now()->format('Y-m-d H:i:s')
                        ];
                        DB::connection(env('DB_CONNECTION_AUDIT'))->table('registration_log')->insert($data);
                        //---------------------ends-------------------------------------------------------------------------------

                        return response()->json([
                            'code' => '200',
                            'status' => 'Success',
                            'data' => $data,
                        ]);
                    }
                } catch (\Throwable $th) {
                    logger()->error($th->getMessage());            
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
                    return response()->json([
                        'code' => '500',
                        'status' => 'Failed',
                        'error' => $th,
                    ]);
                }
        }

        public function getRegistrationLog(Request $request)
        {
            try {
                //code...
              if($request->start && $request->end){
    
                $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('registration_log')
                ->whereDate('created_on', '>=', $request->start)
                ->whereDate('created_on', '<=', $request->end)
                ->orderBy('id','DESC')
                ->get();  
    
              }
              else if($request->selected)
              {
                if($request->selected==1)
                { // today
                    $today = Carbon::now()->toDateString();
                    $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('registration_log')
                            ->whereDate('created_on', '=', $today)
                            ->orderBy('id','DESC')
                            ->get(); 
                }
                if($request->selected==2)
                { //seven days ago
                    $sevenDaysAgo = Carbon::now()->subDays(7)->toDateString();
                    $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('registration_log')
                            ->whereDate('created_on', '>=', $sevenDaysAgo)
                            ->orderBy('id','DESC')
                            ->get(); 
                }
                if($request->selected==3)
                { //30 days ago
                    $thirtyDaysAgo = Carbon::now()->subDays(30)->toDateString();
                    $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('registration_log')
                            ->whereDate('created_on', '>=', $thirtyDaysAgo)
                            ->orderBy('id','DESC')
                            ->get(); 
                }
                if($request->selected==4)
                { //current month
                    $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('registration_log')
                            ->whereMonth('created_on', Carbon::now()->month)
                            ->orderBy('id','DESC')
                            ->get(); 
                }
                if($request->selected==5)
                {//previous month
                    $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
                    $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
                    $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('registration_log')
                            ->whereBetween('created_on', [$lastMonthStart, $lastMonthEnd])
                            ->orderBy('id','DESC')
                            ->get(); 
                }
                if($request->selected==6)
                {//current year
                    $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('registration_log')
                            ->whereYear('created_on', Carbon::now()->year)
                            ->orderBy('id','DESC')
                            ->get(); 
                }
                if($request->selected==7)
                {//previous year
                    $previous_year=Carbon::now()->year-1; 
                    $results = DB::DB::connection(env('DB_CONNECTION_AUDIT'))->table('registration_log')
                            ->whereYear('created_on', $previous_year)
                            ->orderBy('id','DESC')
                            ->get(); 
                }
              }
              else
              {
                $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('registration_log')
                ->orderBy('id','DESC')
                ->get();      
              }
              return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $results,
            ]);

        } catch (\Throwable $th) {
            logger()->error($th->getMessage());            
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
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }
   
    


    public function userMonthlyLoginCount()
    { 
        try {
            $data['userDetails'] = DB::connection(env('DB_CONNECTION_AUDIT'))->table('user_logging_audit')
                                    ->select(DB::raw('count(DISTINCT  (case when jenis_pengguna_id = 1 then user_id else null end)) as jps_users') ,
                                            DB::raw('count(DISTINCT (case when jenis_pengguna_id = 2 then user_id else null end)) as non_jps_users') ,
                                            //DB::raw("users.jenis_pengguna_id"),
                                            DB::raw("MONTH (created_at) as month"),
                                            DB::raw("YEAR (created_at) as year")
                                            )
                                    ->groupBy(DB::raw("MONTH (created_at)"),DB::raw("YEAR (created_at)"))
                                    ->havingRaw(DB::raw("YEAR(created_at) >= YEAR(DATEADD(year, -1, GETDATE()))"))
                                    ->orderBy('year')
                                    ->orderBy('month')
                                    ->get();

            

            return response()->json([
                'code' => '200',
                'status' => 'Sucess',
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());            
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
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    } 

    public function CheckMapserviceStatus(Request $request){
        try{
                $getdata=mapService::all();
                $pautan_list=$getdata->toArray();
                for($i=0;$i<count($getdata->toArray());$i++){
                    // print_r($pautan_list[$i]['pautan_api']);
                    $client = new \GuzzleHttp\Client();
                    $response = $client->get($pautan_list[$i]['pautan_api']); 
                    $response = new Response();
                    $responsecode= $response->getStatusCode();
                    echo $responsecode.',';
                    if($responsecode==200){
                        mapService::where('id',$pautan_list[$i]['id'])->update([
                            'status'=>1
                        ]);
                    }
                    else{
                        mapService::where('id',$request->$pautan_list[$i]['id'])->update([
                            'status'=>0
                        ]);
                    }
                }  
            } catch (\Throwable $th) {
                logger()->error($th->getMessage());            
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
                return response()->json([
                    'code' => '500',
                    'status' => 'Failed',
                    'error' => $th,
                ]);
            } 
    }

    public function ChangePassword(Request $request)
    {
        try{
            $user=User::select('password')->where('id',$request->user_id)->first();
            if (!(Hash::check($request->get('old_password'), $user->password))) {

                return response()->json([
                                'code' => '500',
                                'status' => 'Failed',
                                'error' => "Kata laluan baharu tidak sepadan dengan kata laluan lama.",
                            ]);
            }
            else
            {
                $user->password = Hash::make($request->new_password);
                $user->save();

                $user->notify(new ChangePassword());


                return response()->json([
                    'code' => '200',
                    'status' => 'Success'
                ]);
            }
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());            
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
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function getNotifications(Request $request)
    {
        try{
            $status1='Update_project'; 
            $status2='Reject_project'; 
            $status3='Approve_project';
            
            if($request->user_is==1){ 

                $notification = DB::table('notification')
                                ->join('temp_users','temp_users.id', '=','notification.user_id')
                                ->select('notification.*')
                                ->where('IsActive',1)
                                ->where('notification_type',1)
                                ->orderBy('notification.dibuat_pada','desc')->take(5)->get();
                
                $notifi_penyedia = DB::table('notification')
                                    ->where('IsActive',1)
                                    ->where('notification_type',2)
                                    ->where('user_id',$request->user_id)
                                    ->where(function($query) use ($status1,$status2,$status3){
                                        $query->where('notification.notification_sub_type',$status1)
                                            ->orwhere('notification.notification_sub_type',$status2)
                                            ->orwhere('notification.notification_sub_type',$status3);
                                        })
                                    ->orderBy('notification.dibuat_pada','desc')->take(5)->get();
                $notification = $notification->concat($notifi_penyedia);
            }
            else
            {
                $notification= DB::table('notification')
                                ->where('IsActive',1)
                                ->where('notification_type',2)
                                ->where('user_id',$request->user_id)
                                ->where(function($query) use ($status1,$status2,$status3){
                                    $query->where('notification.notification_sub_type',$status1)
                                        ->orwhere('notification.notification_sub_type',$status2)
                                        ->orwhere('notification.notification_sub_type',$status3);
                                    })
                                ->orderBy('notification.dibuat_pada','desc')->take(5)->get();
            }
            $noti_status='Submit_for_penyemak'; $noti_status1='Submit_for_penyemak1'; $noti_status2='Submit_for_penyemak2'; 
            $count_penyemak = DB::table('notification')
                                    ->where('IsActive',1)
                                    ->where('notification_type',2)
                                    ->where('user_id',$request->user_id)
                                    ->where(function($query) use ($noti_status,$noti_status1,$noti_status2){
                                        $query->where('notification.notification_sub_type',$noti_status)
                                            ->orwhere('notification.notification_sub_type',$noti_status1)
                                            ->orwhere('notification.notification_sub_type',$noti_status2);
                                        })
                                    ->get();
            
            $count_pengesah  = DB::table('notification')
                                    ->where('IsActive',1)
                                    ->where('notification_type',2)
                                    ->where('user_id',$request->user_id)
                                    ->where('notification.notification_sub_type','Submit_for_pengesah')
                                    ->get();
                                    
            $count_peraku     = DB::table('notification')
                                    ->where('IsActive',1)
                                    ->where('notification_type',2)
                                    ->where('user_id',$request->user_id)
                                    ->where('notification.notification_sub_type','Submit_for_peraku')
                                    ->get();


            return response()->json([
                                'code' => '200',
                                'status' => 'Success',
                                'data' => $notification,
                                'count_penyemak'  => count($count_penyemak),
                                'count_pengesah'  => count($count_pengesah),
                                'count_peraku'    => count($count_peraku)
                            ]);

        } catch (\Throwable $th) {
            
            logger()->error($th->getMessage());            
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
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function markNotifications(Request $request)
    {
        try{
                if($request->id=='')
                {
                    if($request->type=='count_penyemak')
                    {
                        $noti_status='Submit_for_penyemak'; $noti_status1='Submit_for_penyemak1'; $noti_status2='Submit_for_penyemak2'; 
                        $notification = \App\Models\Notification::where('user_id',$request->user_id)
                                                                ->where(function($query) use ($noti_status,$noti_status1,$noti_status2){
                                                                    $query->where('notification.notification_sub_type',$noti_status)
                                                                        ->orwhere('notification.notification_sub_type',$noti_status1)
                                                                        ->orwhere('notification.notification_sub_type',$noti_status2);
                                                                    })
                                                                ->update(array('IsActive' => 0,'dikemaskini_oleh' => $request->user_id, 'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s') )); //print_r($notification);exit();
                    }
                    else if($request->type=='count_pengesah')
                    {
                        $notification = \App\Models\Notification::where('user_id',$request->user_id)
                                                                ->where('notification.notification_sub_type','Submit_for_pengesah')
                                                                ->update(array('IsActive' => 0,'dikemaskini_oleh' => $request->user_id, 'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s') )); //print_r($notification);exit();
                    }
                    else
                    {
                        $notification = \App\Models\Notification::where('user_id',$request->user_id)
                                                                ->where('notification.notification_sub_type','Submit_for_peraku')
                                                                ->update(array('IsActive' => 0,'dikemaskini_oleh' => $request->user_id, 'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s') )); //print_r($notification);exit();
                    }

                }
                else
                {
                    $notification = \App\Models\Notification::where('id',$request->id)->first();  //print_r($notification);exit();
                    $notification->IsActive=0;
                    $notification->dikemaskini_oleh=$request->user_id;
                    $notification->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');  
                    $notification->update();   
                }

                
                return response()->json([
                                        'code' => '200',
                                        'status' => 'Success'
                                    ]);
        } catch (\Throwable $th) {
            
            logger()->error($th->getMessage());            
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
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }
}
