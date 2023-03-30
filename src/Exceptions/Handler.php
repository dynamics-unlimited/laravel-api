<?php
    namespace Kairnial\LaravelApi\Exceptions;

    use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
    use Kairnial\LaravelApi\Http\Resources\ExceptionResource;
    use Symfony\Component\HttpFoundation\Response;
    use Illuminate\Validation\ValidationException;
    use Kairnial\LaravelApi\Traits\ApiResponse;
    use Illuminate\Http\JsonResponse;
    use Throwable;

    class Handler extends ExceptionHandler
    {
        use ApiResponse;

        /** @inheritDoc */
        protected function convertValidationExceptionToResponse(ValidationException $e, $request): Response
        {
            if ($e->response)
            {
                return $e->response;
            }

            return $this->invalidJson($request, $e);
        }

        /** @inheritDoc */
        protected function invalidJson($request, ValidationException $exception) : JsonResponse
        {
            return $this->errorResponse(ExceptionResource::make($exception), Response::HTTP_NOT_FOUND);
        }

        /** @inheritdoc */
        public function render($request, Throwable $e): Response|JsonResponse
        {
            $code = ExceptionResource::httpCode($e);

            return $this->errorResponse(ExceptionResource::make($e), $code);
        }
    }
