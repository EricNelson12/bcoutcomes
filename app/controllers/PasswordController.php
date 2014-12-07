<?php

class PasswordController extends BaseController {
 
  public function remind()
  {
    return View::make('password.remind');
  }
  public function request()
	{
	  switch (Password::remind(Input::only('email')))
	    {
	      case Password::INVALID_USER:
	        return Redirect::back()->with('message', 'Invalid email.');

	      case Password::REMINDER_SENT:
	        return Redirect::back()->with('message','Email sent.');
	    }

	}

	  public function reset($token)
	{
	  return View::make('password.reset')->with('token', $token);
	}
	public function update()
	{
		$data = Input::only(['email','password','password_confirmation']);
		$token = Input::get('token');
		$validator = Validator::make(
            $data,
            [
                
                'email' => 'required|email|min:5',
                'password' => 'required|min:5|confirmed',
                'password_confirmation'=> 'required|min:5',
            ]
        );
        if($validator->fails()){
            return Redirect::back()->withErrors($validator)->withInput();
        }
        else{
			$credentials = array('email' => Input::get('email'), 'password' => Input::get('password'), 'password_confirmation' => Input::get('password_confirmation'),'token' => Input::get('token'));

			
			$response = Password::reset($credentials, function($user, $password)
		    {
		      $user->password = Hash::make($password);

		      $user->save();
		    });

		    switch ($response)
		    {
		      case Password::INVALID_PASSWORD:
		      	return Redirect::back()->with('message','Invalid password.');
		      case Password::INVALID_TOKEN:
		      	return Redirect::back()->with('message','Invalid token.');
		      case Password::INVALID_USER:
		      	return Redirect::back()->with('message','Invalid email.');

		      case Password::PASSWORD_RESET:
		        return Redirect::to('')->with('flash', 'Your password has been reset');
		    }


		}
	}
}