<?php 

namespace App\Laravel\Controllers\Portal;

/*
 * Request Validator
 */
use App\Laravel\Requests\PageRequest;

/* App Classes
 */
use Str;

class AuthenticationController extends Controller{
    protected $data;
    
    public function __construct(){
        parent::__construct();
		array_merge($this->data?:[], parent::get_data());
        $this->data['page_title'] .= " - Login";
    }

    public function login(PageRequest $request){
		return view('portal.auth.login',$this->data);
	}
}