<?php 

namespace App\Laravel\Services;

use Illuminate\Validation\Validator;
use App\Laravel\Models\{User};

use Illuminate\Http\Request;
use Auth,Hash,Str,Carbon,Helper,PhoneNumber;

class CustomValidator extends Validator {

    public function validateUniqueUsername($attribute,$value,$parameters){
        $id = (is_array($parameters) AND isset($parameters[0]) ) ? $parameters[0] : "0";
        $type = (is_array($parameters) AND isset($parameters[1]) ) ? $parameters[1] : "user";

        switch (Str::lower($type)) {
            case 'app_user':
                return  AppUser::where('username',Str::lower($value))
                                ->where('id','<>',$id)
                                ->count() ? FALSE : TRUE;
            break;
            
            default:
                return  User::where('username',Str::lower($value))
                                ->where('id','<>',$id)
                                ->count() ? FALSE : TRUE;
        }
    }

    public function validateUniqueEmail($attribute,$value,$parameters){
        $id = (is_array($parameters) AND isset($parameters[0]) ) ? $parameters[0] : "0";
        $type = (is_array($parameters) AND isset($parameters[1]) ) ? $parameters[1] : "user";

        switch (Str::lower($type)) {
            case 'app_user':
                return  AppUser::where('email',Str::lower($value))
                                ->where('id','<>',$id)
                                ->count() ? FALSE : TRUE;
            break;
            
            default:
                return  User::where('email',Str::lower($value))
                                ->where('id','<>',$id)
                                ->count() ? FALSE : TRUE;
        }
    }

    public function validateCurrentPassword($attribute, $value, $parameters){
        $account_id = (is_array($parameters) AND isset($parameters[0]) ) ? $parameters[0] : "";
        $account_type = (is_array($parameters) AND isset($parameters[1]) ) ? $parameters[1] : "user";

        switch (Str::lower($account_type)) {
            case 'app_user':
                $user_id = $parameters[0];
                $user = AppUser::find($user_id);
                return Hash::check($value,$user->password);            
            break;
            
            default:
                $user_id = $parameters[0];
                $user = User::find($user_id);
                return Hash::check($value,$user->password);
        }

        return FALSE;
    }

    public function validateOldPassword($attribute, $value, $parameters){
        if($parameters){
            $user_id = $parameters[0];
            $user = User::find($user_id);
            return Hash::check($value,$user->password);
        }

        return FALSE;
    }

    public function validatePasswordFormat($attribute,$value,$parameters){
        return preg_match(("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-.]).{8,}$/"), $value);
    }

    public function validateUsernameFormat($attribute,$value,$parameters){
        return preg_match(("/^(?=.*)[a-zA-Z\d][a-z\d._+]{6,20}$/"), $value);
    }

    public function validateValidEmail($attribute,$value,$parameters){
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public function validateAlphaSpaces($attribute, $value, $parameters){
        return preg_match('/^[\pL\s]+$/u', $value);
    }

    public function validateNameFormat($attribute,$value,$parameters){
        return preg_match(("/^[a-zA-z._\-~ ']*$/"), $value);
    }
} 