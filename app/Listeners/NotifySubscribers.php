<?php

namespace App\Listeners;

use App\Events\ThreadRecivedNewReply;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifySubscribers
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
        $thread = $event->reply->thread;

        $thread->subscriptions
               ->where('user_id','!=',$event->reply->user_id)
               ->each
               ->notify($event->reply);
    }
}
