<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reply extends Model
{

	use Favoritable,RecordsActivity;

    protected $guarded = [];
    protected $with = ['owner','favorites'];
    protected $appends = ['favoritesCount','isFavorited','isBest'];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($reply){
            $reply->thread->increment('replies_count');
            $reply->body = clean($reply->body,'thread_or_reply_body');
        });

        static::deleted(function ($reply){
            if ($reply->id == $reply->thread->best_reply_id) {
                $reply->thread->update(['best_reply_id' => null]);
            }
            
            $reply->thread->decrement('replies_count');
        });
    }

    public function owner()
    {
    	return $this->belongsTo(User::class,'user_id'); //使用 user_id 字段进行模型关联
    }

    public function favorites()
    {
    	return $this->morphMany(Favorite::class,'favorited');
    }

    public function favorite()
    {
    	$attributes = ['user_id' => auth()->id()];

    	if (! $this->favorites()->where($attributes)->exists()) {
    		return $this->favorites()->create($attributes);
    	}
    }

    public function isFavorited()
    {
    	return !! $this->favorites->where('user_id',auth()->id())->count();
    }

    public function getFavoritesCountAttribute()
    {
    	return $this->favorites->count();
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function wasJustPublished()
    {
        return $this->created_at->gt(Carbon::now()->subMinute());
    }

    public function mentionedUsers()
    {
        preg_match_all('/\@([\w\-]+)/', $this->body, $matches);

        return $matches[1];
    }

    public function path()
    {
        return $this->thread->path() . "#reply-{$this->id}";
    }

    public function setBodyAttribute($body)
    {
        $this->attributes['body'] = preg_replace('/@([\w\-]+)/','<a href="/profiles/$1">$0</a>',$body);
    }

    /* 是否是最佳回复 */
    public function isBest()
    {
        return $this->thread->best_reply_id == $this->id;
    }

    public function getIsBestAttribute() {
        return $this->isBest();
    }
}
