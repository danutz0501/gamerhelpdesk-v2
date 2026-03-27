<?php
/**
 * File: Request.php
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

namespace GamerHelpDesk\Http\Request;

use Uri\Rfc3986\Uri;

/**
 * Request class
 * Represents an HTTP request and provides methods to access its properties.
 * @package GamerHelpDesk\Http\Request
 * @version 1.0.0
 */
class Request
{

    /**
     * Define the types of HTTP verbs
     * This is a string that represents the HTTP method that can be used in the request.
     * Is a regex pattern that matches common HTTP verbs.
     * @var string
     */
    private const string HTTP_VERBS = "/GET|POST/";

    /**
     * The URI of the request
     * This is a object that represents the URI of the request.
     * It is an instance of the Uri class. Used to parse and manipulate URIs.
     * It is set in the constructor.
     * Public getter, protected setter.
     * @var object URI\Rfc3986\Uri
     */
    public protected(set) Uri $uri
    {
        get
        {
            return $this->uri;
        } 
    }

    /**
     * The path of the request
     *  This is a string that represents the path of the request.
     * It is set in the constructor by calling the setPathQueryFragment method.
     * Public getter, protected setter.
     * @var string
     */
    public protected(set) string $path
    {
        get
        {
            return $this->path;
        }    
    }

    /**
     * The query of the request
     * This is a string that represents the query of the request.
     * It is set in the constructor by calling the setPathQueryFragment method.
     * Public getter, protected setter.
     * @var string
     */
    public protected(set) string $query
    {
        get
        {
            return $this->query;
        }   
    }

    /**
     * The fragment of the request
     * This is a string that represents the fragment of the request.
     * It is set in the constructor by calling the setPathQueryFragment method.
     * Public getter, protected setter.
     * @var string
     */
    public protected(set) string $fragment
    {
        get
        {
            return $this->fragment;
        }    
    }

    /**
     * The HTTP method of the request
     * This is a string that represents the HTTP method of the request.
     * It is set in the constructor by calling the setHttpMethod method.
     * Public getter, protected setter.
     * @var string
     */
    public protected(set) string $httpMethod 
    {
        get
        {
            return $this->httpMethod;
        }    
    }

    /**
     * The headers of the request
     * This is an array that represents the headers of the request.
     * It is set in the constructor by calling the setHeaders method.
     * Public getter, protected setter.
     * @var array
     */
    public protected(set) array $headers
    {
        get
        {
            return $this->headers;
        }    
    }

    /**
     * The body of the request
     * This is a string that represents the body of the request.
     * It is set in the constructor by calling the setBody method.
     * Public getter, protected setter.
     * @var string
     */
    public protected(set) string $body
    {
        get
        {
            return $this->body;
        }    
    }

    /**
     * Whether the request is an AJAX request
     * This is a boolean that represents whether the request is an AJAX request or not.
     * It is set in the constructor by calling the setIsAjaxRequest method.
     * Public getter, protected setter.
     * @var bool
     */
    public protected(set) bool $isAjaxRequest
    {
        get
        {
            return $this->isAjaxRequest;
        }    
    }

    /**
     * The files of the request
     * This is an array that represents the files of the request.
     * It is set in the constructor by calling the setFiles method.
     * Public getter, protected setter.
     * @var array
     */
    public protected(set) array $files
    {
        get
        {
            return $this->files;
        }    
    }

    /**
     * The $_GET superglobal
     * This is an array that represents the $_GET superglobal.
     * It is set in the constructor by calling the setGet method.
     * Public getter, protected setter.
     * @var null|array
     */
    public protected(set) ?array $get
    {
        get
        {
            return $this->get;
        }
    }

    /**
     * The $_POST superglobal
     * This is an array that represents the $_POST superglobal.
     * It is set in the constructor by calling the setPost method.
     * Public getter, protected setter.
     * @var null|array
     */
    public protected(set) ?array $post
    {
        get
        {
            return $this->post;
        }
    }

