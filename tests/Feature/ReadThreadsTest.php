<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;

    protected $thread;

    public function setUp()
    {
        parent::setUp();

        $this->thread = create('App\Thread');
    }

    public function test_a_user_can_view_all_threads()
    {
      
        $this->get('/threads')
             ->assertSee($this->thread->title);
       
    }

    /* test */
    public function test_a_user_can_read_a_single_thread()
    {
        $this->get($this->thread->path())
             ->assertSee($this->thread->title);
       
    }

    /* test */
    public function test_a_user_can_read_replies_that_are_associated_with_a_thread()
    {
        /*
         * 如果存在 Thread
         * 并且该 Thread 拥有回复
         */
        $reply = factory('App\Reply')->create(['thread_id' => $this->thread->id]);
        
        /* 那么当我们看到该 Thread 时
         * 我们也要看到回复
         */
        
        $this->get($this->thread->path())->assertSee($reply->body);
    }

    /* @test */
    public function a_user_can_filter_threads_assording_to_a_channel()
    {
        $channel = create('App\Channel');
        $threadInChannel = create('App\Thread',['channel_id' => $channel->id]);
        $threadNoChannel = create('App\Thread');

        $this->get('/threads/' . $channel->slug)
             ->assertSee($threadInChannel->title)
             ->assertDontSee($threadNoChannel->title);
    }

    /* @test */
    public function a_user_can_filter_threads_by_any_username()
    {
        $this->signIn(create('App\User',['name' => 'NoNo1']));

        $threadByNoNo1 = create('App\Thread',['user_id' => auth()->id()]);
        $threadNoByNoNo1 = create('App\Thread');

        $this->get('threads?by=NoNo1')
             ->assertSee($threadByNoNo1->title)
             ->assertDontSee($threadNoByNoNo1->title);
    }
}
