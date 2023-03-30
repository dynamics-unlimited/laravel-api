<?php
    namespace Kairnial\LaravelApi\Models;

    use Kairnial\LaravelApi\Traits\OrderedUuid;

    class Language extends BaseModel
    {
        use OrderedUuid;

        protected $table = 'languages';
        protected $primaryKey = 'pk_language';
        protected $fillable = [ 'language_locale', 'language_name' ];
    }
