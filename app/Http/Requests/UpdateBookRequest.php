<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $bookId = $this->route('book')->id ?? $this->route('book');

        return [
            'title' => 'sometimes|string|max:255|unique:books,title,' . $bookId,
            'description' => 'sometimes|string',
            'author_id' => 'sometimes|integer|exists:authors,id',
            'published_year' => 'sometimes|integer|min:1000|max:' . date('Y'),
        ];
    }
}
