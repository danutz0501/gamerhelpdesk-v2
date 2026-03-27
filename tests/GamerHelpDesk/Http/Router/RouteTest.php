<?php

use GamerHelpDesk\Http\Router\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{    


    public static function routeDataProvider(): array
    {
        return [
            ['/home', '/home', []],
            ['/hme/{#varNumber :number}', '/hme/1', ['varNumber' => 1]],
            ['/hme/{#varString :string}', '/hme/q', ['varString' => 'q']],
            ['/hme/{#varString :string}/{#varNumber :number}', '/hme/q/1', ['varString' => 'q', 'varNumber' => 1]],
            ['/hme/{#varNumber :number}/{#varString :string}', '/hme/1/q', ['varNumber' => 1, 'varString' => 'q']],
            ['/hme/{#varNumber0 :number}/{#varString :string}/{#varNumber1 :number}', '/hme/1/q/1', ['varNumber0' => 1, 'varString' => 'q', 'varNumber1' => 1]],
            ['/hme/{#varString :string}/{#varNumber :number}/{#varAny :any}', '/hme/q/1/q1', ['varString' => 'q', 'varNumber' => 1, 'varAny' => 'q1']],
            ['/hme/{#varString :string}/{#varNumber1 :number}/{#varAny :any}/{#varNumber2 :number}', '/hme/random/41526/q1/1234567890', ['varString' => 'random', 'varNumber1' => 41526, 'varAny' => 'q1', 'varNumber2' => 1234567890]],
           ];
    }

    #[DataProvider("routeDataProvider")]
    public function testRouteVerification($regex, $uri ,$paramArray = [], $routes = 'home'): void
    {
        $route = new Route($regex, $routes);
        $this->assertTrue($route->verify($uri));
        $this->assertEquals($paramArray, $route->parameters);
    }

    public function testRouteCreation(): void
    {
        $route = new Route("/", "home");
        $this->assertTrue($route->verify('/'));
        $this->assertEquals('home', $route->method);
    }
}