<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LockThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /* @test */
    public function test_once_locked_thread_may_not_receive_new_replies()
    {
    	$this->signIn();

    	$thread = create('App\Thread',['locked' => true]);

    	$this->post($thread->path() . '/replies',[
    		'body' => 'Foobar',
    		'user_id' => auth()->id(),
    	])->assertStatus(422);
    }

    /* @test */
    public function test_non_administrator_may_not_lock_threads()
    {
    	$this->withExceptionHandling(); 

    	$this->signIn();

    	$thread = create('App\Thread',[
    		'user_id' =>auth()->id(),
    	]);

    	$this->post(route('locked-threads.store',$thread))->assertStatus(403);

    	$this->assertFalse(!! $thread->fresh()->loked);
    }

    /* @test 有管理员权限才能锁定话题 */
    public function test_administrators_can_lock_threads()
    {
    	$this->signIn(factory('App\User')->states('administrator')->create());

    	$thread = create('App\Thread',['user_id' =>auth()->id()]);

    	$this->post(route('locked-threads.store',$thread));

    	$this->assertTrue(!! $thread->fresh()->locked);
    }

    /* @test 解锁话题 */
    public function test_administrators_can_unlock_threads()
    {
        $this->signIn(factory('App\User')->states('administrator')->create());

        $thread = create('App\Thread',['user_id' =>auth()->id(),'locked' => true]);

        $this->delete(route('locked-threads.destroy',$thread));

        $this->assertFalse($thread->fresh()->locked);
    }
}
