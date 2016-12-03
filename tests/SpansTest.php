<?php namespace _20TRIES\Test;

use _20TRIES\DateRange;
use Carbon\Carbon;
use PHPUnit_Framework_TestCase;

class SpansTest extends PHPUnit_Framework_TestCase
{
    public function test_extreme_values_dont_span_time_period__wrong_start()
    {
        $range = new DateRange(
            Carbon::now()->startOfDay(),
            Carbon::now()->endOfDay()->addSecond()
        );
        $this->assertNull($range->spans(DateRange::DAY));
    }

    public function test_extreme_values_dont_span_time_period__wrong_end()
    {
        $range = new DateRange(
            Carbon::now()->startOfDay()->subSecond(),
            Carbon::now()->endOfDay()
        );
        $this->assertNull($range->spans(DateRange::DAY));
    }

    public function test_extreme_values_dont_span_time_period__inclusive()
    {
        $range = new DateRange(
            Carbon::now()->startOfDay(),
            Carbon::now()->endOfDay()
        );
        $this->assertNull($range->spans(DateRange::DAY));
    }

    public function test_extreme_values_dont_span_time_period__incorrect_position_early()
    {
        $range = new DateRange(
            Carbon::now()->startOfDay()->subSeconds(2),
            Carbon::now()->endOfDay()
        );
        $this->assertNull($range->spans(DateRange::DAY));
    }

    public function test_extreme_values_dont_span_time_period__incorrect_position_late()
    {
        $range = new DateRange(
            Carbon::now()->startOfDay(),
            Carbon::now()->endOfDay()->addSeconds(2)
        );
        $this->assertNull($range->spans(DateRange::DAY));
    }

    public function test_spans_hour()
    {
        $range = new DateRange(
            Carbon::now()->minute(0)->second(0)->subSecond(1),
            Carbon::now()->minute(59)->second(59)->addSecond(1)
        );
        $result = $range->spans(DateRange::HOUR);
        $expected = Carbon::now()->minute(0)->second(0);
        $this->assertInstanceOf(Carbon::class, $result);
        $this->assertEquals($expected, $result);
        $this->assertEquals($expected->timezone, $result->timezone);
    }

    public function test_spans_day()
    {
        $range = new DateRange(
            Carbon::now()->startOfDay()->subSecond(),
            Carbon::now()->endOfDay()->addSecond()
        );
        $result = $range->spans(DateRange::DAY);
        $this->assertInstanceOf(Carbon::class, $result);
        $this->assertEquals(Carbon::now()->startOfDay(), $result);
        $this->assertEquals(Carbon::now()->startOfDay()->timezone, $result->timezone);
    }

    public function test_spans_week()
    {
        $range = new DateRange(
            Carbon::now()->startOfWeek()->subSecond(),
            Carbon::now()->endOfWeek()->addSecond()
        );
        $result = $range->spans(DateRange::WEEK);
        $this->assertInstanceOf(Carbon::class, $result);
        $expected = Carbon::now()->startOfWeek();
        $result = $range->spans(DateRange::WEEK);
        $this->assertEquals($expected, $result);
        $this->assertEquals($expected->timezone, $result->timezone);
    }

    public function test_spans_month()
    {
        $range = new DateRange(
            Carbon::now()->startOfMonth()->subSecond(),
            Carbon::now()->endOfMonth()->addSecond()
        );
        $result = $range->spans(DateRange::MONTH);
        $this->assertInstanceOf(Carbon::class, $result);
        $expected = Carbon::now()->startOfMonth();
        $result = $range->spans(DateRange::MONTH);
        $this->assertEquals($expected, $result);
        $this->assertEquals($expected->timezone, $result->timezone);
    }

    public function test_spans_year()
    {
        $range = new DateRange(
            Carbon::now()->startOfYear()->subSecond(),
            Carbon::now()->endOfYear()->addSecond()
        );
        $result = $range->spans(DateRange::YEAR);
        $this->assertInstanceOf(Carbon::class, $result);
        $expected = Carbon::now()->startOfYear();
        $result = $range->spans(DateRange::YEAR);
        $this->assertEquals($expected, $result);
        $this->assertEquals($expected->timezone, $result->timezone);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid time period
     */
    public function test_exception_is_thrown_if_time_period_is_not_supported() {
        $range = new DateRange(
            Carbon::now()->startOfYear()->subSecond(),
            Carbon::now()->endOfYear()->addSecond()
        );
        $range->spans('FOO');
    }

    public function test_open_ended_ranges_return_null()
    {
        $range = new DateRange(Carbon::now()->startOfYear()->subSecond(), null);
        $this->assertFalse($range->spans(DateRange::YEAR));
    }

    public function test_open_started_ranges_return_null()
    {
        $range = new DateRange(null, Carbon::now()->startOfYear()->subSecond());
        $this->assertFalse($range->spans(DateRange::YEAR));
    }

    public function test_that_span_must_be_single_time_period()
    {
        $range = new DateRange(
            Carbon::yesterday()->subSecond(),
            Carbon::tomorrow()
        );
        $this->assertNull($range->spans(DateRange::DAY));
    }
}