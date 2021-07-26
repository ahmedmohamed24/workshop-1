<?php

namespace App\Http\Controllers;

use App\Http\Requests\CardCreationRequest;
use App\Http\Requests\CardUpdateRequest;

class CardController extends Controller
{
    public function save(CardCreationRequest $request)
    {
        if (0 !== auth()->user()->boards()->where('id', $request->board)->firstOrFail()->cards()->where('title', $request->title)->count()) {
            return \back()->withErrors(['message' => 'Cards can not be duplicated per same board!']);
        }
        auth()->user()->boards()->where('id', $request->board)->first()->cards()->create($request->validated());

        return \back()->with('message', 'success');
    }

    public function show(int $boardId, int $cardId)
    {
    }

    public function update(int $boardId, int $cardId, CardUpdateRequest $request)
    {
        if (0 !== auth()->user()->boards()->where('id', $boardId)->firstOrFail()->cards()->where('title', $request->title)->count()) {
            return \back()->withErrors(['message' => 'Cards can not be duplicated per same board!']);
        }
        auth()->user()->boards()->where('id', $boardId)->firstOrFail()->cards()->where('id', $cardId)->update(['title' => $request->title]);

        return \back()->with('message', 'success');
    }

    public function delete(int $boardId, int $cardId)
    {
        auth()->user()->boards()->where('id', $boardId)->firstOrFail()->cards()->where('id', $cardId)->delete();

        return \back()->with('message', 'success');
    }
}
