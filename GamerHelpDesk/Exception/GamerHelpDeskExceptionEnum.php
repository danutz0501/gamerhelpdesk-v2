<?php
/**
 * File: GamerHelpDeskExceptionEnum.php
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

/**
 * GamerHelpDeskExceptionEnum class
 * Represents the different types of exceptions that can be thrown by the application.
 * @package GamerHelpDesk\Exception
 * @version 1.0.0
 */
enum GamerHelpDeskExceptionEnum: string
{
    case INVALID_ARGUMENT_EXCEPTION = "Invalid argument provided. ";
    case INVALID_RANGE_EXCEPTION = "Value is out of the allowed range. ";
    case NOT_FOUND_EXCEPTION = "Requested resource not found. ";
    case INVALID_DATE_TIME_EXCEPTION = "Invalid date or time format. ";
    case DATABASE_EXCEPTION = "Database error occurred. ";
    case FILE_SYSTEM_EXCEPTION = "File system error occurred. ";
    case SYSTEM_EXCEPTION = "System error occurred. ";
    case UNAUTHORIZED_EXCEPTION = "Unauthorized access. ";
    case HTTP_EXCEPTION = "HTTP error occurred. ";
    case ROUTE_NOT_FOUND_EXCEPTION = "No route found for the given request.";

}
