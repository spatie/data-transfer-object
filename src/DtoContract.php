<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 27.03.19
 * Time: 13:40.
 */

namespace Spatie\DataTransferObject;

interface DtoContract
{
    public function all() :array;

    public function only(string ...$keys) :DtoContract;

    public function except(string ...$keys) :DtoContract;

    public function toArray(): array;
}
