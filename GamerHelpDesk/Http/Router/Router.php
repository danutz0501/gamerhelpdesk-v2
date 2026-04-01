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
use GamerHelpDesk\Http\Request\Request;
use GamerHelpDesk\Http\Response\Response;
use GamerHelpDesk\Http\Router\Attribute\RouteAttribute;
use GamerHelpDesk\Exception\{
    GamerHelpDeskException,
    GamerHelpDeskExceptionEnum
};
use ReflectionClass;
use ReflectionException;
use ReflectionAttribute;

class Router
{
    /** @use SingletonTrait */
    use SingletonTrait;
    /**
     * The base path for the router. This is the path that will be prefixed to all routes defined in the router.
     * @var string
     */

    public string $basePath = "/";
    /**
     * Middleware namespace. This will be prefixed to all middleware classes
     * @var string
     */
    public string $middlewareNamespace = "";

    /**
     * The request object for the router.
     * @var Request
     */

    /**
     * The response object for the router.
     * @var Response
     */
    public protected(set) Request $request
    {
        get
        {
            return $this->request;
        }
    }

    /**
     * The response object for the router.
     * @var Response
     */
    public protected(set) Response $response
    {
        get
        {
            return $this->response;
        }
    }

    /**
     * The collection of GET routes for the router.
     * @var RouteCollection
     */
    public protected(set) RouteCollection $getRoutes
    {
        get
        {
            return $this->getRoutes;
        }
    }

    /**
     * The collection of POST routes for the router.
     * @var RouteCollection
     */
    public protected(set) RouteCollection $postRoutes
    {
        get
        {
            return $this->postRoutes;
        }
    }

    /**
     * The method to call when the route is matched.
     * @var string
     */
    public protected(set) string $method
    {
        get
        {
            return $this->method;
        }
    }   

    /**
     * The parameters extracted from the URI when the route is matched.
     * @var array
     */
    public protected(set) array $params
    {
        get
        {
            return $this->params;
        }
    }

    /**
     * The middleware`s associated with the route.
     * @var array|null
     */
    public protected(set) ?array $middleware
    {
        get
        {
            return $this->middleware;
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
     * Dispatches the request to the appropriate route.
     * It checks the HTTP method and dispatches the request to the appropriate route collection.
     * If no route is found, it throws an exception.
     * If a route is found, it sets the method, parameters, and middleware properties and calls the prepareCallable method to get the class and method to call.
     * If middleware is associated with the route, it will call the handle method of each middleware class before calling the controller method.
     * If a middleware class does not exist or does not have a handle method, it will throw an exception.
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
                $this->middleware = $route->middleware;
                [$class, $method] = $this->prepareCallable();
                if($this->middleware !== null)
                {
                    foreach($this->middleware as $middleware)
                    {
                        $middleware =  $this->middlewareNamespace . ucfirst(string: strtolower(string: $middleware));
                        if(!class_exists(class: $middleware))
                        {
                            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::INVALID_ARGUMENT_EXCEPTION, "Middleware class {$middleware} does not exist.");
                        }
                        $middlewareInstance = new $middleware();
                        if(method_exists(object_or_class:  $middlewareInstance, method: "handle"))
                        {
                            $middlewareInstance->handle(request: $this->request, response: $this->response);
                        }
                        else
                        {
                            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::INVALID_ARGUMENT_EXCEPTION, "Middleware class {$middleware} must have a handle method.");
                        }
                    }
                }
                call_user_func_array(callback: [new $class(), $method], args: $this->params);
                return;
            }
        }
        throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::ROUTE_NOT_FOUND_EXCEPTION, "No matching route found for URI: {$this->request->path}.", 404);
    }

    /**
     * Adds a named route to the router.
     * The named route is identified by its HTTP verb and route path.
     * The method to call when the route is matched is specified by the $method parameter.
     * Optionally, middleware can be associated with the route by providing an array of middleware classes as the $middleware parameter.
     * Supported HTTP verbs are GET and POST.
     * If an unsupported HTTP verb is provided, it will throw a GamerHelpDeskException with the INVALID_ARGUMENT_EXCEPTION code.
     * @param string $verb The HTTP verb (GET or POST).
     * @param string $route The route path.
     * @param string $method The method to call when the route is matched.
     * @param array|null $middleware The middleware classes associated with the route.
     * @throws GamerHelpDeskException
     */
    public function addNamedRoute(string $verb, string $route, string $method, ?array $middleware = null): void
    {
        match(strtoupper(string: $verb))
        {
            "GET"   => $this->getRoutes->addRoute(route: new Route(regexToConvert: $this->basePath . $route, method: $method, middleware: $middleware)),
            "POST"  => $this->postRoutes->addRoute(route: new Route(regexToConvert: $this->basePath . $route, method: $method, middleware: $middleware)),
            default => throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::INVALID_ARGUMENT_EXCEPTION, "Invalid HTTP verb: {$verb}. Supported verbs are GET and POST.")
        };
    }

    /**
     * Adds a named route to the router.
     * The named route is identified by its HTTP verb and route path.
     * The method to call when the route is matched is specified by the $method parameter.
     * Optionally, middleware can be associated with the route by providing an array of middleware classes as the $middleware parameter.
     * Supported HTTP verbs are GET and POST.
     * If an unsupported HTTP verb is provided, it will throw a GamerHelpDeskException with the INVALID_ARGUMENT_EXCEPTION code.
     * @param string $verb The HTTP verb (GET or POST).
     * @param string $route The route path.
     * @param array|null $middleware The middleware classes associated with the route.
     * @throws GamerHelpDeskException
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
                    $this->addNamedRoute(verb: $instance->verb, route: $instance->route, method: "{$attributeClass}::{$method->getName()}", middleware: $instance->middleware);
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