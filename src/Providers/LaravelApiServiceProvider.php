<?php /** @noinspection PhpIllegalPsrClassPathInspection */

namespace Kairnial\LaravelApi\Providers {
    use Kairnial\Common\Providers\BaseServiceProvider;
    use Illuminate\Routing\UrlGenerator;

    /**
     * Generic provider used to configure the framework
     */
    class LaravelApiServiceProvider extends BaseServiceProvider
    {
        /**
         * Root path relative to this class definition
         */
        const ROOT_PATH = __DIR__ . '/../..';

        /**
         * Register any application authentication / authorization services.
         * @return void
         */
        public function boot(): void
        {
            // forcing the HTTPS scheme might be needed when serving through a web server
            // should be off when serving through artisan server
            if (env('FORCE_HTTPS', true))
            {
                resolve(UrlGenerator::class)->forceScheme('https');
            }

            if (app()->runningInConsole())
            {
                // publish the configuration for the service
                $this->publishes([
                    self::ROOT_PATH . '/config/cors.php' => config_path('cors.php'),
                    self::ROOT_PATH . '/config/redoc.php' => config_path('redoc.php'),
                    self::ROOT_PATH . '/config/database.php' => config_path('database.php'),
                    self::ROOT_PATH . '/config/l5-swagger.php' => config_path('l5-swagger.php'),
                    self::ROOT_PATH . '/config/translation-loader.php' => config_path('translation-loader.php'),
                ], 'config');
                // publish the migration files
                $this->publishes([
                    self::ROOT_PATH . '/database/migrations/create_languages_table.php.stub'    => database_path('migrations/2023_03_31_000000_create_languages_table.php'),
                    self::ROOT_PATH . '/database/migrations/create_translations_table.php.stub' => database_path('migrations/2023_03_31_000001_create_translations_table.php'),
                ], 'migrations');
            }
        }
    }
}

namespace
{
    // spatie/laravel-translation-loader only adds its migrations files if this class doesn't exist
    /** @noinspection PhpMultipleClassesDeclarationsInOneFile */
    class CreateLanguageLinesTable {}
}
