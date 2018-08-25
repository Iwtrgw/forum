<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FavoritiesTest extends TestCase
{
    use DatabaseMigrations;

    /* @test */
    public function test_guests_can_not_favorite_anything()
    {
    	$this->withExceptionHandling()
    		 ->post('/replies/1/favorites')
    		 ->assertRedirect('/login');
    }

    /* @test */
    public function test_au_authenticated_user_can_favorite_any_reply()
    {

    	// 登录 
    	$this->signIn();

    	$reply = create('App\Reply');

    	// If I post a "favorite" endpoint
    	$this->post('replies/' . $reply->id . '/favorites');

    	// It Should be recored in the database
    	$this->assertCount(1,$reply->favorites);
    }

    /* @test */
    public function test_au_authenticeted_user_may_only_favorite_a_reply_once()
    {
    	// 登录 
    	$this->signIn();

    	$reply = create('App\Reply');

    	try{
    		$this->post('replies/' . $reply->id . '/favorites');
    		$this->post('replies/' . $reply->id . '/favorites');
    	}catch(\Exception $e){
    		$this->fail('Did not expect to insert the same record set twice.');
    	}

    	$this->assertCount(1,$reply->favorites);
    }
}
