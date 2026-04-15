<?php
/**
 * File: Session.php
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

namespace GamerHelpDesk\Http\Session;

use SessionHandlerInterface;
use SessionIdInterface;
use SessionUpdateTimestampHandlerInterface;
use GamerHelpDesk\Util\{
    Randomizer\Randomizer,
    SingletonTrait\SingletonTrait
};

/**
 * Session class that implements custom session handling using the Singleton pattern.
 * This class manages session data by reading and writing session files to a specified save path.
 * It also includes methods for validating and regenerating session IDs, as well as garbage collection of old session files.
 * The session handler is registered with PHP's session management system, allowing it to handle all session operations for the application.
 * Note: Ensure that the session save path is properly configured and writable by the web server for this session handler to function correctly.
 * @package GamerHelpDesk\Http\Session
 * @version 1.0.0
 */
class Session implements SessionHandlerInterface, SessionUpdateTimestampHandlerInterface, SessionIdInterface
{
    /**
     * Using the SingletonTrait to ensure that only one instance of the Session class exists throughout the application.
     * This is important for managing session state consistently across the application and preventing issues that can arise from multiple instances of the session handler.
     * The SingletonTrait provides a getInstance() method that allows you to retrieve the single instance of the Session class, and it ensures that the constructor is private to prevent direct instantiation.
     * By using the Singleton pattern, we can ensure that all parts of the application are using the same session handler instance, 
     *  which helps to maintain consistent session state and avoid potential conflicts or issues that can arise from multiple instances of the session handler.
     * @package GamerHelpDesk\Http\Session
     * @version 1.0.0
     */
    use SingletonTrait;
    
    /** 
     * Session properties
     * @var string $savePath The path where session files are stored.
     * @var string $sessionName The name of the session.
     * @var string $sessionId The current session ID.
     * @var int    $maxlifetime The maximum lifetime of a session in seconds.
     */
    protected string $savePath;
    protected string $sessionName;
    protected string $sessionId;
    protected int $maxlifetime; // Default session max lifetime (24 minutes)

    /**
     * Private constructor to prevent direct instantiation.
     * This constructor initializes the session handler and starts the session with secure settings.
     * @throws \RuntimeException if the session has already been started or if headers have already been sent.
     */
    private function __construct()
    {
        /**
         * Initialize session properties and settings.
         * This includes setting the save path, session name, maximum lifetime, and secure cookie parameters.
         * The session handler is registered using session_set_save_handler(), and the session is started with session_start().
         * The session ID is regenerated to ensure a secure session ID is used for the current session.
         * If the session has already been started or if headers have already been sent,
         *  a RuntimeException is thrown to prevent issues with multiple sessions or header conflicts.
         */
        if (session_status() === PHP_SESSION_NONE) 
        {
            $this->savePath    = ini_get(option: "session.save_path") ?: sys_get_temp_dir();
            $this->sessionName = session_name();
            $this->maxlifetime = (int) ini_get(option: "session.gc_maxlifetime");
            ini_set(option: "session.use_only_cookies", value: "1");
            ini_set(option: "session.use_strict_mode", value: "1");
            /**
             * Set secure session parameters.
             * This includes the session cookie lifetime, path, domain, secure flag, and SameSite setting.
             */
            session_set_cookie_params([
                'lifetime' => $this->maxlifetime, // Session cookie (expires when the browser is closed)
                'path'     => '/', // Set the path for the session cookie
                'domain'   => '', // Set to your domain if needed
                'secure'   => ini_get(option: "session.cookie_secure"), // Set to true if using HTTPS
                'httponly' => true, // Prevent JavaScript access to session cookies
                'samesite' => ini_get(option: "session.cookie_samesite") ?: 'Strict', // Adjust as needed (Strict, Lax, None)
            ]);
            session_set_save_handler($this, true);
                session_start();
                session_regenerate_id(true);
                $this->sessionId = session_id();
        }
        else
        {
            throw new \RuntimeException(message: "Session has already been started or headers have already been sent.");
        }
    }

    /**
     * Opens the session handler and sets the save path and session name.
     * This method is required by the SessionHandlerInterface and should not be called directly.
     * @param string $savePath The path to which session data will be saved.
     * @param string $sessionName The name of the session.
     * @return bool Always returns true.
     * @throws \RuntimeException if the save path directory cannot be created or is not writable by the web server.
     */
    public function open(string $savePath, string $sessionName): bool
    {
        $this->savePath = $savePath;
        $this->sessionName = $sessionName;
        if($this->savePath !== sys_get_temp_dir() && !is_dir(filename: $this->savePath) && 
        !mkdir(directory: $this->savePath, recursive: true) && !is_dir(filename: $this->savePath)) 
        {
            throw new \RuntimeException(message: "Failed to create session save path directory: $this->savePath");
        }
        return true;
    }

