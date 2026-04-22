<?php
// FormRequest for tag creation
// auto-throws ValidationException (-> 422 JSON via global handler) if rules fail

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
{
    /* always authorized - auth check handled by route middleware */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }
}
