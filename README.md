# Data transfer objects with batteries included

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/data-transfer-object.svg?style=flat-square)](https://packagist.org/packages/spatie/data-transfer-object)
![Test](https://github.com/spatie/data-transfer-object/workflows/Test/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/data-transfer-object.svg?style=flat-square)](https://packagist.org/packages/spatie/data-transfer-object)

## Installation

You can install the package via composer:

```bash
composer require spatie/data-transfer-object
```

* **Note**: v3 of this package only supports `php:^8.0`. If you're looking for the older version, check out [v2](https://github.com/spatie/data-transfer-object/tree/v2).

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/data-transfer-object.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/data-transfer-object)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Usage

All type-checking functionality has been stripped from this package as of v3, in favour of PHP 8's type system. The goal of this package today is to make constructing objects from arrays of (serialized) data as easy as possible. Here's what a DTO looks like:

```php
use Spatie\DataTransferObject\DataTransferObject;

class MyDTO extends DataTransferObject
{
    public OtherDTO $otherDTO;
    
    public OtherDTOCollection $collection;
    
    #[CastWith(ComplexObjectCaster::class)]
    public ComplexObject $complexObject;
    
    public ComplexObjectWithCast $complexObjectWithCast;
    
    #[NumberBetween(1, 100)]
    public int $a;
}
```

You could construct this DTO like so:

```php
$dto = new MyDTO(
    a: 5,
    collection: [
        ['id' => 1],
        ['id' => 2],
        ['id' => 3],
    ],
    complexObject: [
        'name' => 'test',
    ],
    complexObjectWithCast: [
        'name' => 'test',
    ],
    otherDTO: ['id' => 5],
);
```

Let's discuss all possibilities one by one.

## Named arguments

Constructing a DTO can be done with named arguments now. It's also possible to still use the old array notation. This example is equivalent to the one above.

```php
$dto = new MyDTO([
    'a' => 5,
    'collection' => [
        ['id' => 1],
        ['id' => 2],
        ['id' => 3],
    ],
    'complexObject' => [
        'name' => 'test',
    ],
    'complexObjectWithCast' => [
        'name' => 'test',
    ],
    'otherDTO' => ['id' => 5],
]);
```

## Value casts

If a DTO has a property that is another DTO or a DTO collection, the package will take care of automatically casting arrays of data to those DTOs:

```php
$dto = new MyDTO(
    collection: [ // This will become an object of class OtherDTOCollection
        ['id' => 1],
        ['id' => 2], // Each ite will be an instance of OtherDTO
        ['id' => 3],
    ],
    otherDTO: ['id' => 5], // This data will be cast to OtherDTO
);
```

### Custom casters

You can build your own caster classes, which will take whatever input they are given, and will cast that input to the desired result.

Take a look at the `ComplexObject`:

```php
class ComplexObject
{
    public string $name;
}
```

And its caster `ComplexObjectCaster`:

```php
use Spatie\DataTransferObject\Caster;

class ComplexObjectCaster implements Caster
{
    /**
     * @param array|mixed $value
     *
     * @return mixed
     */
    public function cast(mixed $value): ComplexObject
    {
        return new ComplexObject(
            name: $value['name']
        );
    }
}
```

### Class-specific casters

Instead of specifying which caster should be used for each property, you can also define that caster on the target class itself:

```php
class MyDTO extends DataTransferObject
{
    public ComplexObjectWithCast $complexObjectWithCast;
}
```

```php
#[CastWith(ComplexObjectWithCastCaster::class)]
class ComplexObjectWithCast
{
    public string $name;
}
```

### General casters

TODO

```php
#[
    DefaultCast(DateTimeImmutable::class, DateTimeImmutableCaster::class),
    DefaultCast(Enum::class, EnumCaster::class),
]
abstract class BaseDataTransferObject extends DataTransferObject
{
}
```

## Validation

This package doesn't offer any specific validation functionality, but it does give you a way to build your own validation attributes. For example, `NumberBetween` is a user-implemented validation attribute:

```php
class MyDTO extends DataTransferObject
{
    #[NumberBetween(1, 100)]
    public int $a;
}
```

It works like this under the hood:

```php
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class NumberBetween implements Validator
{
    public function __construct(
        private int $min,
        private int $max
    ) {
    }

    public function validate(mixed $value): ValidationResult
    {
        if ($value < $this->min) {
            return new ValidationResult(false, "Value should be greater than or equal to {$this->min}");
        }

        if ($value > $this->max) {
            return new ValidationResult(false, "Value should be less than or equal to {$this->max}");
        }

        return new ValidationResult(true);
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

## Credits

- [Brent Roose](https://github.com/brendt)
- [All Contributors](../../contributors)

Our `Arr` class contains functions copied from Laravels `Arr` helper.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
