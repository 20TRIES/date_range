<?php

use _20TRIES\DateRange;

class HourOffsetsTest extends PHPUnit_Framework_TestCase
{
    public function test_positive_offset_of_hour()
    {
        $range = DateRange::thisHour();
        $offset_range = $range->offset(1, DateRange::HOUR, false, true);
        $this->assertEquals(1, $range->start()->diffInHours($offset_range->start()));
        $this->assertEquals(1, $range->end()->diffInHours($offset_range->end()));
        $this->assertTrue($offset_range->start()->gt($range->start()));
    }

    public function test_negative_offset_of_hour()
    {
        $range = DateRange::thisHour();
        $offset_range = $range->offset(-1, DateRange::HOUR, false, true);
        $this->assertEquals(1, $range->start()->diffInHours($offset_range->start()));
        $this->assertEquals(1, $range->end()->diffInHours($offset_range->end()));
        $this->assertTrue($offset_range->start()->lt($range->start()));
    }
}