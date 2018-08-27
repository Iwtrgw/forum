<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class ProfilesController extends Controller
{

    // 个人中心
    public function show(User $user)
    {

    	

	    return view('profiles.show',[
	        'profileUser'=> $user,
	        'activities' => $this->getActivity($user)
	    ]);
    }

    protected function getActivity(User $user)
    {
    	return $user->activity()->latest()->with('subject')->take(50)->get()->groupBy(function ($activity){
	        return $activity->created_at->format('Y-m-d');
	    });
    }
}
