<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class TaskStoreRequest extends FormRequest
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
        return Arr::only($this->post(), ['name', 'description', 'finished_at']);
    }

    public function rules(): array
    {
        return [
            'name' => 'bail|required|string|max:255',
            'description' => 'bail|nullable|string|max:65535',
            'finished_at' => 'bail|nullable|date_format:Y-m-d',
        ];
    }
}
