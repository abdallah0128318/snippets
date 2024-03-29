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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /* here i created two arraies one called beforeUpdate and another is afterUpdate 
    // beforeUpdate stores pathes for images that will be rendered to the user in summernote to be updated 
    // afterUpdate will store pathes of the posted images of summernote after updating then i will compare
    // the old pathes with new pathes if an old path doesn`t exist in the new pathes array i will remove its 
    // related image file from the server */

    private $beforeUpdate = [];
    private $afterUpdate = [];



    public function publish()
    {
        return view('publish');
    }

    public function autoCompleteCategories(Request $request)
    {
        if($request->ajax())
        {
            $more = true;
            $term = $request->term;
            $categories = Cat::select('id', 'cat_name as text')
            ->orderBy('cat_name', 'ASC')
            ->where('cat_name', 'LIKE', '%' . $term . '%')->paginate(categoriesNumberPerPage);
            if(empty($categories->nextPageUrl()))
            {
                $more = false;
            }
            $results = $categories->items();

            // set the response object or assocciative array as select2 plugin requires

            $response = [
                'results' => $results,
                'pagination' => [
                    'more' => $more
                ]
            ];

            return response()->json($response);
        }
    }

    public function autoCompleteTags(Request $request)
    {
        if($request->ajax())
        {
            $more = true;
            $term = $request->term;
            $tags = Tag::select('id', 'tag_name as text')
            ->orderBy('tag_name', 'ASC')
            ->where('tag_name', 'LIKE', '%' . $term . '%')->paginate(tagsNumberPerPage);
            if(empty($tags->nextPageUrl()))
            {
                $more = false;
            }
            $results = $tags->items();

            // set the response object or assocciative array as select2 plugin requires

            $response = [
                'results' => $results,
                'pagination' => [
                    'more' => $more
                ]
            ];

            return response()->json($response);
        }
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        // delete post_image file from the server after deleting the post
        $file = public_path('storage/postImages/' . $post->post_image);
        if(File::exists($file))
        {
            File::delete($file);
        }

        // delete summernote`s content image files from the server when deleting the post
        $dom = new DomDocument();
        @$dom->loadHTML($post->post_body, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');
        if($images->length > 0)
        {
            foreach ($images as $key => $img) {
                $src = $img->getAttribute('src');
                $file = public_path($src);
                if(File::exists($file))
                {
                    File::delete($file);
                }
            }
        }
        $post->delete();
        return redirect('/home')->with('msg', 'Post deleted successfully');
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:100|min:5',
            'summernote' => 'required',
            'postImage' => 'required|image|mimes:jpeg,png,jpg,svg',
            'categories' => "required|array|min:1|max:3",
            'categories.*' => "required|distinct|string",
            'tags' => "required|array|min:3|max:10",
            'tags.*' => "required|distinct|string",
        ], [
            'categories.required' => 'You have to select 1 category at least',
        ]);

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
        if($images->length > 0)
        {
            foreach ($images as $key => $img) {
                $data = $img->getAttribute('src');
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                list(, $type) = explode(':', $type);
                list(, $type) = explode('/', $type); 
                $image_name = "/storage/contentImages/" . time(). $key . '.' . $type;
                $path = public_path() . $image_name;
                if(!is_dir(public_path().'/storage/contentImages'))
                {
                    mkdir(public_path().'/storage/contentImages');
                }
                file_put_contents($path, $data);
                $img->removeAttribute('src');
                $img->setAttribute('src', $image_name);
    
            }
            $summernote = $dom->saveHTML();

        }
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
            'tag.unique' => 'This tag is already existing just select it!', 
             'tag.regex' => 'Tag has to be 1 to 20 lowercase letter'
            ]);
        $tag = new Tag(['tag_name' => $request->input('tag')]);
        $tag->save();

        return response()->json(['msg' => 'Tag added successfully search it in select box!']);

    }

    public function edit($id)
    {
        $post = Post::with('tags')->with('cats')->find($id);
        return view('edit', ['post' => $post]);
    }


    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|max:100|min:5',
            'summernote' => 'required',
            'postImage' => 'nullable|image|mimes:jpeg,png,jpg,svg',
            'categories' => "required|array|min:1|max:3",
            'categories.*' => "required|distinct|string",
            'tags' => "required|array|min:3|max:10",
            'tags.*' => "required|distinct|string",
        ], [
            'categories.required' => 'You have to select 1 category at least',
        ]);

        // pick the post from the request after validation
        $title = $request->input('title');
        $cats = $request->input('categories');
        $tags = $request->input('tags');
        $summernote = $request->input('summernote');
        $is_featured = $request->input('is_featured') == 'on' ? 1 : 0;

        // pick  the post to be updated 

        $post = Post::find($request->input('id'));

        // pick image files pathes to store them in $beforeUpdate array to compare with 
        // new images to delete the user-deleted images from the server
        $dom = new DomDocument();
        @$dom->loadHTML($post->post_body, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');
        if($images->length > 0)
        {
            foreach ($images as $key => $img) {
                $src = $img->getAttribute('src');

                /*************************************************************************************/
                /************************************************************************************/

                $this->beforeUpdate[] = $src;

                /**************************************************************************************/
                /**************************************************************************************/
            }
        }


        // convert the source of the image nested to the post and decode it using base64_decode() 
        // as summernote decode it using base64 algorithm
        $dom = new DomDocument();
        @$dom->loadHTML($summernote, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');
        if($images->length > 0)
        {
            foreach ($images as $key => $img) {

                $pattern = "/^\/storage\/contentImages\/.+$/";
                $data = $img->getAttribute('src');
                if(!preg_match($pattern, $data))
                {
                    list($type, $data) = explode(';', $data);
                    list(, $data) = explode(',', $data);
                    $data = base64_decode($data);
                    list(, $type) = explode(':', $type);
                    list(, $type) = explode('/', $type); 
                    $image_name = "/storage/contentImages/" . time(). $key . '.' . $type;
                    $path = public_path() . $image_name;
                    if(!is_dir(public_path().'/storage/contentImages'))
                    {
                        mkdir(public_path().'/storage/contentImages');
                    }
                    file_put_contents($path, $data);
                    $img->removeAttribute('src');
                    $img->setAttribute('src', $image_name);
                }
                else
                {
                    /*****************************************************************************************/
                    /*****************************************************************************************/

                    $this->afterUpdate[] = $data;

                    /*****************************************************************************************/
                    /*****************************************************************************************/
                }
            }
            $summernote = $dom->saveHTML();
        }
        $post->title = $title;
        $post->slug = null;
        $post->post_body = $summernote;
        $post->is_featured = $is_featured;
        // handle the post_image if exists image path to store it in the posts table
        if($request->hasFile('postImage'))
        {
            $file = public_path('storage/postImages/' . $post->post_image);
            if(File::exists($file))
            {
                File::delete($file);
            }
            $extension = $request->file('postImage')->getClientOriginalExtension();
            $newImageName = time() . '.' . $extension;
            $request->file('postImage')->storeAs('public/postImages', $newImageName);
            $post->post_image = $newImageName;
        }

        // delete image files from the server if the user deleted it using Backspace or deleet button
        foreach ($this->beforeUpdate as $path) {
            if(!in_array($path, $this->afterUpdate))
            {
                $file = public_path($path);
                if(File::exists($file))
                {
                    File::delete($file);
                }
            }
        }
        // Save the post 
        $post->save();
        // update post related tags and categories
        $post->tags()->sync($tags);
        $post->cats()->sync($cats);
    }

    public function deleteImageOnDelete(Request $request)
    {
        // delete image file from the server after removing it from the editor using bin icon
        $file = public_path($request->input('src'));
        if(File::exists($file))
        {
            File::delete($file);
        }
       return response()->json(['src' => $request->input('src')]);   
    }
    
    public function showPost($slug)
    {
        $post = Post::select('title', 'post_body', 'created_at', 'updated_at', 'id', 'post_image', 'is_featured')->where('slug', $slug)->with('tags')->with('cats')->get();
        return view('show', ['post' => $post]);
    }

    
}
