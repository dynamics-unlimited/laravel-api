<?php
    namespace Kairnial\LaravelApi\Traits;

    use Kairnial\LaravelApi\Models\Enums\ApiResponseStatus;
    use Illuminate\Http\Resources\Json\JsonResource;
    use Symfony\Component\HttpFoundation\Response;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Support\Arr;

    trait ApiResponse
    {
        /**
         * Generates a success response from a payload and code
         * @param string $status : success or error
         * @param string $message : feedback message
         * @param mixed $payload : the response payload
         * @param ?array $errors : [optional] errors
         * @param int $code : the HTTP response code
         * @return JsonResponse
         */
        protected function apiResponse(string $status, string $message, mixed $payload, mixed $errors = null, int $code = Response::HTTP_OK): JsonResponse
        {
            $options = ($payload instanceof JsonResource)
                     ? $payload->jsonOptions() : 0;

            return response()->json([
                'status' => $status,
                'message' => $message,
                'data' => $payload,
                'errors' => $errors,
            ], $code, [], $options);
        }

        /**
         * Generates a success response from a payload, message and code
         * @param mixed $payload : the response payload
         * @param int $code : [optional] the HTTP response code
         * @return JsonResponse|Response
         */
        public function successResponse(mixed $payload, int $code = Response::HTTP_OK): JsonResponse|Response
        {
            if ($code === Response::HTTP_NO_CONTENT)
            {
                return new Response('', $code);
            }

            if ($payload instanceof JsonResponse)
            {
                $payload = json_encode($payload);
            }

            return $this->apiResponse(ApiResponseStatus::SUCCESS->value, Arr::get(Response::$statusTexts, $code, ''), $payload, null, $code);
        }

        /**
         * Generates an error response from error(s), message and code
         * @param mixed $errors : errors
         * @param int $code : [optional] the HTTP response code
         * @return JsonResponse
         */
        public function errorResponse(mixed $errors, int $code = Response::HTTP_BAD_REQUEST): JsonResponse
        {
            return $this->apiResponse(ApiResponseStatus::ERROR->value, Arr::get(Response::$statusTexts, $code, ''), null, $errors, $code);
        }
    }