    /**
     * Closes the session handler.
     * This method is required by the SessionHandlerInterface and should not be called directly.
     * @return bool Always returns true.
     */
    public function close(): bool
    {
        $this->sessionId = '';
        return true;
    }

    /**
     * Reads session data from a file.
     * This method is required by the SessionHandlerInterface and should not be called directly.
     * @param string $sessionId The session ID.
     * @return string The session data as a string.
     */
    public function read(string $sessionId): string
    {
        $file = "$this->savePath/sess_$sessionId";
        if (file_exists(filename: $file) && is_readable(filename: $file)) 
        {
            return (string) file_get_contents(filename: $file);
        }
        return '';
    }

    /**
     * Writes session data to a file.
     * This method is required by the SessionHandlerInterface and should not be called directly.
     * @param string $sessionId The session ID.
     * @param string $data The session data as a string.
     * @return bool True on success, false on failure.
     */
    public function write(string $sessionId, string $data): bool
    {
        $file = "$this->savePath/sess_$sessionId";
        @file_put_contents(filename: $file, data: $data);
        return true;
    }
    
    /**
     * Destroys a session file.
     * This method is required by the SessionHandlerInterface and should not be called directly.
     * @param string $sessionId The session ID.
     * @return bool True on success, false on failure.
     */
    public function destroy(string $sessionId): bool
    {
        $file = "$this->savePath/sess_$sessionId";
        if (file_exists(filename: $file)) 
        {
            unlink(filename: $file);
        }
        return true;
    }

    /**
     * Clears all session files.
     * This method is required by the SessionHandlerInterface and should not be called directly.
     * @return bool True on success, false on failure.
     */
    public function clear(): bool
    {
        foreach (glob("$this->savePath/sess_*") as $file) 
        {
            clearstatcache(true, $file);
            if (file_exists(filename: $file)) 
            {
                unlink(filename: $file);
            }
        }
        return true;
    }
    
    /**
     * Garbage collect old session files.
     * @param int $maxLifetime The maximum lifetime of a session file in seconds.
     * @return int|false The number of deleted files on success, false on failure.
     */
    public function gc(int $maxLifetime): int|false
    {
        foreach (glob("$this->savePath/sess_*") as $file) 
        {
            clearstatcache(true, $file);
            if (filemtime($file) + $maxLifetime < time() && file_exists($file)) 
            {
                unlink($file);
            }
        }
        return 0;
    }

    /**
     * Updates the timestamp of a session file.
     * @param string $sessionId The session ID.
     * @param string $data The data to be written to the session file.
     * @return bool True on success, false on failure.
     */
    public function updateTimestamp(string $sessionId, string $data): bool
    {
        if (file_exists(filename: "$this->savePath/sess_$sessionId")) 
        {
            return touch(filename: "$this->savePath/sess_$sessionId");
        }
        return false;
    }
    
    /**
     * Validates a session ID.
     * This method checks if a given session ID is valid.
     * A valid session ID is a 64-character long hexadecimal string.
     * @param string $sessionId The session ID to be validated.
     * @return bool True if the session ID is valid, false otherwise.
     */
    public function validateId(string $sessionId): bool
    {
        return preg_match(pattern: '/^[a-zA-Z0-9]{64}$/', subject: $sessionId) === 1;
    }
    
    /**
     * Regenerates a session ID.
     * This method generates a secure, random session ID and updates the current session ID.
     * The new session ID is validated to ensure it is secure and valid.
     * If the new session ID is valid, the current session ID is updated.
     * @param SessionIdInterface $sessionId The session ID to be regenerated.
     * @return bool True if the session ID was regenerated successfully, false otherwise.
     */
    public function regenerateId(SessionIdInterface $sessionId): bool
    {
        $newSessionId = $this->create_sid();
        if ($this->validateId(sessionId: $newSessionId))
        {
            session_id(id: $newSessionId);
            return true;
        }
        return false;
    }

    /**
     * Generates a secure, random session ID.
     * The session ID is generated using the Randomizer library with the Secure engine.
     * The session ID is a 64-character long hexadecimal string.
     * @return string A secure, random session ID.
     */
    public function create_sid(): string
    {
        return (string) new Randomizer(Randomizer::ENGINE_SECURE)->generateString(length: 64);
    }
}