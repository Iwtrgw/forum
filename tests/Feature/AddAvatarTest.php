<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class AddAvatarTest extends TestCase
{
    use DatabaseMigrations;

    /* @test 上传图像 */
    public function test_only_members_can_add_avatars()
    {
    	$this->withExceptionHandling();

    	$this->json('POST','api/users/1/avatar')->assertStatus(401);
    }

    /* @test 上传的图像必须是有效的  */
    public function test_a_valid_avatar_must_be_provided()
    {
    	$this->withExceptionHandling()->signIn();

    	$this->json('POST','api/users/' . auth()->id() . '/avatar',[
    		'avatar' => 'not-an-image'
    	])->assertStatus(422);
    }

    /* @test 测试文件上传 */
    public function test_a_user_may_add_an_avatar_to_their_profile()
    {
    	$this->signIn();

    	Storage::fake('public');

    	$this->json('POST','api/users/' . auth()->id() . '/avatar',[
    		'avatar' => $file = UploadedFile::fake()->image('avatar.jpg')
    	]);

    	$this->assertEquals('avatars/' . $file->hashName(),auth()->user()->avatar_path);

    	Storage::disk('public')->assertExists('avatars/' . $file->hashName());
    }
}
