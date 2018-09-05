<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SubscribeToThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /* @test */
    public function test_a_user_can_subscribe_to_threads()
    {
    	$this->signIn();

    	// Given we have a thread
    	$thread = create('App\Thread');

    	// And the user subscribes to the thread 
    	$this->post($thread->path() . '/subscriptions');

    	// A notification should be prepared for the user.
    	$this->assertCount(1,$thread->fresh()->subscriptions);
    }

    /* @test */
    public function test_a_user_can_unsubscribe_from_threads()
    {
        $this->signIn();

        // Given we have a thread
        $thread = create('App\Thread');

        $thread->subscribe();

        // And the user unsubscribes from the thread
        $this->delete($thread->path() . '/subscriptions');

        $this->assertCount(0,$thread->subscriptions);
    }
}
