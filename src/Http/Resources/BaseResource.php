<?php
    namespace Kairnial\LaravelApi\Http\Resources;

    use Illuminate\Http\Resources\Json\JsonResource;

    class BaseResource extends JsonResource
    {
        /** @inheritDoc */
        public function jsonOptions() : int
        {
            return JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION | JSON_PARTIAL_OUTPUT_ON_ERROR;
        }
    }
