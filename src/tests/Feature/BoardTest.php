<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class BoardTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    // @test
    public function testStatus201ReceivedWhenCreatingBoard()
    {
        $this->withoutExceptionHandling();
        $board = Board::factory()->raw();
        $this->post(\route('board.save'), $board)->assertStatus(201);
    }

    public function testSuccessMessageReturnedOnCreation()
    {
        $this->withoutExceptionHandling();
        $board = Board::factory()->raw();
        $this->post(\route('board.save'), $board)->assertSessionHas('message', 'success');
    }

    // @test
    public function testDataSavedInDBAfterBoardCreation()
    {
        $this->withoutExceptionHandling();
        $board = Board::factory()->raw();
        $this->post(\route('board.save'), $board);
        $this->assertDatabaseHas('boards', $board);
    }

    // @test
    public function testTitleIsRequired()
    {
        $board = Board::factory()->raw(['title' => '']);
        $this->post(\route('board.save'), $board)->assertSessionHasErrors('title');
    }

    // @test
    public function testSlugIsCreatedAtBoardCreation()
    {
        $board = Board::factory()->raw(['title' => 'some title']);
        $this->post(\route('board.save'), $board);
        $this->assertDatabaseHas('boards', ['slug' => 'some-title']);
    }

    // @test
    public function testSlugIsUniquePerUser()
    {
        Board::factory()->create(['title' => 'some title', 'slug' => 'some-title']);
        $board = Board::factory()->raw(['title' => 'some title']);
        $response = $this->post(\route('board.save'), $board);
        $response->assertSessionHasErrors('title');
    }

    // @test
    public function testHasAColor()
    {
        $board = Board::factory()->raw();
        $this->post(\route('board.save'), $board);
        $this->assertDatabaseHas('boards', ['color' => $board['color']]);
    }

    // @test
    public function testOnlyAuthCanCreateBoard()
    {
        Auth::logout();
        $board = Board::factory()->raw();
        $response = $this->post(\route('board.save'), $board);
        $response->assertRedirect('/login');
    }

    // @test
    public function testInfoReturnedWhenVisitingItsLink()
    {
        $board = Board::factory()->raw();
        $this->post(\route('board.save'), $board);
        $response = $this->get(\route('board.show', $board['slug']), $board);
        $response->assertViewIs('board.home');
    }

    // @test
    public function testOnlyOwnerCanVisitBoard()
    {
        $board = Board::factory()->raw();
        $this->post(\route('board.save'), $board);
        Auth::logout();
        $this->be(User::factory()->create());
        $response = $this->get(\route('board.show', $board['slug']), $board);
        $response->assertStatus(404);
    }

    // @test
    public function testStatus302WhenEditing()
    {
        $board = Board::factory()->raw();
        $this->post(\route('board.save'), $board);
        $response = $this->put(\route('board.update', $board['slug']), ['title' => 'new title']);
        $response->assertStatus(302);
    }

    // @test
    public function testStatus202WhenEditing()
    {
        $board = Board::factory()->raw();
        $this->post(\route('board.save'), $board);
        $response = $this->put(\route('board.update', $board['slug']), ['title' => 'new title']);
        $response->assertSessionHas('message', 'success');
    }

    // @test
    public function testSlugIsReGeneratedOnUpdating()
    {
        $board = Board::factory()->raw();
        $this->post(\route('board.save'), $board);
        $newData = ['title' => 'new title', 'color' => '#000000'];
        $this->put(\route('board.update', $board['slug']), $newData);
        $this->assertDatabaseHas('boards', ['slug' => 'new-title']);
    }

    // @test
    public function testDataIsChangedInDBAfterUpdating()
    {
        $board = Board::factory()->raw();
        $this->post(\route('board.save'), $board);
        $newData = ['title' => 'new title', 'color' => '#000000'];
        $this->put(\route('board.update', $board['slug']), $newData);
        $this->assertDatabaseHas('boards', $newData);
    }

    // @test
    public function testTitleRequiredOnEditing()
    {
        $board = Board::factory()->raw();
        $this->post(\route('board.save'), $board);
        $newData = ['title' => '', 'color' => '#000000'];
        $response = $this->put(\route('board.update', $board['slug']), $newData);
        $response->assertSessionHasErrors('title');
    }

    // @test
    public function testTitleUniqueOnEditing()
    {
        Board::factory()->create(['title' => 'some title', 'owner' => 1, 'slug' => 'some-title']);
        $board = Board::factory()->raw();
        $this->post(\route('board.save'), $board);
        $newData = ['title' => 'some title', 'color' => '#000000'];
        $response = $this->put(\route('board.update', $board['slug']), $newData);
        $response->assertSessionHasErrors('message');
    }

    // @test
    public function testOnlyOwnerCanUpdateBoard()
    {
        $board = Board::factory()->raw();
        $this->post(\route('board.save'), $board);
        Auth::logout();
        $newData = ['title' => 'some title', 'color' => '#000000'];
        $response = $this->actingAs(User::factory()->create())->put(\route('board.update', $board['slug']), $newData);
        $response->assertNotFound();
    }

    // @test
    public function testStatus302WhenDeleting()
    {
        $board = Board::factory()->raw();
        $this->post(\route('board.save'), $board);
        $response = $this->delete(\route('board.delete', $board['slug']));
        $response->assertStatus(302);
    }

    // @test
    public function testSuccessMessageWhenDeleting()
    {
        $board = Board::factory()->raw();
        $this->post(\route('board.save'), $board);
        $response = $this->delete(\route('board.delete', $board['slug']));
        $response->assertSessionHas('message', 'success');
    }

    // @test
    public function testBoardDeletedFromDBOnDeletingRequest()
    {
        $board = Board::factory()->raw();
        $this->post(\route('board.save'), $board);
        $this->assertDatabaseCount('boards', 1);
        $this->delete(\route('board.delete', $board['slug']));
        $this->assertDatabaseCount('boards', 0);
    }

    // @test
    public function testOnlyOwnerCanDeleteBoard()
    {
        $board = Board::factory()->raw();
        $this->post(\route('board.save'), $board);
        Auth::logout();
        $response = $this->actingAs(User::factory()->create())->delete(\route('board.delete', $board['slug']));
        $response->assertNotFound();
    }
}
