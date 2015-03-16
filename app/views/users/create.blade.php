@extends('default')
@section('body')

<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <h2>Register</h2>
        {{ Form::open(array('route' => array('user.store'), 'method' => 'post')) }}
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
         <div class="form-group">
            {{Form::label('user_type','User Type')}}
           {{Form::select('user_type', array('0' => '--Please Select--', '1' => 'Radiation Oncologist', '2' => 'Medical Oncologist', '3' => 'Surgical Oncologist', '4' => 'Administrator'))}}
        </div>
       
        {{Form::submit('Register', array('class' => 'btn btn-primary'))}}
        {{ Form::close() }}
    </div>
    <a href="password/reset" style="color:black;">Forgot Password?</a>
</div>

@stop