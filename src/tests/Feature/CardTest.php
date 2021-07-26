<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\Card;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class CardTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    // @test
    public function testStatus302OnAttachingNewCard()
    {
        $this->withoutExceptionHandling();
        $this->createUserWithBoard();
        $card = Card::factory()->raw();
        $this->post(route('card.save'), $card)->assertStatus(302);
    }

    // @test
    public function testCardTitleIsUniquePerBoard()
    {
        $this->withoutExceptionHandling();
        $this->createUserWithBoard();
        Card::factory()->create(['title' => 'some text', 'board' => 1]);
        $card = Card::factory()->raw(['title' => 'some text', 'board' => 1]);
        $response = $this->post(route('card.save'), $card);
        $response->assertSessionHasErrors('message');
    }

    // @test
    public function testAttachCardToBoardSavedInDB()
    {
        $this->withoutExceptionHandling();
        $this->createUserWithBoard();
        $card = Card::factory()->raw(['title' => 'some text', 'board' => 1]);
        $this->post(route('card.save'), $card);
        $this->assertDatabaseHas('cards', ['title' => 'some text']);
    }

    // @test
    public function testOnlyBoardOwnerCanAttachCard()
    {
        $this->createUserWithBoard();
        $card = Card::factory()->raw(['title' => 'some text', 'board' => 1]);
        Auth::logout();
        $this->be(User::factory()->create());
        $this->post(route('card.save'), $card)->assertNotFound();
    }

    // @test
    public function testStatus302WhenUpdating()
    {
        $this->createUserWithBoard();
        $card = Card::factory()->raw(['title' => 'some text', 'board' => 1]);
        $this->post(route('card.save'), $card);
        $newCard = ['title' => 'new title'];
        $response = $this->put(\route('card.update', [1, 2]), $newCard);
        $response->assertStatus(302);
    }

    // @test
    public function testUpdateCard()
    {
        $this->createUserWithBoard();
        $card = Card::factory()->raw(['title' => 'some text', 'board' => 1]);
        $this->post(route('card.save'), $card)->assertSessionHas('message');
        $newCard = ['title' => 'new title'];
        $this->put(\route('card.update', [1, 1]), $newCard);
        $this->assertDatabaseHas('cards', ['title' => 'new title']);
    }

    // @test
    public function testTitleUniqueOnUpdating()
    {
        $this->createUserWithBoard();
        Card::factory()->create(['title' => 'new title', 'board' => 1]);
        $card = Card::factory()->raw(['title' => 'some text', 'board' => 1]);
        $this->post(route('card.save'), $card)->assertSessionHas('message');
        $newCard = ['title' => 'new title'];
        $this->put(\route('card.update', [1, 1]), $newCard)->assertSessionHasErrors('message');
    }

    // @test
    public function testOnlyBoardOwnerCanUpdateCard()
    {
        $this->createUserWithBoard();
        Card::factory()->create(['title' => 'new title', 'board' => 1]);
        $card = Card::factory()->raw(['title' => 'some text', 'board' => 1]);
        $this->post(route('card.save'), $card)->assertSessionHas('message');
        $newCard = ['title' => 'new title'];
        $this->actingAs(User::factory()->create())->put(\route('card.update', [1, 1]), $newCard)->assertNotFound();
    }

    // @test
    public function testStatus302WhenDeleting()
    {
        $this->createUserWithBoard();
        $card = Card::factory()->raw(['title' => 'some text', 'board' => 1]);
        $this->post(route('card.save'), $card)->assertSessionHas('message');
        $this->delete(\route('card.update', [1, 1]))->assertStatus(302);
    }

    // @test
    public function testDeletingCardRemovesItFromDB()
    {
        $this->createUserWithBoard();
        $card = Card::factory()->raw(['title' => 'some text', 'board' => 1]);
        $this->post(route('card.save'), $card)->assertSessionHas('message');
        $this->assertDatabaseCount('cards', 1);
        $this->delete(\route('card.update', [1, 1]));
        $this->assertDatabaseCount('cards', 0);
    }

    // @test
    public function testOnlyBoardOwnerCanDeleteCard()
    {
        $this->createUserWithBoard();
        $card = Card::factory()->raw(['title' => 'some text', 'board' => 1]);
        $this->post(route('card.save'), $card)->assertSessionHas('message');
        $this->assertDatabaseCount('cards', 1);
        $this->actingAs(User::factory()->create())->delete(\route('card.update', [1, 1]))->assertNotFound();
    }

    private function createUserWithBoard()
    {
        $user = User::factory()->create();
        $board = Board::factory()->raw();
        $this->actingAs($user);
        $this->post(\route('board.save'), $board);
    }
}
