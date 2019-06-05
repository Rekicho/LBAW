<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

use App\Help;

class SignUpController extends Controller
{
    public function showHelp(){
        $text =  Help::getHelpText(1);

        return $text;
    }
}
