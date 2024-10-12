# NinjaPortal translations 
A Model based translation package for Laravel utilizing the Eloquent ORM.
and storing model translations in the database.

## How to works ?
Assume we have ```Category``` model and we want to translate it to multiple languages.
we will have first to create a new table called ```category_translations``` with the following schema:
```php  
Schema::create('category_translations', function (Blueprint $table) {
    $table->id();
    $table->string('locale')->index();
    // Add the columns that you want to translate ex: name, description, ...
    $table->string('name');
    $table->text('description')->nullable();
    $table->foreignId('category_id')->constrained()->cascadeOnDelete();
    $table->unique(['category_id', 'locale']);
    $table->timestamps();
});
```
Then we will create a new model called ```CategoryTranslation``` 
```php
class CategoryTranslation extends Model
{
    protected $fillable = [
        "name",
        "description",
        "locale",
        "category_id"
    ];
}
```
and in the ```Category``` model we will the ```HasTranslations``` trait
```php  
use NinjaPortal\Translatable\HasTranslations;

class Category extends Model
{
    use HasTranslations;
    
    protected $fillable = [
        "slug",
    ];
    
    public array $translated_attributes = [
        "name",
        "description",
    ];
}
```

and that's it, now you can use the ```Category``` model as you used to do before and you can access the translations using the ```translations``` attribute.

## Example
### 1. Create a new record
```php


$category = Category::create([
    "slug" => "category-slug",
    "name" => "Category Name",
]);
```
by default the package will use the current app locale to save the translation

### 2. Set Locale before creating a new record
The package has a helper class called ```Locales``` that has a method called ```setLocale``` that you can use to set the locale before creating a new record
a singletons instance of the ```Locales``` class is created and you can access it using the ```app``` helper function
```php

use NinjaPortal\Translatable\Locales;

app(Locales::class)->setLocale('ar');

```

and now you can create a new record
```php

$category = Category::create([
    "slug" => "category-slug",
    "name" => "اسم القسم",
]);
```

### 3. Get the translation
```php
$category = Category::find(1);
echo $category->name; // Category Name

// ser the locale to ar
$category->setLocale('ar');
echo $category->name; // اسم القسم  
```

