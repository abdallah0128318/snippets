@extends('layouts.show')
@section('content')
<!-- Here i will customize a style for my post view -->
<div class="post-container">
    <div class="content">

        <!-- display post title -->
        <h1 id="post-title">
            {{$post[0]->title}}
            <!-- display star SVG image if the post is featured -->
            @if($post[0]->is_featured == 1)
            <img  src="{{ asset('images/featured.svg')}}" alt="start icon for featured posts">
            @endif
        </h1>

        <!-- display the timestamp converted from UTC to user`s local timezone -->
        <p id="timestamp">hello timestamps</p>
        @if($post[0]->created_at == $post[0]->updated_at)
            <script>
                // using momentjs to convert UTC timestamp to user local area
                var updated_at = '{{$post[0]->updated_at}}'; 
                var serverTimezone = 'utc'; 
                var momentJsTimeObj = moment.tz(updated_at, serverTimezone); 
                var localTimeZone = jstz.determine(); 
                var localTime = momentJsTimeObj.clone().tz(localTimeZone.name()).format('lll'); 
                $('#timestamp').html('<i>Posted: ' + localTime + '</i>');
            </script>
        @elseif($post[0]->created_at != $post[0]->updated_at)
            <script>
                // using momentjs to convert UTC timestamp to user local area
                var updated_at = '{{$post[0]->updated_at}}'; 
                var serverTimezone = 'utc'; 
                var momentJsTimeObj = moment.tz(updated_at, serverTimezone); 
                var localTimeZone = jstz.determine(); 
                var localTime = momentJsTimeObj.clone().tz(localTimeZone.name()).format('lll'); 
                $('#timestamp').html('<i>Updated: ' + localTime + '</i>');
            </script>
        @endif

        <!-- disply edit delet options to the user -->
        <ul id="options">
            <li title="share via facebook" data-toggle="tooltip"><a href="#"><ion-icon name="logo-facebook" id="facebook-icon"></ion-icon></a></li>
            <li title="share via twitter" data-toggle="tooltip"><a href="#"><ion-icon name="logo-twitter" id="twitter-logo"></ion-icon></a></li>
            <li title="share via linkedin" data-toggle="tooltip"><a href="#"><ion-icon name="logo-linkedin" id="linked-logo"></ion-icon></a></li>
            <li title="share via whatsapp" data-toggle="tooltip"><a href="#"><ion-icon name="logo-whatsapp" id="whatsapp-logo"></ion-icon></a></li>
            <li title="edit post" data-toggle="tooltip"><a href="{{ route('edit.post', $post[0]->id) }}"><ion-icon name="create" id="edit"></ion-icon></a></li>
            <li id="delete" title="delete post" data-toggle="tooltip">
                <form action="{{ route( 'delete.post' , $post[0]->id) }}" method='POST'>
                    @CSRF 
                    @method('DELETE')
                    <button><ion-icon name="trash" id="trash"></ion-icon><button>
                </form>
            </li>
        </ul>


        <!-- display post tags and make them clickable -->
        <div id="post-tags">
            @foreach($post[0]->tags as $tag)
                <a href="" class="shadow">{{$tag->tag_name}}</a>
            @endforeach
        </div>

        <!-- display post Image  -->
        <div id="post-image-container">
            <img src="{{ asset('storage/postImages/') . '/' . $post[0]->post_image}}" alt="post_image">
        </div>

        <!-- display post body -->
        <div id="post-body">
            {!! $post[0]->post_body !!}
        </div>

        <div id="post-categories">
            <span>This post categorized as: </span>
            <div>
                @foreach($post[0]->cats as $category)
                <a href="#">{{$category->cat_name}}</a>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection



@section('style')
<link rel="stylesheet" href="{{asset('css/show.css')}}">
@endsection

@section('script')
<script>
$(function(){
    // initialize tooltip
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endsection
