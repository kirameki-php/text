<?php declare(strict_types=1);

namespace Kirameki\Text;

use Stringable;
use function basename;
use function dirname;
use function sprintf;

class StrBuffer implements Stringable
{
    protected static Str $ref;

    /**
     * @param string $value
     * @return static
     */
    public static function from(string $value): static
    {
        return new static($value);
    }

    /**
     * @param string $value
     */
    public function __construct(protected string $value = '')
    {
        static::$ref ??= new Str();
    }

    /**
     * @see Str::after()
     *
     * @param string $search
     * The substring to look for.
     * @return static
     */
    public function after(string $search): static
    {
        return new static(static::$ref::after($this->value, $search));
    }

    /**
     * @see Str::afterLast()
     *
     * @param string $search
     * The substring to look for.
     * @return static
     */
    public function afterLast(string $search): static
    {
        return new static(static::$ref::afterLast($this->value, $search));
    }

    /**
     * Appends the given string(s) to the end of the current string.
     *
     * @param string ...$string
     * @return static
     */
    public function append(string ...$string): static
    {
        return new static(static::$ref::concat($this->value, ...$string));
    }

    /**
     * Appends the given string(s) to the end of the current string.
     * The string(s) will be formatted using sprintf().
     *
     * @param string $format
     * @param float|int|string ...$values
     * @return static
     */
    public function appendFormat(string $format, float|int|string ...$values): static
    {
        return new static($this->value . sprintf($format, ...$values));
    }

    /**
     * @see basename()
     *
     * Calls and returns the result of calling basename() on the current string.
     *
     * @param string $suffix
     * @return static
     */
    public function basename(string $suffix = ''): static
    {
        return new static(basename($this->value, $suffix));
    }

    /**
     * @see Str::before()
     *
     * @param string $search
     * The substring to look for.
     * @return static
     */
    public function before(string $search): static
    {
        return new static(static::$ref::before($this->value, $search));
    }

    /**
     * @see Str::beforeLast()
     *
     * @param string $search
     * The substring to look for.
     * @return static
     */
    public function beforeLast(string $search): static
    {
        return new static(static::$ref::beforeLast($this->value, $search));
    }

    /**
     * @see Str::between()
     *
     * @param string $from
     * @param string $to
     * @return static
     */
    public function between(string $from, string $to): static
    {
        return new static(static::$ref::between($this->value, $from, $to));
    }

    /**
     * @see Str::betweenFurthest()
     *
     * @param string $from
     * @param string $to
     * @return static
     */
    public function betweenFurthest(string $from, string $to): static
    {
        return new static(static::$ref::betweenFurthest($this->value, $from, $to));
    }

    /**
     * @see Str::betweenLast()
     *
     * @param string $from
     * @param string $to
     * @return static
     */
    public function betweenLast(string $from, string $to): static
    {
        return new static(static::$ref::betweenLast($this->value, $from, $to));
    }

    /**
     * @see Str::capitalize()
     *
     * @return static
     */
    public function capitalize(): static
    {
        return new static(static::$ref::capitalize($this->value));
    }

    /**
     * @see Str::chunk()
     *
     * @param int $size
     * @param int|null $limit
     * @return list<string>
     */
    public function chunk(int $size, ?int $limit = null): array
    {
        return static::$ref::chunk($this->value, $size, $limit);
    }

    /**
     * @param string $needle
     * @return bool
     */
    public function contains(string $needle): bool
    {
        return static::$ref::contains($this->value, $needle);
    }

    /**
     * @param array<string> $needles
     * @return bool
     */
    public function containsAll(array $needles): bool
    {
        return static::$ref::containsAll($this->value, $needles);
    }

    /**
     * @param array<string> $needles
     * @return bool
     */
    public function containsAny(array $needles): bool
    {
        return static::$ref::containsAny($this->value, $needles);
    }

    /**
     * @param string $pattern
     * @return bool
     */
    public function containsPattern(string $pattern): bool
    {
        return static::$ref::containsPattern($this->value, $pattern);
    }

