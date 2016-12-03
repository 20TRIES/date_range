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

    public function test_extreme_values_dont_span_time_period___incorrect_position_late()
    {
        $range = new DateRange(
            Carbon::now()->startOfDay(),
            Carbon::now()->endOfDay()->addSeconds(2)
        );
        $this->assertNull($range->spans(DateRange::DAY));
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
}