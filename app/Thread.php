<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Thread extends Model
{

    use RecordsActivity;

   protected $guarded = [];
   protected $with = ['creator'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('replyCount',function ($builder){
           $builder->withCount('replies');
        });

        static::deleting(function($thread) {
          $thread->replies()->delete();
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
   		$this->replies()->create($reply);
   }

   public function scopeFilter($query,$filters)
   {
      return $filters->apply($query);
   }
}
