<?php namespace _20TRIES\Test\Construction;

use _20TRIES\DateRange;
use Carbon\Carbon;
use PHPUnit_Framework_TestCase;

class HourConstructionTest extends PHPUnit_Framework_TestCase
{
    public function test_this_hour()
    {
        $range = DateRange::thisHour($expected_timezone = 'UTC');

        $this->assertInstanceOf(DateRange::class, $range);

        $expected_after = Carbon::now($expected_timezone)->minute(0)->second(0)->subSecond();
        $after = $range->getAfter();
        $this->assertAttributeInstanceOf(Carbon::class, 'after', $range);
        $this->assertAttributeEquals($expected_after, 'after', $range);
        $this->assertEquals($expected_after->timezone, $after->timezone);

        $expected_before = Carbon::now($expected_timezone)->minute(59)->second(59)->addSecond();
        $before = $range->getBefore();
        $this->assertAttributeEquals($expected_before, 'before', $range);
        $this->assertEquals($expected_before, $before);
    }

    public function test_next_hour()
    {
        $range = DateRange::nextHour($expected_timezone = 'UTC');

        $this->assertInstanceOf(DateRange::class, $range);

        $expected_after = Carbon::now($expected_timezone)->addHour()->minute(0)->second(0)->subSecond();
        $after = $range->getAfter();
        $this->assertAttributeInstanceOf(Carbon::class, 'after', $range);
        $this->assertAttributeEquals($expected_after, 'after', $range);
        $this->assertEquals($expected_after->timezone, $after->timezone);

        $expected_before = Carbon::now($expected_timezone)->addHour()->minute(59)->second(59)->addSecond();
        $before = $range->getBefore();
        $this->assertAttributeEquals($expected_before, 'before', $range);
        $this->assertEquals($expected_before, $before);
    }

    public function test_last_hour()
    {
        $range = DateRange::lastHour($expected_timezone = 'UTC');

        $this->assertInstanceOf(DateRange::class, $range);

        $expected_after = Carbon::now($expected_timezone)->subHour()->minute(0)->second(0)->subSecond();
        $after = $range->getAfter();
        $this->assertAttributeInstanceOf(Carbon::class, 'after', $range);
        $this->assertAttributeEquals($expected_after, 'after', $range);
        $this->assertEquals($expected_after->timezone, $after->timezone);

        $expected_before = Carbon::now($expected_timezone)->subHour()->minute(59)->second(59)->addSecond();
        $before = $range->getBefore();
        $this->assertAttributeEquals($expected_before, 'before', $range);
        $this->assertEquals($expected_before, $before);
    }
}