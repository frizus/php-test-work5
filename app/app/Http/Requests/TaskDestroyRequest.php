<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskDestroyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function validationData(): array
    {
        return [
            'id' => $this->route('task'),
        ];
    }

    public function rules(): array
    {
        return [
            'id' => [
                'bail',
                'required',
                'integer',
                Rule::exists('tasks')
            ]
        ];
    }
}
