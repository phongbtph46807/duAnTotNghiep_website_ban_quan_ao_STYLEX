<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppData;
use Illuminate\Http\Request;

class AppController extends Controller
{
    //
    public function index(){
        try {
            //code...
            $data = AppData::first();
            return view('admin.home-admin', compact('data'));
        } catch (\Exception $e) {
            return abort(404, "Có gì đó không ổn");
        }
    }
}
