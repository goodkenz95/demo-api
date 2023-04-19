<?php namespace App\Laravel\Requests\Api;

use Session,Auth;
use App\Laravel\Requests\ApiRequestManager;

class ArticleRequest extends ApiRequestManager{
	public function rules(){
		$rules = [ 
			'name' => "required|name_format|min:2",
			'description' => "nullable|min:2",
			
            'image' => "required|mimes:jpeg,jpg,png|max:5000",
		];

		if($this->request->has('article_id') && $this->request->get('article_id') > 0){
			$rules['image'] = "nullable|mimes:jpeg,jpg,png|max:5000";
		}

		return $rules;
	}

	public function messages(){
		return [
			'required'	=> "Field is required.",
            'name_format' => "Only specific special character are allowed and number are not allowed.",
            'max' => "Maximum file size is 5MB.",
		];
	}
}