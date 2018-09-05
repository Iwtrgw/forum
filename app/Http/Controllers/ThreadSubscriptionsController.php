<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Thread;

class ThreadSubscriptionsController extends Controller
{
	// 话题订阅
    public function store($channelId,Thread $thread)
    {
    	$thread->subscribe();
    }

    // 取消订阅
    public function destroy($channelId,Thread $thread)
    {
    	$thread->unsubscribe();
    }
}
