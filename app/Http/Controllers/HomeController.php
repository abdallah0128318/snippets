<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // fetch the latest 15 featured posts
        $featuredPosts = Post::select('id', 'title','post_body','slug', 'post_image', 'created_at', 'updated_at')
        ->latest()->take(15)->where('is_featured', 1)->get();

        // fetch the latest 12 posts that are not featured
        $posts = Post::select('id', 'title','post_body','slug', 'post_image', 'created_at', 'updated_at')
        ->latest()->take(12)->where('is_featured', 0)->get();

        return view('home', ['featuredPosts' => $featuredPosts, 'posts' => $posts]);
    }


}
