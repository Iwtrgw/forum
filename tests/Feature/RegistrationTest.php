<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Mail\PleaseConfirmYourEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use App\User;

class RegistrationTest extends TestCase
{
    use DatabaseMigrations;

    /* @test 邮件发送 */
    public function test_a_confirmation_email_is_sent_upon_registration()
    {
    	Mail::fake();

        // 用路由命名代替 url
        $this->post(route('register'),[
            'name' => 'NoNo1',
            'email' => 'NoNo1@example.com',
            'password' => '123456',
            'password_confirmation' => '123456'
        ]);

        Mail::assertSent(PleaseConfirmYourEmail::class);
    }

    /* @test 邮箱链接认证 */
    public function test_user_can_fully_confirm_their_email_addresses()
    {
    	// 应用 【邮件模拟】
    	 Mail::fake();

        $this->post(route('register'),[
            'name' => 'NoNo1',
            'email' => 'NoNo1@example.com',
            'password' => '123456',
            'password_confirmation' => '123456'
        ]);

        $user = User::whereName('NoNo1')->first();

        $this->assertFalse($user->confirmed);
        $this->assertNotNull($user->confirmation_token);

        $this->get(route('register.confirm',['token' => $user->confirmation_token]))
            ->assertRedirect(route('threads'));

        tap($user->fresh(),function($user) {
            $this->assertTrue($user->confirmed);
            $this->assertNull($user->confirmation_token);
        });
    }

    /* @test 测试无效 Token */
    public function test_confirming_an_invalid_token()
    {
    	// 测试无效 Token
    	$this->get(route('register.confirm'),['token' => 'invalid'])
    		 ->assertRedirect(route('threads'))
    		 ->assertSessionHas('flash','Unknown token.');
    }


}
