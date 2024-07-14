<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookController extends Controller
{

    public function index()
    {
        return BookResource::collection(Book::paginate(10));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        try {
            $book = Book::create([
                'title' => $request->title,
                'slug' => Str::slug($request->title . '-' . Str::random(5)),
                'description' => $request->description,
            ]);

            return response()->json([
                'message' => 'Book created successfully',
                'book' => new BookResource($book),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create book',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function show(Book $book)
    {
        return new BookResource($book);
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $slug = $book->slug;
            if ($request->title !== $book->title) {
                $slug = Str::slug($request->title . '-' . Str::random(5));
            }

            $book->update([
                'title' => $request->input('title', $book->title),
                'slug' => $slug,
                'description' => $request->input('description', $book->description),
            ]);

            return response()->json([
                'message' => 'Book updated successfully',
                'book' => new BookResource($book),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update book',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function destroy(Book $book)
    {
        try {
            $book->delete();

            return response()->json([
                'message' => 'Book deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete book',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
