<?php

use GamerHelpDesk\Http\Router\Attribute\Get;

class GetTest extends \PHPUnit\Framework\TestCase
{
    public function testGetVerb()
    {
        $get = new Get(route: '/test');
        $this->assertEquals('GET', $get->verb);
    }
}