<?php

namespace App\Actor\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class ActorsFilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page'       => ['nullable', 'integer', 'min:1'],
            'per_page'   => ['nullable', 'numeric', 'min:10', 'max:100'],
            'search'     => ['nullable', 'string', 'max:255'],
        ];
    }

}
