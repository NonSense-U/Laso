<?php

namespace App\Traits\V1;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public static function success($message = 'ok', $data = [], $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    public static function fail($message = 'failed', $errors = [], $statusCode = 500): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if (!empty($errors))
            $response['errors'] = $errors;

        return response()->json($response, $statusCode);
    }
}
