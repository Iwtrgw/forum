<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserAvatarController extends Controller
{
	// 上传头像
    public function store()
    {
    	$this->validate(request(),[
    		'avatar' => ['required','image']
    	]);

    	auth()->user()->update([
    		'avatar_path' => request()->file('avatar')->store('avatars','public')
    	]);

    	return back();
    }
}
