<?php

namespace App\Http\Controllers;

use App\Models\SupportArticle;
use Illuminate\View\View;

class SupportArticleController extends Controller
{
    public function show(string $slug): View
    {
        $article = SupportArticle::published()->with('category')->where('slug', $slug)->firstOrFail();
        $related = SupportArticle::published()->where('support_category_id', $article->support_category_id)->whereKeyNot($article->id)->orderBy('sort_order')->limit(4)->get();

        return view('support-article', compact('article', 'related'));
    }
}
