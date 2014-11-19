<?php

namespace yolo;

class TestsAreForThoseWhoWriteBugs extends YoloTestCase
{
    function testTestNeverFails()
    {
        $this->assertTrue(false);
    }
} 