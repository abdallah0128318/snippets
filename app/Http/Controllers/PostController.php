<?php

namespace App\Http\Controllers;

use App\Models\Cat;
use App\Models\Post;
use App\Models\User;
use App\Models\Tag;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function publish()
    {
        $categories = DB::table('cats')->select('id', 'cat_name')->get();
        return view('publish', ['cats' => $categories]);
    }

    public function getAllTags()
    {
        $tags = Tag::select('id', 'tag_name')->get();
        return response()->json(['tags' => $tags]);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        return redirect('/home')->with('msg', 'Post deleted successfully');
    }


    public function getAllCategories()
    {
        $categories = Cat::select('id', 'cat_name')->get();
        return response()->json(['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:100|min:5',
            'summernote' => 'required',
            'postImage' => 'required|image|mimes:jpeg,png,jpg,svg|max:3500',
            'categories' => "required|array|min:1|max:3",
            'tags' => "required|array|min:3|max:10",
        ], ['postImage.max' => 'Image size have not to be more than 3.5MBs']);

        // pick the post from the request after validation
        $title = $request->input('title');
        $cats = $request->input('categories');
        $tags = $request->input('tags');
        $summernote = $request->input('summernote');
        $is_featured = $request->input('is_featured') == 'on' ? 1 : 0;

        // handle the post image path to store it in the posts table 
        $extension = $request->file('postImage')->getClientOriginalExtension();
        $newImageName = time() . '.' . $extension;
        $request->file('postImage')->storeAs('public/postImages', $newImageName);
        // convert the source of the image nested to the post and decode it using base64_decode() 
        // as summernote decode it using base64 algorithm
        $dom = new DomDocument();
        $dom->loadHTML($summernote, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');
        foreach ($images as $key => $img) {
            $data = $img->getAttribute('src');
            list($type, $data) = explode(';', $data);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);
            list(, $type) = explode(':', $type);
            list(, $type) = explode('/', $type); 
            $image_path = "public/contentImages/" . time() . $key . '.' . $type;
            Storage::disk('local')->put($image_path, $data);
            $img->removeAttribute('src');
            $img->setAttribute('src', $image_path);

        }
        $summernote = $dom->saveHTML();
        // create a post instance
        $post = new Post([
            'title' => $title,
            'post_body' => $summernote,
            'is_featured' => $is_featured,
            'post_image' => $newImageName
        ]);

        $user = User::find(Auth::user()->id);
        $user->posts()->save($post);
        // store post related tags and categories
        $post->tags()->syncWithoutDetaching($tags);
        $post->cats()->syncWithoutDetaching($cats);
    }

    public function storeNewTag(Request $request)
    {
        $request->validate([
            'tag' => 'regex:/^[a-z.0-9-]{1,20}$/|unique:tags,tag_name'
        ], [
            'tag.unique' => 'This tag is already existing in the tags select box', 
             'tag.regex' => 'Please, enter a valid tag<br>
             Tag should only contain lowercase letters, digits and dot character<br>
             Enter at least 1 character and at most 15 characters'
            ]);

        $latestRecord = Tag::create(['tag_name' => $request->input('tag')]);
        return response()->json(['id' => $latestRecord->id, 'tag_name' => $latestRecord->tag_name]);

    }

    public function showPost($slug)
    {
        $post = Post::select('title', 'post_body', 'created_at')->where('slug', $slug)->get();
        return view('show', ['post' => $post]);
    }

    
}
