<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BestReplyTest extends TestCase
{
    use DatabaseMigrations;

    /* @test 最佳回复 */
    public function test_a_thread_creator_may_mark_any_reply_as_the_best_reply()
    {
    	$this->signIn();

    	$thread = create('App\Thread',['user_id' => auth()->id()]);

    	$replies = create('App\Reply',['thread_id' => $thread->id],2);

    	$this->assertFalse($replies[1]->isBest());

    	$this->postJson(route('best-replies.store',[$replies[1]->id]));

    	$this->assertTrue($replies[1]->fresh()->isBest());
    }

    /* @test 只有创建者才能标记最佳话题 */
    public function test_only_the_thread_creator_may_mark_a_reply_as_best()
    {
    	$this->withExceptionHandling()->signIn();

    	$thread = create('App\Thread',['user_id' => auth()->id()]);

    	$replies = create('App\Reply',['thread_id' => $thread->id],2);

    	$this->signIn(create('App\User'));

    	$this->postJson(route('best-replies.store',[$replies[1]->id]))
    		 ->assertStatus(403);

		$this->assertFalse($replies[1]->fresh()->isBest());
    }

    /* @test 删除最佳回复 */
    public function test_if_a_best_reply_is_deleted_then_the_thread_is_properly_updated_to_reflect_that()
    {
        $this->signIn();

        $reply = create('App\Reply',['user_id' => auth()->id()]);

        $reply->thread->markBestReply($reply);

        $this->deleteJson(route('replies.destroy',$reply));

        $this->assertNull($reply->thread->fresh()->best_reply_id);
    }
}