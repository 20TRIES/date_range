<?php

namespace _20TRIES\Test;

use _20TRIES\DateRange;
use Carbon\Carbon;

class ForMonthTest extends \PHPUnit_Framework_TestCase
{
    public function test_for_month_sets_after()
    {
        $range = DateRange::forMonth(2016, 2, 'GB');

        $expected = Carbon::create(2016, 2, 1, 0, 0, 0, 'GB')->subSecond();

        $this->assertEquals($expected, $range->getAfter());
    }

    public function test_for_month_sets_before()
    {
        $range = DateRange::forMonth(2016, 2, 'GB');

        $expected = Carbon::create(2016, 2, 1, 0, 0, 0, 'GB')->endOfMonth()->addSecond();

        $this->assertEquals($expected, $range->getBefore());
    }

    public function test_for_month_sets_timezone()
    {
        $timezone = 'GB';

        $range = DateRange::forMonth(2016, 2, $timezone);

        $this->assertEquals($timezone, $range->getTimezone()->getName());
    }
}