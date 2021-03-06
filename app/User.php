<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','avatar_path'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','email',
    ];

    protected $casts = ['confirmed' => 'boolean'];

    public function confirm()
    {
        $this->confirmed = true;

        $this->confirmation_token = null;

        $this->save();
    }

    public function isAdmin()
    {
        return in_array($this->name,['NoNo1']);
    }

    public function getRoutekeyName()
    {
        return 'name';
    }

    public function threads()
    {
        return $this->hasMany(Thread::class)->latest();
    }

    public function lastReply()
    {
        return $this->hasOne(Reply::class)->latest();
    }

    public function activity()
    {
        return $this->hasMany(Activity::class);
    }

    // 用户浏览话题
    public function read($thread)
    {
        cache()->forever(
            $this->visitedThreadCacheKey($thread),
            \Carbon\Carbon::now()
        );
    }

    // 用户头像访问器
    public function getAvatarPathAttribute($avatar)
    {
        return $avatar ?: 'avatars/default.jpg';
    }

    public function visitedThreadCacheKey($thread)
    {
        return $key = sprintf("users.%s.visits.%s",$this->id,$thread->id);
    }
}
