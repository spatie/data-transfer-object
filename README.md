**THIS PACKAGE IS IN DEVELOPMENT, DO NOT USE YET**

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

## The goal

- Passing data within your code base in a structured manner.
- Provide type validation for this data.
- Support static analysis for eg. auto completion.

## Usage

Value objects are defined like so:

```php
class DummyData extends ValueObject
{
    /** @var string */
    public $name;
    
    /** @var \Spatie\ValueObject\Dummy */
    public $relation;
    
    public $everythingAllowed;
    
    /** @var null|string */
    public $nullable;
    
    /** @var mixed */
    public $mixed;
}
```

And created like so:

```php
$dummyData = new DummyData([
    'name' => 'Spatie',
    'relation' => new Dummy(),
    'everythingAllowed' => 'abc'
    'mixed' => 123,
    // 'nullable' => 'deliberately left out',
]);
```

In practice, you'll almost always will provide static constructors:

```php
class DummyData
{
    public static function fromRequest(Request $request): DummyData
    {
        return new self([
            // …
        ]);
    }
    
    public static function fromJson(string $json): DummyData
    {
        return new self([
            // …
        ]);
    }
}
```

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

## Support us

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

Does your business depend on our contributions? Reach out and support us on [Patreon](https://www.patreon.com/spatie). 
All pledges will be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
