<?php

namespace AhsanDev\Support;

use Carbon\Carbon;
use JsonSerializable;

class Recurring implements JsonSerializable
{
    /**
     * Determine it's iteration request.
     *
     * @var bool
     */
    protected $isIteration = false;

    /**
     * Determine it's without iteration.
     *
     * @var bool
     */
    protected $isWithoutIteration = false;

    /**
     * The fields.
     *
     * @var array
     */
    protected $fields = [
        'type' => null,
        'every' => 1,
        'hour_at' => 0,
        'day_at' => 0,
        'month_at' => 0,
        'repetitions' => 1,
        'current_iteration' => -1,
        'starts_at' => null,
        'ends_at' => null,
        'ends' => 'never',
        'task_completion_required' => true,
        'repeat_on' => [],
        'phrase' => '',
        // 'repeat_on' => ['Mon', 'Tue', 'Wed'],
        // 'repeat_on' => [1, 15, 30],
        // 'repeat_on' => [['15', '6'], ['15', '12']],
    ];

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  array  $options
     * @return $this
     */
    public function __construct($data = [])
    {
        $methods = [
            'daily' => 'setDaily',
            'weekly' => 'setWeekly',
            'monthly' => 'setMonthly',
            'yearly' => 'setYearly',
        ];

        $method = $methods[$data['type']];

        $this->$method($data);
    }

    /**
     * Get recurring pattern.
     *
     * @return array
     */
    public function pattern()
    {
        return $this->fields;
    }

    /**
     * Set daily recurring pattern.
     *
     * @return void
     */
    public function setDaily($data)
    {
        $attributes = [
            'type' => $data['type'],
            'every' => $data['every'],
            'hour_at' => $data['hour_at'],
            'task_completion_required' => $data['task_completion_required'],
            'ends' => $data['ends'],
            'repetitions' => $data['repetitions'],
            'ends_at' => $data['ends_at'],
            'current_iteration' => $data['current_iteration'] ?? -1,
            'phrase' => $this->dailyPhrase($data),
        ];

        $this->fields = array_merge($this->fields, $attributes);
    }

    /**
     * Set weekly recurring pattern.
     *
     * @return void
     */
    public function setWeekly($data)
    {
        $attributes = [
            'type' => $data['type'],
            'every' => $data['every'],
            'task_completion_required' => $data['task_completion_required'],
            'ends' => $data['ends'],
            'ends_at' => $data['ends_at'],
            'repetitions' => $data['repetitions'],
            'current_iteration' => $data['current_iteration'] ?? -1,
            'repeat_on' => $this->sortWeekdays($data['repeat_on']),
            'phrase' => $this->weeklyPhrase($data),
        ];

        $this->fields = array_merge($this->fields, $attributes);
    }

    /**
     * Set monthly recurring pattern.
     *
     * @return void
     */
    public function setMonthly($data)
    {
        $attributes = [
            'type' => $data['type'],
            'every' => $data['every'],
            'task_completion_required' => $data['task_completion_required'],
            'ends' => $data['ends'],
            'ends_at' => $data['ends_at'],
            'repetitions' => $data['repetitions'],
            'current_iteration' => $data['current_iteration'] ?? -1,
            'day_at' => $data['day_at'],
            'phrase' => $this->monthlyPhrase($data),
        ];

        $this->fields = array_merge($this->fields, $attributes);
    }

    /**
     * Set yearly recurring pattern.
     *
     * @return void
     */
    public function setYearly($data)
    {
        $attributes = [
            'type' => $data['type'],
            'every' => $data['every'],
            'task_completion_required' => $data['task_completion_required'],
            'ends' => $data['ends'],
            'ends_at' => $data['ends_at'],
            'repetitions' => $data['repetitions'],
            'current_iteration' => $data['current_iteration'] ?? -1,
            'day_at' => $data['day_at'],
            'month_at' => $data['month_at'],
            'phrase' => $this->yearlyPhrase($data),
        ];

        $this->fields = array_merge($this->fields, $attributes);
    }

    /**
     * Increase current iteration.
     *
     * @return void
     */
    public function increaseIteration()
    {
        if ($this->isWithoutIteration) {
            return false;
        }

        $this->fields['current_iteration']++;

        // dd( ++$this->fields['current_iteration'] );
        // $this->fields['current_iteration'] = $this->fields['current_iteration'] + 1;
    }

    /**
     * Create a next iteration date.
     *
     * @return \Illuminate\Support\Carbon
     */
    public function nextIteration()
    {
        if ($this->fields['ends'] == 'on' && $this->fields['ends_at']) {
            $endsAt = new Carbon($this->fields['ends_at']);

            if ($endsAt->isPast()) {
                return null;
            }
        }

        if ($this->fields['ends'] == 'after' && $this->fields['repetitions'] === ($this->fields['current_iteration'] + 1)) {
            return null;
        }

        $iterationMethods = [
            'daily' => 'nextIterationDaily',
            'weekly' => 'nextIterationWeekly',
            'monthly' => 'nextIterationMonthly',
            'yearly' => 'nextIterationYearly',
        ];

        $method = $iterationMethods[$this->fields['type']];

        return $this->$method();
    }

