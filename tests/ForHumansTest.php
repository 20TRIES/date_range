<?php namespace _20TRIES\Test;

use _20TRIES\DateRange;
use Carbon\Carbon;
use \PHPUnit_Framework_TestCase;

class ForHumansTest extends PHPUnit_Framework_TestCase
{
    public function test_today()
    {
        $range = new DateRange(
            Carbon::today()->subSecond(),
            Carbon::today()->endOfDay()->addSecond()
        );

        $expected_result = 'Today';

        $this->assertEquals($expected_result, $range->forHumans());
    }

    public function test_tomorrow()
    {
        $range = new DateRange(
            Carbon::tomorrow('GB')->startOfDay()->subSecond(),
            Carbon::tomorrow('GB')->endOfDay()->addSecond()
        );

        $expected_result = 'Tomorrow';

        $this->assertEquals($expected_result, $range->forHumans());
    }

    public function test_yesterday()
    {
        $range = new DateRange(
            Carbon::yesterday()->subSecond(),
            Carbon::yesterday()->endOfDay()->addSecond()
        );

        $expected_result = 'Yesterday';

        $this->assertEquals($expected_result, $range->forHumans());
    }

    public function test_other_day_in_current_week()
    {

    }

    public function test_other_day_in_last_week()
    {

    }

    public function test_other_day_in_next_week()
    {

    }

    public function test_other_day()
    {

    }

    public function test_this_week()
    {

    }

    public function test_next_week()
    {

    }

    public function test_last_week()
    {

    }

    public function test_other_week()
    {

    }

    public function test_this_month()
    {

    }

    public function test_next_month()
    {

    }

    public function test_last_month()
    {

    }

    public function test_other_month()
    {

    }

    public function test_this_year()
    {

    }

    public function test_next_year()
    {

    }

    public function test_last_year()
    {

    }

    public function test_other_year()
    {

    }
}