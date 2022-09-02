<?php

namespace NorbyBaru\Passwordless\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use NorbyBaru\Passwordless\Tests\TestCase;
use NorbyBaru\Passwordless\Facades\Passwordless;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use NorbyBaru\Passwordless\Tests\Fixtures\Models\User as UserModel;

class LoginFeatureTest extends TestCase
{
    protected ?string $signedUrl;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = UserModel::create([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => Hash::make(Str::random(10)),
            'remember_token' => Str::random(10),
        ]);

        $this->user->setGeneratedMagicLinkToken(Passwordless::magicLink()->createToken($this->user));
        $this->signedUrl = Passwordless::magicLink()->generateUrl($this->user);
    }

    public function test_it_should_successfully_login_user()
    {
        $this->assertGuest();
        $response = $this->followingRedirects()->get($this->signedUrl);
        $response->assertSuccessful();
        $this->assertAuthenticatedAs($this->user);
    }

    public function test_tempered_signed_url_should_not_successfully_login_user()
    {
        $this->withoutExceptionHandling();
        $this->assertGuest();
        $this->expectException(InvalidSignatureException::class);
        $this->get($this->signedUrl . '.tempered');
        $this->assertGuest();
    }

    public function test_expired_signed_url_should_not_successfully_login_user()
    {
        $this->withoutExceptionHandling();
        $knowTime = Carbon::now()->addSeconds(config('passwordless.magic_link_timeout') + 1);
        Carbon::setTestNow($knowTime);
        $this->assertGuest();
        $this->expectException(InvalidSignatureException::class);
        $this->get($this->signedUrl);
        $this->assertGuest();
    }

    public function test_successful_login_should_follow_intended_url()
    {
        $this->assertGuest();
        $response = $this->get('/intended-redirect');
        $response->assertStatus(302);
        $response = $this->get($this->signedUrl);
        $response->assertRedirect('/intended-redirect');
        $this->assertAuthenticatedAs($this->user);
    }
    
    public function test_expired_token_should_not_successfully_login_user()
    {
        $this->withoutExceptionHandling();
        $this->assertGuest();
        $knowTime = Carbon::now()->addSeconds(config('passwordless.expire') + 1);
        Carbon::setTestNow($knowTime);
        $this->assertGuest();
        $this->expectException(InvalidSignatureException::class);
        $this->get($this->signedUrl);
        $this->assertGuest();
    }

    public function test_tempered_email_should_not_successfully_login_user()
    {
        $this->withoutExceptionHandling();
        $this->assertGuest();
        $queryParams = collect(explode('&', substr($this->signedUrl, strpos($this->signedUrl, '?') + 1)))
            ->mapWithKeys(function ($value) {
                $values = explode('=', $value);
                return [$values[0] => $values[1]];
            });
        $url = Arr::first(explode('?', $this->signedUrl));
        $queryParams['email'] = $this->faker->email;
        $temperedUrl =  $url . "?" . http_build_query($queryParams->all());
        $this->expectException(InvalidSignatureException::class);
        $this->get($temperedUrl);
        $this->assertGuest();
    }
}