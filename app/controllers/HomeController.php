<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function index()
	{
		if(Auth::check()){
			$role = Auth::user()->role;
			return View::make('hello1')->with('role', $role);
		}
		else{
			return View::make('users.login');
		}
	}

	public function history()
	{
		if(Auth::check()){
			$id = Auth::user()->id;
			$test =Query::where('user_id', $id)->orderBy('date_of_query', 'desc')->get();
			//$test = DB::getQueryLog();
			return View::make('history.history')->with('test', $test);
		}
		else{
			return View::make('users.login');
		}

	}

	public function getQuery()
	{
		if(Auth::check())
		{

			$qid = Input::get("query_id");
			$post = array();
			$query = Query::find($qid);
			$c1 = $query->cohort1params;
			$c2 = $query->cohort2params;

			$c1posts = explode("&",$c1);
			foreach($c1posts as $c)
			{
				$c1pair = explode("=",$c);
				$key = $c1pair[0];
				$value = "";
				if(isset($c1pair[1]))
					$value = $c1pair[1];
				
					$post[$key] = $value;
			}

			$c2posts = explode("&",$c2);
			foreach($c2posts as $c)
			{
				$c2pair = explode("=",$c);
				$key = $c2pair[0];
				$value = "";
				if(isset($c2pair[1]))
					$value = $c2pair[1];
				
					$post[$key] = $value;
			}
			return View::make('hello1')->with('mypost',$post);
			
		}
		else
		{
			return View::make('hello1');
		}
		
	}

	
}
