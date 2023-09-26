<?php

use AhsanDev\Support\Arr;

test('wraps specified keys in a "meta" sub-array', function () {
    $result = Arr::wrapInMeta(
        data: [
            'key1' => 'value1',
            'key2' => 'value2'
        ],
        keysToWrap: ['key2']
    );

    expect($result)->toHaveExactKeys(['key1', 'meta.key2']);
});

test('does not modify the array if the specified key is not present', function () {
    $result = Arr::wrapInMeta(
        data: ['key1' => 'value1'],
        keysToWrap: ['key2']
    );

    expect($result)->toHaveExactKeys(['key1']);
});

test('wraps specified keys in a "meta" sub-array when other keys are not present', function () {
    $result = Arr::wrapInMeta(
        data: ['key1' => 'value1'],
        keysToWrap: ['key1']
    );

    expect($result)->toHaveExactKeys(['meta.key1']);
});
