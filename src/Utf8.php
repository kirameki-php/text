<?php declare(strict_types=1);

namespace Kirameki\Text;

use IntlException;
use RuntimeException;
use ValueError;
use function array_reverse;
use function assert;
use function dump;
use function grapheme_extract;
use function grapheme_strlen;
use function grapheme_strpos;
use function grapheme_strrpos;
use function grapheme_substr;
use function implode;
use function ini_get;
use function intl_get_error_message;
use function mb_strtolower;
use function mb_strtoupper;
use function strlen;
use function strrev;
use const GRAPHEME_EXTR_COUNT;
use const PHP_EOL;

class Utf8 extends Str
{
    /**
     * Counts the size of bytes for the given string.
     *
     * Example:
     * ```php
     * Utf8::byteLength('a'); // 1
     * Utf8::byteLength('あ'); // 3
     * Utf8::byteLength('👨‍👨‍👧‍👦'); // 25
     * ```
     *
     * @param string $string
     * The target string being counted.
     * @return int
     * The byte size of the given string.
     */
    public static function byteLength(string $string): int
    {
        return strlen($string);
    }

    /**
     * Determine if a string contains a given substring.
     *
     * Example:
     * ```php
     * Utf8::contains('Foo bar', 'bar'); // true
     * Utf8::contains('👨‍👨‍👧‍👧‍', '👨'); // false
     * Utf8::contains('a', ''); // true
     * Utf8::contains('', ''); // true
     * ```
     *
     * @inheritDoc
     */
    public static function contains(string $string, string $substring): bool
    {
        return grapheme_strpos($string, $substring) !== false;
    }

    /**
     * Extracts the substring from string based on the given position.
     * The position is determined by bytes.
     * If the position happens to be between a multibyte character, the cut is performed on the entire character.
     * Grapheme string is not supported for this method.
     *
     * Example:
     * ```php
     * Utf8::cut('abc', 1); // 'a'
     * Utf8::cut('あいう', 1); // '' since あ is 3 bytes long.
     * ```
     *
     * @param string $string
     * The string to look in.
     * @param int $position
     * The position where the string will be cut.
     * @param string $ellipsis
     * [Optional] An ellipsis which will be appended to the cut string if string is greater than cut string. Defaults to **''**.
     * @return string
     */
    public static function cut(string $string, int $position, string $ellipsis = self::EMPTY): string
    {
        assert(ini_get('intl.use_exceptions'), 'intl.use_exceptions must be enabled to use this method.');

        if ($string === '') {
            return $string;
        }

        $parts = [];
        $addEllipsis = true;

        try {
            $offset = 0;
            while ($offset <= $position) {
                $char = grapheme_extract($string, 1, GRAPHEME_EXTR_COUNT, $offset, $offset);
                if ($offset > $position) {
                    break;
                }
                $parts[] = $char;
            }
        } catch (IntlException $e) {
            if ($e->getMessage() === 'grapheme_extract: start not contained in string') {
                $addEllipsis = false;
            } else {
                throw $e;
            }
        }

        if ($ellipsis !== self::EMPTY && $addEllipsis) {
            $parts[] = $ellipsis;
        }

        return implode(self::EMPTY, $parts);
    }

    /**
     * Find position (in grapheme units) of first occurrence of substring in string.
     *
     * Example:
     * ```php
     * Utf8::firstIndexOf('abb', 'b'); // 1
     * Utf8::firstIndexOf('abb', 'b', 2); // 2
     * Utf8::firstIndexOf('abb', 'b', 3); // null
     * ```
     *
     * @inheritDoc
     */
    public static function indexOfFirst(string $string, string $substring, int $offset = 0): ?int
    {
        try {
            $result = grapheme_strpos($string, $substring, $offset);
            return $result !== false ? $result : null;
        } catch (ValueError $e) {
            if ($e->getMessage() === 'grapheme_strpos(): Argument #3 ($offset) must be contained in argument #1 ($haystack)') {
                return null;
            }
            throw $e;
        }
    }

