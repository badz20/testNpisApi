<?php

use App\Models\User;
use App\Models\UserType;
use App\Models\refNegeri;
use App\Models\refBahagian;
use App\Models\refKementerian;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


if (! function_exists('SetUserType')) {
    function SetUserType($user_id)
    {        
            $user = User::whereId($user_id)->with('bahagian')->first(); 
            
            if($user->isJps()) {
                if($user->bahagian_id) {
                    if($user->bahagian->acym == 'BKOR' || $user->bahagian->acym == 'BPK') 
                    {
                        $user_type = UserType::where('name','BKOR User')->first();
                        $user->user_type_id = $user_type->id;
                    }
                    else
                    {
                        $user_type = UserType::where('name','Bahagian User')->first();
                        $user->user_type_id = $user_type->id;
                    }
                }

                if($user->negeri_id) {
                    $user_type = UserType::where('name','Negeri User')->first(); Log::info($user_type);
                    $user->user_type_id = $user_type->id;
                }

                if($user->daerah_id) {
                    $user_type = UserType::where('name','Daerah User')->first();
                    $user->user_type_id = $user_type->id;
                }

            }else {

                if($user->negeri_id) {
                    $user_type = UserType::where('name','Negeri User')->first();
                    $user->user_type_id = $user_type->id;
                }

                if($user->daerah_id) {
                    $user_type = UserType::where('name','Daerah User')->first();
                    $user->user_type_id = $user_type->id;
                }

            }

            $user->save();
                
            return $user;
    }
}

if (! function_exists('CallApi')) {

function CallApi($data)
    {        

        $url=env('ERROR_URL').'/api/feedback_data';
        $response = Http::withOptions([
            'verify' => false
        ])->post($url, [
            $data,
        ]);
           
    }
}


if (! function_exists('deleteMedia')) {
    function deleteMedia($ids , $model)
    {    
        foreach($ids as $id) {
            Media::where('model_type', $model)
                ->where('model_id',$id)
                ->delete();
        }
     }
}

if (! function_exists('downloadMedia')) {
    function downloadMedia($modelType, $modelId,$collectionName)
    {    
        
       return Media::where('model_type', $modelType)
            ->where('model_id',$modelId)
            ->where('collection_name',$collectionName)
            ->first();
        
     }
}

if (! function_exists('downloadMediaById')) {
    function downloadMediaById($id)
    {    
       return Media::whereId($id)
            ->first();
        
     }
}

if (! function_exists('getAllNegeri')) {
    function getAllNegeri()
    {    
       return refNegeri::get();
    }
}

if (! function_exists('getAllBahagian')) {
    function getAllBahagian()
    {    
       return refBahagian::orderBy('nama_bahagian')->get();
    }
}

if (! function_exists('getAllKementerian')) {
    function getAllKementerian()
    {    
       return refKementerian::orderBy('nama_kementerian')->get();
    }
}