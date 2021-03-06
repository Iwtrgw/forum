<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Events\ThreadRecivedNewReply;
use Laravel\Scout\Searchable;


class Thread extends Model
{

    use RecordsActivity,Searchable;

   protected $guarded = [];
   protected $with = ['creator','channel'];
   protected $appends = ['isSubscribedTo'];
   protected $casts = ['locked' => 'boolean'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function($thread) {
          $thread->replies->each->delete();
        });

        static::created(function($thread) {
          $thread->update([
            'slug' => $thread->title,
            'body' => clean($thread->body,'thread_or_reply_body'),
          ]);
        });
    }

   public function path()
   {
   		return "/threads/{$this->channel->slug}/{$this->slug}";
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

      event(new ThreadRecivedNewReply($reply));

      return $reply;
   }

   public function notifySubscribers($reply)
   {
      $this->subscriptions
           ->where('user_id','!=',$reply->user_id)
           ->each
           ->notify($reply);
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

  // 判断订阅的话题是否更新
  public function  hasUpdatesFor($user)
  {
    $key = $user->visitedThreadCacheKey($this);

    return $this->updated_at > cache($key);
  }

  public function getRouteKeyName()
  {
    return 'slug';
  }

  public function setSlugAttribute($value)
  {
      $slug = str_slug($value);

      if (static::whereSlug($slug)->exists()) {
            $slug = "{$slug}-" . $this->id;
      }

      $this->attributes['slug'] = $slug;
  }

  public function markBestReply(Reply $reply)
  {
    $this->update(['best_reply_id' => $reply->id]);
  }

  public function toSearchableArray()
  {
    return $this->toArray() + ['path' => $this->path()];
  }

}
