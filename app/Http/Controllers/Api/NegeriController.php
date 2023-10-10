<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\refNegeri;
use \App\Models\refDaerah;
use \App\Models\refMukim;
use \App\Models\refParlimen;
use \App\Models\refDun;

use \App\Models\projectLog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Facades\Agent;


class NegeriController extends Controller
{
    //
    public function list(Request $request)
    {
        try {
            //code...
            if ($request->has('id')) {
                $data = \App\Models\refNegeri::where('id', $request->id)->where('is_hidden', '!=', 1)->where('row_status', '=', 1)->with('updatedBy', 'daerah')->get();
            } else {
                $data = \App\Models\refNegeri::where('is_hidden', '!=', 1)->where('row_status', '=', 1)->with('updatedBy', 'daerah')->get();
            }
            //$data = refNegeri::with('updatedBy')->get();
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

    public function listNegeri(Request $request)
    {
        try {
            //code...
            if ($request->has('id')) {
                $data = \App\Models\refNegeri::where('id', $request->id)->where('row_status', '=', 1)->with('updatedBy', 'daerah')->get();
            } else {
                $data = \App\Models\refNegeri::where('row_status', '=', 1)->with('updatedBy', 'daerah')->get();
            }
            //$data = refNegeri::with('updatedBy')->get();
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

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => ['required', 'string', 'max:255'],
                'name' => ['required', 'string', 'max:255'],
            ]);

            if (!$validator->fails()) {
                if ($request->id) {
                    $negeri = refNegeri::whereId($request->id)->first();
                    $old_negeri_code = $negeri->kod_negeri;

                    $data = refNegeri::where('id', $request->id)->update([
                        'kod_negeri' => $request->code,
                        'nama_negeri' => $request->name,
                        'penerangan_negeri' => $request->description,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);

                    if ($old_negeri_code != $request->code) {
                        refDaerah::where('negeri_id', $request->id)->update([
                            'kod_negeri' => $request->code,
                        ]);
                    }
                } else {
                    $data = refNegeri::create([
                        'kod_negeri' => $request->code,
                        'row_status' => 1,
                        'nama_negeri' => $request->name,
                        'penerangan_negeri' => $request->description,
                        'is_hidden' => 0,
                        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                }


                return response()->json([
                    'code' => '200',
                    'status' => 'Sucess',
                    'data' => $data,
                ]);
            } else {
                return response()->json([
                    'code' => '422',
                    'status' => 'Unprocessable Entity',
                    'data' => $validator->errors(),
                ]);
            }
            // return response()->json([
            //     'code' => '200',
            //     'status' => 'Success',
            //     'data' => $data,
            // ]);
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

    public function edit($id)
    {
        try {
            //code...
            $data = \App\Models\refNegeri::whereId($id)->with('updatedBy')->first();

            //$data = refNegeri::with('updatedBy')->get();
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

    public function update(Request $request)
    {
        try {
            //code...
            $negeri = \App\Models\refNegeri::whereId('id', $request->id)->with('updatedBy')->get();

            //$data = refNegeri::with('updatedBy')->get();
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $negeri,
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

    public function activate(Request $request)
    {

        try {
            $data = refNegeri::where('id', $request->id)->update([
                'is_hidden' => $request->value,
                'dikemaskini_oleh' => $request->loged_user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            $data = refDaerah::where('negeri_id', $request->id)->update([
                'is_hidden' => $request->value,
                'dikemaskini_oleh' => $request->loged_user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            $data = refParlimen::where('negeri_id', $request->id)->update([
                'is_hidden' => $request->value,
                'dikemaskini_oleh' => $request->loged_user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            $data = refDun::where('negeri_id', $request->id)->update([
                'is_hidden' => $request->value,
                'dikemaskini_oleh' => $request->loged_user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            $data = refMukim::where('negeri_id', $request->id)->update([
                'is_hidden' => $request->value,
                'dikemaskini_oleh' => $request->loged_user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
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
    public function deactivate(Request $request)
    {

        try {


            $data = refNegeri::where('id', $request->id)->update([
                'is_hidden' => $request->value,
                'dikemaskini_oleh' => $request->loged_user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            $data = refDaerah::where('negeri_id', $request->id)->update([
                'is_hidden' => $request->value,
                'dikemaskini_oleh' => $request->loged_user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            $data = refParlimen::where('negeri_id', $request->id)->update([
                'is_hidden' => $request->value,
                'dikemaskini_oleh' => $request->loged_user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            $data = refDun::where('negeri_id', $request->id)->update([
                'is_hidden' => $request->value,
                'dikemaskini_oleh' => $request->loged_user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            $data = refMukim::where('negeri_id', $request->id)->update([
                'is_hidden' => $request->value,
                'dikemaskini_oleh' => $request->loged_user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
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
}
