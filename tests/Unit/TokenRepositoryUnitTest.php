<?php

namespace NorbyBaru\Passwordless\Tests\Unit;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use NorbyBaru\Passwordless\Tests\TestCase;
use NorbyBaru\Passwordless\TokenRepository;
use NorbyBaru\Passwordless\Tests\Fixtures\Models\User as UserModel;

class TokenRepositoryUnitTest extends TestCase
{

    protected TokenRepository $tokenRepository;
    protected UserModel $user;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->tokenRepository = $this->getTokenRepository();

        $this->user = UserModel::create([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => Hash::make(Str::random(10)),
            'remember_token' => Str::random(10),
        ]);
    }

    public function test_it_should_create_new_token_for_user()
    {
        Carbon::setTestNow();
        $token = $this->tokenRepository->create($this->user);
        $this->assertNotNull($token);

        $this->assertDatabaseHas(config('passwordless.table'), [
            'email' => $this->user->getEmailForMagicLink(),
            'created_at' => now(),
        ]);
    }

    public function test_it_should_not_create_new_user_token_due_to_throttling()
    {
        $knowTime = Carbon::now()->addSeconds(config('passwordless.throttle') - 1);
        Carbon::setTestNow();
        $token = $this->tokenRepository->create($this->user);
        $this->assertNotNull($token);

        Carbon::setTestNow($knowTime);
        $token = $this->tokenRepository->create($this->user);
        $this->assertNull($token);
    }

    public function test_it_should_determine_whether_token_was_recently_created_or_not()
    {
        Carbon::setTestNow();
        $recent = $this->tokenRepository->recentlyCreatedToken($this->user);
        $this->assertFalse($recent);

        $this->tokenRepository->create($this->user);
        $recent = $this->tokenRepository->recentlyCreatedToken($this->user);
        $this->assertTrue($recent);
    }

    public function test_it_should_validate_that_valid_token_exist_and_is_valid()
    {
        $token = $this->tokenRepository->create($this->user);

        $isValid = $this->tokenRepository->exist($this->user, $token);
        $this->assertTrue($isValid);
    }

    public function test_it_should_validate_that_invalid_token_do_not_exist_and_is_invalid()
    {
        $isValid = $this->tokenRepository->exist($this->user, Str::random(40));
        $this->assertFalse($isValid);
    }
    public function test_it_should_validate_that_expired_token_is_invalid()
    {
        $knowTime = Carbon::now()->subSeconds(config('passwordless.expire'));
        Carbon::setTestNow($knowTime);
        $token = $this->tokenRepository->create($this->user);

        $knowTime = Carbon::now()->addSeconds(config('passwordless.expire') + 1);
        Carbon::setTestNow($knowTime);
        $isValid = $this->tokenRepository->exist($this->user, $token);
        $this->assertFalse($isValid);
    }

    public function test_it_should_delete_all_user_tokens()
    {
        Carbon::setTestNow();
        $this->tokenRepository->create($this->user);

        $this->assertDatabaseHas(config('passwordless.table'), [
            'email' => $this->user->getEmailForMagicLink(),
        ]);

        $this->tokenRepository->delete($this->user);

        $this->assertDatabaseMissing(config('passwordless.table'), [
            'email' => $this->user->getEmailForMagicLink(),
        ]);
    }

    public function test_it_should_delete_all_user_expired_tokens()
    {
        $knowTime = Carbon::now()->subSeconds(config('passwordless.expire'));
        Carbon::setTestNow($knowTime);
        $this->tokenRepository->create($this->user);
        $this->assertDatabaseCount(config('passwordless.table'), 1);

        $knowTime = Carbon::now();
        Carbon::setTestNow($knowTime);
        $this->tokenRepository->deleteExpired();

        $this->assertDatabaseCount(config('passwordless.table'), 0);
    }
    
    private function getTokenRepository(): TokenRepository
    {
        $key = $this->app['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        $config = $this->getPasswordlessConfig();

        return new TokenRepository(
            $this->app['db']->connection(),
            $config['table'],
            $key,
            $config['expire'],
            $config['throttle']
        );
    }

    private function getPasswordlessConfig(): array
    {
        return $this->app['config']->get('passwordless');
    }
}