    /**
     * Find position (in grapheme units) of last occurrence of substring in string.
     *
     * Example:
     * ```php
     * Utf8::indexOfLast('abb', 'b'); // 2
     * Utf8::indexOfLast('abb', 'b', 2); // 2
     * Utf8::indexOfLast('abb', 'b', 3); // null
     * ```
     *
     * @inheritDoc
     */
    public static function indexOfLast(string $string, string $substring, int $offset = 0): ?int
    {
        try {
            $result = grapheme_strrpos($string, $substring, $offset);
            return $result !== false ? $result : null;
        } catch (ValueError $e) {
            if ($e->getMessage() === 'grapheme_strrpos(): Argument #3 ($offset) must be contained in argument #1 ($haystack)') {
                return null;
            }
            throw $e;
        }
    }

    /**
     * Returns the length of the given string.
     * Works with multibyte and grapheme(emoji) strings.
     *
     * Example:
     * ```php
     * Utf8::length(''); // 0
     * Utf8::length('開発'); // 2
     * Utf8::length('👨‍👨‍👧‍👦'); // 1
     * ```
     *
     * @inheritDoc
     */
    public static function length(string $string): int
    {
        $result = grapheme_strlen($string);

        if ($result === null) {
            throw new RuntimeException(intl_get_error_message());
        }

        // @codeCoverageIgnoreStart
        if ($result === false) {
            throw new RuntimeException(
                'Unknown internal error has occurred.' . PHP_EOL .
                'Please see the link below for more info.' . PHP_EOL .
                'https://github.com/php/php-src/blob/9bae9ab/ext/intl/grapheme/grapheme_string.c'
            );
        }
        // @codeCoverageIgnoreEnd

        return $result;
    }

    /**
     * @inheritDoc
     */
    public static function reverse(string $string): string
    {
        $bytes = strlen($string);

        // strrev($string) can only reverse bytes, so it only works for single byte chars.
        // So call strrev only if we can confirm that it only contains single byte chars.
        if (static::length($string) === $bytes) {
            return strrev($string);
        }

        $offset = 0;
        $parts = [];
        while ($offset < $bytes) {
            $char = grapheme_extract($string, 1, GRAPHEME_EXTR_COUNT, $offset, $offset);
            if ($char !== false) {
                $parts[] = $char;
            }
        }
        return implode(self::EMPTY, array_reverse($parts));
    }

    /**
     * Return a subset of given string.
     * If offset is out of range, a RuntimeException is thrown unless a fallback string is defined.
     *
     * Example:
     * ```php
     * Utf8::substring('abc', 1); // 'a'
     * Utf8::substring('abc', 0, 1); // 'a'
     * Utf8::substring('abc', 1, 2); // 'bc'
     * Utf8::substring('a', 1); // RuntimeException: Offset: 1 is out of range for string "a"
     * Utf8::substring('👨‍👨‍👧‍👦', 1, 'not found'); // 'not found'
     * ```
     *
     * @inheritDoc
     */
    public static function substring(string $string, int $offset, ?int $length = null): string
    {
        $substring = grapheme_substr($string, $offset, $length);

        if ($substring === false) {
            throw new RuntimeException(intl_get_error_message());
        }

        return $substring;
    }

    /**
     * Convert the given string to lower case.
     * Supports multibyte strings.
     *
     * Example:
     * ```php
     * Utf8::toLowerCase('AbCd'); // 'abcd'
     * Utf8::toLowerCase('ÇĞİÖŞÜ'); // 'çği̇öşü'
     * ```
     *
     * @param string $string
     * The string being lower-cased.
     * @return string
     * String with all alphabetic characters converted to lower case.
     */
    public static function toLowerCase(string $string): string
    {
        return mb_strtolower($string);
    }

    /**
     * Convert the given string to upper case.
     * Supports multibyte strings.
     *
     * Example:
     * ```php
     * Utf8::toUpperCase('AbCd'); // 'ABCD'
     * Utf8::toUpperCase('çği̇öşü'); // ÇĞİÖŞÜ
     * ```
     *
     * @inheritDoc
     */
    public static function toUpperCase(string $string): string
    {
        return mb_strtoupper($string);
    }

}
