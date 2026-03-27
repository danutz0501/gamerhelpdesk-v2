<?php

use GamerHelpDesk\Http\Router\Attribute\RouteAttribute;
use PHPUnit\Framework\TestCase;

class RouteAttributeTest extends TestCase
{
    public function testRouteAttribute(): void
    {
        $routeAttribute = new RouteAttribute(verb: 'GET', route: '/test');
        $this->assertEquals('GET', $routeAttribute->verb);
        $this->assertEquals('/test', $routeAttribute->route);
    }
}