    /**
     * Constructor to initialize the request with data from the superglobal $_SERVER array.
     * Sets the URI, path, query, fragment, HTTP method, headers, body, and whether the request is an AJAX request.
     */
    public function __construct()
    {
        $this->uri = new Uri(uri: $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $this->setPathQueryFragment();
        $this->setHeaders();
        $this->setHttpMethod();
        $this->setBody();
        $this->setIsAjaxRequest();
        $this->setFiles();
        $this->setGet();
        $this->setPost();
    }

    /**
     * Returns superglobals.
     * It returns an array with the following keys:
     * - get: the $_GET superglobal
     * - post: the $_POST superglobal
     * - files: the $_FILES superglobal
     * - server: the $_SERVER superglobal
     * - request: the $_REQUEST superglobal
     * - cookie: the $_COOKIE superglobal
     * - session: the $_SESSION superglobal
     * - env: the $_ENV superglobal
     * @return array
     */
    public function returnGlobals(): array
    {
        return 
        [
            "get"     => $_GET,
            "post"    => $_POST,
            "files"   => $_FILES,
            "server"  => $_SERVER,
            "request" => $_REQUEST,
            "cookie"  => $_COOKIE,
            "session" => $_SESSION,
            "env"     => $_ENV,
        ];
    }
  
    /**
     * Sets the $_GET superglobal.
     * It sets the $this->get property to the value of the $_GET superglobal, sanitized with FILTER_SANITIZE_SPECIAL_CHARS.
     * @return void
     * @throws Exception
     */
    protected function setGet(): void
    {
        $this->get = filter_input_array(INPUT_GET, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    /**
     * Sets the $_POST superglobal.
     * It sets the $this->post property to the value of the $_POST superglobal, sanitized with FILTER_SANITIZE_SPECIAL_CHARS.
     * @return void
     * @throws Exception
     */
    protected function setPost(): void
    {
        $this->post = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    /**
     * Sets the headers of the request.
     * If the getallheaders() function does not exist (for example, in PHP versions older than 5.4 or in certain server environments),
     * it will loop through the $_SERVER superglobal and set the headers in the $this->headers property.
     * If the getallheaders() function exists, it will use it to set the headers.
     */
    protected function setHeaders(): void
    {
        if(!function_exists(function: 'getallheaders'))
        {
            $this->headers = [];
            foreach ($_SERVER as $name => $value) 
            {
                if (str_starts_with(haystack: $name, needle: 'HTTP_')) 
                {
                    $headerName = str_replace(search: '_', replace: '-', subject: substr(string: $name, offset: 5));
                    $this->headers[$headerName] = $value;
                }
            }
        } 
        else
        {
            $this->headers = getallheaders();
        }
    }
  
    /**
     * Sets the HTTP method of the request.
     * If the $_SERVER['REQUEST_METHOD'] key does not exist in the superglobal $_SERVER array,
     * it will default to 'GET'.
     * If the method is not one of the HTTP verbs, it will default to 'GET'.
     * HTTP verbs are: GET, HEAD, POST, PUT, DELETE, CONNECT, OPTIONS, TRACE, PATCH
     */
    protected function setHttpMethod(): void
    {
       $this->httpMethod = strtoupper(string: $_SERVER['REQUEST_METHOD'] ?? 'GET')
            |> (fn(string $method): string => preg_match(pattern: self::HTTP_VERBS, subject: $method) ? $method : 'GET');
    }

    /**
     * Sets the body of the request.
     * If the request body is empty, it will default to an empty string.
     * The body is read from the php://input stream.
     * @return void
     */
    protected function setBody(): void
    {
        $this->body = file_get_contents(filename: 'php://input') ?: '';
    }

    /**
     * Checks if the request was made via an AJAX request.
     * It sets the $this->isAjaxRequest property to true if the request was made via an AJAX request, and false otherwise.
     * It does this by checking the value of the 'HTTP_X_REQUESTED_WITH' header, and comparing it to the string 'xmlhttprequest'.
     * If the values match, it sets $this->isAjaxRequest to true, otherwise it sets it to false.
     * 
     * @return void
     */
    protected function setIsAjaxRequest(): void
    {
        $header = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        $this->isAjaxRequest = strcasecmp(string1: $header, string2: 'xmlhttprequest') === 0;
    }

    /**
     * Sets the files of the request.
     * It sets the $this->files property to the value of the $_FILES superglobal, or an empty array if it does not exist.
     * @return void
     */
    protected function setFiles(): void
    {
        $this->files = $_FILES ?? [];
    }

    /**
     * Sets the path, query, and fragment of the request.
     * It sets the $this->path, $this->query, and $this->fragment properties by cleaning the respective parts of the URI.
     * The cleaning process involves filtering out any unsafe characters from the string and replacing any special characters with their URL encoded equivalent.
     * It does this by calling the clean method on each part of the URI.
     * @return void
     */
    protected function setPathQueryFragment(): void
    {
        $this->path     = $this->clean(string: $this->uri->getPath());
        $this->query    = is_null(value: $this->uri->getQuery()) ? '' : $this->clean(string: $this->uri->getQuery());
        $this->fragment = is_null(value: $this->uri->getFragment()) ? '' : $this->clean(string: $this->uri->getFragment());
    }

    /**
     * Cleans a string by removing any unsafe characters and replacing any special characters with their URL encoded equivalent.
     * It first filters the string using the FILTER_SANITIZE_URL filter, and then removes any remaining unsafe characters using a regular expression.
     * The regular expression used is '/[\da-z\-\/\#\&\?\=\:\+]/i', which matches any alphanumeric characters, hyphens, forward slashes, hash symbols, ampersands, question marks, equals signs, colons, and plus signs.
     * Any characters that do not match this pattern are removed from the string.
     * @param string $string The string to be cleaned.
     * @return string The cleaned string.
     */
    protected function clean(string $string): string
    {
        return $string
            |>(fn(string $str): string =>filter_var(value:$str, filter: FILTER_SANITIZE_URL))
            |>(fn(string $str): string => preg_replace(pattern: '/[^\da-z\-\/\#\&\?\=\:\+]/i', replacement: '', subject: $str));
    }
}