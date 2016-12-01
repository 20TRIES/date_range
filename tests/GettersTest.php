<?php

namespace _20TRIES\Test;

use _20TRIES\DateRange;
use Carbon\Carbon;

/**
 * Class GettersTest.
 */
class GettersTest extends \PHPUnit_Framework_TestCase
{
    public function test_getAfter_returns_a_new_carbon_instance_with_correct_value()
    {
        $range = new DateRange(
            $after = Carbon::today()->subSecond(),
            Carbon::today()->endOfDay()->addSecond()
        );

        $this->assertEquals(Carbon::today()->subSecond(), $range->getAfter());

        $this->assertTrue($after !== $range->getAfter());
    }

    public function test_getFrom_returns_a_new_carbon_instance_with_correct_value()
    {
        $range = new DateRange(
            $after = Carbon::today()->subSecond(),
            Carbon::today()->endOfDay()->addSecond()
        );

        $this->assertEquals(Carbon::today(), $range->getFrom());

        $this->assertTrue($after !== $range->getFrom());
    }

    public function test_getBefore_returns_a_new_carbon_instance_with_correct_value()
    {
        $range = new DateRange(
            Carbon::today()->subSecond(),
            $before = Carbon::today()->endOfDay()->addSecond()
        );

        $this->assertEquals(Carbon::today()->endOfDay()->addSecond(), $range->getBefore());

        $this->assertTrue($before !== $range->getBefore());
    }

    public function test_getTo_returns_a_new_carbon_instance_with_correct_value()
    {
        $range = new DateRange(
            Carbon::today()->subSecond(),
            $before = Carbon::today()->endOfDay()->addSecond()
        );

        $this->assertEquals(Carbon::today()->endOfDay(), $range->getTo());

        $this->assertTrue($before !== $range->getTo());
    }
}
