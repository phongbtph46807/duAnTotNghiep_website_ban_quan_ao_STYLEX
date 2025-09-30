<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function registerView(){
        try {
            //code...
            return view('auth.register');
        } catch (\Exception $e) {
            return abort(404, "Có gì đó không ổn!");
        }
    }
    public function register(RegisterRequest $request){
        try {
            //code...
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone_number = $request->phone_number;
            $user->password = Hash::make($request->password);
            $user->save();
            return back()->with('success', 'Đăng Kí Thành Công !!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
