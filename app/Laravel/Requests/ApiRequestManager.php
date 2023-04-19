<?php

namespace App\Laravel\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

use Illuminate\Http\Exceptions\HttpResponseException;

class ApiRequestManager extends FormRequest
{
    public function input($key = null, $default = null)
    {
        $input = $this->getInputSource()->all();

        return data_get($input, $key, $default);
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Override Illuminate\Foundation\Http\FormRequest@response method
     *
     * @return Illuminate\Routing\Redirector
     */

    protected function failedValidation(Validator $validator)
    {
		$_response = [
			'msg' => "Incomplete or invalid input",
			'status' => FALSE,
			'status_code' => "INVALID_DATA",
            'has_requirements' => TRUE,
			'errors' => $validator->errors(),
		];

		throw new HttpResponseException(response()->json($_response, 422));
    }

}
