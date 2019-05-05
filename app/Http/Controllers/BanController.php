<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Ban;

class BanController extends Controller
{
    public function create(Request $request)
    {
        $ban = new Ban();

        // $this->authorize('create', $user);

        $ban->id_staff_member = Auth::user()->id;
        $ban->id_client = intval($request->input('id_client'));
        $ban->end_t = $request->input('end_t');
        $ban->reason = $request->input('reason');
      
        $ban->save();

        return $ban;
    }
}
