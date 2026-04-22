<?php
// FormRequest for note creation
// auto-throws ValidationException (-> 422 JSON via global handler) if rules fail

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoteRequest extends FormRequest
{
    /* always authorized - ownership check is in NoteService::delete() */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'text'   => 'required|string',
            'tag_id' => 'required|integer|exists:tags,id',
        ];
    }
}
