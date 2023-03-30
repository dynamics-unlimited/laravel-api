<?php
    namespace Kairnial\LaravelApi\Models\Enums;

    /**
     * Language codes enumeration
     */
    enum LanguageCodes: string
    {
        case en = 'en'; // English
        case fr = 'fr'; // French
        case de = 'de'; // German

        public function name(): string
        {
            return match ($this)
            {
                self::en => 'English',
                self::fr => 'Français',
                self::de => 'Deutsch',
            };
        }

        public function uuid(): string
        {
            return match ($this)
            {
                self::en => '96ce7620-5742-46dd-805b-6d37970338ec',
                self::fr => '96ce7620-734d-4e7c-adf6-346607380bf3',
                self::de => '96ce7620-7356-4fdd-94ff-14551191a3e2',
            };
        }
    }
