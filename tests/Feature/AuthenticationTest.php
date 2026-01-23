<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_request_otp()
    {
        $mobile = '9123456789';
        
        $smsMock = \Mockery::mock(SmsService::class);
        $smsMock->shouldReceive('sendOtp')
            ->once()
            ->with($mobile, \Mockery::type('string'));
            
        $this->app->instance(SmsService::class, $smsMock);

        $response = $this->post(route('loginOtp.post'), [
            'mobile' => $mobile,
        ]);

        $response->assertRedirect(route('verifyOtp'));
        $this->assertEquals($mobile, session('mobile'));
        $this->assertNotNull(session('otp'));
    }

    /** @test */
    public function user_can_login_with_correct_otp()
    {
        $mobile = '9123456789';
        $otp = '123456';
        
        $user = User::factory()->create([
            'mobile' => $mobile,
        ]);

        session([
            'mobile' => $mobile,
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        $response = $this->post(route('verifyOtp.post'), [
            'otp' => $otp,
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }

    /** @test */
    public function user_cannot_login_with_incorrect_otp()
    {
        $mobile = '9123456789';
        
        User::factory()->create([
            'mobile' => $mobile,
        ]);

        session([
            'mobile' => $mobile,
            'otp' => '123456',
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        $response = $this->post(route('verifyOtp.post'), [
            'otp' => '654321',
        ]);

        $response->assertSessionHasErrors('otp');
        $this->assertFalse(Auth::check());
    }
}
