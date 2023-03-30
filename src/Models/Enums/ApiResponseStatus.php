<?php
    namespace Kairnial\LaravelApi\Models\Enums;

    /**
     * Response status enumeration
     */
    enum ApiResponseStatus: string
    {
        case SUCCESS = 'success'; // success
        case ERROR = 'error';     // error
    }
