@extends('layouts.app')
@section('content')
<div class="container p-4">
    <div class='text-center' id='errors'></div>

    <form method='POST' action="" id='editForm'>
        @CSRF

        <!-- post title -->
        <div class="form-group">
            <label for="title"><strong>Title</strong></label>
            <input type="text" class="form-control" id="title" name='title' value="{{$post->title}}">
            <p id='title-error'></p>
        </div>

        <!-- post content -->
        <div class="form-group">
            <label for="summernote"><strong>Summernote</strong></label>
            <textarea class="form-control" id="summernote" name='summernote'>
                {!! $post->post_body !!}
            </textarea>
        </div>

        <!-- post image -->
        <p><strong>Choose a post image</strong></p>
        <img src="{{ asset('storage/postImages') . '/' . $post->post_image }}" width='100px' height='100px' alt="">

        <div class="custom-file">
            <input type="file" name="postImage" id="postImage" class="custom-file-input">
            <label for="postImage" class="custom-file-label">Choose post Image</label>
        </div>
        <p class="mb-3" id="postImage-error"></p>

        <!-- post categories -->
        <div class="form-group">
        <label for="cats"><strong>Categories</strong></label>

        <select class="form-control" id="cats" name='categories[]' multiple>
            <!-- preselect post categories here -->
            @foreach($post->cats as $category)
            <option value="{{$category->id}}" selected>{{$category->cat_name}}</option>
            @endforeach
        </select>
        <p id='cats-error'></p>

        <!-- Add post id as a hidden field to update the post as i am using separeted javaScript file -->
        <input type="hidden" name="id" value="{{$post->id}}">

        <!-- Add new tag -->

        <div class="form-group">
        <label for="newTag"><strong>Add a new tag</strong></label>
        <div class="row">
        <div class="col-9"><input type="text" class="form-control" id="newTag" name='newTag' placeholder="e.g : php-storm or php7.x"></div>
        <div class="col-3"><button type="button" class="btn btn-success btn-small" id="addTag">Add</button></div>
        </div>
        <p id='new-tag-feedback' class="mt-2"></p>


        <!-- post tags -->
        <div class="form-group">
        <label for="tags"><strong>Choose from existing Tags</strong></label>

        <select class="form-control"  id="tags" name='tags[]' multiple>
             <!-- preselect post categories here -->
            @foreach($post->tags as $tag)
            <option value="{{$tag->id}}" selected>{{$tag->tag_name}}</option>
            @endforeach
        </select>

        <div class="form-check mb-4 mt-4">

            @if($post->is_featured == 1)
            <input class="form-check-input" type="checkbox" id="is_featured" name='is_featured' checked>
            @elseif($post->is_featured != 1)
            <input class="form-check-input" type="checkbox" id="is_featured" name='is_featured'>
            @endif
            <label class="form-check-label" for="is_featured"><strong>Make it featured</strong></label>
        </div>
        <button type="submit" class="btn btn-primary" name='update' value='Update' id='update'>Update</button>
    </form>
</div>
@endsection


@section('style')
<style>
.select2-results__message
{
    color: red;
}
.select2-selection--multiple:after{
 content:"";
 position:absolute;
 right:10px;
 top:15px;
 width:0;
 height:0;
 border-left: 5px solid transparent;
 border-right: 5px solid transparent;
 border-top: 5px solid #888;
 cursor: pointer; 
}
</style>
@endsection


@section('script')
<script src="{{ asset('js/editUpdate.js') }}"></script>
@endsection('script')