    /**
     * @param string $substring
     * @param bool $overlapping
     * @return int
     */
    public function count(string $substring, bool $overlapping = false): int
    {
        return static::$ref::count($this->value, $substring, $overlapping);
    }

    /**
     * @return static
     */
    public function decapitalize(): static
    {
        return new static(static::$ref::decapitalize($this->value));
    }

    /**
     * @param int<1, max> $levels
     * @return static
     */
    public function dirname(int $levels = 1): static
    {
        return new static(dirname($this->value, $levels));
    }

    /**
     * @param string $needle
     * @return bool
     */
    public function doesNotContain(string $needle): bool
    {
        return static::$ref::doesNotContain($this->value, $needle);
    }

    /**
     * @param string|iterable<array-key, string> $needle
     * @return bool
     */
    public function doesNotEndWith(string|iterable $needle): bool
    {
        return static::$ref::doesNotEndWith($this->value, $needle);
    }

    /**
     * @param string|iterable<array-key, string> $needle
     * @return bool
     */
    public function doesNotStartWith(string|iterable $needle): bool
    {
        return static::$ref::doesNotStartWith($this->value, $needle);
    }

    /**
     * @param int $amount
     * @return static
     */
    public function dropFirst(int $amount): static
    {
        return new static(static::$ref::dropFirst($this->value, $amount));
    }

    /**
     * @param int $amount
     * @return static
     */
    public function dropLast(int $amount): static
    {
        return new static(static::$ref::dropLast($this->value, $amount));
    }

    /**
     * @param string|iterable<array-key, string> $needle
     * @return bool
     */
    public function endsWith(string|iterable $needle): bool
    {
        return static::$ref::endsWith($this->value, $needle);
    }

    /**
     * @param string $needle
     * @param int $offset
     * @return int|null
     */
    public function indexOfFirst(string $needle, int $offset = 0): ?int
    {
        return static::$ref::indexOfFirst($this->value, $needle, $offset);
    }

    /**
     * @param string $needle
     * @param int $offset
     * @return int|null
     */
    public function indexOfLast(string $needle, int $offset = 0): ?int
    {
        return static::$ref::indexOfLast($this->value, $needle, $offset);
    }

    /**
     * @param int $position
     * @param string $insert
     * @return static
     */
    public function insert(string $insert, int $position): static
    {
        return new static(static::$ref::insert($this->value, $insert, $position));
    }

    /**
     * @return bool
     */
    public function isBlank(): bool
    {
        return static::$ref::isBlank($this->value);
    }

    /**
     * @return bool
     */
    public function isNotBlank(): bool
    {
        return static::$ref::isNotBlank($this->value);
    }

    /**
     * @return int
     */
    public function length(): int
    {
        return static::$ref::length($this->value);
    }

    /**
     * @param string $pattern
     * @return array<int, array<string>>
     */
    public function matchAll(string $pattern): array
    {
        return static::$ref::matchAll($this->value, $pattern);
    }

    /**
     * @param string $pattern
     * @return string
     */
    public function matchFirst(string $pattern): string
    {
        return static::$ref::matchFirst($this->value, $pattern);
    }

    /**
     * @param int $length
     * @param string $pad
     * @return static
     */
    public function padBoth(int $length, string $pad = ' '): static
    {
        return new static(static::$ref::padBoth($this->value, $length, $pad));
    }

    /**
     * @param int $length
     * @param string $pad
     * @return static
     */
    public function padStart(int $length, string $pad = ' '): static
    {
        return new static(static::$ref::padStart($this->value, $length, $pad));
    }

    /**
     * @param int $length
     * @param string $pad
     * @return static
     */
    public function padEnd(int $length, string $pad = ' '): static
    {
        return new static(static::$ref::padEnd($this->value, $length, $pad));
    }

