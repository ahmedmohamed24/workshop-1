<?php

namespace App\Http\Controllers;

use App\Http\Requests\BoardCreationRequest;
use App\Models\Board;
use Illuminate\Support\Str;

class BoardController extends Controller
{
    public function save(BoardCreationRequest $request)
    {
        //create slug, make sure slug is unique, merge with original data, and save
        $slug = Str::slug($request->title);
        if (0 !== Board::where('slug', $slug)->count()) {
            return \back()->withErrors(['title' => 'Title can not be duplicated']);
        }
        $data = \array_merge($request->validated(), ['slug' => $slug], ['owner_id' => \auth()->id()]);
        Board::create($data);

        return \back(201)->with('message', 'success');
    }

    public function show(string $boardSlug)
    {
        $board = Board::where('slug', $boardSlug)->firstOrFail();

        if (\auth()->user()->cannot('view', $board)) {
            \abort(403);
        }

        return \view('board.home')->with('board', $board);
    }
}
