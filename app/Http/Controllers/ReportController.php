<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Validation\Rule;

use App\Report;

class ReportController extends Controller
{
    public function create(Request $request){
        $report = new Report();

        $this->authorize('create', $report);

        $id_review = $request->input('id_review');
        $id_client = Auth::user()->id;

        $validator = \Validator::make($request->all(), [
            'id_review' => Rule::unique('reports')->where(function ($query) use($id_review,$id_client) {
                return $query->where('id_review', $id_review)
                ->where('id_client', $id_client);
            }),
            'reason' => 'required|string|min:6'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors'=> $validator->errors()->all()]);
        }

        $report->id_client = Auth::user()->id;
        $report->id_review = $request->input('id_review');
        $report->reason = $request->input('reason');

        $report->save();

        return $report;
    }
}
