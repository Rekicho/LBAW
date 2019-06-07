<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\ReportLog;

class ReportLogController extends Controller
{
    public function create(Request $request){
        $reportLog = new ReportLog;

        $this->authorize('create', $reportLog);

        $reportLog->id_report = $request->input('id_report');
        $reportLog->has_deleted = true;
        $reportLog->id_staff_member = Auth::user()->id;

        $reportLog->save();
        return $reportLog;
    }
}
