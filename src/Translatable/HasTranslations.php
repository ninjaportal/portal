<?php

namespace NinjaPortal\Portal\Translatable;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use NinjaPortal\Portal\Translatable\Traits\Relationship;
use NinjaPortal\Portal\Translatable\Traits\Scopes;

/**
 * Trait HasTranslations
 *
 * Provides translation capabilities for Eloquent models.
 * Allows models to have translatable attributes stored in a separate translation model.
 *
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
    use Relationship, Scopes;

    /**
     * The current locale for the model instance.
     */
    protected ?string $locale = null;

    /**
     * The key name for the locale column in translation table.
     */
    protected string $localeKey = 'locale';

    /**
     * Whether to automatically load translations when accessing translatable attributes.
     */
    protected ?bool $autoloadTranslations = true;

    /**
     * Whether to always load translations when converting to array.
     */
    protected ?bool $toArrayAlwaysLoadsTranslations = false;

    /**
     * Whether to use fallback locale when translation is not found.
     */
    protected ?bool $useTranslationFallback = true;

    /**
     * Whether to delete translations when the parent model is deleted.
     */
    public static bool $deleteTranslationsCascade = true;

    /**
     * Cached translation model for the current locale.
     */
    protected ?Model $translation = null;

    /**
     * Boot the HasTranslations trait for a model.
     */
    public static function bootHasTranslations(): void
    {
        static::saved(function (Model $model) {
            /** @var self $model */
            return $model->saveTranslations();
        });

        static::deleting(function (Model $model) {
            /** @var self $model */
            if (self::$deleteTranslationsCascade === true) {
                $model->deleteTranslations();
            }
        });
    }

    /**
     * Delete translations for specific locales or all translations.
     *
     * @param  string|array|null  $locales  Specific locale(s) to delete, or null for all
     */
    public function deleteTranslations($locales = null): void
    {
        if ($locales === null) {
            $translations = $this->translations()->get();
        } else {
            $locales = (array) $locales;
            $translations = $this->translations()->whereIn($this->getLocaleKey(), $locales)->get();
        }

        $translations->each->delete();

        // Manually reload the relationship collection to ensure consistency
        // otherwise $this->translations()->get() would NOT be the same as $this->translations
        $this->load('translations');
    }

    /**
     * Convert the model's attributes to an array.
     * Includes translatable attributes if translations are loaded.
     */
    public function attributesToArray(): array
    {
        $attributes = parent::attributesToArray();

        if ($this->shouldSkipTranslations()) {
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

    /**
     * Get an attribute from the model.
     * Retrieves translatable attributes from the translation model.
     *
     * @param  string|null  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if ($key === null || $key === '') {
            return null;
        }

        if ($this->isTranslatedAttribute($key)) {
            return $this->getTranslatedAttribute($key);
        }

        return parent::getAttribute($key);
    }

    /**
     * Set a given attribute on the model.
     * Sets translatable attributes on the translation model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        if ($this->isTranslatedAttribute($key)) {
            $locale = $this->getLocale();
            $this->getTranslationOrNew($locale)->$key = $value;

            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Fill the model with an array of attributes.
     * Supports two formats:
     * 1. Locale-keyed arrays: ['en' => ['title' => '...'], 'ar' => ['title' => '...']]
     * 2. Direct attributes: ['title' => '...'] (uses current locale)
     *
     * Note: When locale-keyed arrays are present, direct translatable attributes are ignored
     * to prevent ambiguity and duplicate translations.
     *
     * @return $this
     */
    public function fill(array $attributes)
    {
        $supportedLocales = $this->getSupportedLocales();
        $translatableAttributes = $this->getTranslatableAttributes();
        $hasLocaleKeyedTranslations = false;

        // First pass: identify and process locale-keyed translations
        foreach ($attributes as $key => $values) {
            if (is_array($values) && in_array($key, $supportedLocales, true)) {
                // Validate that the array contains translatable attributes
                if ($this->arrayContainsTranslatableAttributes($values, $translatableAttributes)) {
                    $this->getTranslationOrNew($key)->fill($values);
                    $hasLocaleKeyedTranslations = true;
                    unset($attributes[$key]); // Remove processed locale-keyed translation
                }
            }
        }

        // Second pass: handle direct translatable attributes only if no locale-keyed translations were provided
        if (! $hasLocaleKeyedTranslations) {
            $currentLocale = $this->getLocale();
            foreach ($attributes as $key => $value) {
                if ($this->isTranslatedAttribute($key)) {
                    $this->getTranslationOrNew($currentLocale)->fill([$key => $value]);
                    unset($attributes[$key]); // Remove processed translatable attribute
                }
            }
        } else {
            // Remove any direct translatable attributes to prevent conflicts
            foreach ($attributes as $key => $value) {
                if ($this->isTranslatedAttribute($key)) {
                    unset($attributes[$key]);
                }
            }
        }

        // Pass remaining non-translatable attributes to parent
        return parent::fill($attributes);
    }

    /**
     * Get a translation for the given locale or create a new one if it doesn't exist.
     */
    public function getTranslationOrNew(?string $locale = null): Model
    {
        $locale = $locale ?: $this->getLocale();

        if (($translation = $this->findTranslationForLocale($locale)) === null) {
            $translation = $this->getNewTranslation($locale);
        }

        return $translation;
    }

    /**
     * Create a new translation instance for the given locale.
     */
    public function getNewTranslation(?string $locale = null): Model
    {
        $locale = $locale ?: $this->getLocale();
        $modelName = $this->getTranslationModelName();
        $translation = new $modelName;
        $translation->{$this->getLocaleKey()} = $locale;

        // Ensure translations collection exists
        if (! $this->relationLoaded('translations')) {
            $this->setRelation('translations', new Collection);
        }

        $this->translations->add($translation);

        // Clear cached translation to force re-lookup
        $this->translation = null;

        return $translation;
    }

    /**
     * Set the locale for the model instance.
     *
     * @return $this
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        $this->translation = null; // Clear cached translation to ensure fresh lookup

        return $this;
    }

    /**
     * Get the translatable attributes array defined on the model.
     */
    public function getTranslatableAttributes(): array
    {
        return $this->translated_attributes ?? [];
    }

    /**
     * Get the translation model for the given locale.
     * Uses fallback locale if enabled and translation not found.
     */
    public function getTranslation(?string $locale = null): ?Model
    {
        $locale = $locale ?: $this->getLocale();

        // Return cached translation if it matches the requested locale
        if ($this->isCachedTranslationValid($locale)) {
            return $this->translation;
        }

        // Ensure translations are loaded to prevent silent failures
        if (! $this->relationLoaded('translations')) {
            $this->load('translations');
        }

        // Find translation in the loaded collection
        $this->translation = $this->translations->firstWhere($this->getLocaleKey(), $locale);

        // Use fallback if enabled and no translation found
        if ($this->translation === null && $this->useFallback()) {
            $fallbackLocale = $this->getFallbackLocale();
            // Avoid infinite loop if fallback is same as requested locale
            if ($fallbackLocale !== $locale) {
                $this->translation = $this->translations->firstWhere($this->getLocaleKey(), $fallbackLocale);
            }
        }

        return $this->translation;
    }

    /**
     * Check if an attribute is translatable.
     *
     * @param  string  $key
     */
    public function isTranslatedAttribute($key): bool
    {
        return in_array($key, $this->getTranslatableAttributes(), true);
    }

    /**
     * Get the fallback locale from configuration.
     */
    public function getFallbackLocale(): string
    {
        return config('ninjaportal.translatable.fallback_locale', 'en');
    }

    /**
     * Check if fallback locale should be used when translation is not found.
     */
    public function useFallback(): bool
    {
        return $this->useTranslationFallback ?? config('ninjaportal.translatable.with_fallback', false);
    }

    /**
     * Determine if an attribute or relation exists on the model.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->isTranslatedAttribute($key) || parent::__isset($key);
    }

    /**
     * Get the list of supported locale codes from configuration.
     */
    protected function getSupportedLocales(): array
    {
        return config('ninjaportal.translatable.locales', ['en', 'ar']);
    }

    /**
     * Determine if translations should be skipped during array conversion.
     */
    protected function shouldSkipTranslations(): bool
    {
        return (
            ! $this->relationLoaded('translations')
            && ! $this->toArrayAlwaysLoadsTranslations()
            && is_null($this->autoloadTranslations)
        ) || $this->autoloadTranslations === false;
    }

    /**
     * Get a translated attribute value from the translation model.
     * Handles mutators and accessors properly.
     *
     * @return mixed
     */
    protected function getTranslatedAttribute(string $key)
    {
        if ($this->getTranslation() === null) {
            return $this->getAttributeValue($key);
        }

        // Handle mutators: get the raw value from translation, then apply mutator
        // This ensures type casting, date fields, and custom accessors work correctly
        if ($this->hasGetMutator($key)) {
            $this->attributes[$key] = $this->translation->getAttribute($key);

            return $this->getAttributeValue($key);
        }

        return $this->translation->getAttribute($key);
    }

    /**
     * Check if an array contains any translatable attributes.
     * Used to validate locale-keyed arrays during fill operations.
     */
    protected function arrayContainsTranslatableAttributes(array $values, array $translatableAttributes): bool
    {
        foreach (array_keys($values) as $key) {
            if (in_array($key, $translatableAttributes, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the cached translation is valid for the given locale.
     */
    protected function isCachedTranslationValid(string $locale): bool
    {
        return $this->relationLoaded('translations')
            && $this->translation !== null
            && $this->translation->getAttribute($this->getLocaleKey()) === $locale;
    }

    /**
     * Check if translations should always be loaded during array conversion.
     */
    protected function toArrayAlwaysLoadsTranslations(): bool
    {
        return $this->toArrayAlwaysLoadsTranslations ?? false;
    }

    /**
     * Get the current locale for the model instance.
     */
    protected function getLocale(): string
    {
        return $this->locale ?? $this->getLocalesHelper()->getLocale();
    }

    /**
     * Get the locale key attribute name.
     */
    protected function getLocaleKey(): string
    {
        return $this->localeKey;
    }

    /**
     * Save all dirty translations associated with the model.
     */
    protected function saveTranslations(): bool
    {
        if (! $this->relationLoaded('translations')) {
            return true;
        }

        // Collect dirty translations
        $dirtyTranslations = $this->translations->filter(function ($translation) {
            return $this->isTranslationDirty($translation);
        });

        if ($dirtyTranslations->isEmpty()) {
            return true;
        }

        // Use transaction to ensure atomicity
        return DB::transaction(function () use ($dirtyTranslations) {
            foreach ($dirtyTranslations as $translation) {
                $this->prepareTranslationForSave($translation);
                if (! $translation->save()) {
                    throw new \RuntimeException("Failed to save translation for locale: {$translation->getAttribute($this->getLocaleKey())}");
                }
            }

            return true;
        });
    }

    /**
     * Prepare a translation model for saving.
     * Sets connection and foreign key relationship.
     */
    protected function prepareTranslationForSave(Model $translation): void
    {
        if (! empty($connectionName = $this->getConnectionName())) {
            $translation->setConnection($connectionName);
        }

        $translation->setAttribute($this->getTranslationRelationKey(), $this->getKey());
    }

    /**
     * Check if a translation has unsaved changes.
     * Excludes the locale key from dirty check.
     */
    protected function isTranslationDirty(Model $translation): bool
    {
        $dirtyAttributes = $translation->getDirty();
        unset($dirtyAttributes[$this->getLocaleKey()]);

        return count($dirtyAttributes) > 0;
    }

    /**
     * Find a translation for the exact locale without applying fallback logic.
     */
    protected function findTranslationForLocale(string $locale): ?Model
    {
        if (! $this->relationLoaded('translations')) {
            $this->load('translations');
        }

        return $this->translations->firstWhere($this->getLocaleKey(), $locale);
    }

    /**
     * Get the locales helper instance from the container.
     */
    protected function getLocalesHelper(): Locales
    {
        return app('translatable.locales');
    }
}
