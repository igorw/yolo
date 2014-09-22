<?php

namespace yolo;

class UniversalTest extends \PHPUnit_Framework_TestCase
{
    /** @dataProvider truths @test */
    function unifiedTheoryOfYolo()
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
