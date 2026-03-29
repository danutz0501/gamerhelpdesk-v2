<?php
/**
 * File: Cookie.php
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

namespace GamerHelpDesk\Http\Cookie;

/**
 * Cookie Class
 * AES-256-CBC encryption + HMAC signing + key rotation + auto re-encryption
 * Keys are loaded from environment variables for security.
 * This class provides secure methods to set, get, check existence, and delete cookies with encryption and integrity verification.
 * Note: Ensure that the COOKIE_KEYS environment variable is properly set with secure keys before using this class.
 * Example usage:
 * Cookie::set("user", "JohnDoe", 3600);
 * $user = Cookie::get("user", 3600);
 * if ($user !== null) {
 *     echo "Decrypted cookie value: " . htmlspecialchars($user);
 * } else {
 *     echo "Cookie missing or invalid.";
 * }
 * Environment Variable Setup:
 * export COOKIE_KEYS='[
 * {"enc": "0123456789abcdef0123456789abcdef", "hmac": "abcdef0123456789abcdef0123456789"},
 * {"enc": "oldkey0123456789abcdef0123456789", "hmac": "oldhmacabcdef0123456789abcdef0123"}
 * ]'
 * THX TO ChatGPT&COPILOT for the code template and guidance on implementing secure cookie handling with encryption, HMAC signing, and key rotation in PHP.
 * AND THX TO ME for implementing the final version of the Cookie class based on the provided template and requirements.
 * Don't use this code in production without proper testing and security review, especially regarding key management and environment variable handling.
 * Always ensure that your encryption keys are stored securely and that your environment variables are protected from unauthorized access.
 * @package GamerHelpDesk\Http\Cookie
 * @version 1.0.0
 */
class Cookie
{
    /** @var array Array of key sets [{ "enc": "...", "hmac": "..." }, ...] */
    private static array $KEYS = [];

    /** @var string AES-256-CBC encryption method */
    private const CIPHER_METHOD = 'AES-256-CBC';

    /**
     * Load keys from environment variables
     * Expected format: JSON array of objects [{ "enc": "...", "hmac": "..." }, ...]
     * The first key set is the active key, and the rest are old keys for rotation.
     * @throws RuntimeException if keys are not set or invalid.
     */
    private static function loadKeys(): void
    {
        if (!empty(self::$KEYS)) 
        {
            return; // Already loaded
        }

        $keysJson = getenv(name:'COOKIE_KEYS');
        if (!$keysJson) 
        {
            throw new \RuntimeException("COOKIE_KEYS environment variable not set.");
        }

        $keys = json_decode(json: $keysJson, associative: true);
        if (!is_array(value:$keys) || empty($keys)) 
        {
            throw new \RuntimeException("Invalid COOKIE_KEYS format.");
        }

        // Validate keys
        foreach ($keys as $keySet) 
        {
            if (!isset($keySet['enc'], $keySet['hmac']) || strlen(string: $keySet['enc']) < 32 || strlen(string: $keySet['hmac']) < 32) 
            {
                throw new \RuntimeException("Invalid key set in COOKIE_KEYS.");
            }
        }

        self::$KEYS = $keys;
    }

    /**
     * Encrypt and sign a value using the active key
     * Returns the encrypted and signed value as a base64-encoded string.
     * @throws RuntimeException if encryption fails or keys are not loaded.
     */
    private static function encryptAndSign(string $data): string
    {
        self::loadKeys();
        $activeKey = self::$KEYS[0];
        $ivLength = openssl_cipher_iv_length(self::CIPHER_METHOD);
        $iv = openssl_random_pseudo_bytes($ivLength);

        $encrypted = openssl_encrypt($data, self::CIPHER_METHOD, $activeKey['enc'], 0, $iv);
        $hmac = hash_hmac('sha256', $iv . $encrypted, $activeKey['hmac'], true);

        return base64_encode($iv . $hmac . $encrypted);
    }

