<?php
    namespace Kairnial\LaravelApi\Models;

    use Illuminate\Database\Eloquent\Concerns\HasRelationships;
    use Illuminate\Database\Eloquent\Relations\BelongsToMany;
    use Kairnial\LaravelApi\Models\Enums\LanguageCodes;
    use Spatie\TranslationLoader\LanguageLine;

    /**
     * @property string $group
     * @property string $text
     * @property string $key
     */
    class Translation extends LanguageLine
    {
        use HasRelationships;

        protected $casts = [];
        protected $table = 'translations';
        protected $fillable = [ 'group', 'key', 'text' ];

        public static function getTranslationsForGroup(string $locale, string $group): array
        {
            $lng = LanguageCodes::from($locale);

            $query = static::query()->where('fk_language', $lng->uuid());

            if ($group !== '*') {
                $query = $query->where('group', $group);
            }

            return $query->get()->map(function (Translation $translation) {
                $key = "$translation->group.$translation->key";
                $text = $translation->text;

                return compact('key', 'text');
            })->pluck('text', 'key')->toArray();
        }

        public function languages(): BelongsToMany
        {
            return $this->belongsToMany(Language::class);
        }
    }
