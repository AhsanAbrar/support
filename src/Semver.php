<?php

namespace AhsanDev\Support;

use InvalidArgumentException;

class Semver
{
    /**
     * Compares two semantic version strings.
     */
    public static function compare(string $version1, string $operator, string $version2): bool
    {
        $validOperators = ['<', '<=', '>', '>=', '==', '!=', '==='];

        if (!in_array($operator, $validOperators, true)) {
            throw new InvalidArgumentException("Invalid operator. Use one of: " . implode(', ', $validOperators));
        }

        $normalizedVersion1 = self::normalizeVersion($version1);
        $normalizedVersion2 = self::normalizeVersion($version2);

        return version_compare($normalizedVersion1, $normalizedVersion2, $operator);
    }

    /**
     * Checks if a new version is available.
     */
    public static function isNewVersionAvailable(string $currentVersion, string $newVersion): bool
    {
        return self::compare($currentVersion, '<', $newVersion);
    }

    /**
     * Normalizes a semantic version to ensure it has three parts.
     */
    private static function normalizeVersion(string $version): string
    {
        $parts = explode('.', $version);

        while (count($parts) < 3) {
            $parts[] = '0';
        }

        return implode('.', $parts);
    }
}
