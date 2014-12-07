<?php

class UserController extends BaseController {

    /**
     * Show the profile for the given user.
     */
    public function login()
    {
       return View::make('users.login');
    }
    public function handleLogin(){
    	$data = Input::only(['username', 'password']);
    	$validator = Validator::make(
            $data,
            [
                'username' => 'required|min:3',
                'password' => 'required',
            ]
        );

        if($validator->fails()){
            return Redirect::route('login')->withErrors($validator)->withInput();
        }

        if(Auth::attempt(['username' => $data['username'], 'password' => $data['password']])){
            return Redirect::to('/');
        }
        $errors = 'Login Failed';
        return Redirect::route('login')->withErrors($errors)->withInput();
    }

    public function profile(){
    	return View::make('users.profile');

    }

    public function logout(){
    	if(Auth::check()){
		  Auth::logout();
		}
		 return Redirect::route('login');
    }

    public function create(){
    	return View::make('users.create');
    }

    public function store(){
    	$data = Input::only(['username','email','password','password_confirmation','user_type']);

    	$validator = Validator::make(
            $data,
            [
                'username' => 'required|min:3|unique:users',
                'email' => 'required|email|min:5',
                'password' => 'required|min:5|confirmed',
                'password_confirmation'=> 'required|min:5',
                'user_type'=>'not_in:0',
            ]
        );

		if($validator->fails()){
            return Redirect::route('user.create')->withErrors($validator)->withInput();
        }
        else{

	        $user = new User;
		    $user->username = Input::get('username');
		    $user->email = Input::get('email');
		    $user->password = Hash::make(Input::get('password'));
		    $user->role = Input::get('user_type');
		    $user->save();
	 
	    	return Redirect::to('login')->with('message', 'Thanks for registering!');
	    }
    }


}