<?php namespace _20TRIES\Test;

use _20TRIES\DateRange;
use Carbon\Carbon;
use PHPUnit_Framework_TestCase;

class StaticDaysTest extends PHPUnit_Framework_TestCase
{
    public function test_today()
    {
        $range = DateRange::today($expected_timezone = 'UTC');

        $this->assertInstanceOf(DateRange::class, $range);

        $expected_after = Carbon::now($expected_timezone)->startOfDay()->subSecond();
        $this->assertAttributeInstanceOf(Carbon::class, 'after', $range);
        $this->assertAttributeEquals($expected_after, 'after', $range);
        $this->assertAttributeEquals($expected_timezone, 'timezone', $range->getAfter());

        $expected_before = Carbon::now($expected_timezone)->endOfDay()->addSecond();
        $this->assertAttributeInstanceOf(Carbon::class, 'before', $range);
        $this->assertAttributeEquals($expected_before, 'before', $range);
        $this->assertAttributeEquals($expected_timezone, 'timezone', $range->getBefore());
    }

    public function test_tomorrow()
    {
        $range = DateRange::tomorrow($expected_timezone = 'UTC');

        $this->assertInstanceOf(DateRange::class, $range);

        $tomorrow = Carbon::now($expected_timezone)->startOfDay()->addDay();

        $expected_after = $tomorrow->copy()->subSecond();
        $this->assertAttributeInstanceOf(Carbon::class, 'after', $range);
        $this->assertAttributeEquals($expected_after, 'after', $range);
        $this->assertAttributeEquals($expected_timezone, 'timezone', $range->getAfter());

        $expected_before = $tomorrow->copy()->endOfDay()->addSecond();
        $this->assertAttributeInstanceOf(Carbon::class, 'before', $range);
        $this->assertAttributeEquals($expected_before, 'before', $range);
        $this->assertAttributeEquals($expected_timezone, 'timezone', $range->getBefore());
    }

    public function test_yesterday()
    {
        $range = DateRange::yesterday($expected_timezone = 'UTC');

        $this->assertInstanceOf(DateRange::class, $range);

        $yesterday = Carbon::now($expected_timezone)->startOfDay()->subDay();

        $expected_after = $yesterday->copy()->subSecond();
        $this->assertAttributeInstanceOf(Carbon::class, 'after', $range);
        $this->assertAttributeEquals($expected_after, 'after', $range);
        $this->assertAttributeEquals($expected_timezone, 'timezone', $range->getAfter());

        $expected_before = $yesterday->copy()->endOfDay()->addSecond();
        $this->assertAttributeInstanceOf(Carbon::class, 'before', $range);
        $this->assertAttributeEquals($expected_before, 'before', $range);
        $this->assertAttributeEquals($expected_timezone, 'timezone', $range->getBefore());
    }
}
