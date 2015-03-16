@extends('default')
@section('body')



<div class="row" style="padding-bottom:200px;background-image: url(earth1.jpg);background-repeat: no-repeat; background-position: center; ">
    <div class="col-md-4 col-md-offset-4" >
        <h2>Login</h2>
        {{ Form::open(array('url' => 'login', 'method' => 'post')) }}
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
            {{Form::label('remember','Remember Me?')}}
            {{Form::checkbox('remember','false')}}

             <a href="password/reset" style="color:black;">Forgot Password?</a>
        </div>
      
        {{Form::submit('Login', array('class' => 'btn btn-primary'))}}
        {{ Form::close() }}

    </div>

</div>

@stop
