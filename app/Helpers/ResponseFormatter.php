<?php

namespace App\Helpers;

/**
 * Format response.
 */
class ResponseFormatter
{
    /**
     * API Response
     *
     * @var array
     */
    protected static $response = [
        'code' => 200,
        'message' => null,
        'data' => null,
    ];

    /**
     * Give success response.
     */
    public static function success($data = null, $token = null)
    {
        self::$response['message'] = "Success";
        self::$response['data'] = $data;

        $token == null ? null : self::$response['token'] = $token;

        return response()->json(self::$response, self::$response['code']);
    }

    /**
     * Give error response.
     */
    public static function error($message = null, $code = 400)
    {
        self::$response['code'] = $code;
        self::$response['message'] = $message;
        self::$response['data'] = json_decode("{}");

        return response()->json(self::$response, self::$response['code']);
    }
}
