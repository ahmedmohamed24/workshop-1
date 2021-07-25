<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class RegisterTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    // @test
    public function testViewIsRegisterWhenVisitRegisterationRoute()
    {
        $this->withoutExceptionHandling();
        $this->get(route('user.register'))->assertViewIs('user.register');
    }

    // @test
    public function testRedirectAfterSuccessRegister()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->raw(['password' => 'ahmed1254', 'password_confirmation' => 'ahmed1254']);
        $response = $this->post(route('user.doRegister'), $user);
        $response->assertStatus(302);
    }

    // @test
    public function testUserDataSavedIntoDBWhenRegister()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->raw(['password' => 'ahmed1254', 'password_confirmation' => 'ahmed1254']);
        $this->post(route('user.doRegister'), $user);
        $this->assertDatabaseHas('users', ['email' => $user['email']]);
    }

    // @test
    public function testUserDataReturnedAfterRegister()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->raw(['password' => 'ahmed1254', 'password_confirmation' => 'ahmed1254']);
        $response = $this->post(route('user.doRegister'), $user);
        $response->assertSessionHas('user.name');
    }

    // @test
    public function testUserNameIsRequired()
    {
        $user = User::factory()->raw(['name' => '']);
        $response = $this->post(route('user.doRegister'), $user);
        $response->assertSessionHasErrors('name');
    }

    // @test
    public function testUserNameIsString()
    {
        $user = User::factory()->raw(['name' => 333]);
        $response = $this->post(route('user.doRegister'), $user);
        $response->assertSessionHasErrors('name');
    }

    // @test
    public function testUserEmailIsRequired()
    {
        $user = User::factory()->raw(['email' => '']);
        $response = $this->post(route('user.doRegister'), $user);
        $response->assertSessionHasErrors('email');
    }

    // @test
    public function testUserEmailIsValid()
    {
        $user = User::factory()->raw(['email' => 'ahmed']);
        $response = $this->post(route('user.doRegister'), $user);
        $response->assertSessionHasErrors('email');
    }

    // @test
    public function testUserEmailIsUnique()
    {
        User::factory()->create(['email' => 'a@gmail.com']);
        $user = User::factory()->raw(['email' => 'a@gmail.com']);
        $response = $this->post(route('user.doRegister'), $user);
        $response->assertSessionHasErrors('email');
    }

    // @test
    public function testUserPasswordIsRequired()
    {
        $user = User::factory()->raw(['password' => '']);
        $response = $this->post(route('user.doRegister'), $user);
        $response->assertSessionHasErrors('password');
    }

    // @test
    public function testUserPasswordIsConfirmed()
    {
        $user = User::factory()->raw();
        $response = $this->post(route('user.doRegister'), $user);
        $response->assertSessionHasErrors('password');
    }

    // @test
    public function testUserAuthenticatedAfterRegister()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->raw(['password' => 'ahmed1254', 'password_confirmation' => 'ahmed1254']);
        $this->post(route('user.doRegister'), $user);
        $this->assertAuthenticated();
    }
}
