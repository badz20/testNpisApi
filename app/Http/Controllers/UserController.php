<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DataTables;
use File;
use Carbon\Carbon;



class UserController extends Controller
{
    public function index()
    {
        return view('admin.user_verification_list');
    }

    public function getUsers(Request $request)
    {
        // dd($request);
        if ($request->ajax()) {
            $data = User::latest()->where('id','!=',1)->get(); //print_r($data);exit;
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $actionBtn = '<a href="javascript:void(0)" class="edit btn btn-danger btn-sm">Tidak Lulus</a> <a href="javascript:void(0)" class="delete btn btn-primary btn-sm">Lulus</a>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    function gambarProfil(Request $request)
    { 
        $year  = Carbon::now()->format('Y');
        $month = Carbon::now()->format('M');

        $year_path = env('IMAGE_UPLOAD_PATH').'/'.$year;
        $path = public_path($year_path); //print_r($year_path);

        if (!file_exists($path)) {
           File::makeDirectory($path, $mode = 0777, true, true);
        }

        $new_path = public_path($year_path.'/'.$month);
        if(!file_exists($new_path))
        {
            File::makeDirectory($new_path, $mode = 0777, true, true);
        }
        
        //print_r($request->file('file'));
        $image = $request->file('file');
        $name = $request->file('file')->getClientOriginalName(); //print_r($name);
        $new_name = rand() . '_' . $name; //print_r($new_name);
        $image->move($new_path, $new_name);


        return response()->json([
            'image_name'   => $new_name,
            'uploaded_path' => env('IMAGE_UPLOAD_PATH').'/'.$year.'/'.$month.'/'.$new_name
           ]);
    }
    

    function dokumenSokongan(Request $request)
    {
        $year  = Carbon::now()->format('Y');
        $month = Carbon::now()->format('M');

        $year_path = env('FILE_UPLOAD_PATH').'/'.$year;
        $path = public_path($year_path); //print_r($year_path);

        if (!file_exists($path)) {
           File::makeDirectory($path, $mode = 0777, true, true);
        }

        $new_path = public_path($year_path.'/'.$month);
        if(!file_exists($new_path))
        {
            File::makeDirectory($new_path, $mode = 0777, true, true);
        }
        
        //print_r($request->file('file'));
        $image = $request->file('file');
        $name = $request->file('file')->getClientOriginalName(); //print_r($name);
        $new_name = rand() . '_' . $name; //print_r($new_name);
        $image->move($new_path, $new_name);


        return response()->json([
            'dokumen_name'   => $new_name,
            'dokumen_path' => env('FILE_UPLOAD_PATH').'/'.$year.'/'.$month.'/'.$new_name
           ]);
    }
}
