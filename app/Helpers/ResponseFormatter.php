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
    public static function success($data = null, $message = null, $token = null)
    {
        $data == null ? self::$response['data'] = json_decode("{}") : self::$response['data'] = $data;
        $message == null ? self::$response['message'] = "Success" : self::$response['message'] = $message;
        $token == null ? null : self::$response['token'] = $token;

        return response()->json(self::$response, self::$response['code']);
    }

    /**
     * Give error response.
     */
    public static function error($data = null, $message = null, $code = 400)
    {
        self::$response['code'] = $code;
        self::$response['message'] = $message;
        $data == null ? self::$response['data'] = json_decode("{}") : self::$response['data'] = $data;

        return response()->json(self::$response, self::$response['code']);
    }
}
