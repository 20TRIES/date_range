<?php

namespace _20TRIES\Test\Construction;

use _20TRIES\DateRange;
use Carbon\Carbon;
use PHPUnit_Framework_TestCase;

class StaticForTimePeriodsTest extends PHPUnit_Framework_TestCase
{
    public function test_for_month()
    {
        $expected_start = Carbon::now($expected_timezone = 'UTC')->startOfMonth();

        $range = DateRange::forMonth('Y-m-d', $expected_start->toDateString(), $expected_timezone);

        $this->assertInstanceOf(DateRange::class, $range);

        $expected_after = $expected_start->copy()->subSecond();
        $this->assertAttributeInstanceOf(Carbon::class, 'after', $range);
        $this->assertAttributeEquals($expected_after, 'after', $range);
        $this->assertAttributeEquals($expected_timezone, 'timezone', $range->getAfter());

        $expected_before = $expected_start->copy()->endOfMonth()->addSecond();
        $this->assertAttributeInstanceOf(Carbon::class, 'before', $range);
        $this->assertAttributeEquals($expected_before, 'before', $range);
        $this->assertAttributeEquals($expected_timezone, 'timezone', $range->getBefore());
    }

    public function test_timezone_is_optional_forMonth()
    {
        $expected_start = Carbon::now()->startOfMonth();
        $range = DateRange::forMonth('Y-m-d', $expected_start->toDateString());
        $expected_timezone = 'GB';
        $this->assertAttributeEquals($expected_timezone, 'timezone', $range->getAfter());
        $this->assertAttributeEquals($expected_timezone, 'timezone', $range->getBefore());
    }
}