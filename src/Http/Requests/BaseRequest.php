<?php
    namespace Kairnial\LaravelApi\Http\Requests;

    use Illuminate\Contracts\Validation\Validator as ValidatorContract;
    use Kairnial\LaravelApi\Exceptions\ModelValidationException;
    use Symfony\Component\HttpFoundation\Response;
    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Support\Facades\Gate;
    use Illuminate\Validation\Validator;

    class BaseRequest extends FormRequest
    {
        /**
         * Scopes required to fulfill the request
         * @var string[]
         */
        const REQUIRED_SCOPES = [];

        /**
         * The HTTP response code returned if the validation fails
         * @var int
         */
        protected int $responseCode = Response::HTTP_NOT_FOUND;

        /**
         * Performs extra model validation on the request
         * @param Validator $validator : the request validator
         * @return void
         */
        protected function modelValidation(Validator $validator): void {}

        /**
         * Determine if the user is authorized to make this request.
         *
         * @return bool
         */
        public function authorize(): bool
        {
            return Gate::allows('user-scopes', [ static::REQUIRED_SCOPES ]);
        }

        /**
         * Configure the validator instance.
         *
         * @param Validator $validator
         * @return void
         */
        public function withValidator(Validator $validator): void
        {
            $validator->after(function (Validator $validator) {
                if (empty($validator->failed()))
                {
                    $this->modelValidation($validator);
                }
            });
        }

        /**
         * Sets the error message if the model validation fails
         * @param Validator $validator : the validator
         * @param int $responseCode : the HTTP response code
         * @param string $message : the error message
         * @param string $key : the error key
         * @return void
         */
        protected function setModelError(Validator $validator, int $responseCode, string $message, string $key): void
        {
            $validator->errors()->add($key, $message);
            $this->responseCode = $responseCode;
        }

        /** @inheritdoc */
        public function failedValidation(ValidatorContract $validator)
        {
            throw new ModelValidationException($validator, null, $this->errorBag, $this->responseCode);
        }
    }
