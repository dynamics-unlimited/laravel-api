<?php
    namespace Kairnial\LaravelApi\Http\Resources;

    use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
    use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
    use Kairnial\LaravelApi\Exceptions\ModelValidationException;
    use Symfony\Component\HttpKernel\Exception\HttpException;
    use Illuminate\Database\Eloquent\ModelNotFoundException;
    use Laravel\Sanctum\Exceptions\MissingAbilityException;
    use Illuminate\Auth\Access\AuthorizationException;
    use Illuminate\Validation\UnauthorizedException;
    use Symfony\Component\HttpFoundation\Response;
    use Illuminate\Validation\ValidationException;
    use Firebase\JWT\SignatureInvalidException;
    use Firebase\JWT\BeforeValidException;
    use Firebase\JWT\ExpiredException;
    use InvalidArgumentException;
    use UnexpectedValueException;
    use Throwable;

    class ExceptionResource extends BaseResource
    {
        /** @inheritDoc */
        public function toArray($request): array
        {
            if ($this->resource instanceof ValidationException)
            {
                return $this->resource->errors();
            }
            // extract the exception message
            $exceptionMessage = $this->resource->getMessage();
            $statusCode = self::httpCode($this->resource);
            $error = empty($exceptionMessage) || (!env('APP_DEBUG') && array_key_exists($statusCode, Response::$statusTexts))
                   ? Response::$statusTexts[$statusCode] : $exceptionMessage;

            return [ 'exception' => [$error] ];
        }

        /**
         * Retrieves an HTTP response code based on the type of exception
         * @param \Throwable $e : the exception to match
         * @return int the response code
         */
        public static function httpCode(Throwable $e): int
        {
            return match(get_class($e)) {
                AuthorizationException::class, SignatureInvalidException::class,
                  UnexpectedValueException::class, BeforeValidException::class,
                  ExpiredException::class, MissingAbilityException::class       => Response::HTTP_FORBIDDEN,
                ModelNotFoundException::class, ValidationException::class,
                  NotFoundHttpException::class                                  => Response::HTTP_NOT_FOUND,
                ModelValidationException::class, HttpException::class           => $e->getStatusCode(),
                MethodNotAllowedHttpException::class                            => Response::HTTP_METHOD_NOT_ALLOWED,
                InvalidArgumentException::class                                 => Response::HTTP_BAD_REQUEST,
                UnauthorizedException::class                                    => Response::HTTP_UNAUTHORIZED,
                default                                                         => Response::HTTP_INTERNAL_SERVER_ERROR
            };
        }
    }
