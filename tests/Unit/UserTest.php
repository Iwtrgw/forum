<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
   use DatabaseMigrations;

   /* @test */
   public function test_a_user_can_fetch_their_most_recent_reply()
   {
   		$user = create('App\User');

   		$reply = create('App\Reply',['user_id' => $user->id]);

   		$this->assertEquals($reply->id,$user->lastReply->id);
   }

   /* @test 用户头像 */
   public function test_a_can_determine_their_avatar_path()
   {
   		$user = create('App\User');

   		$this->assertEquals('avatars/default.jpg',$user->avatar_path);

   		$user->avatar_path = 'avatars/me.jpg';

   		$this->assertEquals('avatars/me.jpg',$user->avatar_path);
   }
}
