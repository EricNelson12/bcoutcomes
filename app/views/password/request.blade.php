@extends('default')
@section('body')
@if (Session::has('message'))
  {{ trans(Session::get('message')) }}
@endif
<p>Email sent with password reset instructions.</p>