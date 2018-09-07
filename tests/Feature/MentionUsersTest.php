<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MentionUsersTest extends TestCase
{
    use DatabaseMigrations;

    /* @test @某人*/
    public function test_mentioned_users_in_a_reply_are_notified()
    {
    	$john = create('App\User',['name' => 'John']);

        $this->signIn($john);

        $jane = create('App\User',['name' => 'Jane']);

        $thread = create('App\Thread');

        $reply = make('App\Reply',[
           'body' => '@Jane look at this. And also @Luke'
        ]);

        $this->json('post',$thread->path() . '/replies',$reply->toArray());

        $this->assertCount(1,$jane->notifications);
    }
}
