<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class LoginTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    // @test
    public function testSeeLoginViewOnVisitingItsRoute()
    {
        $this->withoutExceptionHandling();
        $this->get(\route('login'))->assertViewIs('login');
    }

    // @test
    public function testRedirectAfterLogin()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create(['password' => 'password']);
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect('/');
    }

    // @test
    public function testCredentialsMustBeValid()
    {
        $user = User::factory()->create(['password' => 'password']);
        $this->post('/login', ['email' => $user->email.'a', 'password' => 'password'])->assertSessionHasErrors('email');
    }

    // @test
    public function testUserIsAuthAfterLogin()
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);
        $this->post('/login', ['email' => $user->email, 'password' => 'password']);
        $this->assertAuthenticated();
    }

    // @test
    public function testAuthUserCannotLogin()
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);
        $this->post('/login', ['email' => $user->email, 'password' => 'password']);
        $this->get('/login')->assertRedirect('/');
    }
}