    /**
     * Create a next iteration based on daily pattern.
     *
     * @return \Illuminate\Support\Carbon
     */
    public function nextIterationDaily()
    {
        if ($this->fields['current_iteration'] == -1 && now()->hour < $this->fields['hour_at']) {
            $this->increaseIteration();

            return today()->hour($this->fields['hour_at']);
        }

        $this->increaseIteration();

        return today()->hour($this->fields['hour_at'])->addDay(1 * $this->fields['every']);
    }

    /**
     * Create a next iteration based on weekly pattern.
     *
     * @return \Illuminate\Support\Carbon
     */
    public function nextIterationWeekly()
    {
        // dd( in_array(now()->shortDayName, $this->fields['repeat_on']) );
        // dd( now()->shortDayName );
        if ($this->fields['current_iteration'] == -1 && in_array(now()->shortDayName, $this->fields['repeat_on'])) {
            // dd('hitting first time if same day!');
            $this->increaseIteration();

            // dd( today()->hour($this->fields['hour_at']) );
            // dd( now()->previous('Sat') );
            // dd( now()->hour(23)->next('Sat')->hour(23) );
            // dd( now()->hour(23)->next('Sat') );
            return today();
        }

        $this->increaseIteration();

        // dd( today()->shortDayName );
        return today()->next($this->nextRepeatOn());
    }

    /**
     * Create a next iteration based on monthly pattern.
     *
     * @return \Illuminate\Support\Carbon
     */
    public function nextIterationMonthly()
    {
        if ($this->fields['current_iteration'] == -1) {
            $isPast = $this->fields['day_at'] == 'lastday'
                ? (new Carbon('last day of this month'))->startOfDay()->isPast()
                : today()->day($this->fields['day_at'])->isPast();

            $this->increaseIteration();

            if ($isPast) {
                return $this->fields['day_at'] == 'lastday'
                            ? today()->addMonths($this->fields['every'])->endOfMonth()->startOfDay()
                            : today()->addMonth($this->fields['every'])->day($this->fields['day_at']);
            } else {
                return $this->fields['day_at'] == 'lastday'
                    ? (new Carbon('last day of this month'))->startOfDay()
                    : today()->day($this->fields['day_at']);
            }
        }

        $this->increaseIteration();

        return $this->fields['day_at'] == 'lastday'
            ? today()->addMonths($this->fields['every'])->endOfMonth()->startOfDay()
            : today()->addMonth($this->fields['every']);
    }

    /**
     * Create a next iteration based on yearly pattern.
     *
     * @return \Illuminate\Support\Carbon
     */
    public function nextIterationYearly()
    {
        if ($this->fields['current_iteration'] == -1) {
            $isPast = $this->fields['day_at'] == 'lastday'
                ? today()->month($this->fields['month_at'])->endOfMonth()->startOfDay()->isPast()
                : today()->month($this->fields['month_at'])->day($this->fields['day_at'])->isPast();

            $this->increaseIteration();

            if ($isPast) {
                return $this->fields['day_at'] == 'lastday'
                    ? today()->addYear($this->fields['every'])->month($this->fields['month_at'])->endOfMonth()->startOfDay()
                    : today()->addYear($this->fields['every'])->month($this->fields['month_at'])->day($this->fields['day_at']);
            } else {
                return $this->fields['day_at'] == 'lastday'
                    ? today()->month($this->fields['month_at'])->endOfMonth()->startOfDay()
                    : today()->month($this->fields['month_at'])->day($this->fields['day_at']);
            }
        }

        $this->increaseIteration();

        return $this->fields['day_at'] == 'lastday'
            ? today()->addYear($this->fields['every'])->endOfMonth()->startOfDay()
            : today()->addYear($this->fields['every']);
    }

    /**
     * Create a new element.
     *
     * @return static
     */
    protected function nextRepeatOn()
    {
        // ['Mon', 'Wed', 'Fri', 'Sat'],

        // dd('nextRepeatOn!');
        // dd( today()->shortDayName );
        $key = array_search(today()->shortDayName, $this->fields['repeat_on']);
        $total = array_key_last($this->fields['repeat_on']);

        // dd($key);
        // dd(++$key <= $total);
        if (++$key <= $total) {
            return $this->fields['repeat_on'][$key];
        }
        // dd( $key > $total );

        // what if there is single item in the array.
        // i guest it's working fine :)
        if ($key > $total) {
            return $this->fields['repeat_on'][0];
        }

        // $key = array_search('Tue', $arr);
        // $total = array_key_last($arr);
        // dd($total);
        // dd(--$key);

        // dd( array_search('Tue', $arr) );
        // dd( array_search('Tue', $arr) );
        // dd( array_search('Tue', $arr) );
        // dd( array_search('Tue', $arr) );

        // return 'Wed';
    }

