<?php

namespace Tests\Unit;

use App\Activity;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ActivityTest extends TestCase
{
    use DatabaseMigrations;

    /* @test */
    public function test_it_records_activity_when_a_thread_is_created()
    {
    	 $this->signIn();

        $thread = create('App\Thread');

        $this->assertDatabaseHas('activities',[
           'user_id' => auth()->id(),
           'subject_id' => $thread->id,
           'subject_type' => 'App\Thread',
           'type' => 'created_thread',
        ]);

        // 当前测试中，表里只存在一条记录
        $activity = Activity::first();

        $this->assertEquals($activity->subject->id,$thread->id);
    }

    /* @test */
    public function test_it_records_activity_when_a_reply_is_created()
    {
    	$this->signIn();

    	$reply = create('App\Reply');

    	$this->assertEquals(2,Activity::count());
    }
}
