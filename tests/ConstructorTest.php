<?php

namespace _20TRIES\Test;

use _20TRIES\DateRange;
use Carbon\Carbon;

/**
 * Class ConstructorTest.
 *
 * @author Marcus T <marcus.turner@creare.uk>
 *
 * @since v0
 */
class ConstructorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @inherit
     */
    public function setUp()
    {
        parent::setUp();

        Carbon::setTestNow();
    }

    public function test_basic_construction()
    {
        new DateRange(
            Carbon::today()->subSecond(),
            Carbon::today()->endOfDay()->addSecond()
        );
    }

    /**
     * @expectedException \_20TRIES\Exceptions\TimezoneException
     */
    public function test_timezone_exception_thrown_if_date_range_constructed_with_multiple_timezones()
    {
        $timezone_1 = 'GB';
        $timezone_2 = null;

        new DateRange(
            Carbon::today($timezone_1)->subSecond(),
            Carbon::today($timezone_2)->endOfDay()->addSecond()
        );
    }

    /**
     * @expectedException \_20TRIES\Exceptions\DateRangeException
     */
    public function test_date_time_exception_thrown_if_date_range_constructed_without_dates()
    {
        new DateRange();
    }

    public function test_timezone_is_initialised_with_both_dates_provided()
    {
        $timezone = 'GB';

        $range = new DateRange(
            Carbon::today($timezone)->subSecond(),
            Carbon::today($timezone)->endOfDay()->addSecond()
        );

        $this->assertEquals($timezone, $range->getTimezone()->getName());
    }

    public function test_after_date_is_initialised_with_both_dates_provided()
    {
        $range = new DateRange(
            $expected = Carbon::today()->subSecond(),
            Carbon::today()->endOfDay()->addSecond()
        );

        $this->assertEquals($expected, $range->getAfter());
    }

    public function test_before_date_is_initialised_with_both_dates_provided()
    {
        $range = new DateRange(
            Carbon::today()->subSecond(),
            $expected = Carbon::today()->endOfDay()->addSecond()
        );

        $this->assertEquals($expected, $range->getBefore());
    }

    public function test_timezone_is_initialised_without_after_date()
    {
        $range = new DateRange(null, Carbon::today($expected = 'GB')->endOfDay()->addSecond());

        $this->assertEquals($expected, $range->getTimezone()->getName());
    }

    public function test_after_date_is_initialised_without_after_date()
    {
        $range = new DateRange($expected = null, Carbon::today()->endOfDay()->addSecond());

        $this->assertEquals($expected, $range->getAfter());
    }

    public function test_before_date_is_initialised_without_after_date()
    {
        $range = new DateRange(null, $expected = Carbon::today()->endOfDay()->addSecond());

        $this->assertEquals($expected, $range->getBefore());
    }

    public function test_timezone_is_initialised_without_before_date()
    {
        $range = new DateRange(Carbon::today($expected = 'GB')->subSecond(), null);

        $this->assertEquals($expected, $range->getTimezone()->getName());
    }

    public function test_after_date_is_initialised_without_before_date()
    {
        $range = new DateRange($expected = Carbon::today()->subSecond(), null);

        $this->assertEquals($expected, $range->getAfter());
    }

    public function test_before_date_is_initialised_without_before_date()
    {
        $range = new DateRange(Carbon::today()->subSecond(), $expected = null);

        $this->assertEquals($expected, $range->getBefore());
    }
}
