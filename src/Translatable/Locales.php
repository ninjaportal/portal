<?php

namespace NinjaPortal\Portal\Translatable;

class Locales
{

    protected string $locale;

    public function __construct()
    {
        $this->locale = app()->getLocale();
    }

    public function gettLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }
}