    /**
     * Verify signature and decrypt a value using all keys (for rotation)
     * Returns null if verification fails, or the decrypted value if successful.
     * @throws RuntimeException if decryption fails or keys are not loaded.
     * Returns [value, usedOldKey]
     */
    private static function verifyAndDecrypt(string $data): array
    {
        self::loadKeys();
        $decoded = base64_decode($data, true);
        if ($decoded === false) 
        {
            return [null, false];
        }

        $ivLength = openssl_cipher_iv_length(self::CIPHER_METHOD);
        $hmacLength = 32;

        if (strlen($decoded) < ($ivLength + $hmacLength)) 
        {
            return [null, false];
        }

        $iv = substr($decoded, 0, $ivLength);
        $hmac = substr($decoded, $ivLength, $hmacLength);
        $encrypted = substr($decoded, $ivLength + $hmacLength);

        foreach (self::$KEYS as $index => $keySet) 
        {
            $calculatedHmac = hash_hmac('sha256', $iv . $encrypted, $keySet['hmac'], true);
            if (!hash_equals($hmac, $calculatedHmac)) 
            {
                continue;
            }

            $decrypted = openssl_decrypt($encrypted, self::CIPHER_METHOD, $keySet['enc'], 0, $iv);
            if ($decrypted !== false) 
            {
                return [$decrypted, $index > 0];
            }
        }

        return [null, false];
    }

    /**
     * Set a secure cookie
     * Returns true on success, false on failure (e.g., headers already sent, invalid name).
     * Note: Cookie name must be alphanumeric with optional underscores or dashes to prevent header injection.
     * @throws RuntimeException if keys are not loaded or encryption fails.
     */
    public static function set(
        string $name,
        string $value,
        int $expire = 3600,
        string $path = "/",
        string $domain = "",
        bool $secure = false,
        bool $httponly = true
    ): bool 
    {
        if (headers_sent()) 
        {
            return false;
        }

        if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $name)) 
        {
            return false;
        }

        $encryptedValue = self::encryptAndSign($value);

        return setcookie(
            $name,
            $encryptedValue,
            [
                'expires'  => time() + $expire,
                'path'     => $path,
                'domain'   => $domain ?: '',
                'secure'   => $secure,
                'httponly' => $httponly,
                'samesite' => 'Lax'
            ]
        );
    }

    /**
     * Get and verify a secure cookie
     * Auto re-encrypts if old key was used
     * Returns the decrypted value if valid, or null if the cookie is missing or invalid.
     * @throws RuntimeException if keys are not loaded or decryption fails.
     */
    public static function get(string $name, int $expire = 3600, string $path = "/", string $domain = "", bool $secure = false, bool $httponly = true): ?string
    {
        if (!isset($_COOKIE[$name])) 
        {
            return null;
        }

        [$value, $usedOldKey] = self::verifyAndDecrypt($_COOKIE[$name]);

        if ($value !== null && $usedOldKey) 
        {
            self::set($name, $value, $expire, $path, $domain, $secure, $httponly);
        }

        return $value;
    }

    /**
     * Check if a cookie exists
     * Returns true if the cookie exists (regardless of validity), false otherwise.
     */
    public static function exists(string $name): bool
    {
        return isset($_COOKIE[$name]);
    }

    /**
     * Delete a cookie
     * Returns true on success, false on failure (e.g., headers already sent).
     * Note: To delete a cookie, we set it with an expiration time in the past.
     * @throws RuntimeException if keys are not loaded or encryption fails (though encryption is not needed for deletion, we check keys for consistency).
     */
    public static function delete(string $name, string $path = "/", string $domain = ""): bool
    {
        if (headers_sent()) 
        {
            return false;
        }

        return setcookie(
            $name,
            '',
            [
                'expires'  => time() - 3600,
                'path'     => $path,
                'domain'   => $domain ?: '',
                'secure'   => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );
    }
}

// // -------------------
// // Example Usage
// // -------------------

// // Set a secure cookie
// Cookie::set("user", "JohnDoe", 3600);

// // Get decrypted cookie value (auto re-encrypts if old key used)
// $user = Cookie::get("user", 3600);
// if ($user !== null) {
//     echo "Decrypted cookie value: " . htmlspecialchars($user);
// } else {
//     echo "Cookie missing or invalid.";
// }
// ?>
// 🔐 Environment Variable Setup
// In your .env or server config, set:

// export COOKIE_KEYS='[
//   {"enc": "0123456789abcdef0123456789abcdef", "hmac": "abcdef0123456789abcdef0123456789"},
//   {"enc": "oldkey0123456789abcdef0123456789", "hmac": "oldhmacabcdef0123456789abcdef0123"}
// ]'
