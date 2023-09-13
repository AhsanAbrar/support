<?php

use AhsanDev\Support\Facades\Option;
use AhsanDev\Support\Contracts\StaticData as StaticDataContract;
use AhsanDev\Support\StaticData;

beforeEach(function () {
    $this->staticData = new StaticData;

    $reflection = new ReflectionClass($this->staticData);

    $reflection->getProperty('default')
        ->setValue($this->staticData, 'key2');

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
    it('returns the default item')
        ->expect(fn () => $this->staticData->default())
        ->toBe('value2');

    it('returns the default item mocked', function () {
        Option::shouldReceive('get')->with('key2', 'key2')->andReturn('key2');

        expect($this->staticData->default())
            ->toBe('value2');
    });

    it('throws exception when default item does not exist')
        ->expect(fn () => fn () => (new StaticData)->default())
        ->toThrow(InvalidArgumentException::class);
});

describe('random', function () {
    it('returns a random item')
        ->expect(fn () => $this->staticData->random())
        ->toBeIn(['value1', 'value2', 'value3']);

    it('throws exception when items array empty')
        ->expect(fn () => fn () => (new StaticData)->random())
        ->toThrow(InvalidArgumentException::class);

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
            ->toHaveRandom(2, $this->staticData->all()));
});

describe('options', function () {
    it('returns items as options')
        ->expect(fn () => $this->staticData->options()[0])
        ->toHaveExactKeys(['label', 'value']);
});
