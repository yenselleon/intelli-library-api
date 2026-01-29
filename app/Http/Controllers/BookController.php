<?php

namespace App\Http\Controllers;

use App\Book;
use App\Events\BookAuthorChanged;
use App\Events\BookCreated;
use App\Events\BookDeleted;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('author')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $books->items(),
            'meta' => [
                'current_page' => $books->currentPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
                'last_page' => $books->lastPage(),
            ],
            'message' => 'Books retrieved successfully'
        ], 200);
    }

    public function store(StoreBookRequest $request)
    {
        $book = Book::create($request->validated());

        event(new BookCreated($book->author_id));

        $book->load('author');

        return response()->json([
            'success' => true,
            'data' => $book,
            'message' => 'Book created successfully'
        ], 201);
    }

    public function show(Book $book)
    {
        $book->load('author');

        return response()->json([
            'success' => true,
            'data' => $book,
            'message' => 'Book retrieved successfully'
        ], 200);
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        $oldAuthorId = $book->author_id;

        $book->update($request->validated());

        if ($request->has('author_id') && $oldAuthorId != $book->author_id) {
            event(new BookAuthorChanged($oldAuthorId, $book->author_id));
        }

        $book->load('author');

        return response()->json([
            'success' => true,
            'data' => $book,
            'message' => 'Book updated successfully'
        ], 200);
    }

    public function destroy(Book $book)
    {
        $authorId = $book->author_id;

        $book->delete();

        event(new BookDeleted($authorId));

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Book deleted successfully'
        ], 200);
    }
}
