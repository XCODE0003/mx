<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::query()
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return Inertia::render('Reviews', [
            'reviews' => $reviews,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:3000'],
        ]);

        Review::create($validated);

        return back()->with('success', 'Спасибо за отзыв!');
    }
}


