<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Helper class to standardize API responses.
 * Mapper Class
 */
class ApiResponse
{

    /**
     * Returns a standardized JSON response with the provided data and message.
     *
     * @param mixed $data The data to include in the response.
     * @param string $message The message to include in the response.
     * @return JsonResponse The JSON response.
     */
    public static function data(mixed $data, string $message = "Data Retrieved Successfully"): JsonResponse
    {
        return JsonResponse::fromJsonString(json_encode([
            "status" => true,
            "message" => $message,
            "data" => $data
        ]));
    }

    /**
     * Returns a standardized JSON response with the provided data and message.
     * map the validation errors to [parameter , message]
     *
     * @param mixed $data The data to include in the response.
     * @param string $message The message to include in the response.
     * @return JsonResponse The JSON response.
     */
    public static function error(mixed $data, string $message = "Sorry Something went wrong"): JsonResponse
    {

        return JsonResponse::fromJsonString(json_encode([
            "status" => false,
            "message" => $message,
            "errors" => $data
        ]));
    }

}