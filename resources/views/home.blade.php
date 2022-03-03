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
                        <a href="/post/{{$featuredPost->slug}}" class="show-more float-left">Show more</a>
                        <form action="/post/{{$featuredPost->id}}" method='POST'>
                            @CSRF 
                            @method('DELETE')
                            <button class="float-right pl-2 ml-2 btn btn-danger btn-sm px-3">Delete</button>
                        </form>
                        <a href="/edit/{{$featuredPost->id}}" class="pl-2 edit btn edit btn-sm px-3 float-right">Edit</a>
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
                        <a href="/post/{{$post->slug}}" class="show-more float-left">Show more</a>
                        <form action="/post/{{$post->id}}" method='POST'>
                            @CSRF 
                            @method('DELETE')
                            <button class="float-right pl-2 ml-2 btn btn-danger btn-sm px-3">Delete</button>
                        </form>
                        
                        <a href="/edit/{{$post->id}}" class="pl-2 btn btn-sm edit float-right px-3">Edit</a>

                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div> 
@endsection


@section('style')
<style>
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
    .edit
    {
        background-color: #27285C;
        color: white;
    }
    .edit:hover 
    {
        background-color: #27445C;
        color: white;
    }
    .card{
        width: 100%;
    }

</style>
@endsection('style')

@section('script')
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endsection

