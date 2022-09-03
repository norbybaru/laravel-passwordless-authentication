<?php

namespace NorbyBaru\Passwordless\Tests\Unit;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use NorbyBaru\Passwordless\CanUsePasswordlessAuthenticatable;
use NorbyBaru\Passwordless\Tests\Fixtures\Models\User;
use NorbyBaru\Passwordless\Tests\TestCase;

class UserUnitTest extends TestCase
{
    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => Hash::make(Str::random(10)),
            'remember_token' => Str::random(10),
        ]);
    }

    public function test_user_model_should_implement_passwordless()
    {
        $this->assertInstanceOf(CanUsePasswordlessAuthenticatable::class, $this->user);
    }

    public function test_it_should_return_email_for_magic_link()
    {
        $this->assertNotNull($this->user->getEmailForMagicLink());
    }
}
