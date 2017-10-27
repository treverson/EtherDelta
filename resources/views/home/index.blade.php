@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ route('feemng') }}" class="btn btn-success">Edit Fee</a>
    <a href="{{ route('ordermarket') }}" class="btn btn-success">Order Exchange</a>
</div>
<div id="app">
    <p> This is the Event Listener page and when the event is fired off, this page will listen to the status update, and fire off the related listener command.</p>
    
</div>
@endsection