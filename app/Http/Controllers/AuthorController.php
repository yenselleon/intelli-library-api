<?php

namespace App\Http\Controllers;

use App\Author;
use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use Illuminate\Http\JsonResponse;

class AuthorController extends Controller
{
    public function index()
    {
        $authors = Author::paginate(15);

        return response()->json([
            'success' => true,
            'data' => $authors->items(),
            'meta' => [
                'current_page' => $authors->currentPage(),
                'per_page' => $authors->perPage(),
                'total' => $authors->total(),
                'last_page' => $authors->lastPage(),
            ],
            'message' => 'Authors retrieved successfully'
        ], 200);
    }

    public function store(StoreAuthorRequest $request)
    {
        $author = Author::create($request->validated());

        return response()->json([
            'success' => true,
            'data' => $author,
            'message' => 'Author created successfully'
        ], 201);
    }

    public function show(Author $author)
    {
        return response()->json([
            'success' => true,
            'data' => $author,
            'message' => 'Author retrieved successfully'
        ], 200);
    }

    public function update(UpdateAuthorRequest $request, Author $author)
    {
        $author->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => $author,
            'message' => 'Author updated successfully'
        ], 200);
    }

    public function destroy(Author $author)
    {
        $author->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Author deleted successfully'
        ], 200);
    }
}
