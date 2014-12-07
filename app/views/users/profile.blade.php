@extends('default')
@section('body')
<div class="container">
    <div>
        @if(Auth::check())
            <h2>Welcome to your profile page, <b>{{Auth::user()->username}}</b></h2>
        @endif
    </div>
    <a href="password/reset">Reset Password</a>
</div>