<?php namespace _20TRIES\Test;

use _20TRIES\DateRange;
use Carbon\Carbon;

/**
 * Class ConstructorTest
 *
 * @package _20TRIES\Test
 * @author Marcus T <marcus.turner@creare.uk>
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

    public function test_timezone_is_set_from_dates_provided()
    {
        $timezone = 'GB';

        $range = new DateRange(
            Carbon::today($timezone)->subSecond(),
            Carbon::today($timezone)->endOfDay()->addSecond()
        );

        $this->assertEquals($timezone, $range->getTimezone()->getName());
    }

    public function test_after_date_is_set()
    {
        $timezone = 'GB';

        $range = new DateRange(
            $after = Carbon::today($timezone)->subSecond(),
            Carbon::today($timezone)->endOfDay()->addSecond()
        );

        $this->assertEquals($after, $range->getAfter());
    }

    public function test_before_date_is_set()
    {
        $timezone = 'GB';

        $range = new DateRange(
            Carbon::today($timezone)->subSecond(),
            $before = Carbon::today($timezone)->endOfDay()->addSecond()
        );

        $this->assertEquals($before, $range->getBefore());
    }
}