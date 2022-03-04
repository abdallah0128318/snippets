@extends('layouts.main')
@section('content')
<div class="container-fluid mt-3">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (session('msg'))
                <div class="alert alert-success" role="alert">
                    <h6 class="text-center">{{ session('msg') }}</h6>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="container-fluid mt-4">
    <div class="row" id='posts-container'>
        <!-- display featured posts -->
        @foreach($featuredPosts as $featuredPost)
        <div class="col-xl-4 my-xl-2 col-md-6 my-md-2 my-2">
                <div class="card shadow" style="width:100%">
                    <img class="card-img-top" src="{{ asset('storage/postImages/') . '/' . $featuredPost->post_image}}" alt="post image">
                    <div class="card-body">
                        <p><i><span id="span-{{$featuredPost->id}}">
                             <!-- Here is compared updated_at with created_at if they are the same then post posted else it was updated -->
                            @if($featuredPost->created_at == $featuredPost->updated_at)
                                <script>
                                    // using momentjs to convert UTC timestamp to user local area
                                    var updated_at = '{{$featuredPost->updated_at}}'; 
                                    var serverTimezone = 'utc'; 
                                    var momentJsTimeObj = moment.tz(updated_at, serverTimezone); 
                                    var localTimeZone = jstz.determine(); 
                                    var localTime = momentJsTimeObj.clone().tz(localTimeZone.name()).format('lll'); 
                                    $('#span-{{$featuredPost->id}}').text('Posted: ' + localTime);
                                </script>
                            @elseif($featuredPost->created_at != $featuredPost->updated_at)
                            <script>
                                // using momentjs to convert UTC timestamp to user local area
                                var updated_at = '{{$featuredPost->updated_at}}'; 
                                var serverTimezone = 'utc'; 
                                var momentJsTimeObj = moment.tz(updated_at, serverTimezone); 
                                var localTimeZone = jstz.determine(); 
                                var localTime = momentJsTimeObj.clone().tz(localTimeZone.name()).format('lll'); 
                                $('#span-{{$featuredPost->id}}').text('Updated: ' + localTime);
                            </script>
                            @endif
                        </span><img  src="{{ asset('images/featured.svg')}}" width="30px" height="30px"></i></p>
                        <h4 class="card-title" ><a data-toggle="tooltip" title="{{$featuredPost->title}}"  href="/post/{{$featuredPost->slug}}"><i>{{$featuredPost->title}}</i></a></h4>
                        <a href="/post/{{$featuredPost->slug}}" class="show-more float-left"><i>View Post</i></a>

                        <!-- option button -->
                    
                        <i class="fa fa-ellipsis-v float-right d-block options-button"></i>

                        <!-- options menu -->

                        <ul  class="m-0 p-0 options">
                            <li><a href="#" class="options-item"><i>share</i></a></li>
                            <li><a href="/editPost/{{$featuredPost->id}}" class="options-item"><i>edit</i></a></li>
                            <li>
                            <form action="/post/{{$featuredPost->id}}" method='POST'>
                                @CSRF 
                                @method('DELETE')
                                <button class="options-item delete"><i>delete</i></button>
                            </form>
                            </li>
                        </ul>

                    </div>
                </div>
            </div>
        @endforeach

        <!-- Display posts -->
        @foreach($posts as $post)
        <div class="col-xl-4 my-xl-2 col-md-6 my-md-2 my-2">
                <div class="card shadow-lg">
                    <img class="card-img-top"  src="{{ asset('storage/postImages/') . '/' . $post->post_image}}" alt="post image">
                    <div class="card-body">
                        <p><i><span id="span-{{$post->id}}">
                            <!-- Here is compared updated_at with created_at if they are the same then post posted else it was updated -->
                        @if($post->created_at == $post->updated_at)
                                <script>
                                    // using momentjs to convert UTC timestamp to user local area
                                    var updated_at = '{{$post->updated_at}}'; 
                                    var serverTimezone = 'utc'; 
                                    var momentJsTimeObj = moment.tz(updated_at, serverTimezone); 
                                    var localTimeZone = jstz.determine(); 
                                    var localTime = momentJsTimeObj.clone().tz(localTimeZone.name()).format('lll'); 
                                    $('#span-{{$post->id}}').text('Posted: ' + localTime);
                                </script>
                            @elseif($post->created_at != $post->updated_at)
                            <script>
                                // using momentjs to convert UTC timestamp to user local area
                                var updated_at = '{{$post->updated_at}}'; 
                                var serverTimezone = 'utc'; 
                                var momentJsTimeObj = moment.tz(updated_at, serverTimezone); 
                                var localTimeZone = jstz.determine(); 
                                var localTime = momentJsTimeObj.clone().tz(localTimeZone.name()).format('lll'); 
                                $('#span-{{$post->id}}').text('Updated: ' + localTime);
                            </script>
                            @endif
                        </span></i></p>
                        <h4 class="card-title"><a data-toggle="tooltip" title="{{$post->title}}" href="/post/{{$post->slug}}"><i>{{$post->title}}</i></a></h4>
                        <a href="/post/{{$post->slug}}" class="show-more float-left"><i>View Post</i></a>

                       <!-- option button -->
                    
                       <i class="fa fa-ellipsis-v float-right d-block options-button"></i>

                        <!-- options menu -->

                        <ul class="m-0 p-0 options">
                            <li><a href="#" class="options-item"><i>share</i></a></li>
                            <li><a href="/editPost/{{$post->id}}" class="options-item"><i>edit</i></a></li>
                            <li>
                            <form action="/post/{{$post->id}}" method='POST'>
                                @CSRF 
                                @method('DELETE')
                                <button class="options-item delete"><i>delete</i></button>
                            </form>
                            </li>
                        </ul>

                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div> 
@endsection


@section('style')
<style>
    *{
        box-sizing: border-box;
    }
    .card-title{
        height: 60px;
        overflow: hidden;
    } 
    .card-title a 
    {
        color:#27285C;
    }
    .show-more
    {
        color: #27445C;
    }
    .card-img-top{
        height: 300px;
    }
    .card{
        width: 100%;
    }
    .fa-ellipsis-v
    {
        font-size:25px;
        cursor: pointer;
    }
    .options
    {
        display: none;
        position: absolute;
        background-color: white;
        width: 170px;
        top: 67%;
        right: 7%;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        border-radius: 10px;
    }
    .options li 
    {
        list-style-type: none;
        text-align: center;
    }
    .options  .options-item
    {
        display: block;
        width: 100%;
        padding: 0.25rem 1.5rem;
        clear: both;
        font-weight: 400;
        color: #212529;
        text-align: inherit;
        white-space: nowrap;
        background-color: transparent;
        border: 0;
        outline: none;
        text-decoration: none;
    }
    .options .options-item:hover
    {
        background-color: #27445C;
        color: white;
        border-radius: 10px;
    }

</style>
@endsection('style')

@section('script')
<script src="{{asset('js/home.js')}}"></script>
@endsection

