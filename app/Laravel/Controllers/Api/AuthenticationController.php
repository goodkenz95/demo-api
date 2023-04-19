<?php 

namespace App\Laravel\Controllers\Api;


/* Request validator
 */
use App\Laravel\Requests\PageRequest;
use App\Laravel\Requests\Api\{RegisterRequest};

/* Models
 */
use App\Laravel\Models\{User};


/* Data Transformer
 */
use App\Laravel\Transformers\{TransformerManager, UserTransformer};

/* App classes
 */
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

use Carbon,DB,Str,Log,Helper;

class AuthenticationController extends Controller{
	protected $response = [];
	protected $response_code;
	protected $guard = 'api';


	public function __construct(){
		$this->response = array(
			"msg" => "Bad Request.",
			"status" => FALSE,
			'status_code' => "BAD_REQUEST"
			);
		$this->response_code = 400;
		$this->transformer = new TransformerManager;
	}

	public function check_login(PageRequest $request,$format = NULL){
		$user = auth($this->guard)->user();

		if(!$user){
			$this->response['status'] = FALSE;
			$this->response['status_code'] = "UNAUTHORIZED";
			$this->response['msg'] = "Invalid/Expired token. Do refresh token.";
			$this->response_code = 401;
			goto  callback;
		}

		$this->response['status'] = TRUE;
		$this->response['status_code'] = "LOGIN_SUCCESS";
		$this->response['msg'] = "Welcome {$user->name}!";
		$this->response['data'] = $this->transformer->transform($user,new UserTransformer,'item');

		$this->response_code = 200;
		callback:
		switch(Str::lower($format)){
		    case 'json' :
		        return response()->json($this->response, $this->response_code);
		    break;
		    case 'xml' :
		        return response()->xml($this->response, $this->response_code);
		    break;
		}	
	}

	public function logout(PageRequest $request,$format = NULL){
		$user = auth($this->guard)->user();

		if(!$user){
			$this->response['status'] = FALSE;
			$this->response['status_code'] = "UNAUTHORIZED";
			$this->response['msg'] = "Invalid/Expired token. Do refresh token.";
			$this->response_code = 401;
			goto  callback;
		}

		auth($this->guard)->logout(true);
		
		$this->response['status'] = TRUE;
		$this->response['status_code'] = "LOGOUT_SUCCESS";
		$this->response['msg'] = "Session closed.";
		$this->response_code = 200;

		callback:
		switch(Str::lower($format)){
		    case 'json' :
		        return response()->json($this->response, $this->response_code);
		    break;
		    case 'xml' :
		        return response()->xml($this->response, $this->response_code);
		    break;
		}	
	}

	public function authenticate(PageRequest $request,$format = NULL){
		$password  = $request->input('password');
		$email =  Str::lower($request->get('email'));

		$field = filter_var($email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

		if(!$token = auth($this->guard)->attempt([$field => $email,'password' => $password])){
			$this->response['status'] = FALSE;
			$this->response['status_code'] = "UNAUTHORIZED";
			$this->response['msg'] = "Invalid account credentials.";
			$this->response_code = 401;
			goto  callback;
		}

		$user =  auth($this->guard)->user();

		$this->response['status'] = TRUE;
		$this->response['status_code'] = "LOGIN_SUCCESS";
		$this->response['msg'] = "Welcome {$user->name}!";
		$this->response['token'] = $token;
		$this->response['token_type'] = "bearer";
		$this->response['data'] = $this->transformer->transform($user,new UserTransformer,'item');

		$this->response_code = 200;
		callback:
		switch(Str::lower($format)){
		    case 'json' :
		        return response()->json($this->response, $this->response_code);
		    break;
		    case 'xml' :
		        return response()->xml($this->response, $this->response_code);
		    break;
		}	
	}

	public function refresh_token(PageRequest $request,$format = NULL){
        $old_token = $request->bearerToken();

        $user =  auth('api')->user();
        $new_token = auth('api')->refresh();

        $this->response['status'] = TRUE;
		$this->response['status_code'] = "ACCESS_TOKEN_UPDATED";
		$this->response['msg'] = "New access token assigned.";
		$this->response['token'] = $new_token;
		$this->response['token_type'] = "Bearer";
		 $this->response['data'] = $this->transformer->transform($user,new UserTransformer,'item');
        $this->response_code = 200;

        callback:
        switch(Str::lower($format)){
            case 'json' :
                return response()->json($this->response, $this->response_code);
            break;
            case 'xml' :
                return response()->xml($this->response, $this->response_code);
            break;
        }    
    }

	public function store(RegisterRequest $request,$format = NULL){

		DB::beginTransaction();
		try{

			$user = new User;
			$user->setConnection(env('WRITER_DB_CONNECTION'));

			$user->email = strtolower($request->input('email'));
			$user->username = strtolower($request->input('username'));

			$user->type = "user";

			$user->firstname = Str::upper($request->input('firstname'));
			$user->lastname = Str::upper($request->input('lastname'));
			$user->middlename = Str::upper($request->input('middlename'));

			$user->password = bcrypt($request->input('password'));
			$user->save();

			DB::commit();

			$this->response['status'] = TRUE;
			$this->response['status_code'] = "REGISTERED";
			$this->response['msg'] = "Successfully registered.";
			$this->response_code = 201;
		}catch(\Exception $e){
			DB::rollback();

			Log::info("ERROR: ", array($e));

			$this->response['status'] = FALSE;
			$this->response['status_code'] = "SERVER_ERROR";
			$this->response['msg'] = "Server Error: Code #{$e->getMessage()}";
			$this->response_code = 500;
		}

		callback:
		switch(Str::lower($format)){
		    case 'json' :
		        return response()->json($this->response, $this->response_code);
		    break;
		    case 'xml' :
		        return response()->xml($this->response, $this->response_code);
		    break;
		}	
	}
}