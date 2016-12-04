<?php

namespace _20TRIES;

use _20TRIES\Exceptions\DateRangeException;
use _20TRIES\Exceptions\TimezoneException;
use Carbon\Carbon;
use InvalidArgumentException;

/**
 * A Date Range Object.
 *
 * @method static DateRange thisHour($tz = 'GB')
 * @method static DateRange thisDay($tz = 'GB')
 * @method static DateRange thisWeek($tz = 'GB')
 * @method static DateRange thisMonth($tz = 'GB')
 * @method static DateRange thisYear($tz = 'GB')
 *
 * @method static DateRange nextHour($tz = 'GB')
 * @method static DateRange nextDay($tz = 'GB')
 * @method static DateRange nextWeek($tz = 'GB')
 * @method static DateRange nextMonth($tz = 'GB')
 * @method static DateRange nextYear($tz = 'GB')
 *
 * @method static DateRange lastHour($tz = 'GB')
 * @method static DateRange lastDay($tz = 'GB')
 * @method static DateRange lastWeek($tz = 'GB')
 * @method static DateRange lastMonth($tz = 'GB')
 * @method static DateRange lastYear($tz = 'GB')
 *
 * @method static DateRange forHour($format, $date_time_string, $tz = 'GB')
 * @method static DateRange forDay($format, $date_time_string, $tz = 'GB')
 * @method static DateRange forWeek($format, $date_time_string, $tz = 'GB')
 * @method static DateRange forMonth($format, $date_time_string, $tz = 'GB')
 * @method static DateRange forYear($format, $date_time_string, $tz = 'GB')
 */
