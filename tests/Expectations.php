<?php

use PHPUnit\Framework\Assert;
use Pest\Exceptions\InvalidExpectationValue;

expect()->extend('toHaveExactKeys', function (array $keys) {
    $this->toHaveKeys($keys);

    $extraKeys = collect($this->value)->dot()->keys()->diff($keys)->values()->all();

    if (! empty($extraKeys)) {
        throw new Exception("Failed asserting that an array has only the expected keys; found an extra key '{$extraKeys[0]}'");
    }

    return $this;
});

expect()->extend('toHaveRandom', function (int $number, array $array, string $message = '') {
    if (! is_array($this->value)) {
        InvalidExpectationValue::expected('array');
    }

    Assert::assertCount($number, array_intersect_assoc($array, $this->value), $message);

    return $this;
});
