<?php

namespace App\Movie\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class MoviesFilterRequest extends FormRequest
{
    public function rules()
    {
        return [
            'recent'     => ['nullable', 'boolean'],
            'popular'     => ['nullable', 'boolean'],
            'imdb'       => ['nullable', 'array'],
            'language'   => ['nullable', 'array'],
            'genre'      => ['nullable', 'array'],
            'year'       => ['nullable', 'array'],
            'page'       => ['nullable', 'integer', 'min:1'],
            'per_page'   => ['nullable', 'numeric', 'min:10', 'max:100'],
            'search'     => ['nullable', 'string', 'max:255'],
        ];
    }
}
