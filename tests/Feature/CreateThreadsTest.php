<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Activity;

class CreateThreadsTest extends TestCase
{

	use DatabaseMigrations;

	/* @test */
	public function test_guests_may_not_create_threads()
	{
		$this->withExceptionHandling();

        $this->get('/threads/create')
             ->assertRedirect(route('login'));

        $this->post(route('threads'))
             ->assertRedirect(route('login'));
	}

    /* @test 邮箱认证 */
    public function test_new_users_must_first_confirm_their_email_address_before_creating_threads()
    {
        // 调用 unconfirmed,生成未认证用户
        $user = factory('App\User')->states('unconfirmed')->create();

        $this->signIn($user);

        $thread = make('App\Thread');

        $this->post(route('threads'),$thread->toArray())
             ->assertRedirect('/threads')
             ->assertSessionHas('flash','You must first confirm your email address.');
    }

    /** @test */
    public function test_a_user_can_create_new_forum_threads()
    {
        // Given we have a signed in user
        $this->signIn();  // 已登录用户

        // When we hit the endpoint to cteate a new thread
        $thread = make('App\Thread');
        $response = $this->post(route('threads'),$thread->toArray());
        
        // We should see the new thread
        $this->get($response->headers->get('Location'))
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }

    /* @test*/
    public function test_a_thread_requires_a_title()
    {

        $this->publishThread(['title'=> null])
             ->assertSessionHasErrors('title');
    }

    /* @test */
    public function test_a_thread_requires_a_body()
    {
        $this->publishThread(['body' => null])
             ->assertSessionHasErrors('body');
    }

    /* @test */
    public function test_a_thread_requires_a_valid_channel()
    {
        // 新建两个 Channel,id 分别为 1 跟 2
        factory('App\Channel',2)->create();

        $this->publishThread(['channel_id' => null])
             ->assertSessionHasErrors('channel_id');

        // channle_id 为 999，是一个不存在的 Channel
        $this->publishThread(['channel_id' => 999])
             ->assertSessionHasErrors('channel_id');
    }

    /* @test 删除操作权限认证 */
    public function test_unauthorized_users_may_not_delete_threads()
    {
        $this->withExceptionHandling();

        $thread = create('App\Thread');

        $this->delete($thread->path())->assertRedirect(route('login'));

        $this->signIn();
        $this->delete($thread->path())->assertStatus(403);
    }

    /* @test 话题删除功能 */
    public function test_authorized_users_can_delete_threads()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $reply = create('App\Reply', ['thread_id' => $thread->id]);

        $response = $this->json('DELETE', $thread->path());

        $response->assertStatus(204);

        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        $this->assertEquals(0,Activity::count());
    }

    public function publishThread($overrides = [])
    {
        $this->withExceptionHandling()->signIn();

        $thread = make('App\Thread',$overrides);

        return $this->post(route('threads'),$thread->toArray());
    }



   
}
