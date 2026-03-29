<?php
/**
 * File: Hash.php
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

namespace GamerHelpDesk\Util\Hash;

use GamerHelpDesk\Exception\{
    GamerHelpDeskException,
    GamerHelpDeskExceptionEnum
};

/**
 * The Hash class provides methods for generating and verifying hashes for files and data.
 * It supports various hashing algorithms and can be used to ensure data integrity and security.
 * @package GamerHelpDesk\Util\Hash
 * @version 1.0.0
 */
class Hash
{
    /** @var string The hash algorithm to use for generating and verifying hashes.*/
    public protected(set) string $algorithm = 'sha256'
    {
        get
        {
            return $this->algorithm;
        }
    }
    public function __construct(string $algorithm = 'sha256')
    {
        if (!in_array($algorithm, hash_algos())) 
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::INVALID_ARGUMENT_EXCEPTION, "Invalid hash algorithm: " . $algorithm);
        }
        $this->algorithm = $algorithm;
    }

    /**
     * Sets the hash algorithm.
     * @param string $algorithm The hash algorithm to set.
     * @throws GamerHelpDeskException If the algorithm is invalid.
     */
    public function setAlgorithm(string $algorithm): void
    {
        if (!in_array($algorithm, hash_algos())) 
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::INVALID_ARGUMENT_EXCEPTION, "Invalid hash algorithm: " . $algorithm);
        }
        $this->algorithm = $algorithm;
    }

    /**
     * Generates a hash for a given file.
     * @param string $filePath The path to the file.
     * @return string The generated hash.
     */
    public function generateHash(string $filePath): string
    {
        if (!file_exists($filePath)) 
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "File not found: " . $filePath);
        }
        if (!is_readable($filePath)) 
        {
            throw new GamerHelpDeskException(GamerHelpDeskExceptionEnum::FILE_SYSTEM_EXCEPTION, "File is not readable: " . $filePath);
        }
        return hash_file($this->algorithm, $filePath);
    }

    /**
     * Compares two hashes and returns true if they are the same, false otherwise.
     * @param string $hash1 The first hash to compare.
     * @param string $hash2 The second hash to compare.
     * @return bool True if the hashes are the same, false otherwise.
     */
    public static function compareHashes(string $hash1, string $hash2): bool
    {
        return hash_equals($hash1, $hash2);
    }

    /**
     * Generates a hash for a given string of data.
     * @param string $data The string of data to hash.
     * @return string The generated hash.
     */
    public function hashData(string $data): string
    {
        return hash($this->algorithm, $data);
    }

    /**
     * Verifies that a given hash matches the hash of a given string of data.
     * @param string $data The string of data to verify.
     * @param string $hash The hash to compare against.
     * @return bool True if the hash matches, false otherwise.
     */
    public function verifyHash(string $data, string $hash): bool
    {
        return hash_equals(hash($this->algorithm, $data), $hash);
    }

    /**
     * Generates a HMAC for a given string of data and key.
     * @param string $data The string of data to generate the HMAC for.
     * @param string $key The key to use for the HMAC.
     * @return string The generated HMAC.
     */
    public function hmac(string $data, string $key): string
    {
        return hash_hmac($this->algorithm, $data, $key);
    }

    /**
     * Verifies that a given HMAC matches the HMAC of a given string of data and key.
     * @param string $data The string of data to verify.
     * @param string $key The key to use for the HMAC.
     * @param string $hmac The HMAC to compare against.
     * @return bool True if the HMAC matches, false otherwise.
     */
    public function verifyHmac(string $data, string $key, string $hmac): bool
    {
        return hash_equals(hash_hmac($this->algorithm, $data, $key), $hmac);
    }

}