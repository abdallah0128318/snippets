@extends('layouts.main')
@section('content')
<p id="title">
<script>
    var data = '{{ $post[0]->title}}';
    var element = $('#title');
    element.text(data);
</script>
</p>
@endsection