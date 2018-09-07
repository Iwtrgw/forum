<?php

namespace App\Listeners;

use App\Events\ThreadRecivedNewReply;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\YouWereMentioned;
use App\User;

class NotifyMentionedUsers
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ThreadRecivedNewReply  $event
     * @return void
     */
    public function handle(ThreadRecivedNewReply $event)
    {
       collect($event->reply->mentionedUsers())
            ->map(function ($name) {
                return User::where('name',$name)->first();
            })
            ->filter()
            ->each(function ($user) use ($event){
                $user->notify(new YouWereMentioned($event->reply));
            });
    }
}
