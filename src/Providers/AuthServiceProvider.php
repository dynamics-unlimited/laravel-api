<?php
    namespace Kairnial\LaravelApi\Providers;

    use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
    use Illuminate\Validation\UnauthorizedException;
    use Kairnial\LaravelApi\Services\Auth\JwtGuard;
    use Kairnial\LaravelApi\Models\ExternalUser;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Gate;
    use Illuminate\Http\Request;
    use LogicException;

    /**
     * Authentication providers used to secure the API routes
     */
    class AuthServiceProvider extends ServiceProvider
    {
        /**
         * Register any application authentication / authorization services.
         * @return void
         */
        public function boot(): void
        {
            // publish the configuration for the service
            $this->publishes([
                __DIR__.'/../../config/auth.php' => config_path('auth.php'),
            ], 'config');
            // validate the configuration
            env('JWT_TOKEN_AUDIENCE') or throw new LogicException('No audience set in the configuration');
            // add a JWT guard to validated the token from the access_token cookie or authorization header
            Auth::extend('jwt', function ($app, $name, array $config) {
                $jwt = self::GetTokenFromRequest($app['request']) or throw new UnauthorizedException('No credentials were provided');

                return new JwtGuard($jwt, $config['audience'], $config['keys']);
            });
            // verify the user in the request matches the authenticated user
            Gate::define('user-owner', function (ExternalUser $authenticatedUser, ExternalUser $requestUser) {
                return ($authenticatedUser->getAuthIdentifier() === $requestUser->getAuthIdentifierName());
            });
            // verify the user scopes
            Gate::define('user-scopes', function (ExternalUser $authenticatedUser, array $requiredScopes) {
                // check if the required scopes the user has
                $matchedScopes = array_intersect($requiredScopes, $authenticatedUser->getScopes());
                // check if the user has all the required scopes
                $diff = array_diff($requiredScopes, $matchedScopes);
                // the user has all the required scopes if there is no difference
                return empty($diff);
            });
        }

        /**
         * Retrieves the JWT token from the Authorization header or the access_token cookie
         * @param Request $request : the HTTP request
         * @return ?string the JWT string if found; null otherwise
         */
        protected static function GetTokenFromRequest(Request $request): ?string
        {
            if ($request->hasHeader('Authorization'))
            {
                return str_ireplace('Bearer ', '', $request->header('Authorization'));
            }
            else if ($request->hasCookie('access_token'))
            {
                return json_decode($request->cookie('access_token'));
            }

            return null;
        }
    }
