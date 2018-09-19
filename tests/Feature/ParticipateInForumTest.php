<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ParticipateInForumTest extends TestCase
{
	use DatabaseMigrations;

	/** @test */
    public function test_unauthenticated_user_may_no_add_replies()
    {
        $this->withExceptionHandling()
	         ->post('threads/some-channel/1/replies',[])
	         ->assertRedirect('/login');
    }

    /** @test */
    function test_an_authenticated_user_may_participate_in_forum_threads()
    {
        // Given we have a authenticated user
	    $this->signIn();
	    // And an existing thread
	    $thread = create('App\Thread');

	    // When the user adds a reply to the thread
	    $reply = make('App\Reply');

	    //dd($thread->path() . '/replies');  // 打印出来

	    $this->post($thread->path() .'/replies',$reply->toArray());

	    // Then their reply should be visible on the page
	    $this->assertDatabaseHas('replies',['body' => $reply->body]);
        $this->assertEquals(1,$thread->fresh()->replies_count);
    }

    /* @test */
    public function test_a_reply_reqiures_a_body()
    {
    	$this->withExceptionHandling()->signIn();

    	$thread = create('App\Thread');
    	$reply = make('App\Reply',['body' => null]);

    	$this->post($thread->path() . '/replies',$reply->toArray())
    		 ->assertSessionHasErrors('body');
    }

    /* @test 删除回复 */
    public function test_unauthorized_users_cannot_delete_replies()
    {
        $this->withExceptionHandling();

        $reply = create('App\Reply');

        // 未登录
        $this->delete("/replies/{$reply->id}")->assertRedirect('login');

        // 已登录没权限
        $this->signIn()
             ->delete("/replies/{$reply->id}")
             ->assertStatus(403);
    }

    /* @test 删除回复；已登录有权限 */
    public function test_authorized_users_can_delete_replies()
    {
        $this->signIn();

        $reply = create('App\Reply',['user_id' => auth()->id()]);

        $this->delete("/replies/{$reply->id}")->assertStatus(302);

        $this->assertDatabaseMissing('replies',['id' => $reply->id]);
        $this->assertEquals(0,$reply->thread->fresh()->replies_count);
    }

    /* @test 更新回复 无权限 */
    public function test_unauthorized_users_cannot_update_replies()
    {
        $this->withExceptionHandling();

        $reply  = create('App\Reply');

        // 未登录 
        $this->patch("/replies/{$reply->id}")
             ->assertRedirect('login');

        // 已登录无权限 
        $this->signIn()
             ->patch("/replies/{$reply->id}")
             ->assertStatus(403);
    }

    /* @test 更新回复 有权限 */
    public function test_authorized_users_can_update_replies()
    {
        $this->signIn();

        $reply = create('App\Reply',['user_id' => auth()->id()]);

        $updatedReply = 'You have been changed,foo.';
        $this->patch("/replies/{$reply->id}",['body' => $updatedReply]);

        $this->assertDatabaseHas('replies',['id' => $reply->id,'body' => $updatedReply]);
    }

    /* @test 关键字检测 */
    public function test_replies_contain_spam_may_not_be_created()
    {
        $this->withExceptionHandling();

        $this->signIn();

        $thread = create('App\Thread');
        $reply = make('App\Reply',[
           'body' => 'something forbidden'
        ]);

        $this->json('post',$thread->path() . '/replies',$reply->toArray())
            ->assertStatus(422);
    }

    /* @test 限制用户回复频率 */
    public function test_users_may_only_reply_a_maximum_of_once_per_minute()
    {
        $this->withExceptionHandling();
        $this->signIn();

        $thread = create('App\Thread');
        $reply = make('App\Reply',[
            'body' => 'My simple reply.'
        ]);

        $this->post($thread->path() . '/replies',$reply->toArray())
            ->assertStatus(200);

        $this->post($thread->path() . '/replies',$reply->toArray())
            ->assertStatus(429);
    }
}
