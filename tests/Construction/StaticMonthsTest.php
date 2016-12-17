<?php namespace _20TRIES\Test\Construction;

use _20TRIES\DateRange;
use Carbon\Carbon;
use PHPUnit_Framework_TestCase;

class StaticMonthsTest extends PHPUnit_Framework_TestCase
{
    public function test_this_month()
    {
        $range = DateRange::thisMonth($expected_timezone = 'UTC');

        $this->assertInstanceOf(DateRange::class, $range);

        $expected_start = Carbon::now($expected_timezone)->startOfMonth();

        $expected_after = $expected_start->copy()->subSecond();
        $this->assertAttributeInstanceOf(Carbon::class, 'after', $range);
        $this->assertAttributeEquals($expected_after, 'after', $range);
        $this->assertAttributeEquals($expected_timezone, 'timezone', $range->getAfter());

        $expected_before = $expected_start->copy()->endOfMonth()->addSecond();
        $this->assertAttributeInstanceOf(Carbon::class, 'before', $range);
        $this->assertAttributeEquals($expected_before, 'before', $range);
        $this->assertAttributeEquals($expected_timezone, 'timezone', $range->getBefore());
    }

    public function test_timezone_is_optional_for_thisMonth()
    {
        $range = DateRange::thisMonth();
        $expected_timezone = 'GB';
        $this->assertAttributeEquals($expected_timezone, 'timezone', $range->getAfter());
        $this->assertAttributeEquals($expected_timezone, 'timezone', $range->getBefore());
    }

    public function test_next_month()
    {
        $range = DateRange::nextMonth($expected_timezone = 'UTC');

        $this->assertInstanceOf(DateRange::class, $range);

        $expected_start = Carbon::now($expected_timezone)->startOfMonth()->addMonth();

        $expected_after = $expected_start->copy()->subSecond();
        $this->assertAttributeInstanceOf(Carbon::class, 'after', $range);
        $this->assertAttributeEquals($expected_after, 'after', $range);
        $this->assertAttributeEquals($expected_timezone, 'timezone', $range->getAfter());

        $expected_before = $expected_start->copy()->endOfMonth()->addSecond();
        $this->assertAttributeInstanceOf(Carbon::class, 'before', $range);
        $this->assertAttributeEquals($expected_before, 'before', $range);
        $this->assertAttributeEquals($expected_timezone, 'timezone', $range->getBefore());
    }

    public function test_timezone_is_optional_for_nextMonth()
    {
        $range = DateRange::nextMonth();
        $expected_timezone = 'GB';
        $this->assertAttributeEquals($expected_timezone, 'timezone', $range->getAfter());
        $this->assertAttributeEquals($expected_timezone, 'timezone', $range->getBefore());
    }

    public function test_last_month()
    {
        $range = DateRange::lastMonth($expected_timezone = 'UTC');

        $this->assertInstanceOf(DateRange::class, $range);

        $expected_start = Carbon::now($expected_timezone)->startOfMonth()->subMonth();

        $expected_after = $expected_start->copy()->subSecond();
        $this->assertAttributeInstanceOf(Carbon::class, 'after', $range);
        $this->assertAttributeEquals($expected_after, 'after', $range);
        $this->assertAttributeEquals($expected_timezone, 'timezone', $range->getAfter());

        $expected_before = $expected_start->copy()->endOfMonth()->addSecond();
        $this->assertAttributeInstanceOf(Carbon::class, 'before', $range);
        $this->assertAttributeEquals($expected_before, 'before', $range);
        $this->assertAttributeEquals($expected_timezone, 'timezone', $range->getBefore());
    }

    public function test_timezone_is_optional_for_lastMonth()
    {
        $range = DateRange::lastMonth();
        $expected_timezone = 'GB';
        $this->assertAttributeEquals($expected_timezone, 'timezone', $range->getAfter());
        $this->assertAttributeEquals($expected_timezone, 'timezone', $range->getBefore());
    }
}
