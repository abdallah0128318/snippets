@extends('layouts.app')

@section('content')
<div class="container p-4 shadow rounded">
    <div class='text-center' id='errors'></div>
    <form method='POST' action="" id='postForm'>
        @CSRF
        <!-- post title -->
        <div class="form-group mb-3">
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
        <select class="form-control" data-dropup-auto="false" id="cats" name='categories[]' style="width: 100%"></select>
        <p id='cats-error'></p>
        </div>

        <!-- Add new tag -->

        <div class="form-group">
        <label for="newTag"><strong>Add a new tag</strong></label>
        <div class="row">
        <div class="col-9"><input type="text" class="form-control" id="newTag" name='newTag' placeholder="e.g : php-storm or php7.x"></div>
        <div class="col-3"><button type="button" class="btn btn-success btn-small" id="addTag">Add</button></div>
        </div>
        <p id='new-tag-feedback' class="mt-2"></p>
        </div>


        <!-- post tags -->
        <div class="form-group">
        <label for="tags"><strong>Choose from existing Tags</strong></label>
        <select class="form-control" data-dropup-auto="false" id="tags" name='tags[]' style="width: 100%"></select>
        <p id='tags-error'></p>
        </div>

        <div class="form-check mb-4 mt-4">
            <input class="form-check-input" type="checkbox" id="is_featured" name='is_featured'>
            <label class="form-check-label" for="is_featured"><strong>Make it featured</strong></label>
        </div>
        <button type="submit" class="btn btn-primary" name='publish' value='Publish' id='Publish'>Publish</button>
    </form>
</div>
@endsection



@section('style')
<style>
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
i.validation 
{
    color: red;
}
</style>
@endsection

@section('script')
<script src="{{asset('js/publish.js')}}"></script>
@endsection


















