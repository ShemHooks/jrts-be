<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller as Controller;


class BaseController extends Controller
{
    public function sendResponse($result = null, $message)
    {
        $response = [
            'success' => true,
            'message' => $message
        ];

        if ($result !== null) {
            $response['data'] = $result;
        }

        return response()->json($response, 200);

    }

    public function sendError($message, $errorData = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => is_array($message) ? ($message['error'] ?? 'error') : $message,
        ];

        if (!empty($errorData)) {
            $response['data'] = $errorData;
        }

        return response()->json($response, $code);
    }
}