<?php

declare(strict_types=1);

namespace App\Http\Requests\Traits;

use Illuminate\Contracts\Validation\Validator;

trait OrderTrait
{
    final public const array ORDER_PARAMS = ['order', 'direction'];

    public function getOrder(): ?array
    {
        $validationData = $this->validationData();

        if (!$validationData['order']) {
            return null;
        }

        return [
            [
                $validationData['order'], $validationData['direction'],
            ]
        ];
    }

    public function filterParams(): array
    {
        return $this->notOrderParams();
    }

    public function notOrderParams(): array
    {
        $result = [];

        foreach ($this->validationData() as $key => $value) {
            if (!in_array($key, static::ORDER_PARAMS, true)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    protected function notOrderParamFailed(Validator $validator): bool
    {
        foreach ($validator->getMessageBag()->keys() as $key) {
            if (!in_array($key, static::ORDER_PARAMS, true)) {
                return true;
            }
        }

        return false;
    }
}
