<?php

namespace NorbyBaru\Passwordless\Tests\Unit;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use NorbyBaru\Passwordless\MagicLink;
use NorbyBaru\Passwordless\Tests\TestCase;
use NorbyBaru\Passwordless\Facades\Passwordless;
use NorbyBaru\Passwordless\Tests\Fixtures\Models\User as UserModel;

class MagicLinkUnitTest extends TestCase 
{
    protected MagicLink $magicLink;
    protected UserModel $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->magicLink = Passwordless::magicLink();

        $this->user = UserModel::create([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => Hash::make(Str::random(10)),
            'remember_token' => Str::random(10),
        ]);
    }

    public function test_it_should_create_a_token()
    {
        $token = $this->magicLink->createToken($this->user);
        $this->assertNotEmpty($token);
    }

    public function test_it_should_created_signed_url()
    {
        $signedUrl = $this->magicLink->generateUrl($this->user, Str::random(40));

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
        $this->assertNotEmpty($url);
    }
}