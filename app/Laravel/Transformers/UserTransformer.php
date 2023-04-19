<?php 

namespace App\Laravel\Transformers;

use Str,Carbon,Helper;

use App\Laravel\Models\User;

use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract{

	protected $availableIncludes = [];


	public function transform(User $user) {
	    return [
	     	'user_id' => $user->id ?: 0,

	     	'firstname' => $user->firstname ?: "",
	     	'lastname' => $user->lastname ?: "",
	     	'middlename' => $user->middlename ?: "",

	     	'name' => $user->name ?: "",

	     	'email' => $user->email ?:"",
	     	'username' => $user->username ?:"",

	     	'date_created' => [
	     		'date_db' => $user->created_at ? $user->created_at->format("Y-m-d") : '',
	     		'month_year' => $user->created_at ? $user->created_at->format("F Y") : '',
	     		'time_passed' => $user->created_at ? Helper::time_passed($user->created_at) : '',
	     		'timestamp' => $user->created_at ? $user->created_at : '',
	     	],

 			'avatar' => [
				'path' => $user->directory ?: "",
				'filename' => $user->filename ?: "",
				'path' => $user->path ?: "",
				'directory' => $user->directory ?: "",
				'full_path' => strlen($user->filename) > 0 ? "{$user->directory}/resized/{$user->filename}" : "",
				'thumb_path' => strlen($user->filename) > 0 ? "{$user->directory}/thumbnails/{$user->filename}" : "",
			],
	    ];
	}
}