    /**
     * Sort weekdays.
     *
     * @return array
     */
    protected function sortWeekdays($data = [])
    {
        $original = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        return collect($data)
                ->sortBy(fn ($item) => array_search($item, $original))
                ->values()
                ->all();
    }

    /**
     * Prepare recurring phrases.
     *
     * @return string
     */
    protected function dailyPhrase($data = [])
    {
        $phrase = '';

        if ($data['ends'] == 'after' && $data['repetitions'] == 1) {
            return 'Once';
        }

        if ($data['every'] == 1) {
            $phrase = 'every day';
        } else {
            $phrase = "every {$data['every']} days";
        }

        if ($data['hour_at'] > 0) {
            // $phrase .= " at {$data['hour_at']}:00";
            $phrase .= ' at '.sprintf('%02d:00', $data['hour_at']);
        }

        if ($data['ends'] == 'on' && $data['ends_at']) {
            $phrase .= ', until '.today()->create($data['ends_at'])->toFormattedDateString();
        }

        if ($data['ends'] == 'after' && $data['repetitions'] > 1) {
            $phrase .= ", {$data['repetitions']} times";
        }

        return $phrase;
    }

    /**
     * Prepare recurring phrases.
     *
     * @return string
     */
    protected function weeklyPhrase($data = [])
    {
        $phrase = '';

        if ($data['ends'] == 'after' && $data['repetitions'] == 1) {
            return 'Once';
        }

        if ($data['every'] == 1) {
            $weekDays = count($data['repeat_on']) == 7 ? 'all days' : implode(', ', $data['repeat_on']);

            $phrase = "Weekly on {$weekDays}";
        } else {
            $weekDays = count($data['repeat_on']) == 7 ? 'all days' : implode(', ', $data['repeat_on']);

            $phrase = "Every {$data['every']} weeks on {$weekDays}";
        }

        if ($data['ends'] == 'on' && $data['ends_at']) {
            $phrase .= ', until '.today()->create($data['ends_at'])->toFormattedDateString();
        }

        if ($data['ends'] == 'after' && $data['repetitions'] > 1) {
            $phrase .= ", {$data['repetitions']} times";
        }

        return $phrase;
    }

    /**
     * Prepare recurring phrases.
     *
     * @return string
     */
    protected function monthlyPhrase($data = [])
    {
        $phrase = '';

        if ($data['ends'] == 'after' && $data['repetitions'] == 1) {
            return 'Once';
        }

        if ($data['every'] == 1) {
            $day = $data['day_at'] == 'lastday' ? 'last day' : 'day '.$data['day_at'];

            $phrase = "Monthly on {$day}";
        } else {
            $day = $data['day_at'] == 'lastday' ? 'last day' : 'day '.$data['day_at'];

            $phrase = "Every {$data['every']} months on {$day}";
        }

        if ($data['ends'] == 'on' && $data['ends_at']) {
            $phrase .= ', until '.today()->create($data['ends_at'])->toFormattedDateString();
        }

        if ($data['ends'] == 'after' && $data['repetitions'] > 1) {
            $phrase .= ", {$data['repetitions']} times";
        }

        return $phrase;
    }

    /**
     * Prepare recurring phrases.
     *
     * @return string
     */
    protected function yearlyPhrase($data = [])
    {
        $phrase = '';

        if ($data['ends'] == 'after' && $data['repetitions'] == 1) {
            return 'Once';
        }

        if ($data['every'] == 1) {
            $day = $data['day_at'];
            $month = today()->create($data['month_at'])->format('M');
            $date = $day == 'lastday' ? 'the last day of '.$month : "{$month} {$day}";

            $phrase = "Annually on {$date}";
        } else {
            $day = $data['day_at'];
            $month = today()->create($data['month_at'])->format('M');
            $date = $day == 'lastday' ? 'the last day of '.$month : "{$month} {$day}";

            $phrase = "Every {$data['every']} years on {$date}";
        }

        if ($data['ends'] == 'on' && $data['ends_at']) {
            $phrase .= ', until '.today()->create($data['ends_at'])->toFormattedDateString();
        }

        if ($data['ends'] == 'after' && $data['repetitions'] > 1) {
            $phrase .= ", {$data['repetitions']} times";
        }

        return $phrase;
    }

    /**
     * Create a new element.
     *
     * @return static
     */
    public function iteration()
    {
        $this->isIteration = true;
    }

    /**
     * Create a new element.
     *
     * @return static
     */
    public function updateNextDateWithoutIteration()
    {
        $this->isWithoutIteration = true;
    }

    /**
     * Create a new element.
     *
     * @return static
     */
    public static function make($data = [])
    {
        return new static($data);
    }

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->fields;
    }
}
