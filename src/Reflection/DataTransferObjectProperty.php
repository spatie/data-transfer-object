<?php

namespace Spatie\DataTransferObject\Reflection;

use Faker\Factory;
use Faker\Generator;
use JetBrains\PhpStorm\Immutable;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Attributes\DefaultCast;
use Spatie\DataTransferObject\Caster;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Validator;

class DataTransferObjectProperty
{
    #[Immutable]
    public string $name;

    private DataTransferObject $dataTransferObject;

    private ReflectionProperty $reflectionProperty;

    private ?Caster $caster;

    private static ?Generator $faker = null;

    public function __construct(
        DataTransferObject $dataTransferObject,
        ReflectionProperty $reflectionProperty
    ) {
        $this->dataTransferObject = $dataTransferObject;
        $this->reflectionProperty = $reflectionProperty;

        $this->name = $this->reflectionProperty->name;

        $this->caster = $this->resolveCaster();
    }

    public static function withFaker( $faker): void
    {
        static::$faker = $faker;
    }

    protected static function faker(): Generator
    {
        if (static::$faker === null) {
            static::withFaker(Factory::create());
        }

        return static::$faker;
    }

    public function fake(): mixed
    {
        if ($this->reflectionProperty->hasDefaultValue()) {
            return $this->reflectionProperty->getDefaultValue();
        }

        $faker = static::faker();
        $type = $this->reflectionProperty->getType();

        if (! $type) {
            return $faker->word;
        }

        /** @var ReflectionNamedType[] $types */
        $types = match ($type::class) {
            ReflectionNamedType::class => [$type],
            ReflectionUnionType::class => $type->getTypes(),
        };

        usort($types, fn(ReflectionNamedType $a, ReflectionNamedType $b) => $b->isBuiltin() - $a->isBuiltin());

        foreach ($types as $type) {
            $name = $type->getName();

            if ($type->isBuiltin()) {
                return match ($name) {
                    'float' => $faker->randomFloat(),
                    'int' => $faker->randomNumber(),
                    'string' => $faker->word,
                    'bool' => $faker->boolean,
                    'array' => [],
                };
            }

            if (class_exists($name)) {
                if (is_subclass_of($name, DataTransferObject::class)) {
                    return $name::fake();
                }

                return new $name;
            }
        }

        return null;
    }

    public function setValue(mixed $value): void
    {
        if ($this->caster && $value !== null) {
            $value = $this->caster->cast($value);
        }

        $this->reflectionProperty->setValue($this->dataTransferObject, $value);
    }

    /**
     * @return \Spatie\DataTransferObject\Validator[]
     */
    public function getValidators(): array
    {
        $attributes = $this->reflectionProperty->getAttributes(
            Validator::class,
            ReflectionAttribute::IS_INSTANCEOF
        );

        return array_map(
            fn (ReflectionAttribute $attribute) => $attribute->newInstance(),
            $attributes
        );
    }

    public function getValue(): mixed
    {
        return $this->reflectionProperty->getValue($this->dataTransferObject);
    }

    private function resolveCaster(): ?Caster
    {
        $attributes = $this->reflectionProperty->getAttributes(CastWith::class);

        if (! count($attributes)) {
            $attributes = $this->resolveCasterFromType();
        }

        if (! count($attributes)) {
            return $this->resolveCasterFromDefaults();
        }

        /** @var \Spatie\DataTransferObject\Attributes\CastWith $attribute */
        $attribute = $attributes[0]->newInstance();

        return new $attribute->casterClass(
            $this->reflectionProperty->getType()?->getName(),
            ...$attribute->args,
        );
    }

    private function resolveCasterFromType(): array
    {
        $type = $this->reflectionProperty->getType();

        if (! $type) {
            return [];
        }

        /** @var ReflectionNamedType[]|null $types */
        $types = match ($type::class) {
            ReflectionNamedType::class => [$type],
            ReflectionUnionType::class => $type->getTypes(),
        };

        foreach ($types as $type) {
            if (! class_exists($type->getName())) {
                continue;
            }

            $reflectionClass = new ReflectionClass($type->getName());

            do {
                $attributes = $reflectionClass->getAttributes(CastWith::class);

                $reflectionClass = $reflectionClass->getParentClass();
            } while (! count($attributes) && $reflectionClass);

            if (count($attributes) > 0) {
                return $attributes;
            }
        }

        return [];
    }

    private function resolveCasterFromDefaults(): ?Caster
    {
        $defaultCastAttributes = [];

        $class = $this->reflectionProperty->getDeclaringClass();

        do {
            array_push($defaultCastAttributes, ...$class->getAttributes(DefaultCast::class));

            $class = $class->getParentClass();
        } while ($class !== false);

        if (! count($defaultCastAttributes)) {
            return null;
        }

        foreach ($defaultCastAttributes as $defaultCastAttribute) {
            /** @var \Spatie\DataTransferObject\Attributes\DefaultCast $defaultCast */
            $defaultCast = $defaultCastAttribute->newInstance();

            if ($defaultCast->accepts($this->reflectionProperty)) {
                return $defaultCast->resolveCaster();
            }
        }

        return null;
    }
}
