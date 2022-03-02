@extends('layouts.app')

@section('content')
<div class="container p-4">
    <div class='text-center' id='errors'></div>
    <form method='POST' action="" id='postForm'>
        @CSRF
        <!-- post title -->
        <div class="form-group">
            <label for="title"><strong>Title</strong></label>
            <input type="text" class="form-control" id="title" name='title'>
            <p id='title-error'></p>
        </div>
        <!-- post content -->
        <div class="form-group">
            <label for="summernote"><strong>Summernote</strong></label>
            <textarea class="form-control" id="summernote" rows='15' name='summernote'></textarea>
            <p id='editor-error'></p>
        </div>

        <!-- post image -->
        <p><strong>Choose a post image</strong></p>
        <div class="custom-file">
            <input type="file" name="postImage" id="postImage" class="custom-file-input">
            <label for="postImage" class="custom-file-label">Choose post Image</label>
        </div>
        <p class="mb-3" id="postImage-error"></p>

        <!-- post categories -->
        <div class="form-group">
        <label for="cats"><strong>Categories</strong></label>
        <select class="form-control" data-dropup-auto="false" id="cats" name='categories[]' multiple='multiple'></select>
        <p id='cats-error'></p>
        </div>

        <!-- Add new tag -->

        <div class="form-group">
        <label for="newTag"><strong>Add a new tag</strong></label>
        <div class="row">
        <div class="col-9"><input type="text" class="form-control" id="newTag" name='newTag' placeholder="e.g : php-storm or php7.x"></div>
        <div class="col-3"><button type="button" class="btn btn-success btn-small" id="addTag">Add</button></div>
        </div>
        <p id='new-tag-error' class="mt-2"></p>
        <p class="alert-container text-center px-5"></p>
        </div>


        <!-- post tags -->
        <div class="form-group">
        <label for="tags"><strong>Choose from existing Tags</strong></label>
        <select class="form-control" data-dropup-auto="false" id="tags" name='tags[]' multiple='multiple'></select>
        <p id='tags-error'></p>
        </div>

        <div class="form-check mb-4 mt-4">
            <input class="form-check-input" type="checkbox" id="is_featured" name='is_featured'>
            <label class="form-check-label" for="is_featured"><strong>Make it featured</strong></label>
        </div>
        <button type="submit" class="btn btn-primary" name='publish' value='Publish' id='submit'>Publish</button>
    </form>
</div>
@endsection

@section('script')
<script src="{{asset('js/publish.js')}}"></script>
@endsection


















