<?php

namespace yolo;

class DoubleTruthTest extends YoloTestCase
{
    /** @dataProvider truths @test */
    function needMoreTruths()
    {
        $this->assertTrue(true);
    }

    function truths()
    {
        $_ENV['TRUTHS'] = isset($_ENV['TRUTHS']) ? $_ENV['TRUTHS'] : '1';
        while ($_ENV['TRUTHS']--) {
            yield [];
        }
    }
}
