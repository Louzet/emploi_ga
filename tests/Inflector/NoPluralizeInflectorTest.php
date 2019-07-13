<?php

namespace App\Tests\Inflector;

use App\Inflector\NoPluralizeInflector;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class NoPluralizeInflectorTest extends TestCase
{
    public function testPluralizeReturnSingular()
    {
        $singular = 'baby';
        $pluralize = (new NoPluralizeInflector())->pluralize($singular);

        $this->assertEquals('baby', $pluralize);
        $this->assertIsString($pluralize);
    }
}