<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\Card;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ItemTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    // @test
    public function testStatus302OnAttachingNewItem()
    {
        $this->withoutExceptionHandling();
        $this->createUserWithBoardAndAttachCard();
        $item = Item::factory()->raw();
        $response = $this->post(\route('item.save', [Board::latest()->first()->id, Card::latest()->first()->id]), $item);
        $response->assertStatus(302);
    }

    // @test
    public function testItemTitleIsRequired()
    {
        $this->createUserWithBoardAndAttachCard();
        $item = Item::factory()->raw(['content' => '']);
        $response = $this->post(\route('item.save', [Board::first()->id, Card::first()->id]), $item);
        $response->assertSessionHasErrors('content');
    }

    // @test
    public function testItemIsSavedAfterCreation()
    {
        $this->createUserWithBoardAndAttachCard();
        $item = Item::factory()->raw();
        $this->post(\route('item.save', [Board::first()->id, Card::first()->id]), $item);
        $this->assertDatabaseHas('items', $item);
    }

    // @test
    public function testOnlyCardOwnerCanAttachItem()
    {
        $this->createUserWithBoardAndAttachCard();
        $item = Item::factory()->raw();
        $response = $this->actingAs(User::factory()->create())->post(\route('item.save', [Board::first()->id, Card::first()->id]), $item);
        $response->assertNotFound();
    }

    // @test
    public function testStatus302WhenUpdating()
    {
        $this->createUserWithBoardAndAttachCard();
        $item = Item::factory()->raw();
        $this->post(\route('item.save', [Board::first()->id, Card::first()->id]), $item);
        $newItem = ['content' => $this->faker->sentence()];
        $response = $this->put(\route('item.update', [Board::first()->id, Card::first()->id, Item::first()->id]), $newItem);
        $response->assertStatus(302);
    }

    // @test
    public function testUpdateItemInDB()
    {
        $this->createUserWithBoardAndAttachCard();
        $item = Item::factory()->raw();
        $this->post(\route('item.save', [Board::first()->id, Card::first()->id]), $item);
        $newItem = ['content' => $this->faker->sentence(), 'done' => true];
        $this->put(\route('item.update', [Board::first()->id, Card::first()->id, Item::first()->id]), $newItem);
        $this->assertDatabaseHas('items', $newItem);
    }

    // @test
    public function testStatus302WhenDeleting()
    {
        $this->createUserWithBoardAndAttachCard();
        $item = Item::factory()->raw();
        $this->post(\route('item.save', [Board::first()->id, Card::first()->id]), $item);
        $response = $this->delete(\route('item.update', [Board::first()->id, Card::first()->id, Item::first()->id]));
        $response->assertStatus(302);
    }

    // @test
    public function testDeletingCardRemovesItFromDB()
    {
        $this->createUserWithBoardAndAttachCard();
        $item = Item::factory()->raw();
        $this->post(\route('item.save', [Board::first()->id, Card::first()->id]), $item);
        $this->assertDatabaseCount('items', 1);
        $this->delete(\route('item.update', [Board::first()->id, Card::first()->id, Item::first()->id]));
        $this->assertDatabaseCount('items', 0);
    }

    public function createUserWithBoardAndAttachCard()
    {
        $this->actingAs(User::factory()->create());
        $board = Board::factory()->raw();
        $this->post(\route('board.save'), $board);
        $card = Card::factory()->raw();
        $this->post(\route('card.save', Board::latest()->first()->id), $card);
    }
}
