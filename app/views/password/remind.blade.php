@extends('default')
@section('body')

<div class="row">
	<div class="col-md-4 col-md-offset-4">
	    <h2>Reset Password</h2>
	   	{{ Form::open(array('route' => 'password.request')) }}
	    
	    <div class="form-group">
	        {{Form::label('email','Email')}}
	        {{Form::text('email', null,array('class' => 'form-control'))}}
	    </div>
	 
	    {{Form::submit('Submit', array('class' => 'btn btn-primary'))}}
	    {{ Form::close() }}
	</div>
</div>
@stop