    /**
     * @param string ...$string
     * @return static
     */
    public function prepend(string ...$string): static
    {
        $string[] = $this->value;
        return new static(static::$ref::concat(...$string));
    }

    /**
     * @param string $search
     * @param int|null $limit
     * @param int $count
     * @return static
     */
    public function remove(string $search, ?int $limit = null, int &$count = 0): static
    {
        return new static(static::$ref::remove($this->value, $search, $limit ?? -1, $count));
    }

    /**
     * @param int<0, max> $times
     * @return static
     */
    public function repeat(int $times): static
    {
        return new static(static::$ref::repeat($this->value, $times));
    }

    /**
     * @param string $search
     * @param string $replace
     * @return static
     */
    public function replace(string $search, string $replace): static
    {
        return new static(static::$ref::replace($this->value, $search, $replace));
    }

    /**
     * @param string $search
     * @param string $replace
     * @return static
     */
    public function replaceFirst(string $search, string $replace): static
    {
        return new static(static::$ref::replaceFirst($this->value, $search, $replace));
    }

    /**
     * @param string $search
     * @param string $replace
     * @return static
     */
    public function replaceLast(string $search, string $replace): static
    {
        return new static(static::$ref::replaceLast($this->value, $search, $replace));
    }

    /**
     * @param string $pattern
     * @param string $replace
     * @param int|null $limit
     * @return static
     */
    public function replaceMatch(string $pattern, string $replace, ?int $limit = null): static
    {
        return new static(static::$ref::replaceMatch($this->value, $pattern, $replace, $limit ?? -1));
    }

    /**
     * @return static
     */
    public function reverse(): static
    {
        return new static(static::$ref::reverse($this->value));
    }

    /**
     * @param non-empty-string $separator
     * @param int<0, max>|null $limit
     * @return array<int, string>
     */
    public function split(string $separator, ?int $limit = null): array
    {
        return static::$ref::split($this->value, $separator, $limit);
    }

    /**
     * @param string|iterable<array-key, string> $needle
     * @return bool
     */
    public function startsWith(string|iterable $needle): bool
    {
        return static::$ref::startsWith($this->value, $needle);
    }

    /**
     * @param int $offset
     * @param int|null $length
     * @return static
     */
    public function substring(int $offset, ?int $length = null): static
    {
        return new static(static::$ref::substring($this->value, $offset, $length));
    }

    /**
     * @param int $position
     * @return static
     */
    public function takeFirst(int $position): static
    {
        return new static(static::$ref::takeFirst($this->value, $position));
    }

    /**
     * @return static
     */
    public function toCamelCase(): static
    {
        return new static(static::$ref::toCamelCase($this->value));
    }

    /**
     * @return static
     */
    public function toKebabCase(): static
    {
        return new static(static::$ref::toKebabCase($this->value));
    }

    /**
     * @return static
     */
    public function toLowerCase(): static
    {
        return new static(static::$ref::toLowerCase($this->value));
    }

    /**
     * @return static
     */
    public function toPascalCase(): static
    {
        $this->value = static::$ref::toPascalCase($this->value);
        return $this;
    }

    /**
     * @return static
     */
    public function toUpperCase(): static
    {
        return new static(static::$ref::toUpperCase($this->value));
    }

    /**
     * @return static
     */
    public function toSnakeCase(): static
    {
        return new static(static::$ref::toSnakeCase($this->value));
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * @param string $characters
     * @return static
     */
    public function trim(string $characters = '\s'): static
    {
        return new static(static::$ref::trim($this->value, $characters));
    }

    /**
     * @param string $characters
     * @return static
     */
    public function trimStart(string $characters = '\s'): static
    {
        return new static(static::$ref::trimStart($this->value, $characters));
    }

    /**
     * @param string $characters
     * @return static
     */
    public function trimEnd(string $characters = '\s'): static
    {
        return new static(static::$ref::trimEnd($this->value, $characters));
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @param string $string
     * @return static
     */
    public static function of(string $string): static
    {
        return new static($string);
    }
}