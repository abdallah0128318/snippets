@extends('layouts.main')
@section('content')

<!-- bootstrap4 modal to be displayed with deletion message after deleting a post -->
<div class="container">
  <!-- The Modal -->
  <div class="modal fade" id="myModal">
    <div class="modal-dialog">
      <div class="modal-content d-flex justify-content-center align-items-center">
            <!-- Modal Header -->
            <div class="modal-header">
            <h4 class="modal-title"></h4>
            </div>
      </div>
    </div>
  </div>
</div>
<!-- bootstrap4 modal to be displayed with deletion message after deleting a post -->

<div class="container-fluid mt-3">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (session('msg'))
            <script>
                // display deletion message after deletion and redirecting to the home page
                var msg = "{{ session('msg') }}";
                $('.modal-title').html('<i>' + msg + '</i>');
                $('.modal').modal();
            </script>
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
                        <h4 class="card-title" ><a data-toggle="tooltip" title="{{$featuredPost->title}}"  href="{{ route('view.post', $featuredPost->slug) }}"><i>{{$featuredPost->title}}</i></a></h4>
                        <a href="{{ route('view.post', $featuredPost->slug) }}" class="show-more float-left"><i>View Post</i></a>

                        <!-- option button -->
                    
                        <i class="fa fa-ellipsis-v float-right d-block options-button"></i>

                        <!-- options menu -->

                        <ul  class="m-0 p-0 options">
                            <li><a href="#" class="options-item"><i>share</i></a></li>
                            <li><a href="{{ route('edit.post', $featuredPost->id) }}" class="options-item"><i>edit</i></a></li>
                            <li>
                            <form action="{{ route( 'delete.post' , $featuredPost->id) }}" method='POST'>
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
                        <h4 class="card-title"><a data-toggle="tooltip" title="{{$post->title}}" href="{{ route('view.post', $post->slug) }}"><i>{{$post->title}}</i></a></h4>
                        <a href="{{ route('view.post', $post->slug) }}" class="show-more float-left"><i>View Post</i></a>

                       <!-- option button -->
                    
                       <i class="fa fa-ellipsis-v float-right d-block options-button"></i>

                        <!-- options menu -->

                        <ul class="m-0 p-0 options">
                            <li><a href="#" class="options-item"><i>share</i></a></li>
                            <li><a href="{{route( 'edit.post' , $post->id)}}" class="options-item"><i>edit</i></a></li>
                            <li>
                            <form action="{{route( 'delete.post' , $post->id)}}" method='POST'>
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
<link rel="stylesheet" href="{{asset('css/home.css')}}">
@endsection('style')

@section('script')
<script src="{{asset('js/home.js')}}"></script>
@endsection

