<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemCreateRequest;
use App\Http\Requests\ItemUpdateRequest;

class ItemController extends Controller
{
    public function save(int $boardId, int $cardId, ItemCreateRequest $request)
    {
        auth()->user()->boards()->where('id', $boardId)->firstOrFail()
            ->cards()->where('id', $cardId)->firstOrFail()
            ->items()
            ->create($request->validated())
        ;

        return \back()->with('message', 'success');
    }

    public function update(int $boardId, int $cardId, int $itemId, ItemUpdateRequest $request)
    {
        auth()->user()->boards()->where('id', $boardId)->firstOrFail()
            ->cards()->where('id', $cardId)->firstOrFail()
            ->items()->where('id', $itemId)->firstOrFail()
            ->update($request->validated())
        ;

        return \back()->with('message', 'success');
    }

    public function delete(int $boardId, int $cardId, int $itemId)
    {
        auth()->user()->boards()->where('id', $boardId)->firstOrFail()
            ->cards()->where('id', $cardId)->firstOrFail()
            ->items()->where('id', $itemId)->firstOrFail()
            ->delete()
        ;

        return \back()->with('message', 'success');
    }
}
