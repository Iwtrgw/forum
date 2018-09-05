<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Thread extends Model
{

    use RecordsActivity;

   protected $guarded = [];
   protected $with = ['creator','channel'];
   protected $appends = ['isSubscribedTo'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function($thread) {
          $thread->replies->each->delete();
        });
    }

   public function path()
   {
   		return "/threads/{$this->channel->slug}/{$this->id}";
   }

   public function replies()
   {
   		return $this->hasMany(Reply::class);
                  // ->withCount('favorites')
                  // ->with('owner');
   }

   public function creator()
   {
   		return $this->belongsTo(User::class,'user_id'); // 使用 user_id 字段进行模型关联
   }

   public function channel()
   {
      return $this->belongsTo(Channel::class);
   }

   public function addReply($reply)
   {
   		$reply = $this->replies()->create($reply);

      $this->subscriptions
           ->filter(function ($sub) use ($reply){
              return $sub->user_id != $reply->user_id;
           })
           ->each->notify($reply);

      return $reply;
   }

   public function scopeFilter($query,$filters)
   {
      return $filters->apply($query);
   }

   // 话题订阅
   public function subscribe($userId = null)
   {
      $this->subscriptions()->create([
        'user_id' => $userId ?: auth()->id()
      ]);

      return $this;
   }

   // 取消话题订阅
   public function unsubscribe($userId = null)
   {
      $this->subscriptions()
           ->where('user_id',$userId ?: auth()->id())
           ->delete();
   }

   public function subscriptions()
   {
    return $this->hasMany(ThreadSubscription::class);
   }

   public function getIsSubscribedToAttribute()
  {
      return $this->subscriptions()
          ->where('user_id',auth()->id())
          ->exists();
  }
}
