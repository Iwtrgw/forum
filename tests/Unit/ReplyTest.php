<?php

namespace Tests\Unit;

use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReplyTest extends TestCase
{
	use DatabaseMigrations;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_a_reply_has_an_owner()
    {
        $reply = create('App\Reply');

        $this->assertInstanceOf('App\User',$reply->owner);
    }

    /* @test */
    public function test_it_knows_if_it_was_just_published()
    {
        $reply = create('App\Reply');

        $this->assertTrue($reply->wasJustPublished());

        $reply->created_at = Carbon::now()->subMonth();

        $this->assertFalse($reply->wasJustPublished());
    }

    /* @test */
    public function test_it_can_detect_all_mentioned_users_in_the_body()
    {
        $reply = create('App\Reply',[
            'body' => '@JaneDoe wants to talk to @JohnDoe'
        ]);

        $this->assertEquals(['JaneDoe','JohnDoe'],$reply->mentionedUsers());
    }

    /* @test 给被 @ 的用户名加上链接 */
    public function test_it_warps_mentioned_username_in_the_body_within_archor_tags()
    {
        $reply = create('App\Reply',[
            'body' => 'Hello @Jane-Doe.'
        ]);

        $this->assertEquals('Hello <a href="/profiles/Jane-Doe">@Jane-Doe</a>.',$reply->body);
    }

    /* @test 最佳回复功能测试 */
    public function test_it_knows_if_it_is_the_best_reply()
    {
        $reply = create('App\Reply');

        $this->assertFalse($reply->isBest());

        $reply->thread->update(['best_reply_id' => $reply->id]);

        $this->assertTrue($reply->isBest());
    }


    /* @test 防止XSS安全漏洞 */
    public function test_a_reply_body_is_sanitized_automatically()
    {
        $reply = create('App\Reply',['body' => "<script>alert('bad')</script><p>This is OK.</p>"]);

        $this->assertEquals("<p>This is OK.</p>",$reply->body);
    }
}
