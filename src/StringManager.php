<?php

namespace Ispahbod\StringManager;

use Ispahbod\EmailManager\EmailManager;
use Random\RandomException;

class StringManager
{
    public static function sanitizeUsername(string $input, $toLowerCase = true): string
    {
        $input = self::convertPersianNumbersToEnglish($input);
        $input = preg_replace('/\s+/', '_', $input);
        $input = preg_replace('/\W/', '', $input);
        $input = preg_replace('/_{2,}/', '_', $input);
        $input = trim($input, '_');
        return $toLowerCase ? strtolower($input) : $input;
    }

    public static function sanitizeEmail(string $input): string
    {
        $input = self::convertPersianNumbersToEnglish($input);
        return EmailManager::cleanEmail($input);
    }

    public static function sanitizePassword(string $input): string
    {
        $input = self::convertPersianNumbersToEnglish($input);
        return strtolower($input);
    }

    public static function sanitizeSlug(string $input, $toLowerCase = true): string
    {
        $input = self::convertPersianNumbersToEnglish($input);
        $input = preg_replace('/^\d+/', '', $input);
        $input = preg_replace('/\W+/', '-', $input);
        $input = preg_replace('/-{2,}/', '-', $input);
        $input = trim($input, '-');
        return $toLowerCase ? strtolower($input) : $input;
    }

    public static function convertPersianNumbersToEnglish(string $input): string
    {
        $persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return str_replace($persianNumbers, $englishNumbers, $input);
    }

    public static function isInArray(?string $input, array $array): bool
    {
        return is_null($input) ? false : in_array($input, $array);
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
        return round($percent, 1);
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

    public static function formatAsBold(...$strings): string
    {
        return '<b>' . implode(' ', $strings) . '</b>';
    }

    public static function formatAsItalic(...$strings): string
    {
        return '<i>' . implode(' ', $strings) . '</i>';
    }

    public static function formatAsUnderline(...$strings): string
    {
        return '<u>' . implode(' ', $strings) . '</u>';
    }

    public static function formatAsStrike(...$strings): string
    {
        return '<strike>' . implode(' ', $strings) . '</strike>';
    }

    public static function createLink($text, $url): string
    {
        return '<a href="' . htmlspecialchars($url) . '">' . $text . '</a>';
    }

    public static function formatAsCode($text): string
    {
        return "<code>$text</code>";
    }

    public static function formatAsPre(...$strings): string
    {
        return '<pre>' . implode(' ', $strings) . '</pre>';
    }

    public static function formatAsBlockquote(...$strings): string
    {
        return '<blockquote>' . implode(' ', $strings) . '</blockquote>';
    }

    public static function concatenate(...$strings): string
    {
        return implode('', $strings);
    }

    public static function applyEach(array $array, callable $callable, ?int $limit = null): string
    {
        $result = array_map($callable, $array);
        if ($limit !== null) {
            $result = array_slice($result, 0, $limit);
        }
        return implode(' ', $result);
    }

    public static function singleNewLine(): string
    {
        return PHP_EOL;
    }

    public static function doubleNewLine(): string
    {
        return PHP_EOL . PHP_EOL;
    }

    public static function joinWithNewLine($array): string
    {
        return implode(PHP_EOL, $array);
    }

    public static function repeatString($text, $times): string
    {
        return str_repeat($text, $times);
    }

    public static function repeatStringWithNewLine($text, $times): string
    {
        return rtrim(str_repeat($text . PHP_EOL, $times), PHP_EOL);
    }

    public static function concatenateWithSpace(...$strings): string
    {
        return implode(' ', $strings);
    }

    public static function concatenateWithNewLine(...$strings): string
    {
        return implode(' ', $strings) . PHP_EOL;
    }

    public static function concatenateWithDoubleNewLine(...$strings): string
    {
        return implode(' ', $strings) . PHP_EOL . PHP_EOL;
    }

    public static function concatenateWithLeadingNewLine(...$strings): string
    {
        return PHP_EOL . implode(' ', $strings);
    }

    public static function conditionalOutput($condition, $trueValue, $falseValue = ''): string
    {
        return $condition ? $trueValue : $falseValue;
    }
}
