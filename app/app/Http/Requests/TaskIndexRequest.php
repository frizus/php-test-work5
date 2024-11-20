<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\Requests\Traits\OrderTrait;
use App\Repositories\AbstractRepository;
use App\Repositories\TaskRepository;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class TaskIndexRequest extends FormRequest
{
    use OrderTrait;

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
            ...array_fill_keys(static::ORDER_PARAMS, null),
            ...Arr::only(
                $this->query(),
                array_merge(TaskRepository::SEARCH_FIELDS, static::ORDER_PARAMS)
            )
        ];
    }

    public function rules(): array
    {
        return [
            'status' => [
                'bail',
                'nullable',
                'string',
                'in:1,0',
            ],
            'order' => [
                'bail',
                'nullable',
                'string',
                Rule::in(TaskRepository::ORDER_FIELDS)
            ],
            'direction' =>  [
                'bail',
                'nullable',
                'string',
                Rule::in(AbstractRepository::ORDER_DIRECTIONS)
            ],

        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        if ($this->notOrderParamFailed($validator)) {
            parent::failedValidation($validator);
            return;
        }

        $this->query->set('order', null);
        $this->query->set('direction', null);
    }

    protected function passedValidation(): void
    {
        if (!$this->query('direction')) {
            $this->query->set(
                'direction',
                AbstractRepository::DEFAULT_ORDER_DIRECTION
            );
        }
    }
}
