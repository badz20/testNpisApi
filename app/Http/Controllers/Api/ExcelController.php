<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\ProjectExport;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    //

    public function export_projects(Request $request)
    {
        $excel = new ProjectExport($request);
        
        return Excel::download($excel, 'projects.xlsx');
    }
}
