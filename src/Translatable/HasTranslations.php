<?php

namespace NinjaPortal\Portal\Translatable;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use NinjaPortal\Portal\Translatable\Traits\Relationship;
use NinjaPortal\Portal\Translatable\Traits\Scopes;

/**
 * @property-read array $translated_attributes
 * @property-read null|Model $translation
 * @property-read Collection|Model[] $translations
 * @property-read string $translationModel
 * @property-read string $translationForeignKey
 * @property-read string $localeKey
 * @property-read bool $useTranslationFallback
 *
 * @mixin Model
 */
trait HasTranslations
{
    use Scopes, Relationship;

    protected ?string $locale = null;

    protected string $localeKey = 'locale';

    protected ?bool $autoloadTranslations = true;

    protected ?bool $toArrayAlwaysLoadsTranslations = false;

    public static bool $deleteTranslationsCascade = true;

    protected ?Model $translation = null;

    public static function bootHasTranslations(): void
    {
        static::saved(function (Model $model) {
            /* @var self $model */
            return $model->saveTranslations();
        });

        static::deleting(function (Model $model) {
            /* @var self $model */
            if (self::$deleteTranslationsCascade === true) {
                $model->deleteTranslations();
            }
        });
    }


    public function deleteTranslations($locales = null): void
    {
        if ($locales === null) {
            $translations = $this->translations()->get();
        } else {
            $locales = (array)$locales;
            $translations = $this->translations()->whereIn($this->getLocaleKey(), $locales)->get();
        }

        $translations->each->delete();

        // we need to manually "reload" the collection built from the relationship
        // otherwise $this->translations()->get() would NOT be the same as $this->translations
        $this->load('translations');
    }

    public function attributesToArray(): array
    {
        $attributes = parent::attributesToArray();

        if (
            (!$this->relationLoaded('translations')
                && !$this->toArrayAlwaysLoadsTranslations()
                && is_null($this->autoloadTranslations))
            || $this->autoloadTranslations === false
        ) {
            return $attributes;
        }

        $hiddenAttributes = $this->getHidden();

        foreach ($this->getTranslatableAttributes() as $field) {
            if (in_array($field, $hiddenAttributes)) {
                continue;
            }

            $attributes[$field] = $this->getAttribute($field);
        }

        return $attributes;
    }

    public function getAttribute($key)
    {
        if (!$key)
            return null;

        if ($this->isTranslatedAttribute($key)) {
            if ($this->getTranslation() === null) {
                return $this->getAttributeValue($key);
            }

            // If the given $attribute has a mutator, we push it to $attributes and then call getAttributeValue
            // on it. This way, we can use Eloquent's checking for Mutation, type casting, and
            // Date fields.
            if ($this->hasGetMutator($key)) {
                $this->attributes[$key] = $this->getAttribute($key);
                return $this->getAttributeValue($key);
            }
            return $this->translation->getAttribute($key);
        }

        return parent::getAttribute($key);
    }

    public function setAttribute($key, $value)
    {
        $locale = $this->getLocale();

        if ($this->isTranslatedAttribute($key)) {
            $this->getTranslationOrNew($locale)->$key = $value;

            return $this;
        }

        return parent::setAttribute($key, $value);
    }


    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $values) {
            if (is_array($values)) {
                $this->getTranslationOrNew($key)->fill($values);
                unset($attributes[$key]);
            } else {
                [$attribute, $locale] = [$key, $this->getLocale()];

                if ($this->isTranslatedAttribute($attribute)) {
                    $this->getTranslationOrNew($locale)->fill([$attribute => $values]);
                    unset($attributes[$key]);
                }

            }
        }

        return parent::fill($attributes);
    }


    public function getTranslationOrNew(?string $locale = null): Model
    {
        $locale = $locale ?: $this->getLocale();

        if (($translation = $this->getTranslation($locale)) === null) {
            $translation = $this->getNewTranslation($locale);
        }

        return $translation;
    }

    public function getNewTranslation(?string $locale = null): Model
    {
        $locale = $locale ?: $this->getLocale();
        $modelName = $this->getTranslationModelName();
        $translation = new $modelName();
        $translation->{$this->getLocaleKey()} = $locale;
        $this->translations->add($translation);
        return $translation;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    public function getTranslatableAttributes()
    {
        return $this->translated_attributes;
    }

    public function getTranslation(?string $locale = null): ?Model
    {
        if (!$locale) {
            $locale = $this->getLocale();
        }

        if (
            $this->relationLoaded('translations')
            && $this->translation
            && $this->translation->getAttribute($this->getLocaleKey()) === $locale
        ) {
            return $this->translation;
        }
        $this->translation = $this->translations->firstWhere($this->getLocaleKey(), $locale);
        return $this->translation;
    }

    public function isTranslatedAttribute($key): bool
    {
        return in_array($key, $this->translated_attributes);
    }

    protected function toArrayAlwaysLoadsTranslations()
    {
        return $this->toArrayAlwaysLoadsTranslations ?? false;
    }

    protected function getLocale(): string
    {
        return $this->locale ?? $this->getLocalesHelper()->gettLocale();

    }

    protected function getLocaleKey(): string
    {
        return $this->localeKey ?? $this->getLocaleKey();
    }

    protected function saveTranslations(): bool
    {
        $saved = true;

        if (!$this->relationLoaded('translations')) {
            return true;
        }

        foreach ($this->translations as $translation) {
            if ($saved && $this->isTranslationDirty($translation)) {
                if (!empty($connectionName = $this->getConnectionName())) {
                    $translation->setConnection($connectionName);
                }

                $translation->setAttribute($this->getTranslationRelationKey(), $this->getKey());
                $saved = $translation->save();
            }
        }

        return $saved;
    }

    protected function isTranslationDirty(Model $translation): bool
    {
        $dirtyAttributes = $translation->getDirty();
        unset($dirtyAttributes[$this->getLocaleKey()]);

        return count($dirtyAttributes) > 0;
    }

    protected function getLocalesHelper(): Locales
    {
        return app('translatable.locales');
    }

    public function __isset($key)
    {
        return $this->isTranslatedAttribute($key) || parent::__isset($key);
    }
}
