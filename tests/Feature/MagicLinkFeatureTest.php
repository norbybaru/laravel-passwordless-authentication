<?php

namespace NorbyBaru\Passwordless\Tests\Feature;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use NorbyBaru\Passwordless\Facades\Passwordless;
use NorbyBaru\Passwordless\MagicLink;
use NorbyBaru\Passwordless\Notifications\SendMagicLinkNotification;
use NorbyBaru\Passwordless\Tests\Fixtures\Models\User as UserModel;
use NorbyBaru\Passwordless\Tests\TestCase;

class MagicLinkFeatureTest extends TestCase
{
    public function test_it_can_generate_magic_link()
    {
        $token = Passwordless::magicLink()->createToken($this->user);

        $this->assertNotNull($token);
    }

    public function test_it_should_validate_token_successfully()
    {
        $token = Passwordless::magicLink()->createToken($this->user);
        $isValid = Passwordless::magicLink()->isValidToken($this->user, $token);

        $this->assertTrue($isValid);
    }

    public function test_it_should_unsuccessfully_validate_invalid_token()
    {
        Passwordless::magicLink()->createToken($this->user);
        $isValid = Passwordless::magicLink()->isValidToken($this->user, Str::random(24));

        $this->assertFalse($isValid);
    }

    public function test_it_should_unsuccessfully_validate_expired_token()
    {
        $knowTime = Carbon::now()->addSeconds(config('passwordless.expire') + 1);
        Carbon::setTestNow();
        $token = Passwordless::magicLink()->createToken($this->user);
        Carbon::setTestNow($knowTime);

        $isValid = Passwordless::magicLink()->isValidToken($this->user, $token);
        $this->assertFalse($isValid);
    }

    public function test_it_should_generate_temporary_signed_url()
    {
        $token = Passwordless::magicLink()->createToken($this->user);
        $signedUrl = Passwordless::magicLink()->generateUrl($this->user, $token);

        $queryParams = collect(explode('&', substr($signedUrl, strpos($signedUrl, '?') + 1)))
            ->mapWithKeys(function ($value) {
                $values = explode('=', $value);

                return [$values[0] => $values[1]];
            });
        $url = Arr::first(explode('?', $signedUrl));

        $this->assertNotNull($queryParams['hash']);
        $this->assertNotNull($queryParams['signature']);
        $this->assertNotNull($queryParams['expires']);
        $this->assertEquals($this->user->email, urldecode($queryParams['email']));
        $this->assertEquals(config('app.url').config('passwordless.callback_url'), $url);
        $this->assertNotNull($url);
    }

    public function test_it_should_send_login_link_to_user()
    {
        $status = Passwordless::magicLink()->sendLink(['email' => $this->user->email]);

        Notification::assertSentTo($this->user, SendMagicLinkNotification::class);

        $this->assertEquals(MagicLink::MAGIC_LINK_SENT, $status);
    }

    public function test_it_should_throttle_magic_link_generation_subsequent_request()
    {
        $knowTime = Carbon::now()->addSeconds(config('passwordless.throttle') - 1);

        Carbon::setTestNow();
        $status = Passwordless::magicLink()->sendLink(['email' => $this->user->email]);
        Notification::assertSentTo($this->user, SendMagicLinkNotification::class);
        $this->assertEquals(MagicLink::MAGIC_LINK_SENT, $status);

        Notification::fake();
        Carbon::setTestNow($knowTime);
        $status = Passwordless::magicLink()->sendLink(['email' => $this->user->email]);
        Notification::assertNotSentTo($this->user, SendMagicLinkNotification::class);
        $this->assertEquals(MagicLink::TOKEN_THROTTLED, $status);
    }

    public function test_it_should_not_generate_return_message_when_invalid_user_found()
    {
        $status = Passwordless::magicLink()->sendLink(['email' => $this->faker->email]);
        $this->assertEquals(MagicLink::INVALID_USER, $status);
    }

    public function test_it_should_successfully_validate_magic_link()
    {
        $token = Passwordless::magicLink()->createToken($this->user);
        $signedUrl = Passwordless::magicLink()->generateUrl($this->user, $token);
        $queryParams = collect(explode('&', substr($signedUrl, strpos($signedUrl, '?') + 1)))
            ->mapWithKeys(function ($value) {
                $values = explode('=', $value);

                return [$values[0] => $values[1]];
            });

        $credentials = [
            'token' => $queryParams->get('token'),
            'email' => urldecode($queryParams->get('email')),
        ];

        $result = Passwordless::magicLink()->validateMagicLink($credentials);

        $this->assertInstanceOf(UserModel::class, $result);
    }
}
