<?php

namespace AhsanDev\Support;

use DateTime;
use DateTimeZone;

/**
 * The Timezonelist facade.
 *
 * @author Jackie Do <anhvudo@gmail.com>
 */
class Timezonelist
{
    /**
     * Whitespace seperate
     */
    const WHITESPACE_SEP = '&nbsp;&nbsp;&nbsp;&nbsp;';

    /**
     * All continents of the world
     *
     * @var array
     */
    protected $continents = [
        'Africa' => DateTimeZone::AFRICA,
        'America' => DateTimeZone::AMERICA,
        'Antarctica' => DateTimeZone::ANTARCTICA,
        'Arctic' => DateTimeZone::ARCTIC,
        'Asia' => DateTimeZone::ASIA,
        'Atlantic' => DateTimeZone::ATLANTIC,
        'Australia' => DateTimeZone::AUSTRALIA,
        'Europe' => DateTimeZone::EUROPE,
        'Indian' => DateTimeZone::INDIAN,
        'Pacific' => DateTimeZone::PACIFIC,
    ];

    /**
     * Format to display timezones
     *
     * @param  string  $timezone
     * @param  string  $continent
     * @return string
     */
    protected function formatTimezone($timezone, $continent, $htmlencode = true)
    {
        $actualTimezone = $timezone;

        $time = new DateTime(null, new DateTimeZone($timezone));
        $offset = $time->format('P');

        if ($htmlencode) {
            $offset = str_replace('-', ' &minus; ', $offset);
            $offset = str_replace('+', ' &plus; ', $offset);
        }

        $timezone = substr($timezone, strlen($continent) + 1);
        $timezone = str_replace('St_', 'St. ', $timezone);
        $timezone = str_replace('_', ' ', $timezone);

        $formatted = '(GMT/UTC '.$offset.')'.($htmlencode ? self::WHITESPACE_SEP : ' ').$timezone;
        $formatted = '(GMT/UTC '.$offset.')'.' '.$actualTimezone;

        return $formatted;
    }

    /**
     * Create a timezone array
     *
     * @return mixed
     **/
    public function toArray($htmlencode = true)
    {
        $list = [];

        // Add all timezone of continents to list
        foreach ($this->continents as $continent => $mask) {
            $timezones = DateTimeZone::listIdentifiers($mask);

            foreach ($timezones as $timezone) {
                // $list[$timezone] = $this->formatTimezone($timezone, $continent, $htmlencode);

                $time = new DateTime(null, new DateTimeZone($timezone));
                $offset = $time->format('P');

                // $list[$offset] = $timezone . ' || ' . $this->formatTimezone($timezone, $continent, $htmlencode);
                $list[$offset.' - '.$timezone] = $this->formatTimezone($timezone, $continent, $htmlencode);
            }
        }

        $sorted = collect($list)->sortKeys()->all();

        $currentList = collect($sorted)->map(function ($label, $value) {
            // dd(explode(' - ', $value)[1]);
            // return compact('label', 'value');
            return ['label' => $label, 'value' => explode(' - ', $value)[1]];
        })->values();

        $data = $currentList->all();

        array_unshift($data, ['label' => 'UTC', 'value' => 'UTC']);

        return $data;
    }
}
