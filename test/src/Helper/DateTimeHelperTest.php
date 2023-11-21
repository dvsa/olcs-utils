<?php

namespace OlcsTest\Utils\Helper;

use Dvsa\Olcs\Utils\Helper\DateTimeHelper;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers \Dvsa\Olcs\Utils\Helper\DateTimeHelper
 */
class DateTimeHelperTest extends MockeryTestCase
{
    /** @dataProvider dpTestFormat */
    public function testFormat($date, $format, $expect)
    {
        if ($format !== null) {
            $actual = DateTimeHelper::format($date, $format);
        } else {
            $actual = DateTimeHelper::format($date);
        }

        static::assertEquals($expect, $actual);
    }

    public function dpTestFormat()
    {
        $time = strtotime('2017-11-12T13:14:15+0000');

        return [
            [
                'date' => '2017-11-12T13:14:15+0000',
                'format' =>  'd/m/Y H:i:s',
                'expect' => date('d/m/Y H:i:s', $time),
            ],
            [
                'date' => '2017-11-12 13:14:15',
                'format' => 'd/m/Y H:i:s',
                'expect' => date('d/m/Y H:i:s', $time),
            ],
            [
                'date' => '2017-11-12 13:14:15',
                'format' => 'g:ia \o\n l jS F Y',
                'expect' => date('g:ia \o\n l jS F Y', $time),
            ],
            [
                'date' => '2017-11-12',
                'format' => 'd/m/Y',
                'expect' => date('d/m/Y', $time),
            ],
            [
                'date' => 'AAAA-02-12',
                'format' => null,
                'expect' => null,
            ],
        ];
    }
}
