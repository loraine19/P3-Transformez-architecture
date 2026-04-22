<?php
// FormRequest for password update
// auto-throws ValidationException (-> 422 JSON via global handler) if rules fail

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    /* always authorized - auth check handled by route middleware */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'password'         => ['required', 'string', Password::defaults(), 'confirmed'],
        ];
    }
}
