<?php namespace App\Laravel\Requests\Api;

use Session,Auth;
use App\Laravel\Requests\ApiRequestManager;

class RegisterRequest extends ApiRequestManager{
	public function rules(){
		$rules = [ 
			'firstname' => "required|name_format|min:2",
			'lastname' => "required|name_format|min:2",
			'middlename' => "nullable|name_format|min:2",

			'username' => "required|username_format|unique_username:0,user", 
			'email' => "required|email:rfc,strict,dns,filter|unique_email:0,user", 

			'password' => "required|password_format",
		];

		return $rules;
	}

	public function messages(){
		return [
			'confirmed' => "Password mismatch.",
			'required'	=> "Field is required.",
			'email'		=> "Invalid email address format.",
			'unique_email' => "Email address is already taken. Please try again.",
			'unique_username' => "Username is already taken. Please try again.",
			'password_format' => "Password must be atleast 8 characters long, should contain atleast 1 uppercase, 1 lowercase, 1 numeric and 1 special character.",
            'name_format' => "Only specific special character are allowed and number are not allowed.",
			'username.username_format'   => "Display name is invalid. Please try another.",
		];
	}
}