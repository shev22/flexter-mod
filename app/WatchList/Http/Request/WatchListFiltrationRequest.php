<?php

namespace App\WatchList\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class WatchListFiltrationRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer'],
        ];
    }
}
