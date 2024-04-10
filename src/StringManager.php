<?php

namespace Ispahbod\StringManager;

use Random\RandomException;

class StringManager
{
    public static function sanitizeUsername(string $input): string
    {
        $input = preg_replace('/\W/', '', $input);
        $input = preg_replace('/_{2,}/', '_', $input);
        $input = trim($input, '_');
        return strtolower($input);
    }

    public static function sanitizeSlug(string $input): string
    {
        $input = preg_replace('/^\d+/', '', $input);
        $input = preg_replace('/\W+/', '-', $input);
        $input = preg_replace('/-{2,}/', '-', $input);
        return trim($input, '-');
    }

    public static function containsArray(string $input, array $array, bool $all = false): bool
    {
        $contains = [];
        foreach ($array as $element) {
            $contains[] = str_contains($input, $element);
        }
        if ($all) {
            return count(array_filter($contains)) === count($array);
        }

        return count(array_filter($contains)) > 0;
    }

    public static function containsPattern(string $input, string $pattern): bool
    {
        return (bool)preg_match($pattern, $input);
    }

    public static function containsText(string $input, string $searchTerm): bool
    {
        return str_contains($input, $searchTerm);
    }

    public static function filterEnglishAlphabetCharacters(string $input, bool $removeSpaces = true): string
    {
        $pattern = $removeSpaces ? '/[^a-zA-Z]/' : '/[^a-zA-Z ]/';
        return trim(preg_replace($pattern, '', $input));
    }

    public static function filterEnglishAlphanumericCharacters(string $input, bool $removeSpaces = true): string
    {
        $pattern = $removeSpaces ? '/[^a-zA-Z0-9]/' : '/[^a-zA-Z0-9 ]/';
        return trim(preg_replace($pattern, '', $input));
    }

    public static function filterNumericCharacters(string $input, bool $removeSpaces = true): string
    {
        $pattern = $removeSpaces ? '/[^0-9]/' : '/[^0-9 ]/';
        return trim(preg_replace($pattern, '', $input));
    }

    public static function matchPattern(string $input, string $pattern): bool
    {
        return (bool)preg_match('/' . str_replace('/', '\/', $pattern) . '/', $input);
    }

    public static function removeCharacters(string $input, string|array $charactersToRemove): string
    {
        if (is_array($charactersToRemove)) {
            $charactersToRemove = implode('', $charactersToRemove);
        }
        return preg_replace('/[' . preg_quote($charactersToRemove, '/') . ']/u', '', $input);
    }

    public static function removeSpaces(string $input): string
    {
        return str_replace(' ', '', $input);
    }

    public static function reverse(string $input): string
    {
        return strrev($input);
    }

    public static function truncate(string $input, int $maxChars, string $ellipsis = '...'): string
    {
        if ($maxChars < 1) {
            return '';
        }

        $textLength = mb_strlen($input, 'utf-8');
        if ($textLength <= $maxChars) {
            return $input;
        }

        $truncatedText = mb_substr($input, 0, $maxChars, 'utf-8');
        $truncatedText = rtrim($truncatedText);
        $truncatedText .= $ellipsis;

        return $truncatedText;
    }

    public static function validateUuid(string $input): bool
    {
        $pattern = '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/';
        return (bool)preg_match($pattern, $input);
    }

    public static function capitalizeFirstLetter(string $input): string
    {
        return ucfirst($input);
    }

    public static function convertToCamelCase(string $input): string
    {
        $input = strtolower($input);
        $input = preg_replace('/[^a-zA-Z0-9]+/', ' ', $input);
        $input = ucwords($input);
        $input = str_replace(' ', '', $input);
        return lcfirst($input);
    }

    public static function generateRandomString(int $length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function convertToSnakeCase(string $input): string
    {
        $input = preg_replace('/\s+/u', '', ucwords($input));
        return strtolower(preg_replace('/(?<!^)[A-Z]/u', '_$0', $input));
    }

    public static function obfuscateEmail(string $email): string
    {
        return str_replace(['@', '.'], [' at ', ' dot '], $email);
    }

    /**
     * @throws RandomException
     */
    public static function generateUuidV4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function extractEmails(string $input): array
    {
        preg_match_all('/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}/i', $input, $matches);
        return $matches[0];
    }

    public static function convertToTitleCase(string $input): string
    {
        return ucwords(strtolower($input));
    }

    public static function maskString(string $input, int $start = 0, int $length = null, string $maskChar = '*'): string
    {
        if (is_null($length)) {
            $length = strlen($input) - $start;
        }
        $mask = str_repeat($maskChar, $length);
        return substr_replace($input, $mask, $start, $length);
    }

    public static function levenshteinDistance(string $input1, string $input2): int
    {
        return levenshtein($input1, $input2);
    }

    public static function similarTextPercentage(string $input1, string $input2): float
    {
        similar_text($input1, $input2, $percent);
        return $percent;
    }

    public static function convertToKebabCase(string $input): string
    {
        $input = preg_replace('/\s+/u', '-', ucwords($input));
        $input = strtolower(preg_replace('/(?<!^)[A-Z]/u', '-$0', $input));
        return trim($input, '-');
    }

    public static function identifyLoginFieldType(string $input): string
    {
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }
        return (preg_match('/^\+?\d{10,15}$/', $input)) ? 'phone_number' : 'username';
    }
}