<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
	protected $fillable = ['username','password','role','email'];

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

	public function isSysAdmin()
	{
		if($this->attributes['role']==99)
			return true;
		else
			return false;
	}

	public function isAdmin()
	{
		if($this->attributes['role']==4)
			return true;
		else
			return false;
	}


	public function isSurgical()
		{
			if($this->attributes['role']==3)
				return true;
			else
				return false;
		}


	public function isMedical()
		{
			if($this->attributes['role']==2)
				return true;
			else
				return false;
		}


	public function isRadiation()
		{
			if($this->attributes['role']==1)
				return true;
			else
				return false;
		}


}
