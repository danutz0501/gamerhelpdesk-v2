<?php
/**
 * File: HttpCodeEnum.php
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

namespace GamerHelpDesk\Http\Response;

/**
 * HttpCodeEnum class
 * This class defines HTTP status codes as constants for easy reference throughout the application.
 * It provides a centralized location for all HTTP status codes used in the application, improving readability and maintainability.
 * 
 * @package GamerHelpDesk\Http\Response
 * @version 1.0.0
 */
enum HttpCodeEnum: int
{
    case OK = 200;
    case CREATED = 201;
    case NO_CONTENT = 204;
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case METHOD_NOT_ALLOWED = 405;
    case INTERNAL_SERVER_ERROR = 500;

    /**
     * Returns the HTTP status code as an integer.
     * @return int
     */
    public function getCode(): int
    {
        return $this->value;
    }

    /**
     * Returns the HTTP status code as a string.
     * This method is called when the object is cast to a string, and is used to display the HTTP status code in a human-readable format.
     * @return string The HTTP status code as a string.
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Returns the HTTP status message associated with the current HTTP status code.
     * @return string The HTTP status message.
     */
    public function message(): string
    {
        return static::getMessage($this);
    }

    /**
     * Returns the HTTP status code associated with the given HTTP status message.
     * @param string $message The HTTP status message.
     * @return self The HTTP status code.
     */
    public static function fromMessage(string $message): self
    {
        return match(strtolower($message)) {
            "ok" => self::OK,
            "created" => self::CREATED,
            "no content" => self::NO_CONTENT,
            "bad request" => self::BAD_REQUEST,
            "unauthorized" => self::UNAUTHORIZED,
            "forbidden" => self::FORBIDDEN,
            "not found" => self::NOT_FOUND,
            "method not allowed" => self::METHOD_NOT_ALLOWED,
            "internal server error" => self::INTERNAL_SERVER_ERROR,
            default => throw new \InvalidArgumentException("Invalid HTTP status message: $message"),
        };
    }

    /**
     * Returns the HTTP status code associated with the given integer code.
     * @param int $code The HTTP status code as an integer.
     * @return self The HTTP status code enum value.
     */
    public static function fromCode(int $code): self
    {
        return match($code) {
            200 => self::OK,
            201 => self::CREATED,
            204 => self::NO_CONTENT,
            400 => self::BAD_REQUEST,
            401 => self::UNAUTHORIZED,
            403 => self::FORBIDDEN,
            404 => self::NOT_FOUND,
            405 => self::METHOD_NOT_ALLOWED,
            500 => self::INTERNAL_SERVER_ERROR,
            default => throw new \InvalidArgumentException("Invalid HTTP status code: $code"),
        };
    }

    /**
     * Returns the HTTP status message associated with the given HTTP status code.
     * @param self $code The HTTP status code.
     * @return string The HTTP status message.
     */
    public static function getMessage(self $code): string
    {
        return match($code) {
            self::OK => "OK",
            self::CREATED => "Created",
            self::NO_CONTENT => "No Content",
            self::BAD_REQUEST => "Bad Request",
            self::UNAUTHORIZED => "Unauthorized",
            self::FORBIDDEN => "Forbidden",
            self::NOT_FOUND => "Not Found",
            self::METHOD_NOT_ALLOWED => "Method Not Allowed",
            self::INTERNAL_SERVER_ERROR => "Internal Server Error",
        };
    }
}