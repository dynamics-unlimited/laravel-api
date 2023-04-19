<?php
    use Kairnial\Common\Models\Enums\ApiResponseStatus;
    use Symfony\Component\HttpFoundation\Response;

    return [
        'default'        => 'default',
        'documentations' => [
            'default' => [
                'api' => [
                    'title' => env('L5_SWAGGER_DOC_TITLE', 'Generic API'),
                ],

                'routes' => [
                    /*
                     * Route for accessing api documentation interface
                    */
                    'api' => 'api/documentation',
                ],
                'paths'  => [
                    /*
                     * Edit to include full URL in ui for assets
                    */
                    'use_absolute_path'      => env('L5_SWAGGER_USE_ABSOLUTE_PATH', true),

                    /*
                     * File name of the generated json documentation file
                    */
                    'docs_json'              => 'swagger.json',

                    /*
                     * File name of the generated YAML documentation file
                    */
                    'docs_yaml'              => 'swagger.yaml',

                    /*
                    * Set this to `json` or `yaml` to determine which documentation file to use in UI
                    */
                    'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),

                    /*
                     * Absolute paths to directory containing the swagger annotations are stored.
                    */
                    'annotations'            => [
                        base_path('app'),
                    ],

                ],
            ],
        ],
        'defaults'       => [
            'routes' => [
                /*
                 * Route for accessing parsed swagger annotations.
                */
                'docs'            => 'docs',

                /*
                 * Route for Oauth2 authentication callback.
                */
                'oauth2_callback' => 'api/oauth2-callback',

                /*
                 * Middleware allows to prevent unexpected access to API documentation
                */
                'middleware'      => [
                    'api'             => [],
                    'asset'           => [],
                    'docs'            => [],
                    'oauth2_callback' => [],
                ],

                /*
                 * Route Group options
                */
                'group_options'   => [],
            ],

            'paths' => [
                /*
                 * Absolute path to location where parsed annotations will be stored
                */
                'docs'                   => storage_path('api-docs'),

                /*
                 * Absolute path to directory where to export views
                */
                'views'                  => base_path('resources/views/vendor/l5-swagger'),

                /*
                 * Edit to set the api's base path
                */
                'base'                   => env('L5_SWAGGER_BASE_PATH'),

                /*
                 * Edit to set path where swagger ui assets should be stored
                */
                'swagger_ui_assets_path' => env('L5_SWAGGER_UI_ASSETS_PATH', 'vendor/swagger-api/swagger-ui/dist/'),

                /*
                 * Absolute path to directories that should be exclude from scanning
                 * @deprecated Please use `scanOptions.exclude`
                 * `scanOptions.exclude` overwrites this
                */
                'excludes'               => [],
            ],

            'scanOptions'           => [
                /**
                 * analyser: defaults to \OpenApi\StaticAnalyser .
                 */
                'analyser'   => null,

                /**
                 * analysis: defaults to a new \OpenApi\Analysis .
                 */
                'analysis'   => null,

                /**
                 * Custom query path processors classes.
                 *
                 * @link https://github.com/zircote/swagger-php/tree/master/Examples/schema-query-parameter-processor
                 */
                'processors' => [
                    // new \App\SwaggerProcessors\SchemaQueryParameter(),
                ],

                /**
                 * pattern: string       $pattern File pattern(s) to scan (default: *.php) .
                 */
                'pattern'    => null,

                /*
                 * Absolute path to directories that should be exclude from scanning
                 * @note This option overwrites `paths.excludes`
                 * @see \OpenApi\scan
                */
                'exclude'    => [],
            ],

            /*
             * API security definitions. Will be generated into documentation file.
            */
            'securityDefinitions'   => [
                'securitySchemes' => [
                    'oauth_bearer' => [ // Unique name of security
                                        'type'        => 'oauth2',
                                        // The type of the security scheme. Valid values are "basic", "apiKey" or "oauth2".
                                        'description' => 'Access for the micro-frontend.',
                                        'flow'        => 'password',
                                        // The flow used by the OAuth2 security scheme. Valid values are "implicit", "password", "application" or "accessCode".
                                        'tokenUrl'    => env('L5_SWAGGER_DOC_LOGIN_URL'),
                                        // The authorization URL to be used for (password/application/accessCode)
                                        'scopes'      => [
                                            // Add required scopes here. Example:
                                            // 'read:notifications' => 'Read notifications preferences through the API',
                                            // 'write:notifications' => 'Create or update notifications preferences through the API',
                                        ],
                    ],
                    'oauth_client' => [ // Unique name of security
                                        'type'        => 'oauth2',
                                        // The type of the security scheme. Valid values are "basic", "apiKey" or "oauth2".
                                        'description' => 'Access for confidential clients.',
                                        'flow'        => 'clientCredentials',
                                        // The flow used by the OAuth2 security scheme. Valid values are "implicit", "password", "application" or "accessCode".
                                        //'authorizationUrl' => 'http://example.com/auth', // The authorization URL to be used for (implicit/accessCode)
                                        'tokenUrl'    => env('L5_SWAGGER_DOC_LOGIN_URL'),
                                        // The authorization URL to be used for (password/application/accessCode)
                                        'scopes'      => [
                                            // Add required scopes here. Example:
                                            // 'read:notifications:global_prefs' => 'Read all the user preferences for notifications',
                                        ],
                    ],
                ],
                'security'        => [],
            ],

            /*
             * Set this to `true` in development mode so that docs would be regenerated on each request
             * Set this to `false` to disable swagger generation on production
            */
            'generate_always'       => env('L5_SWAGGER_GENERATE_ALWAYS', false),

            /*
             * Set this to `true` to generate a copy of documentation in yaml format
            */
            'generate_yaml_copy'    => env('L5_SWAGGER_GENERATE_YAML_COPY', false),

            /*
             * Edit to trust the proxy's ip address - needed for AWS Load Balancer
             * string[]
            */
            'proxy'                 => false,

            /*
             * Configs plugin allows to fetch external configs instead of passing them to SwaggerUIBundle.
             * See more at: https://github.com/swagger-api/swagger-ui#configs-plugin
            */
            'additional_config_url' => null,

            /*
             * Apply a sort to the operation list of each API. It can be 'alpha' (sort by paths alphanumerically),
             * 'method' (sort by HTTP method).
             * Default is the order returned by the server unchanged.
            */
            'operations_sort'       => env('L5_SWAGGER_OPERATIONS_SORT'),

            /*
             * Pass the validatorUrl parameter to SwaggerUi init on the JS side.
             * A null value here disables validation.
            */
            'validator_url'         => null,

            /*
             * Swagger UI configuration parameters
            */
            'ui'                    => [
                'display' => [
                    /*
                     * Controls the default expansion setting for the operations and tags. It can be :
                     * 'list' (expands only the tags),
                     * 'full' (expands the tags and operations),
                     * 'none' (expands nothing).
                     */
                    'doc_expansion' => env('L5_SWAGGER_UI_DOC_EXPANSION', 'none'),

                    /**
                     * If set, enables filtering. The top bar will show an edit box that
                     * you can use to filter the tagged operations that are shown. Can be
                     * Boolean to enable or disable, or a string, in which case filtering
                     * will be enabled using that string as the filter expression. Filtering
                     * is case-sensitive matching the filter expression anywhere inside
                     * the tag.
                     */
                    'filter'        => env('L5_SWAGGER_UI_FILTERS', true), // true | false
                ],

                'authorization' => [
                    /*
                     * If set to true, it persists authorization data, and it would not be lost on browser close/refresh
                     */
                    'persist_authorization' => env('L5_SWAGGER_UI_PERSIST_AUTHORIZATION', false),

                    'oauth2' => [
                        /*
                        * If set to true, adds PKCE to AuthorizationCodeGrant flow
                        */
                        'use_pkce_with_authorization_code_grant' => false,
                    ],
                ],
            ],
            /*
             * Constants which can be used in annotations
             */
            'constants'             => [
                'L5_SWAGGER_DOC_HOST'        => env('L5_SWAGGER_DOC_HOST', env('APP_URL') . '/api/'),
                'L5_SWAGGER_DOC_CONTACT'     => env('L5_SWAGGER_DOC_CONTACT'),
                'L5_SWAGGER_DOC_TITLE'       => env('L5_SWAGGER_DOC_TITLE'),
                'L5_SWAGGER_DOC_VERSION'     => env('L5_SWAGGER_DOC_VERSION'),
                'L5_SWAGGER_DOC_DESCRIPTION' => env('L5_SWAGGER_DOC_DESCRIPTION'),
                // Response enums
                'RESPONSE_MESSAGE_VALUES'    => array_values(Response::$statusTexts),
                'RESPONSE_STATUS_VALUES'     => ApiResponseStatus::cases(),
            ],
        ],
    ];
