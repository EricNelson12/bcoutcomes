@extends('default')
@section('body')
<div class="container">
    <div>
        @if(Auth::check())
            <h2>Registered email: <b>{{Auth::user()->email}}</b></h2>
        @endif
    </div>
    <a href="password/reset">Reset Password</a>

   
</div>