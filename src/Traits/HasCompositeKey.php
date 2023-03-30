<?php
    namespace Kairnial\LaravelApi\Traits;

    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Model;

    trait HasCompositeKey
    {
        /**
         * @noinspection PhpUnused
         */
        public function getIncrementing(): bool
        {
            return false;
        }

        /**
         * @noinspection PhpUnused
         */
        protected function setKeysForSaveQuery($query): Builder
        {
            /** @var Model $this */
            $keys = $this->getKeyName();

            if (!is_array($keys))
            {
                /** @noinspection PhpUndefinedClassInspection */
                return parent::setKeysForSaveQuery($query);
            }

            foreach ($keys as $keyName)
            {
                $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
            }

            return $query;
        }

        protected function getKeyForSaveQuery($keyName = null)
        {
            /** @var Model $this */
            $keyName = $keyName ?? $this->getKeyName();

            if (isset($this->original[$keyName]))
            {
                return $this->original[$keyName];
            }

            return $this->getAttribute($keyName);
        }
    }
