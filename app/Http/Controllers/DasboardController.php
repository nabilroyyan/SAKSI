<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DasboardController extends Controller
{
     function superadmin(){
         return view('/superadmin/dasboard'); //mengakses view superadmin.dashboard
    }
}
