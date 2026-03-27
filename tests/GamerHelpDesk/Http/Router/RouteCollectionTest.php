<?php

use GamerHelpDesk\Http\Router\RouteCollection;
use GamerHelpDesk\Http\Router\Route;
use PHPUnit\Framework\TestCase;

class RouteCollectionTest extends TestCase
{
    public function testRouteCollection()
    {
        $collection = new RouteCollection();
        $this->assertInstanceOf(RouteCollection::class, $collection);
    }

    public function testAddRoute()
    {
        $collection = new RouteCollection();
        $route = new Route('/', 'home');
        $collection->addRoute($route);
        $this->assertCount(1, $collection);
    }

    public function testIfIsEmpty()
    {
        $collection = new RouteCollection();
        $this->assertTrue($collection->isEmpty());
    }

    public function testIfIsNotEmpty()
    {
        $collection = new RouteCollection();
        $route = new Route('/', 'home');
        $collection->addRoute($route);
        $this->assertFalse($collection->isEmpty());
    }

    public function testIterator()
    {
        $collection = new RouteCollection();
        $route = new Route('/', 'home');
        $collection->addRoute($route);
        foreach ($collection as $item) {
            $this->assertInstanceOf(Route::class, $item);
        }
    }

    public function testJsonSerialize()
    {
        $collection = new RouteCollection();
        $route = new Route('/', 'home');
        $collection->addRoute($route);
        $json = json_encode($collection);
        $this->assertIsString($json);
    }
}