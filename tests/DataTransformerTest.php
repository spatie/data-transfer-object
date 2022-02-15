<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\DataTransformer;
use Spatie\DataTransferObject\Attributes\TransformWith;

class DataTransformerTest extends TestCase
{
    /** @test */
    public function dto_is_transformed_with_single_data_transformer()
    {
        $input = ['full_name' => 'John Doe', 'result' => [3, 1, 4, 0, 0, 7]];
        $dto = new UserResult($input);

        $this->assertEquals('John', $dto->firstName);
        $this->assertEquals('Doe', $dto->lastName);
        $this->assertEquals(2.5, $dto->result);
    }

    /** @test */
    public function dto_is_transformed_with_many_data_transformers()
    {
        $input = ['full_name' => 'John Doe', 'result' => [3, 1, 4, 0, 0, 7]];
        $dto = new UserResultWithDetails($input);

        $this->assertEquals('John', $dto->firstName);
        $this->assertEquals('Doe', $dto->lastName);
        $this->assertEquals(2.5, $dto->result);
        $this->assertEquals('John Doe\'s result is 2.5 and 2 answers are incorrect.', $dto->resultInterpretation);
    }
}

#[TransformWith(UserResultDataTransformer::class)]
class UserResult extends DataTransferObject
{
    public string $firstName;
    public string $lastName;
    public float $result;
}

#[TransformWith(UserResultDataTransformer::class)]
#[TransformWith(ResultInterpretationDataTransformer::class)]
class UserResultWithDetails extends UserResult
{
    public string $resultInterpretation;
}

class UserResultDataTransformer implements DataTransformer
{
    public function transform(DataTransferObject|UserResult $object, ...$args): void
    {
        $fullName = explode(' ', $args[0]['full_name']);

        $object->firstName = $fullName[0];
        $object->lastName = $fullName[1];
        $object->result = array_sum($args[0]['result']) / count($args[0]['result']);
    }

    public function argsToForget(): array
    {
        return ['result'];
    }
}

class ResultInterpretationDataTransformer implements DataTransformer
{
    public function transform(DataTransferObject|UserResultWithDetails $object, ...$args): void
    {
        $object->resultInterpretation = sprintf(
            '%s\'s result is %.1f and %d answers are incorrect.',
            $args[0]['full_name'],
            $object->result,
            count(array_keys($args[0]['result'], 0))
        );
    }

    public function argsToForget(): array
    {
        return [];
    }
}
