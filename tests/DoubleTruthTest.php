<?php

namespace yolo;

class DoubleTruthTest extends \PHPUnit_Framework_TestCase
{
    /** @dataProvider truths @test */
    function needMoreTruths()
    {
        $this->assertTrue(boolval(yolo\YOLO::class));
    }

    function truths()
    {
        $_ENV['TRUTHS'] = isset($_ENV['TRUTHS']) ? $_ENV['TRUTHS'] : '1';
        while ($_ENV['TRUTHS']--) {
            yield [];
        }
    }
}
