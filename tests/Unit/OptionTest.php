<?php

use AhsanDev\Support\Contracts\Option as OptionContract;
use AhsanDev\Support\Facades\Option;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use function Pest\Laravel\{assertDatabaseCount, assertDatabaseHas};

it('retrieves an option when not cached', function () {
    DB::table('options')->insert([
        'key' => 'key',
        'value' => 'db_value'
    ]);

    expect(Option::get('key'))
        ->toBe('db_value');

    expect(Cache::has('localhost.option.key'))
        ->toBeTrue();
});

it('retrieves a cached option by key', function () {
    Cache::put('localhost.option.key', 'cached_value');

    expect(Option::get('key'))
        ->toBe('cached_value');

    assertDatabaseCount('options', 0);
});

it('retrieves a default value when option does not exist')
    ->expect(fn () => Option::get('key', 'default'))
    ->toBe('default');

it('puts a single option value', function () {
    expect(Option::put('key', 'value'))
        ->toBeTrue();

    assertDatabaseHas('options', ['key' => 'key', 'value' => 'value']);
});

it('puts multiple option values', function () {
    expect(Option::put([
        'key1' => 'value1',
        'key2' => 'value2'
    ]))->toBeTrue();

    assertDatabaseHas('options', ['key' => 'key1', 'value' => 'value1']);
    assertDatabaseHas('options', ['key' => 'key2', 'value' => 'value2']);
});

it('throws exception when putting non-associative array', function () {
    expect(fn () => Option::put(['value1', 'value2']))->toThrow(InvalidArgumentException::class);
});

test('option helper', function () {
    // 1. option()
    expect(option())->toBeInstanceOf(OptionContract::class);

    // 2. option(['foo' => 'bar']);
    Option::shouldReceive('put')->once()->with(['foo' => 'bar']);
    option(['foo' => 'bar']);

    // 3. option('foo');
    Option::shouldReceive('get')->once()->with('foo', null)->andReturn('bar');
    expect(option('foo'))->toBe('bar');

    // 4. option('baz', 'default');
    Option::shouldReceive('get')->once()->with('baz', 'default')->andReturn('default');
    expect(option('baz', 'default'))->toBe('default');
});
