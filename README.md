# Value objects with batteries included

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/value-object.svg?style=flat-square)](https://packagist.org/packages/spatie/value-object)
[![Build Status](https://img.shields.io/travis/spatie/value-object/master.svg?style=flat-square)](https://travis-ci.org/spatie/value-object)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/value-object.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/value-object)
[![StyleCI](https://github.styleci.io/repos/153632216/shield?branch=master)](https://github.styleci.io/repos/153632216)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/value-object.svg?style=flat-square)](https://packagist.org/packages/spatie/value-object)

## Installation

You can install the package via composer:

```bash
composer require spatie/value-object
```

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

- Structural: we're able to type hint value objects, instead of just calling them `array`.
- Integrity: by making all properties on our objects typeable, we're sure that their values are never something we didn't expect.
- Clarity: because of typed properties, we can statically analyze them and have auto completion.

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
This package allows you to create value object definitions, classes, which will represent the data in a structured way.

We did our best to keep the syntax and overhead as little as possible:

```php
class PostData extends ValueObject
{
    /** @var string */
    public $title;
    
    /** @var string */
    public $body;
    
    /** @var \Author */
    public $author;
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

It's, of course, possible to add static constructors to `PostData`:

```php
class PostData extends ValueObject
{
    // …
    
    public static function fromRequest(Request $request): self
    {
        return new self([
            'title' => $request->get('title'),
            'body' => $request->get('body'),
            'author' => Author::find($request->get('author_id')),
        ]);
    }
}
```

By adding doc blocks to our properties, their values will be validated against the given type; 
and a `TypeError` will be thrown if the value doesn't comply with the given type.

Here are the possible ways of declaring types:

```php
class PostData extends ValueObject
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
     * No type, which allows everything
     */
    public $property;
}
```

When PHP 7.4 introduces typed properties, you'll be able to simply remove the doc blocks and type the properties with the new, built-in syntax.


### A note on immutability

These value objects are meant to be only constructed once, and not changed thereafter.
You should never write data to the properties once the value object is created, even though technically it's possible.

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

It's important to note that `except` and `only` are immutable, they won't change the original value object.

### Exception handling

Beside property type validation, you can also be certain that the value object in its whole is always valid.
On constructing a value object, we'll validate whether all required (non-nullable) properties are set. 
If not, a `Spatie\ValueObject\ValueObjectError` will be thrown.

Likewise, if you're trying to set non-defined properties, you'll get a `ValueObjectError`.

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Brent Roose](https://github.com/brentgd)
- [All Contributors](../../contributors)

Our `Arr` class contains functions copied from Laravels `Arr` helper.

## Support us

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

Does your business depend on our contributions? Reach out and support us on [Patreon](https://www.patreon.com/spatie). 
All pledges will be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
