<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class AssignModuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // will delegate that to the middleware for now
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'contact_email' => 'required|email',
        ];
    }

    public function messages() 
    {
        return [
            'contact_email.required' => 'Input data not present.',
            'contact_email.email' => 'Email not valid.',
        ];
    }

    /**
     * Custom validation output
     * 
     * @param  Validator $validator 
     * @throws HttpResponseException
     */
    public function failedValidation(Validator $validator) { 
         self::jsonError($validator->errors()->first());
    }

    public static function jsonError(string $message) 
    {
        throw new HttpResponseException(response()->json([
            'message' => $message,
            'success' => false,
        ], 422));
    }
}
