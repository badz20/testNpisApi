<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\User;
use App\Models\tempUser;
use Illuminate\Support\Facades\Log;

class DownloadFileController extends Controller
{
    //
    function downloadFile(Request $request,Media $mediaItem){
        // print_r($mediaItem->toArray());
        $user_id = $request->user_id;
        $user_type = $request->type;
//dd($user_id,$user_type);
        if($user_type == 'temp') {
            $user = tempUser::whereId($user_id)->first();
        }else {
            $user = User::whereId($user_id)->first();
        }
        $mediaItem = $user->getFirstMedia('document');
        return response()->download($mediaItem->getPath(), $mediaItem->file_name);
        // $file = Storage::disk('public')->get($file_name);
  
        // return (new Response($file, 200))
        //       ->header('Content-Type', 'image/jpeg');
    }

    function downloadMedia(Request $request){
        Log::info($request);
        if($request->model_id != '') {
            Log::info('if');
           $mediaItem = downloadMediaById($request->model_id);
           Log::info($mediaItem);
           return response()->download($mediaItem->getPath(), $mediaItem->file_name);
        } else {
            Log::info('else');
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => 'Invalid model id',
            ]);
        }

    }

    function downloadPrestasiTemplate(Request $request){
        Log::info($request);
        if($request->type == 'AMARAN') {
           return response()->download(public_path('downloadTempates/Template_Surat Amaran_Modul_Perunding.docx'), '');
        } else {
            return response()->download(public_path('downloadTempates/Template_Surat_Peringatan_Modul_Perunding.docx'), '');
        }
        return response()->json([
            'code' => '500',
            'status' => 'Failed',
            'error' => 'Invalid model id',
        ]);

    }
}
