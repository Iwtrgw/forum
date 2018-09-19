<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateThreadsTest extends TestCase
{
   use RefreshDatabase;

   public function setUp()
   {
   		parent::setUp();

   		// 每个测试都要用到以下操作
   		$this->withExceptionHandling();

   		$this->signIn();
   }


   /* @test 只有话题创建者才能更新 */
   public function test_unauthorized_users_may_not_update_threads()
   {
   		$thread = create('App\Thread',['user_id' => create('App\User')->id]);

        $this->patch($thread->path(),[])->assertStatus(403);
   }

   /* @test 更新的字段要符合规则 */
   public function test_a_thread_requires_a_title_and_body_to_be_updated()
   {
   		$thread = create('App\Thread',['user_id' => auth()->id()]);

   		$this->patch($thread->path(),[
   			'title' => 'Changed.',
   		])->assertSessionHasErrors('body');

   		$this->patch($thread->path(),[
   			'body' => 'Changed.',
   		])->assertSessionHasErrors('title');
   }


   /* @test 话题可以更新*/
   public function test_a_thread_can_be_updated_by_its_creator()
   {
   		$thread = create('App\Thread',['user_id' => auth()->id()]);

        $this->patch($thread->path(),[
            'title' => 'Changed.',
            'body' => 'Changed body.'
        ]);

        tap($thread->fresh(),function ($thread) {
            $this->assertEquals('Changed.',$thread->title);
            $this->assertEquals('Changed body.',$thread->body);
        });
   }
}
