<?php

namespace yolo;

class DoubleTruthTest extends \PHPUnit_Framework_TestCase
{
    /** @dataProvider truths @test */
    function needMoreTruths()
    {
        $this->assertTrue(true);
    }

    function truths()
    {
        $_ENV['TRUTHS'] = getenv('TRUTHS') ?: '1';
        while ($_ENV['TRUTHS']--) {
            yield [];
        }
    }
}
