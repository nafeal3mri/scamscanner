<?php

namespace App\Http\Requests;

use App\Models\DomainCategor;
use Illuminate\Foundation\Http\FormRequest;

class DomainListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $domainid = \Request::segments()[1];
        return [
            // 'name' => 'required|min:5|max:255'
            'domain_url' => 'required',
            'main_domain' => 'required|unique:domain_lists'.$domainid,
            'category' => 'required',
            // 'report_token' => 'required'
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        //
        return [
            // 'type' => ,s
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
