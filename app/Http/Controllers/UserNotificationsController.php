<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserNotificationsController extends Controller
{
    public function __construct()
    {
    	$this->middleware('auth');
    }


    // 订阅通知消息展示 
    public function index()
    {
    	return auth()->user()->unreadNotifications;
    }

    // 清除已读通知消息
    public function destroy(User $user,$notificationId)
    {
    	auth()->user()->notifications()->findOrFail($notificationId)->markAsRead();
    }
}
