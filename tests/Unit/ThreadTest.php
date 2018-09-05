<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Support\Facades\Notification;

class ThreadTest extends TestCase
{
	use DatabaseMigrations;

	protected $thread;

	public function setUp()
	{
		parent::setUp();

		$this->thread = create('App\Thread');
	}

    /* @test */

    function a_thread_can_make_a_string_path()
    {
        $thread = create('App\Thread');

        $this->assertEquals("/threads/{$thread->channel->slug}/{$thread->id}",$thread->path());
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_a_thread_has_replies()
    {

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection',$this->thread->replies);
    }

    /* test */
    public function test_a_thread_has_a_creator()
    {
    	$this->assertInstanceOf('App\User',$this->thread->creator);
    }

    /* test */
    public function test_a_thread_can_add_a_reply()
    {
    	$this->thread->addReply([
    		'body' => 'Foobar',
    		'user_id' => 1
    	]);

    	$this->assertCount(1,$this->thread->replies);
    }

    /* @test 消息通知 */
    public function test_a_thread_notifies_all_registered_subscribers_when_a_reply_is_added()
    {
        Notification::fake();

        $this->signIn()
             ->thread
             ->subscribe()
             ->addReply([
                'body' => 'Foobar',
                'user_id' => 999 // 伪造一个与当前登录用户不同的 id
             ]);

         Notification::assertSentTo(auth()->user(),ThreadWasUpdated::class);
    }

    /* @test */
    function a_thread_belongs_to_a_channel()
    {
        $thread = create('App\Thread');

        $this->assertInstanceOf('APP\channel',$thread->channel);
    }

    /* @test 话题订阅 */
    public function test_a_thread_can_be_subscribed_to()
    {
        // Given we have a thread
        $thread = create('App\Thread');

        // And an authenticated user
        $this->signIn();

        // When the user subscribes to the thread
        $thread->subscribe($userId = 1);

        // Then we should be able to fetch all threads that the user has subscribed to.
        $this->assertEquals(
            1,
            $thread->subscriptions()->where('user_id',$userId)->count()
        );
    }


    /* @test 取消话题订阅 */
    public function test_a_thread_can_be_unsubscribed_from()
    {
        // Given we have a thread
        $thread = create('App\Thread');

        // And a user who is subscribed to the thread
        $thread->subscribe($userId = 1);

        $thread->unsubscribe($userId);

        $this->assertCount(0,$thread->subscriptions);
    }

    /* @test */
    public function test_it_knows_if_the_authenticated_user_is_subscribed_to_it()
    {
        // Given we have a thread
        $thread = create('App\Thread');

        // And a user who is subscribed to the thread
        $this->signIn();

        $this->assertFalse($thread->isSubscribedTo);

        $thread->subscribe();

        $this->assertTrue($thread->isSubscribedTo);
    }

    /* @test 跟踪话题更新 */
    public function test_a_thread_can_check_if_the_authenticated_user_has_read_all_replies()
    {
        $this->signIn();

        $thread = create('App\Thread');

        tap(auth()->user(),function ($user) use ($thread){
            
            // 对标题进行加粗显示
            $this->assertTrue($thread->hasUpdatesFor($user));
            // 浏览话题
            $user->read($thread);
            // 取消加粗
            $this->assertFalse($thread->hasUpdatesFor($user));
        });
    }
}
