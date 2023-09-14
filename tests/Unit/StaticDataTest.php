<?php

use AhsanDev\Support\Facades\Option;
use AhsanDev\Support\Contracts\StaticData as StaticDataContract;
use AhsanDev\Support\StaticData;

beforeEach(function () {
    $this->staticData = new StaticData;

    $this->reflection = $reflection = new ReflectionClass($this->staticData);

    $reflection->getProperty('items')
        ->setValue($this->staticData, [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        ]);
});

describe('implements', function () {
    it('implements JsonSerializable')
        ->expect(fn () => $this->staticData)
        ->toBeInstanceOf(JsonSerializable::class);

    it('implements StaticDataContract')
        ->expect(fn () => $this->staticData)
        ->toBeInstanceOf(StaticDataContract::class);
});

describe('get', function () {
    it('returns the value for a valid key')
        ->expect(fn () => $this->staticData->get('key1'))
        ->toBe('value1');

    it('returns null for an invalid key')
        ->expect(fn () => $this->staticData->get('nonexistent_key'))
        ->toBeNull();

    it('returns the default value for a missing key when specified')
        ->expect(fn () => $this->staticData->get('nonexistent_key', 'default_value'))
        ->toBe('default_value');
});

describe('all', function () {
    it('returns all items as an array')
        ->expect(fn () => $this->staticData->all())
        ->toBeArray()
        ->toHaveExactKeys(['key1', 'key2', 'key3']);
});

describe('default', function () {
    it('returns the default item', function () {
        Option::shouldReceive('get')
            ->with('default_static_data', null)
            ->andReturn('key2');

        expect($this->staticData->default())->toBe('value2');
    });

    it('throws exception when key is missing in options table', function () {
        Option::shouldReceive('get')
            ->with('default_static_data', null)
            ->andReturn(null);

        expect(fn () => $this->staticData->default())
            ->toThrow(InvalidArgumentException::class);
    });

    it('throws exception when default item does not exist')
        ->expect(fn () => fn () => (new StaticData)->default())
        ->toThrow(InvalidArgumentException::class);
});

describe('random', function () {
    it('returns a random item')
        ->expect(fn () => $this->staticData->random())
        ->toBeIn(['value1', 'value2', 'value3']);

    it('throws exception when items array empty', fn () =>
        expect(fn () => (new StaticData)->random())
            ->toThrow(InvalidArgumentException::class)
    );

    it('returns an array with specified number of items')
        ->expect(fn () => $this->staticData->random(2))
        ->toHaveCount(2);

    it('preserves keys')
        ->expect(fn () => array_intersect_assoc(
            $this->staticData->all(),
            $this->staticData->random(2, true))
        )->toHaveCount(2);

    // duplicate test above
    it('preserves keys random pest way', fn () =>
        expect($this->staticData->random(2, true))
            ->toHaveRandom(2, $this->staticData->all())
    );
});

describe('options', function () {
    it('returns items as options')
        ->expect(fn () => $this->staticData->options()[0])
        ->toHaveExactKeys(['label', 'value']);
});

describe('default key', function () {
    it('returns the default key based on the class name', function () {
        $method = $this->reflection->getMethod('getDefaultKey')->invoke($this->staticData);

        expect($method)->toBe('default_static_data');
    });

    it('returns the default key when explicitly set', function () {
        $this->reflection->getProperty('defaultKey')
            ->setValue($this->staticData, 'my_default_key');

        $method = $this->reflection->getMethod('getDefaultKey')
            ->invoke($this->staticData);

        expect($method)->toBe('my_default_key');
    });
});
