<?php
/**
 * File: GamerHelpDeskException.php
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

namespace GamerHelpDesk\Exception;

use Exception;
use ErrorException;

/**
 * GamerHelpDeskException class
 * A custom exception class for the GamerHelpDesk application that extends the base Exception class.
 * It allows for creating exceptions with specific types defined by the GamerHelpDeskExceptionEnum, along with optional custom messages, error codes, and previous exceptions for chaining.
 * @package GamerHelpDesk\Exception
 * @version 1.0.0
 */
class GamerHelpDeskException extends Exception 
{
    /**
     * This constructor allows for creating an exception with a specific type defined by the GamerHelpDeskExceptionEnum, along with an optional custom message, error code, and previous exception for chaining.
     * @param GamerHelpDeskExceptionEnum $exceptionType The type of exception being thrown, represented by an enum value.
     * @param string|null $message An optional custom message to provide more details about the exception. If not provided, the default message from the enum value will be used.
     * @param int|null $code An optional error code to provide more context about the exception. If not provided, it defaults to 0.
     * @param Exception|null $previous An optional previous exception that led to this exception being thrown. This allows for exception chaining, where multiple exceptions can be linked together to provide a more complete picture of the error that occurred.
     */
    public function __construct(GamerHelpDeskExceptionEnum $exceptionType, ?string $message = null, ?int $code = null, ?Exception $previous = null)
    {
        $message ??= $exceptionType->value;
        parent::__construct($message, $code ?? 0, $previous);
    }

    /**
     * Handles errors and throws an ErrorException with the error message, error number, file, and line number.
     * @param int $errno The error number.
     * @param string $errstr The error message.
     * @param string $errfile The file where the error occurred.
     * @param int $errline The line number where the error occurred.
     * @return bool Returns false to indicate that the error was handled.
     */
    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
    }
}