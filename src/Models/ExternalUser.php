<?php
    namespace Kairnial\LaravelApi\Models;

    use Illuminate\Contracts\Auth\Authenticatable;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Http\Request;
    use stdClass;

    /**
     * User model for external users identified by their UUID
     */
    class ExternalUser implements Authenticatable
    {
        /**
         * Access token
         * @var ?\stdClass
         */
        protected ?stdClass $jwt = null;

        /**
         * Constructor
         * @param string $identifier : [property] the UUID of the user
         * @param array $scopes : [property] the scopes of the user's token
         * @param ?string $email : [property] the email of the user
         * @param ?string $name : [property] the name of the user
         */
        private final function __construct(private readonly string $identifier,
                                           private readonly array $scopes = [],
                                           private readonly ?string $email = null,
                                           private readonly ?string $name = null) {}

        /**
         * Creates a user from a model
         * @param Model $model
         * @param string $param
         * @return static
         */
        public static function CreateFromModel(Model $model, string $param = 'fk_user'): static
        {
            return new static($model->getAttribute($param));
        }

        /**
         * Creates a user from a request
         * @param Request $request : the HTTP request
         * @param string $param : [optional] the name of the parameter containing the user identifier
         * @return static
         */
        public static function CreateFromRequest(Request $request, string $param = 'user'): static
        {
            return new static($request->route($param));
        }

        /**
         * Creates a user from a decoded JWT token
         * @param \stdClass $token : the decoded token
         * @return static
         */
        public static function CreateFromJWT(stdClass $token): static
        {
            return new static($token->sub, $token->scopes, $token->email ?? '', $token->name ?? '');
        }

        /**
         * Sets the access token of the user
         * @param stdClass $jwt
         * @return $this
         */
        public function SetAccessToken(stdClass $jwt): ExternalUser
        {
            $this->jwt = $jwt;

            return $this;
        }

        /**
         * Retrieves the access token
         * @return stdClass
         */
        public function currentAccessToken(): stdClass
        {
            return $this->jwt;
        }

        /**
         * Checks if the user's access token has the specified scope
         * @param string $scope : the scope to check for
         * @return bool true if the user has the scope; false otherwise
         */
        public function tokenCan(string $scope): bool
        {
            return $this->jwt instanceof stdClass && in_array($scope, $this->jwt->scopes);
        }

        /**
         * Retrieves the user's email
         * @return ?string
         */
        public function getEmail(): ?string
        {
            return $this->email;
        }

        /**
         * Retrieves the user's name
         * @return ?string
         */
        public function getName(): ?string
        {
            return $this->name;
        }

        /**
         * * Retrieves the user's token scopes
         * @return string[]
         */
        public function getScopes(): array
        {
            return $this->scopes;
        }

        /** @inheritdoc */
        public function getAuthIdentifierName(): string
        {
            return $this->identifier;
        }

        /** @inheritdoc */
        public function getAuthIdentifier(): string
        {
            return $this->identifier;
        }

        /** @inheritdoc */
        public function getAuthPassword(): ?string
        {
            return null;
        }

        /** @inheritdoc */
        public function getRememberToken(): ?string
        {
            return null;
        }

        /** @inheritdoc */
        public function setRememberToken($value): void {}

        /** @inheritdoc */
        public function getRememberTokenName(): ?string
        {
            return null;
        }
    }
