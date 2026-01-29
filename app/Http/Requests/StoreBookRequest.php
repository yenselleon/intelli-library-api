<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255|unique:books,title',
            'description' => 'nullable|string',
            'author_id' => 'required|integer|exists:authors,id',
            'published_year' => 'required|integer|min:1000|max:' . date('Y'),
        ];
    }
}
