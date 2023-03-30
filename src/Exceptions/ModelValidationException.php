<?php
    namespace Kairnial\LaravelApi\Exceptions;

    use Illuminate\Validation\ValidationException;
    use Symfony\Component\HttpFoundation\Response;

    class ModelValidationException extends ValidationException
    {
        /**
         * The HTTP response code returned if the validation fails
         * @var int
         */
        protected int $statusCode = Response::HTTP_NOT_FOUND;

        public function __construct($validator, $response = null, $errorBag = 'default',
                                    int $statusCode = Response::HTTP_NOT_FOUND)
        {
            parent::__construct($validator, $response, $errorBag);
            $this->statusCode = $statusCode;
        }

        /**
         * Retrieves the status code of the exception
         * @return int
         */
        public function getStatusCode(): int
        {
            return $this->statusCode;
        }
    }
