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
                'duration' => 'required|numeric|min:1',
                'country' => 'required|string',
                'poster' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
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
                'poster.required' => 'Постер не загружен',
                'poster.image' => 'Файл должен быть изображением',
                'poster.mimes' => 'Допустимые форматы: jpeg, png, jpg, gif',
                'poster.max' => 'Размер изображения не должен превышать 2MB',
            ]
        );
        $movie = new Movie;
        $movie->name = $request->name;
        $movie->description = $request->description;
        $movie->duration = $request->duration;
        $movie->country = $request->country;
        if ($request->hasFile('poster')) {
            $file = $request->file('poster');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('posters'), $filename);
            $movie->poster = 'posters/' . $filename;
        }

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
