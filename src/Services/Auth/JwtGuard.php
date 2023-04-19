<?php
    namespace Kairnial\LaravelApi\Services\Auth;

    use Illuminate\Validation\UnauthorizedException;
    use Illuminate\Contracts\Auth\Authenticatable;
    use Kairnial\Common\Models\ExternalUser;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Contracts\Auth\Guard;
    use Illuminate\Auth\GuardHelpers;
    use InvalidArgumentException;
    use Illuminate\Http\Request;
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    use LogicException;
    use stdClass;
    use Closure;

    /**
     * Custom authentication guard used to verify a JWT token
     */
    class JwtGuard implements Guard
    {
        use GuardHelpers;

        const RS256 = 'RS256';
        /**
         * Keys used to validate the JWT signature
         * @var Key[]
         */
        protected array $publicKeys = [];

        /**
         * Constructor
         * @param string $jwt : a JWT token string representation
         * @param string[] $audiences : the expected audience of the token
         * @param array $keys : the available keys used to validate the signature of the token
         */
        public function __construct(protected string $jwt, protected array $audiences, array $keys)
        {
            $storage = Storage::disk('local');

            foreach ($keys as $keyID => list('path' => $keyPath, 'algorithm' => $algorithm))
            {
                if ($storage->exists($keyPath))
                {
                    $publicKey = openssl_pkey_get_public($storage->get($keyPath));
                    $this->publicKeys[$keyID] = new Key($publicKey, $algorithm ?? self::RS256);
                }
                else
                {
                    throw new LogicException("JWT key not found: `$keyPath`. Add it to the `storage\\app` directory.");
                }
            }
        }

        /**
         * Handle an incoming request.
         *
         * @param  Request  $request
         * @param  \Closure  $next
         * @return mixed
         */
        public function handle(Request $request, Closure $next): mixed
        {
            return $next($request);
        }

        /** @inheritdoc */
        public function user(): ?Authenticatable
        {
            if ($this->user instanceof ExternalUser === false)
            {
                $decodedToken = $this->validateToken();
                $this->user = ExternalUser::CreateFromJWT($decodedToken);
                $this->user->SetAccessToken($decodedToken);
            }

            return $this->user;
        }

        /**
         * Validates the JWT token and returns its decoded form
         * @return ?\stdClass the decoded token
         */
        protected function validateToken(): ?stdClass
        {
            $decodedToken = null;

            if (empty($this->jwt) === false)
            {
                // legacy tokens have a KID of 0 which is not compatible with checking against multiple keys
                $keys = (count($this->publicKeys) === 1) ? $this->publicKeys[0] : $this->publicKeys;
                $decodedToken = JWT::decode($this->jwt, $keys);

                if (empty($decodedToken) === false)
                {
                    $error = 'Invalid audience';

                    foreach ($this->audiences as $audience)
                    {
                        if (JWT::constantTimeEquals($decodedToken->aud, $audience))
                        {
                            $error = null;
                            break;
                        }
                    }
                }
                else
                {
                    $error = 'Invalid credentials';
                }
            }
            else
            {
                $error = 'No credentials provided';
            }

            if (empty($error) === false)
            {
                throw new UnauthorizedException($error);
            }

            return $decodedToken;
        }

        /** @inheritdoc */
        public function validate(array $credentials = []): bool
        {
            throw new InvalidArgumentException('Credentials are not supported');
        }
    }
