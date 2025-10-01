<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class VerificationController extends Controller
{
    //
    public function verify($token){
       $user = User::where('verification_token', $token)->first(); 
    
        if(!$user){
            return abort(404, "Có gì đó không ổn!");
        }

        if($user->token_expires_at < Carbon::now()){
            $msg = 'Mã xác minh đã hết hạn. Vui lòng yêu cầu gửi email xác minh mới.';
            return view('auth.verification.verification-message', compact('msg'));
        }

        $user->is_verified = 1;
        $user->email_verified_at = Carbon::now();
        $user->verification_token = null;
        $user->token_expires_at = null;
        $user->save();

        $msg = 'Xác thực email thành công!';
        return view('auth.verification.verification-message', compact('msg'));
        

    }
}
