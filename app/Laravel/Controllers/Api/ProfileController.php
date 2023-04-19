<?php 

namespace App\Laravel\Controllers\Api;


/* Request validator
 */
use App\Laravel\Requests\PageRequest;
use App\Laravel\Requests\Api\{ProfileRequest, PasswordRequest, ProfileImageRequest, PhoneNumberRequest, OtpVerificationRequest};


/* Models
 */
use App\Laravel\Models\{AppUser};


/* Data Transformer
 */
use App\Laravel\Transformers\{TransformerManager, UserTransformer};

/* App classes
 */
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon,DB,Str,URL,Helper,ImageUploader,Log,PhoneNumber;

class ProfileController extends Controller{
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

	public function show(PageRequest $request,$format = NULL){
		$user = $request->user($this->guard);
		$this->response['status'] = TRUE;
		$this->response['status_code'] = "PROFILE_INFO";
		$this->response['msg'] = "Profile information.";
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

	public function update_profile(ProfileRequest $request,$format = NULL){
		DB::beginTransaction();
		try{
			$user = $request->user($this->guard);
			$user->setConnection(env('WRITER_DB_CONNECTION'));

			$user->firstname = Str::upper($request->get('firstname'));
			$user->middlename = Str::upper($request->get('middlename'));
			$user->lastname = Str::upper($request->get('lastname'));

			$user->save();

			DB::commit();

			$this->response['status'] = TRUE;
			$this->response['status_code'] = "PROFILE_UPDATED";
			$this->response['msg'] = "Profile updated  successfully.";
			$this->response['data'] = $this->transformer->transform($user,new UserTransformer,'item');
			$this->response_code = 200;


		}catch(\Exception $e){
			DB::rollback();

			$this->response['status'] = FALSE;
			$this->response['status_code'] = "SERVER_ERROR";
			$this->response['msg'] = "Server Error: Code #{$e->getLine()}";
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

	public function update_password(PasswordRequest $request,$format = NULL){
		DB::beginTransaction();
		try{
			$user = $request->user($this->guard);
			$user->setConnection(env('WRITER_DB_CONNECTION'));

			$user->password = bcrypt($request->get('password'));
			$user->save();

			DB::commit();

			$this->response['status'] = TRUE;
			$this->response['status_code'] = "PASSWORD_UPDATED";
			$this->response['msg'] = "New password has been set.";

			$this->response['data'] = $this->transformer->transform($user,new UserTransformer,'item');

			$this->response_code = 200;


		}catch(\Exception $e){
			DB::rollback();
			$this->response['status'] = FALSE;
			$this->response['status_code'] = "SERVER_ERROR";
			$this->response['msg'] = "Server Error: Code #{$e->getLine()}";
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

	public function update_phone_number(PhoneNumberRequest $request, $format = NULL)
	{
		try {
			$user = $request->user($this->guard);

			$session_key = 'api-' . trim($request->header('device-id'));

			$contact_number = PhoneNumber::make($request->input('phone_number'),"PH");
			$mobile_number = $contact_number->formatE164();

			Cache::put("{$session_key}.phone_number", $mobile_number);
			Cache::put("{$session_key}.otp", rand(1000, 9999));

			$otp_code = Cache::get("{$session_key}.otp");

			$message = "Your ZIACARE OTP verification code is {$otp_code}";
	        $response = Helper::send_sms($mobile_number, $message);

	        if($response == true){
                $this->response['status'] = TRUE;
				$this->response['status_code'] = "SUCCESS";
				$this->response['msg'] = "OTP was sent to your contact number. Please verify your account to continue on updating your phone number.";
				$this->response_code = 200;
				goto callback;
            } else {
            	Cache::forget("{$session_key}.otp");
            	Cache::forget("{$session_key}.phone_number");

            	$this->response['status'] = FALSE;
				$this->response['status_code'] = "FAILED";
				$this->response['msg'] = "Something went wrong on sending the OTP verification code. Please contact the administrator.";
				$this->response_code = 200;
				goto callback;
            }
		} catch (\Exception $e) {
			Log::info("ERROR: ", array($e));
			$this->response['status'] = FALSE;
			$this->response['status_code'] = "SERVER_ERROR";
			$this->response['msg'] = "Server Error: Code #{$e->getLine()}";
			$this->response_code = 500;
		}

		callback:
		switch (Str::lower($format)) {
			case 'json':
				return response()->json($this->response, $this->response_code);
				break;
			case 'xml':
				return response()->xml($this->response, $this->response_code);
				break;
		}
	}

	public function otp(OtpVerificationRequest $request, $format = NULL){
		$user = $request->user($this->guard);

		$session_key = 'api-' . trim($request->header('device-id'));
		Log::info("SESSIONKEY - {$session_key}");

		$otp_code = Cache::get("{$session_key}.otp");

		Log::info("OTP CODE: {$otp_code}");
		Log::info("INPUT OTP CODE:", array($request->input('otp')));

		$contact_number = PhoneNumber::make($request->get('phone_number'),"PH");
		$mobile_number = $contact_number->formatE164();

		if($request->input('otp') != $otp_code || $mobile_number != Cache::get("{$session_key}.phone_number")){
			$this->response['status'] = FALSE;
			$this->response['status_code'] = "INVALID_OTP_CODE";
			$this->response['msg'] = "Invalid OTP verification code.";
			$this->response_code = 412;
			goto callback;
		} 

		$app_user = AppUser::where('phone_number', Cache::get("{$session_key}.phone_number"))
									->first();

		if($app_user){
			$this->response['status'] = FALSE;
			$this->response['status_code'] = "PHONE_NUMBER_EXISTS";
			$this->response['msg'] = "Phone number already exists.";
			$this->response_code = 412;
			goto callback;
		}

		DB::beginTransaction();
		try {

			$user->setConnection(env('WRITER_DB_CONNECTION'));
			$user->phone_number = Cache::get("{$session_key}.phone_number");
			$user->save();

			DB::commit();

			Cache::forget("{$session_key}.otp");
            Cache::forget("{$session_key}.phone_number");

			$this->response['status'] = TRUE;
			$this->response['status_code'] = "PHONE_NUMBER_UPDATED";
			$this->response['msg'] = "Phone number was successfully updated.";
			$this->response_code = 200;
		} catch (\Exception $e) {
			DB::rollback();

			$this->response['status'] = FALSE;
			$this->response['status_code'] = "SERVER_ERROR";
			$this->response['msg'] = "Server Error: Code #{$e->getLine()}";
			$this->response_code = 500;
		}

		callback:
		switch (Str::lower($format)) {
			case 'json':
				return response()->json($this->response, $this->response_code);
				break;
			case 'xml':
				return response()->xml($this->response, $this->response_code);
				break;
		}
	}
}