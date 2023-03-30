<?php
    namespace Kairnial\LaravelApi\Http;

    use Illuminate\Routing\Middleware\SubstituteBindings;
    use Illuminate\Foundation\Http\Kernel as HttpKernel;
    use Laravel\Sanctum\Http\Middleware\CheckAbilities;
    use Illuminate\Http\Middleware\HandleCors;

    class Kernel extends HttpKernel
    {
        /** @inheritdoc **/
        protected $middleware = [
            HandleCors::class,
        ];
        /** @inheritdoc  */
        protected $routeMiddleware = [
            'scopes' => CheckAbilities::class,
        ];
        /** @inheritdoc **/
        protected $middlewareGroups = [
            'web' => [ SubstituteBindings::class, ],
            'api' => [ SubstituteBindings::class, ],
        ];
    }
