<?php

namespace App\Http\Controllers;

use App\Models\News;
use Inertia\Inertia;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::published()
            ->paginate(9)
            ->withQueryString()
            ->through(fn ($item) => [
                'id'           => $item->id,
                'title'        => $item->title,
                'slug'         => $item->slug,
                'excerpt'      => $item->excerpt,
                'thumbnail'    => $item->thumbnail
                    ? asset('storage/' . $item->thumbnail)
                    : null,
                'published_at' => $item->published_at?->format('d.m.Y'),
            ]);

        return Inertia::render('News', [
            'news' => $news,
        ]);
    }

    public function show(string $slug)
    {
        $item = News::published()
            ->where('slug', $slug)
            ->firstOrFail();

        return Inertia::render('NewsDetail', [
            'article' => [
                'id'          => $item->id,
                'title'       => $item->title,
                'excerpt'     => $item->excerpt,
                'content'     => $item->content,
                'cover_image' => $item->cover_image
                    ? asset('storage/' . $item->cover_image)
                    : null,
                'published_at' => $item->published_at?->format('d.m.Y'),
            ],
        ]);
    }
}
