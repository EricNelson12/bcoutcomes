@extends('default')
@section('body')



<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <h2>Reset Password</h2>
       	{{ Form::open(array('route' => array('password.update', $token))) }}
 		@foreach ($errors->all() as $message)
        <li>{{$message}}</li>
        @endforeach
        <div class="form-group">
            {{Form::label('email','Email')}}
            {{Form::text('email', null,array('class' => 'form-control'))}}
        </div>
        <div class="form-group">
            {{Form::label('password','Password')}}
            {{Form::password('password',array('class' => 'form-control'))}}
        </div>
        <div class="form-group">
            {{Form::label('password_confirmation','Password Confirmation')}}
            {{Form::password('password_confirmation',array('class' => 'form-control'))}}
        </div>
        {{ Form::hidden('token', $token) }}
       
        {{Form::submit('Reset Password', array('class' => 'btn btn-primary'))}}
        {{ Form::close() }}
    </div>
</div>
@stop