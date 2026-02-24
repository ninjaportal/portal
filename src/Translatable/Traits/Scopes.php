<?php

namespace NinjaPortal\Portal\Translatable\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\JoinClause;

/**
 * @method whereTranslation(string $translationField, $value, ?string $locale = null)
 * @method whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method ListsTranslations(string $translationField)
 * @method notTranslatedIn(?string $locale = null)
 */
trait Scopes
{
    public function scopeListsTranslations(Builder $query, string $translationField): Builder
    {
        $translationTable = $this->getTranslationsTable();
        $localeKey = $this->getLocaleKey();

        $query
            ->select($this->getTable().'.'.$this->getKeyName(), $translationTable.'.'.$translationField)
            ->leftJoin($translationTable, $translationTable.'.'.$this->getTranslationRelationKey(), '=', $this->getTable().'.'.$this->getKeyName())
            ->where($translationTable.'.'.$localeKey, $this->getLocale());

        return $query;
    }

    public function scopeNotTranslatedIn(Builder $query, ?string $locale = null): Builder
    {
        $locale = $locale ?? $this->getLocale();

        return $query->whereDoesntHave('translations', function (Builder $q) use ($locale) {
            $q->where($this->getLocaleKey(), '=', $locale);
        });
    }

    public function scopeOrderByTranslation(Builder $query, string $translationField, string $sortMethod = 'asc'): Builder
    {
        $translationTable = $this->getTranslationsTable();
        $localeKey = $this->getLocaleKey();
        $table = $this->getTable();
        $keyName = $this->getKeyName();

        return $query
            ->with('translations')
            ->select("{$table}.*")
            ->leftJoin($translationTable, function (JoinClause $join) use ($translationTable, $localeKey, $table, $keyName) {
                $join
                    ->on("{$translationTable}.{$this->getTranslationRelationKey()}", '=', "{$table}.{$keyName}")
                    ->where("{$translationTable}.{$localeKey}", $this->getLocale());
            })
            ->orderBy("{$translationTable}.{$translationField}", $sortMethod);
    }

    public function scopeOrWhereTranslation(Builder $query, string $translationField, $value, ?string $locale = null)
    {
        return $this->scopeWhereTranslation($query, $translationField, $value, $locale, 'orWhereHas');
    }

    public function scopeOrWhereTranslationLike(Builder $query, string $translationField, $value, ?string $locale = null)
    {
        return $this->scopeWhereTranslation($query, $translationField, $value, $locale, 'orWhereHas', 'LIKE');
    }

    public function scopeTranslated(Builder $query): Builder
    {
        return $query->has('translations');
    }

    public function scopeTranslatedIn(Builder $query, ?string $locale = null): Builder
    {
        $locale = $locale ?? $this->getLocale();

        return $query->whereHas('translations', function (Builder $q) use ($locale) {
            $q->where($this->getLocaleKey(), '=', $locale);
        });
    }

    public function scopeWhereTranslation(Builder $query, string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
    {
        $locale = $locale ?? $this->getLocale();

        return $query->$method('translations', function (Builder $query) use ($translationField, $value, $locale, $operator) {
            $query->where($translationField, $operator, $value);
            $query->where($this->getLocaleKey(), '=', $locale);
        });
    }

    public function scopeWhereTranslationLike(Builder $query, string $translationField, $value, ?string $locale = null)
    {
        return $this->scopeWhereTranslation($query, $translationField, $value, $locale, 'whereHas', 'LIKE');
    }

    public function scopeWithTranslation(Builder $query): void
    {
        $query->with([
            'translations' => function (Relation $query) {
                if ($this->useFallback()) {
                    $locale = $this->getLocale();
                    $fallbackLocale = $this->getFallbackLocale();
                    $locales = array_unique([$locale, $fallbackLocale]);

                    return $query->whereIn($this->getTranslationsTable().'.'.$this->getLocaleKey(), $locales);
                }

                return $query->where($this->getTranslationsTable().'.'.$this->getLocaleKey(), $this->getLocale());
            },
        ]);
    }

    protected function getTranslationsTable(): string
    {
        return app()->make($this->getTranslationModelName())->getTable();
    }
}
