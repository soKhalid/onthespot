<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Brand $brand)
    {
        $reviews = $brand->reviews()
            ->with('user:id,name')
            ->approved()
            ->latest()
            ->paginate(20);

        return response()->json($reviews);
    }

    public function store(Request $request, Brand $brand)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check if user already reviewed this brand
        if ($brand->reviews()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['error' => 'You have already reviewed this brand'], 422);
        }

        $review = $brand->reviews()->create([
            'user_id' => $request->user()->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json($review, 201);
    }

    public function update(Request $request, Review $review)
    {
        if ($review->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update($request->all());

        return response()->json($review);
    }

    public function destroy(Review $review, Request $request)
    {
        if ($review->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $review->delete();

        return response()->json(['message' => 'Review deleted successfully']);
    }
}