class DateRange
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var Carbon
     */
    protected $after;

    /**
     * @var Carbon
     */
    protected $before;

    /**
     * @var \DateTimeZone
     */
    protected $timezone;

    /**
     * @var string A time period
     */
    const HOUR = 'Hour';

    /**
     * @var string A time period
     */
    const DAY = 'Day';

    /**
     * @var string A time period
     */
    const WEEK = 'Week';

    /**
     * @var string A time period
     */
    const MONTH = 'Month';

    /**
     * @var string A time period
     */
    const YEAR = 'Year';

    /**
     * @var string A time period position.
     */
    const START = 'start';

    /**
     * @var string A time period position.
     */
    const END = 'end';

    /**
     * @var array An array of accepted time periods
     */
    public static $time_periods = [self::DAY => self::DAY, self::WEEK => self::WEEK, self::MONTH => self::MONTH, self::YEAR => self::YEAR, self::HOUR => self::HOUR];

    /**
     * @var array Static method aliases that are available for use within the class.
     */
    protected static $dynamic_method_aliases = [
        'today'     => 'thisDay',
        'tomorrow'  => 'nextDay',
        'yesterday' => 'lastDay',
    ];

    protected $timeperiod_formats = [
        self::DAY   => 'l',
        self::WEEK  => '\W\e\ek \S\t\a\r\t\i\n\g dS M',
        self::MONTH => 'F',
        self::YEAR  => 'o',
    ];

    /**
     * Constructor.
     *
     * @param Carbon|null $after
     * @param Carbon|null $before
     * @param null|string $name
     *
     * @throws TimezoneException|DateRangeException
     */
    public function __construct(Carbon $after = null, Carbon $before = null, $name = null)
    {
        $this->name = $name;

        $this->after = $after;

        $this->before = $before;

        if (is_null($after) && is_null($before)) {
            throw new DateRangeException('Either an after date or before date must be provided.');
        } elseif (is_null($after) && !is_null($before)) {
            $this->timezone = $before->getTimezone();
        } elseif (is_null($before)) {
            $this->timezone = $after->getTimezone();
        } else {
            $this->timezone = $this->after->getTimezone();

            $before_tz = is_null($this->before->getTimezone()) ? null : $this->before->getTimezone();

            if ($before_tz->getName() !== $this->timezone->getName()) {
                throw new TimezoneException('Multiple timezones are not supported.');
            }
        }
    }

    /**
     * Handles unknown method calls.
     *
     * @param string $method
     * @param array $args
     * @return static|null
     */
    public static function __callStatic($method, $args)
    {
        $method = array_key_exists($method, self::$dynamic_method_aliases) ? self::$dynamic_method_aliases[$method] : $method;
        $prefix = preg_replace('/[A-Z].*/', '', $method);
        $time_period = substr($method, strlen($prefix));

        // If the method is prefixed with this, next or last and the postfix is a valid timestamp, then we will attempt
        // to generate a date range for the given time period.
        if (in_array($prefix, ['this', 'next', 'last', 'for']) && in_array($time_period, self::$time_periods)) {
            $time_period = self::parseTimePeriod($time_period);

            // Create a date time instance from the input provided.
            if ($prefix === 'for') {
                $date_time = Carbon::createFromFormat(
                    $args[0],
                    $args[1],
                    array_key_exists(2, $args) ? $args[2] : 'GB'
                );
            } else {
                $date_time = Carbon::now($args[0]);
            }

            // Offset the date time according to the language used in the method call.
            $date_time = $time_period === self::HOUR
                ? $date_time->minute(0)->second(0)
                : $date_time->{"startOf{$time_period}"}();

            // Generate a date range.
            switch ($prefix) {
                case "for":
                case "this":
                    return self::forTimePeriod($time_period, $date_time);
                case "next":
                    return self::forTimePeriod($time_period, $date_time->{"add{$time_period}"}());
                case "last":
                    return self::forTimePeriod($time_period, $date_time->{"sub{$time_period}"}());
            }
        }

        $trace = debug_backtrace();
        trigger_error("Undefined method via __callStatic(): {$method} in {$trace[0]['file']} on line {$trace[0]['line']}", E_USER_NOTICE);
        return null;
    }

    /**
     * Creates an inclusive date range between two dates.
     *
     * @param Carbon $from
     * @param Carbon $to
     *
     * @return static
     */
    public static function between(Carbon $from, Carbon $to)
    {
        return new static($from->subSecond(), $to->addSecond());
    }

    /**
     * Gets a date range for a time period.
     *
     * @param string $time_period
     * @param Carbon $date_time
     * @return static
     */
    public static function forTimePeriod($time_period, Carbon $date_time)
    {
        $time_period = self::parseTimePeriod($time_period);
        switch ($time_period) {
            case self::HOUR:
                return static::between(
                    $date_time->copy()->minute(0)->second(0),
                    $date_time->copy()->minute(59)->second(59)
                );
            default:
                return static::between(
                    $date_time->copy()->{"startOf{$time_period}"}(),
                    $date_time->copy()->{"endOf{$time_period}"}()
                );
        }
    }

    /**
     * Gets the timezone for a date range.
     *
     * @return \DateTimeZone|null
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    public static function before(Carbon $date_time)
    {
        return new static(null, $date_time);
    }

    public static function after(Carbon $date_time)
    {
        return new static($date_time, null);
    }

    /**
     * Gets the name for a date range (if set).
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets a carbon instance that represents the date and time that a date range range begins
     * immediately after.
     *
     * @return Carbon
     */
    public function getAfter()
    {
        return (is_null($this->after)) ? $this->after : $this->after->copy();
    }

    /**
     * Gets a carbon instance that represents the earliest datetime within a given date range.
     *
     * @deprecated 0.0.3 Replaced by start method to improve readability; will be removed in 1.0.0
     *
     * @return Carbon
     */
    public function getFrom()
    {
        return $this->getAfter()->addSecond();
    }

    /**
     * Gets the start of a date range.
     *
     * @return Carbon
     */
    public function start()
    {
        return $this->getAfter()->addSecond();
    }

    /**
     * Gets a carbon instance that represents the datetime that a date range range ends immediately
     * before.
     *
     * @return Carbon
     */
    public function getBefore()
    {
        return (is_null($this->before)) ? $this->before : $this->before->copy();
    }

    /**
     * Gets a carbon instance that represents the latest date and time within a given date range.
     *
     * @deprecated 0.0.3 Replaced by end method to improve readability; will be removed in 1.0.0
     *
     * @return Carbon
     */
    public function getTo()
    {
        return $this->getBefore()->subSecond();
    }

    /**
     * Gets the start of a date range.
     *
     * @return Carbon
     */
    public function end()
    {
        return $this->getBefore()->subSecond();
    }

    /**
     * Gets a human readable format for a date range.
     *
     * @TODO Add support for "inverted" date ranges
     * @TODO Add support for "open ended" date ranges
     * @TODO Add support for "open started" date ranges
     * @TODO Add support for different timezones
     *
     * @param string $glue
     *
     * @return string
     */
    public function forHumans($glue = ' to ')
    {
        if (!$this->isBounded()) {
            return $this->toInclusiveString('l jS \of F Y', $glue);
        }

        // Check if time period is a DAY
        $day = $this->spans(self::DAY);

        if (!is_null($day)) {
            if ($day->copy()->startOfDay()->eq(Carbon::today($this->timezone))) {
                // If date is in current week
                return 'Today';
            } elseif ($day->copy()->startOfDay()->eq(Carbon::tomorrow($this->timezone))) {
                // If date is in current week
                return 'Tomorrow';
            } elseif ($day->copy()->startOfDay()->eq(Carbon::yesterday($this->timezone))) {
                // If date is in current week
                return 'Yesterday';
            } elseif ($day->copy()->startOfWeek()->eq(Carbon::now($this->timezone)->startOfWeek())) {
                // If date is in current week
                return 'On '.$day->format('l');
            } elseif ($day->copy()->startOfWeek()->eq(Carbon::now($this->timezone)->subWeek()->startOfWeek())) {
                // If date is in the week previous
                return 'Last '.$day->format('l');
            } elseif ($day->copy()->startOfWeek()->eq(Carbon::now($this->timezone)->addWeek()->startOfWeek())) {
                // If date is in the next week
                return 'Next '.$day->format('l');
            } else {
                return $this->toInclusiveString('l jS \of F Y', $glue);
            }
        }

        // Check if time period is a WEEK
        $week = $this->spans(self::WEEK);
        if (!is_null($week)) {
            if ($week->copy()->startOfWeek()->eq(Carbon::now($this->timezone)->startOfWeek())) {
                // If date is in current week
                return 'This Week';
            } elseif ($week->copy()->startOfWeek()->eq(Carbon::now($this->timezone)->subWeek()->startOfWeek())) {
                // If date is in the week previous
                return 'Last Week';
            } elseif ($week->copy()->startOfWeek()->eq(Carbon::now($this->timezone)->addWeek()->startOfWeek())) {
                // If date is in the next week
                return 'Next Week';
            } else {
                return $this->toInclusiveString('l jS \of F Y', $glue);
            }
        }

        // Check if time period is a single month
        $month = $this->spans(self::MONTH);
        if (!is_null($month)) {
            if ($month->copy()->year != Carbon::now($this->timezone)->year) {
                // If date is in the next week
                return 'In '.$month->format('F Y');
            } else {
                return 'In '.$month->format('F');
            }
        }

        // Check if time period is a single year
        $start_of_range = $this->spans(self::YEAR);

        if (!is_null($start_of_range)) {
            return 'In '.$start_of_range->year;
        }

        return $this->toInclusiveString('l jS \of F Y', $glue);
    }

    public function spansDay()
    {
        return $this->spans(self::DAY);
    }

    public function spansWeek()
    {
        return $this->spans(self::WEEK);
    }

    public function spansMonth()
    {
        return $this->spans(self::MONTH);
    }

    public function spansYear()
    {
        return $this->spans(self::YEAR);
    }

    /**
     * Determines if a date range spans exactly one entire time period.
     *
     * @param $time_period
     * @return Carbon|boolean The beginning of the time period, or null.
     */
    public function spans($time_period)
    {
        $time_period = self::parseTimePeriod($time_period);

        // If the date range does not have a bound then it will not span any time periods.
        if (! $this->isBounded()) {
            return false;
        }

        // After must be positioned at the end of the time period
        $end = self::forTimePeriod($time_period, $this->after)->end();
        if (! $end->eq($this->after)) {
            return;
        }

        // Before must be positioned at the start of the time period
        $start = self::forTimePeriod($time_period, $this->before)->start();
        if (! $start->eq($this->before)) {
            return;
        }

        // The start of the date range should equal the end of the date range set back to the start of its time period.
        // This ensures that the range spans only a single time period; otherwise if we only checked the bounds, they
        // could be two or more time periods apart.
        if (! self::forTimePeriod($time_period, $this->end())->start()->eq($this->start())) {
            return;
        }

        return $this->start();
    }

    /**
     * Determines the time period that a date range spans.
     *
     * @return string|null A string which corresponds to a class constant time period
     *                     or null if the date range does not match any supported time spans.
     */
    public function getTimeperiod()
    {
        foreach (array_keys(self::$time_periods) as $time_period) {
            if (!is_null($this->spans($time_period))) {
                return $time_period;
            }
        }

        return;
    }

    /**
     * Determines if a date range has an upper bound.
     *
     * @return bool
     */
    public function isOpenEnded()
    {
        return is_null($this->before) || (!is_null($this->after) && $this->before->lte($this->after));
    }

    /**
     * Combines a collection of date ranges into a single date range.
     *
     * @param DateRange[] $ranges
     *
     * @return DateRange
     *
     * @TODO Doesn't support open started or open ended ranges.
     * @TODO Doesn't support non-intersecting ranges.
     *
     * @TODO Look at the possibility of implementing an "inersect" method instead.
     */
    public static function combine(array $ranges)
    {
        $range = new static();

        foreach ($ranges as $cur_range) {
            $cur_start = $cur_range->after;
            $cur_end = $cur_range->before;

            if (is_null($range->before) || $cur_end->lt($range->before)) {
                $range->before = $cur_end;
            }

            if (is_null($range->after) || $cur_start->gt($range->after)) {
                $range->after = $cur_start;
            }
        }

        return $range;
    }

    /**
     * Gets the number of days that a date range spans.
     *
     * This is not possible, for obvious reasons, on open ended or open start date ranges.
     *
     * @throws Exception
     *
     * @return int
     */
    public function days()
    {
        if ($this->isOpenEnded() || $this->isOpenStarted()) {
            throw new Exception('The number of days within a range cannot be calculated for ranges that are open ended or open started.');
        }

        return $this->after->diffInDays($this->before);
    }

    /**
     * Determines if a date range has a lower bound.
     *
     * @return bool
     */
    public function isOpenStarted()
    {
        return is_null($this->after) ||  (!is_null($this->before) && $this->before->lte($this->after));
    }

    /**
     * Offsets a DateRange by an offset number of time periods.
     *
     * For example, you could offset 1 day, 6 months, 4 years.
     *
     * @param int        $offset
     * @param string     $time_period   A class constant that represents a time period.
     * @param bool|true  $rollover      A flag that indicates whether dates should be
     *                                  allowed to rollover to the next month if the number of days is greater then
     *                                  the number of days in the current month. For example, if Month A has 31 days
     *                                  and is offset by -1 month to Month B and his month on has 30 days, then the
     *                                  new date would become the first day in Month A; not Month B! If you set
     *                                  rollover to false then the new date would become the last day in Month B and
     *                                  time would remain unchanged; micro seconds are not supported.
     * @param bool|false $keep_position A flag that, if enabled, will ensure that if
     *                                  a date is set to the start or end of the time period, then it will keep this
     *                                  position after the offset. For example, if moved from Month A to Month B, but
     *                                  the date range ends on the last day of the month, then once moved to Month B,
     *                                  the date will be set to the end of this month. This is particularly important
     *                                  because Month A may only have 30 days, meaning that if this is disabled, the
     *                                  new date would be the 30th of Month B; which could have 31 days.
     *
     * @return static
     */
    public function offset($offset, $time_period, $rollover = true, $keep_position = false)
    {
        // First we need to validate the time period.
        if (!array_key_exists($time_period, self::$time_periods)) {
            throw new \InvalidArgumentException('Time period must be one of the time periods listed in the class constants');
        }

        // Now we will determine which method will be used to make the offset adjustment. If the
        // offset is greater then 0 we will use the "add" prefix for the start of the method name,
        // otherwise we will use the "sub" prefix. For example, addMonth(), subMonth().
        $offset_method = $offset > 0 ? "add$time_period" : "sub$time_period";

        // Make a copy of the original dates.
        $original_dates = [];

        $original_dates['after']['date'] = is_null($this->after) ? null : $this->after->copy();

        $original_dates['after']['position'] = is_null($this->after) ? null : $this->getDatePositionIn($this->after->copy(), $time_period);

        $original_dates['before']['date'] = is_null($this->before) ? null : $this->before->copy();

        $original_dates['before']['position'] = is_null($this->before) ? null : $this->getDatePositionIn($this->before->copy(), $time_period);

        // Calculate the new dates.
        $new_dates = [];

        foreach (['after', 'before'] as $date_name) {
            if (is_null($this->$date_name)) {
                $new_dates[$date_name] = null;

                continue;
            }

            $original_date = &$original_dates[$date_name]['date'];

            // If the time period is MONTH or YEAR then we will need to handle these differently to
            // ensure that rollover is handled correctly; rollover issues are only seen with month
            // and year because of the differences in the number of days that each may container.
            // Some months have more days then over; some years have more days then others; but all
            // days have the same number of hours, minutes etc.
            if ($time_period == self::MONTH) {
                $new_dates[$date_name] = $this->$date_name->copy()->month($original_date->month + $offset);

                $new_date = &$new_dates[$date_name];

                // Now if rollover should be disabled we will check to see whether the new date has
                // a month with the same value that we get from a local method that calculates the
                // new month after being offset without rollover. If these two values do not match
                // then we will refactor the new date.
                if ($rollover == false && $this->offsetMonthInDate($original_date, $offset) != $new_date->month) {
                    $new_date = $this->refactorRolledOverDate($new_date, $original_date);
                }
            } elseif ($time_period == self::YEAR) {
                $new_dates[$date_name] = $this->$date_name->copy()->year($original_date->year + $offset);

                $new_date = &$new_dates[$date_name];

                // Now if rollover should be disabled we will check to see whether the month for the
                // new date has changed. If it has then the month will hvae rolled over; in which
                // case we will refactor it.
                if ($rollover == false && $original_date->month != $new_date->month) {
                    $new_date = $this->refactorRolledOverDate($new_date, $original_dates[$date_name]['date']);
                }
            } else {
                $new_dates[$date_name] = $this->$date_name->copy()->$offset_method();
            }

            // Make any position adjustments that are necessary. If a date is positioned at the
            // start or end of a time period then we will adjust any offset dates to be at the
            // start and end of the new time period.
            if (!is_null($original_dates[$date_name]['position']) && $keep_position) {
                $method = $original_dates[$date_name]['position']."of$time_period";
                $new_dates[$date_name]->$method();
            }
        }

        return new static($new_dates['after'], $new_dates['before']);
    }

    /**
     * Determines a dates position within a time period.
     *
     * @param Carbon $date_time
     * @param $time_period
     *
     * @return string|null A class constant that represents a time period, or null.
     */
    protected function getDatePositionIn(Carbon $date_time, $time_period)
    {
        // First we need to validate the time period.
        if (!array_key_exists($time_period, self::$time_periods)) {
            throw new \InvalidArgumentException('Time period must be one of the time periods listed in the class constants');
        }

        // Now we will check to see whether the date is positioned at the beginning of the time
        // period provided.
        $start_date = self::forTimePeriod($time_period, $date_time)->start();
        if ($date_time->copy()->eq($start_date)) {
            return self::START;
        }

        // Now we will check to see whether the date is positioned at the end of the time period
        // provided.
        $end_date = self::forTimePeriod($time_period, $date_time)->end();
        if ($date_time->copy()->eq($end_date)) {
            return self::END;
        }
    }

    /**
     * Offsets a month in a date by a set value; offsets >12 are not currently supported.
     *
     * @param Carbon $original_date
     * @param $offset
     *
     * @return bool
     *
     * @TODO Add support for offsets greater then 12 for months.
     */
    protected function offsetMonthInDate(Carbon $original_date, $offset)
    {
        $original_month = $original_date->month;

        // Calculate expected month if offset is positive.
        if ($offset >= 0 && ($original_month + $offset) <= 12) {
            $expected_month = $original_month + $offset;
        } elseif ($offset >= 0) {
            $expected_month = 0 + ($offset - (12 - $original_month));
        }

        // Calculate expected month if offset is negative.
        elseif ($offset < 0 && ($original_month + $offset) >= 1) {
            $expected_month = $original_month + $offset;
        } elseif ($offset < 0) {
            $expected_month = 12 + ($offset + $original_month);
        }

        return $expected_month;
    }

    /**
     * Adjusts a date that has rolled over back to the previous month; setting the day
     * to the last day of the previous month; time values are maintained.
     *
     * @param Carbon $date
     * @param Carbon $original_date
     *
     * @return \DateTime
     */
    protected function refactorRolledOverDate(Carbon $date, Carbon $original_date)
    {
        return $date
            ->subMonth()
            ->endOfMonth()
            ->setTime($original_date->hour, $original_date->minute, $original_date->second);
    }

    /**
     * Determines is a date range as explicit start and end boundaries.
     *
     * @return bool
     */
    public function isBounded()
    {
        return !$this->isOpenEnded() && !$this->isOpenStarted();
    }

    /**
     * Creates a date range which represents the inverse of a date range.
     *
     * @return static
     */
    public function invert()
    {
        return new static($this->getBefore()->subSecond(), $this->getAfter()->addSecond());
    }

    /**
     * Determines if a date range is inverted.
     *
     * @return bool
     */
    public function isInverted()
    {
        return $this->after->gt($this->before);
    }

    /**
     * Outputs a string format of a date range.
     *
     * @param string $date_format
     * @param string $glue
     *
     * @return string
     */
    public function toInclusiveString($date_format = 'l jS \\of F Y', $glue = ' to ')
    {
        // Return full date range
        if (is_null($this->getAfter())) {
            return 'Before '.$this->getBefore()->subSecond()->format($date_format);
        }

        if (is_null($this->getBefore())) {
            return 'After '.$this->getAfter()->subSecond()->format($date_format);
        }

        return $this->getAfter()->addSecond()->format($date_format).$glue.$this->getBefore()->subSecond()->format($date_format);
    }

    /**
     * Outputs a string format of a date range.
     *
     * @param string $date_format
     * @param string $glue
     *
     * @return string
     */
    public function toString($date_format = 'l jS \\of F Y', $glue = ' to ')
    {
        if ($this->isOpenEnded()) {
            // Check if time period is open ended
            return 'After '.$this->after->format($date_format);
        } elseif ($this->isOpenStarted()) {
            // Check if time period is open started
            return 'Before '.$this->before->format($date_format);
        } else {
            // Return full date range
            return $this->after->format($date_format).$glue.$this->before->format($date_format);
        }
    }

    public function contains(Carbon $date_time)
    {
        return $date_time->gt($this->after) && $date_time->lt($this->before);
    }

    /**
     * Parses a time period provided as input.
     *
     * @param string $time_period
     * @return string
     * @throws InvalidArgumentException Invalid time period.
     */
    protected static function parseTimePeriod($time_period)
    {
        if (! array_key_exists($time_period, self::$time_periods)) {
            throw new InvalidArgumentException("Invalid time period");
        }
        return $time_period;
    }
}
