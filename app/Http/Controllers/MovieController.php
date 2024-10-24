<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MovieController extends Controller
{

    public function index() {}

    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|unique:movies',
                'description' => 'required|string',
                'duration' => 'required|numeric',
                'country' => 'required|string',
            ],
            [
                'name.required' => 'Название фильма не заполнено',
                'name.unique' => 'Такой фильм уже существует',
                'duration.numeric' => 'Продолжительность фильма должна быть числом',
                'duration.required' => 'Продолжительность фильма не заполнена',
                'description.string' => 'Описание фильма должено содержать текст',
                'description.required' => 'Описание фильма не заполнено',
                'country.string' => 'Страна должена быть текстом',
                'country.required' => 'Страна не заполнена',
            ]
        );
        $movie = new Movie;
        $movie->name = $request->name;
        $movie->description = $request->description;
        $movie->duration = $request->duration;
        $movie->country = $request->country;
        $movie->save();

        return redirect()->route('admin');
    }

    public function destroy(Request $request)
    {
        $movie = Movie::find($request['id']);
        $movie->delete();

        return redirect()->route('admin');
    }
}
