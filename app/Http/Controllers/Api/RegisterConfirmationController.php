<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class RegisterConfirmationController extends Controller
{
	// 邮箱链接认证
    public function index()
    {
    	try {
    		User::where('confirmation_token',request('token'))
    		->firstOrFail()
    		->confirm();
    	}catch (\Exception $e) {
    		return redirect(route('threads'))->with('flash','Unknown token.');
    	}

    	return redirect(route('threads'))->with('flash','Your account is now confirmed! You may post to the forum.');
    }
}
