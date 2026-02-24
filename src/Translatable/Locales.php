<?php

namespace NinjaPortal\Portal\Translatable;

class Locales
{
    protected ?string $locale = null;

    public function getLocale(): string
    {
        return $this->locale ?? app()->getLocale();
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;

        if (app()->getLocale() !== $locale) {
            app()->setLocale($locale);
        }
    }
}
