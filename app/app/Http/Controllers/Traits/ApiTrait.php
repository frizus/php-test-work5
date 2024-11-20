<?php

declare(strict_types=1);

namespace App\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;

trait ApiTrait
{
    public function success(int $status = 200): JsonResponse
    {
        return response()->json(['status' => 'success'], $status);
    }

    public function error(mixed $message = null, int $status = 500): JsonResponse
    {
        $data = [];

        if (is_scalar($message)) {
            $data['message'] = $message;
        } elseif (!is_null($message)) {
            $data = $message;
        } else {
            $data = ['message' => 'error'];
        }

        return response()->json($data, $status);
    }
}
