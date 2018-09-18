<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Thread;

class LockedThreadsController extends Controller
{
	// 话题锁定
    public function store(Thread $thread)
    {
    	$thread->update(['locked' => true]);
    }

    // 解锁话题
    public function destroy(Thread $thread)
    {
    	$thread->update(['locked' => false]);
    }
}
