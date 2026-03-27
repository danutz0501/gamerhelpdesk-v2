<?php
/**
 * File: Router.php
 * Project: GamerHelpDesk
 * Created Date: March 2026
 * Author: danutz0501 (M. Dumitru Daniel)
 * -----
 * Last Modified:
 * Modified By:
 * -----
 * Copyright (c) 2026 M. Dumitru Daniel (M. Dumitru Daniel)
 *  This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace GamerHelpDesk\Http\Router;

use GamerHelpDesk\Util\SingletonTrait\SingletonTrait;
use GamerHelpDesk\Http\Router\Attribute\RouteAttribute;
use GamerHelpDesk\Http\Request\Request;
use GamerHelpDesk\Http\Response\Response;
use GamerHelpDesk\Exception\{
    GamerHelpDeskException,
    GamerHelpDeskExceptionEnum
};
use ReflectionClass;
use ReflectionException;
use ReflectionAttribute;

class Router
{
    /**
     * The router instance. It is a singleton.
     * @var Router $instance
     */
    use SingletonTrait;

    /**
     * The base path of the router.
     * @var string $basePath
     */
    public string $basePath = "/";

    /**
     * The request object.
     */
    public protected(set) Request $request
    {
        get
        {
            return $this->request;
        }
    }

    /**
     * The response object.
     */
    public protected(set) Response $response
    {
        get
        {
            return $this->response;
        }
    }

    /**
     * The collection of GET routes.
     */
    public protected(set) RouteCollection $getRoutes
    {
        get
        {
            return $this->getRoutes;
        }
    }

    /**
     * The collection of POST routes.
     */
    public protected(set) RouteCollection $postRoutes
    {
        get
        {
            return $this->postRoutes;
        }
    }

    /**
     * The method(handler) of the request.
     * @var string $method
     */
    public protected(set) string $method
    {
        get
        {
            return $this->method;
        }
    }   

    /**
     * The parameters passed to the route handler.
     * @var array $params
     */
    public protected(set) array $params
    {
        get
        {
            return $this->params;
        }
    }

    /**
     * Constructor to initialize the router.
     * It sets the request, response, GET routes, and POST routes properties.
     */
    public function __construct()
    {
        $this->request    = new Request();
        $this->response   = new Response();
        $this->getRoutes  = new RouteCollection();
        $this->postRoutes = new RouteCollection();
    }

    /**
     * Dispatches the request to the appropriate route handler.
     * It checks if any routes are defined for the given HTTP method, and if so, it iterates over them, verifying if the current route matches the given URI.
     * If a matching route is found, it sets the method and parameters properties and calls the matching route handler using call_user_func_array.
     * If no matching route is found, it throws a GamerHelpDeskException with the ROUTE_NOT_FOUND_EXCEPTION code.
     * @throws GamerHelpDeskException
     */
    public function dispatch(): void
    {
        if(count(value: $this->{strtolower(string: $this->request->httpMethod)."Routes"}) === 0)
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::ROUTE_NOT_FOUND_EXCEPTION, "No routes defined for HTTP method: {$this->request->httpMethod}");
        }
        foreach($this->{strtolower(string: $this->request->httpMethod)."Routes"} as $route)
        {
            if($route->verify(uri: $this->request->path))
            {
                $this->method = $route->method;
                $this->params = $route->parameters;
                [$class, $method] = $this->prepareCallable();
                call_user_func_array(callback: [new $class(), $method], args: $this->params);
                return;
            }
        }
        throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::ROUTE_NOT_FOUND_EXCEPTION, "No matching route found for URI: {$this->request->path}.", 404);
    }

    /**
     * Adds a named route to the router.
     * This method adds a route to the GET or POST routes collection.
     * It takes three parameters: the HTTP verb, the route path, and the method to call when the route is matched.
     * Supported HTTP verbs are GET and POST.
     * If an unsupported HTTP verb is provided, it will throw a GamerHelpDeskException with the INVALID_ARGUMENT_EXCEPTION code.
     * @param string $verb The HTTP verb to use for the route (GET or POST)
     * @param string $route The path of the route (relative to the base path)
     * @param string $method The method to call when the route is matched
     * @throws GamerHelpDeskException
     */
    public function addNamedRoute(string $verb, string $route, string $method): void
    {
        match(strtoupper(string: $verb))
        {
            "GET"   => $this->getRoutes->addRoute(route: new Route(regexToConvert: $this->basePath . $route, method: $method)),
            "POST"  => $this->postRoutes->addRoute(route: new Route(regexToConvert: $this->basePath . $route, method: $method)),
            default => throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::INVALID_ARGUMENT_EXCEPTION, "Invalid HTTP verb: {$verb}. Supported verbs are GET and POST.")
        };
    }

    /**
     * Adds a set of routes defined by the RouteAttribute class to the router.
     * This method takes an array of class names as its parameter, and adds each class's methods that are annotated with the RouteAttribute class to the router.
     * The RouteAttribute class must be used to annotate the methods that should be added to the router.
     * The RouteAttribute class takes three parameters: the HTTP verb, the route path, and the method to call when the route is matched.
     * Supported HTTP verbs are GET and POST.
     * If an unsupported HTTP verb is provided, it will throw a GamerHelpDeskException with the INVALID_ARGUMENT_EXCEPTION code.
     * @param array $routes An array of class names whose methods should be added to the router.
     */
    public function addNamedRoutes(array $routes): void
    {
        foreach($routes as $route)
        {
            $this->addNamedRoute(verb: $route['verb'], route: $route['route'], method: $route['method']);
        }
    }

    /**
     * Adds a set of routes defined by the RouteAttribute class to the router.
     * This method takes an array of class names as its parameter, and adds each class's methods that are annotated with the RouteAttribute class to the router.
     * The RouteAttribute class must be used to annotate the methods that should be added to the router.
     * The RouteAttribute class takes three parameters: the HTTP verb, the route path, and the method to call when the route is matched.
     * Supported HTTP verbs are GET and POST.
     * If an unsupported HTTP verb is provided, it will throw a GamerHelpDeskException with the INVALID_ARGUMENT_EXCEPTION code.
     * @param array $routes An array of class names whose methods should be added to the router.
     */
    public function addAttributeRoute(array $routes): void
    {
        foreach($routes as $attributeClass)
        {
            preg_replace(pattern:"/\\\\/", replacement:"\\", subject: $attributeClass);
            $reflection = new ReflectionClass(objectOrClass: $attributeClass);
            foreach($reflection->getMethods() as $method)
            {
                if(!$method->isPublic() || $method->isConstructor())
                    {
                        continue;
                    }
                $attributes = $method->getAttributes(name: RouteAttribute::class, flags: ReflectionAttribute::IS_INSTANCEOF);
                foreach($attributes as $attribute)
                {
                    $instance = $attribute->newInstance();
                    $this->addNamedRoute(verb: $instance->verb, route: $instance->route, method: "{$attributeClass}::{$method->getName()}");
                }
            }
        }        
    }

    /**
     * Prepares a callable based on the provided method string.
     * The method string must be in the format Class::method or Namespace\\Class::method.
     * If the method string is empty, it will throw a GamerHelpDeskException with the INVALID_ARGUMENT_EXCEPTION code.
     * If the method string does not contain '::' or '\\', it will throw a GamerHelpDeskException with the INVALID_ARGUMENT_EXCEPTION code.
     * @return array An array containing the class name and method name.
     * @throws GamerHelpDeskException If the method string is empty or does not contain '::' or '\\'.
     */
    private function prepareCallable(): array
    {
        $temp_array = [];
        if(empty($this->method))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::INVALID_ARGUMENT_EXCEPTION, "No method defined for the route.");
        }

        if(!str_contains(haystack: $this->method, needle: '\\') && !str_contains(haystack: $this->method, needle: '::'))
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::INVALID_ARGUMENT_EXCEPTION, "Method must be in the format Class::method or Namespace\\Class::method");
        }

        if(str_contains(haystack: $this->method, needle: '::'))
        {
            return explode(separator: '::', string: $this->method);
        }
        else
        {
            $temp = explode(separator: "\\", string: $this->method);
            $method = array_pop(array: $temp);
            $class = implode(separator: "\\", array: $temp);
            return [$class, $method];
        }
    }

    /**
     * Returns an array containing the routes for the GET and POST methods.
     * The keys of the array are "GET" and "POST", and the values are arrays
     * of routes for each method, respectively.
     * @return array
     */
    public function getRoutesArray(): array
    {
        return 
        [
            "GET"  => iterator_to_array(iterator: $this->getRoutes),
            "POST" => iterator_to_array(iterator: $this->postRoutes),
        ];
    }
}
    