<?php

namespace App\Http\Controllers;

use App\Http\Requests\BoardCreationRequest;
use App\Http\Requests\BoardUpdateRequest;
use App\Models\Board;
use Illuminate\Support\Str;

class BoardController extends Controller
{
    public function save(BoardCreationRequest $request)
    {
        //create slug, make sure slug is unique, merge with original data, and save
        $slug = Str::slug($request->title);
        if (0 !== auth()->user()->boards()->where('slug', $slug)->count()) {
            return \back()->withErrors(['title' => 'Title can not be duplicated']);
        }
        $data = \array_merge($request->validated(), ['slug' => $slug], ['owner' => \auth()->id()]);
        Board::create($data);

        return \back(201)->with('message', 'success');
    }

    public function show(string $boardSlug)
    {
        $board = auth()->user()->boards()->where('slug', $boardSlug)->firstOrFail();

        return \view('board.home')->with('board', $board);
    }

    public function update(string $slug, BoardUpdateRequest $request)
    {
        $board = auth()->user()->boards()->where('slug', $slug)->firstOrFail();
        $newSlug = Str::slug($request->title);
        if ($slug !== $newSlug) {
            if (auth()->user()->boards()->where('slug', $newSlug)->count() > 0) {
                return \back()->withErrors(['message' => 'cannot duplicate title!']);
            }
            $slug = $newSlug;
        }
        $board->update(['slug' => $slug, 'title' => $request->title, 'color' => $request->color ?? $board->color]);

        return \back()->with('message', 'success');
    }

    public function delete(string $slug)
    {
        auth()->user()->boards()->where('slug', $slug)->firstOrFail()->delete();

        return \back()->with('message', 'success');
    }
}
