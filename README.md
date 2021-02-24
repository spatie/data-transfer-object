# Data transfer objects with batteries included

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/data-transfer-object.svg?style=flat-square)](https://packagist.org/packages/spatie/data-transfer-object)
![Test](https://github.com/spatie/data-transfer-object/workflows/Test/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/data-transfer-object.svg?style=flat-square)](https://packagist.org/packages/spatie/data-transfer-object)

## Installation

You can install the package via composer:

```bash
composer require spatie/data-transfer-object
```

* **Note**: This package requires PHP 7.4 so it can take full advantage of type casting in PHP.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/data-transfer-object.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/data-transfer-object)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Have you ever…

… worked with an array of data, retrieved from a request, a CSV file or a JSON API; and wondered what was in it?

Here's an example:

```php
public function handleRequest(array $dataFromRequest)
{
    $dataFromRequest[/* what to do now?? */];
}
```

The goal of this package is to structure "unstructured data", which is normally stored in associative arrays.
By structuring this data into an object, we gain several advantages:

- We're able to type hint data transfer objects, instead of just calling them `array`.
- By making all properties on our objects typeable, we're sure that their values are never something we didn't expect.
- Because of typed properties, we can statically analyze them and have auto completion.

Let's look at the example of a JSON API call:

```php
$post = $api->get('posts', 1); 

[
    'title' => '…',
    'body' => '…',
    'author_id' => '…',
]
```

Working with this array is difficult, as we'll always have to refer to the documentation to know what's exactly in it. 
This package allows you to create data transfer object definitions, classes, which will represent the data in a structured way.

We did our best to keep the syntax and overhead as little as possible:

```php
use App\Models\Author;
use Spatie\DataTransferObject\DataTransferObject;

class PostData extends DataTransferObject
{
    public string $title;
    
    public string $body;
    
    public Author $author;
}
```

An object of `PostData` can from now on be constructed like so:

```php
$postData = new PostData([
    'title' => '…',
    'body' => '…',
    'author_id' => '…',
]);
```

Now you can use this data in a structured way:

```php
$postData->title;
$postData->body;
$postData->author_id;
```

It's, of course, possible to add static constructors to `PostData`:

```php
use App\Models\Author;
use Spatie\DataTransferObject\DataTransferObject;

class PostData extends DataTransferObject
{
    // …
    
    public static function fromRequest(Request $request): self
    {
        return new self([
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'author' => Author::find($request->input('author_id')),
        ]);
    }
}
```

When defining typed properties, you can take advantage of the built-in types supported by PHP. These properties will be validated against the given type and a `TypeError` will be thrown if the value does not comply with it.

```php
use App\Models\Author;
use Iterator;
use Spatie\DataTransferObject\DataTransferObject;

class PostData extends DataTransferObject
{
    /**
     * Built in types:
     */
    public string $property;

    /**
     * Imported class or fully qualified class name:
     */
    public Author $property;

    /**
     * Nullable types:
     */
    public ?string $property;
    
    /**
     * Any iterator:
     */
    public Iterator $property;
    
    /**
     * No type, which allows everything
     */
    public $property;
}
```

By adding doc blocks to our properties we can enforce stricter typing. Below are the possible ways of declaring types with doc blocks.

* **Attention**: When type casting to a class, your Docblock definition needs to be a Fully Qualified Class Name (`\App\Models\Author` instead of `Author` and a use statement at the top).

```php
use Spatie\DataTransferObject\DataTransferObject;

class PostData extends DataTransferObject
{
    /**
     * Built in types: 
     *
     * @var string 
     */
    public $property;
    
    /**
     * Classes with their FQCN: 
     *
     * @var \App\Models\Author
     */
    public $property;
    
    /**
     * Lists of types: 
     *
     * @var \App\Models\Author[]
     */
    public $property;
    
    /**
     * Iterator of types: 
     *
     * @var iterator<\App\Models\Author>
     */
    public $property;
    
    /**
     * Union types: 
     *
     * @var string|int
     */
    public $property;
    
    /**
     * Nullable types: 
     *
     * @var string|null
     */
    public $property;
    
    /**
     * Mixed types: 
     *
     * @var mixed|null
     */
    public $property;
    
    /**
     * Any iterator : 
     *
     * @var iterator
     */
    public $property;
    
    /**
     * No type, which allows everything
     */
    public $property;
}
```

### Working with collections

If you're working with collections of DTOs, you probably want auto completion and proper type validation on your collections too.
This package adds a simple collection implementation, which you can extend from.

```php
use App\DataTransferObjects\PostData;
use Spatie\DataTransferObject\DataTransferObjectCollection;

class PostCollection extends DataTransferObjectCollection
{
    public function current(): PostData
    {
        return parent::current();
    }
}
```

By overriding the `current` method, you'll get auto completion in your IDE.
Alternatively you can also use a phpdoc for this:

```php
use Spatie\DataTransferObject\DataTransferObjectCollection;

/**
 * @method \App\DataTransferObjects\PostData current
 */
class PostCollection extends DataTransferObjectCollection
{
}
```

Then you can use the collections like so:

```php
foreach ($postCollection as $postData) {
    $postData-> // … your IDE will provide autocompletion.
}

$postCollection[0]-> // … and also here.
```

Of course you're free to implement your own static constructors:

```php
use App\DataTransferObjects\PostData;
use Spatie\DataTransferObject\DataTransferObjectCollection;

class PostCollection extends DataTransferObjectCollection
{
    public static function create(array $data): PostCollection
    {
        return new static(PostData::arrayOf($data));
    }
}
```

### Automatic casting of nested DTOs

If you've got nested DTO fields, data passed to the parent DTO will automatically be cast.

```php
use App\DataTransferObjects\AuthorData;
use Spatie\DataTransferObject\DataTransferObject;

class PostData extends DataTransferObject
{
    public AuthorData $author;
}
```

`PostData` can now be constructed like so:

```php
$postData = new PostData([
    'author' => [
        'name' => 'Foo',
    ],
]);
```

### Automatic casting of nested array DTOs

Similarly to above, nested array DTOs will automatically be cast. For example, we can define the following DTO:

```php
namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class TagData extends DataTransferObject
{
   public string $name;
}
```

By referencing this object in our `PostData` DTO, a `TagData` DTO will be automatically cast.

```php
namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class PostData extends DataTransferObject
{
    /** @var \App\DataTransferObjects\TagData[] */
   public $tags;
}
```

`PostData` will automatically construct tags like such:

```php
$postData = new PostData([
    'tags' => [
        ['name' => 'foo'],
        ['name' => 'bar']
    ]
]);
```
**Attention**: Remember, for nested type casting to work, your Docblock definition needs to be a Fully Qualified Class Name (`\App\DataTransferObjects\TagData[]` instead of `TagData[]` and a use statement at the top).

### Immutability

If you want your data object to be never changeable (this is a good idea in some cases), you can make them immutable:

```php
$postData = PostData::immutable([
    'tags' => [
        ['name' => 'foo'],
        ['name' => 'bar']
    ]
]);
```

Trying to change a property of `$postData` after it's constructed, will result in a `DataTransferObjectError`.

### Helper functions

There are also some helper functions provided for working with multiple properties at once. 

```php
$postData->all();

$postData
    ->only('title', 'body')
    ->toArray();
    
$postData
    ->except('author')
    ->toArray();
``` 

You can also chain these methods:

```php
$postData
    ->except('title')
    ->except('body')
    ->toArray();
```

It's important to note that `except` and `only` are immutable, they won't change the original data transfer object.

### Exception handling

Beside property type validation, you can also be certain that the data transfer object in its whole is always valid.
On constructing a data transfer object, we'll validate whether all required (non-nullable) properties are set. 
If not, a `Spatie\DataTransferObject\DataTransferObjectError` will be thrown.

Likewise, if you're trying to set non-defined properties, you'll get a `DataTransferObjectError`.

### Flexible Data Transfer Objects
Sometimes you might want to be able to instantiate a DTO with a subset of an array. A good example of this is a large
API response where only a small amount of the fields are used. Normally, if you tried to instantiate a standard DTO
with superfluous properties, a `DataTransferObjectError` will be throw.

You can avoid this behaviour by instead extending from `FlexibleDataTransferObject`. For example:

```php
use Spatie\DataTransferObject\FlexibleDataTransferObject;

class PostData extends FlexibleDataTransferObject
{
    public string $content;
}


// No errors thrown
$dto = new PostData([
    'author' => [
        'id' => 1,
    ],
    'content' => 'blah blah',
    'created_at' => '2020-01-02',
]);

$dto->toArray(); // ['content' => 'blah blah']
```

### PHPStan

If you're using [phpstan](https://phpstan.org/) and set `checkUninitializedProperties: true`, phpstan by default doesn't understand that the DTO properties will always be correctly initialized.

To help with that, this package provides a rule you can add to your `.neon` config file:
```yaml
services:
  -
    class: Spatie\DataTransferObject\PHPstan\PropertiesAreAlwaysInitializedExtension
    tags:
      - phpstan.properties.readWriteExtension
#…
parameters:
  checkUninitializedProperties: true
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Kruikstraat 22, 2018 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## External tools

- [json2dto](https://json2dto.atymic.dev): a GUI to convert JSON objects to DTO classes (with nesting support). Also provides a [CLI tool](https://github.com/atymic/json2dto#cli-tool) for local usage.
- [Data Transfer Object Factory](https://github.com/anteris-dev/data-transfer-object-factory): generates a DTO instance or collection with fake data based on its definition. Supports type casting and doc blocks.
- [Laravel Castable Data Transfer Object](https://github.com/jessarcher/laravel-castable-data-transfer-object): Allow casting to and from DTO instances directly from JSON columns using Eloquent.

## Credits

- [Brent Roose](https://github.com/brendt)
- [All Contributors](../../contributors)

Our `Arr` class contains functions copied from Laravels `Arr` helper.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
