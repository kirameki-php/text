<?php declare(strict_types=1);

namespace Kirameki\Text;

use Stringable;
use function basename;
use function dirname;
use function sprintf;

class StringBuilder implements Stringable
{
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
    }

    /**
     * @param string $search
     * @return static
     */
    public function afterFirst(string $search): static
    {
        return new static(Unicode::afterFirst($this->value, $search));
    }

    /**
     * @param string $search
     * @return static
     */
    public function afterLast(string $search): static
    {
        return new static(Unicode::afterLast($this->value, $search));
    }

    /**
     * @param string ...$string
     * @return static
     */
    public function append(string ...$string): static
    {
        return new static(Unicode::concat($this->value, ...$string));
    }

    /**
     * @param string $format
     * @param float|int|string ...$values
     * @return static
     */
    public function appendFormat(string $format, float|int|string ...$values): static
    {
        return new static($this->value . sprintf($format, ...$values));
    }

    /**
     * @param string $suffix
     * @return static
     */
    public function basename(string $suffix = ''): static
    {
        return new static(basename($this->value, $suffix));
    }

    /**
     * @param string $search
     * @return static
     */
    public function beforeFirst(string $search): static
    {
        return new static(Unicode::beforeFirst($this->value, $search));
    }

    /**
     * @param string $search
     * @return static
     */
    public function beforeLast(string $search): static
    {
        return new static(Unicode::beforeLast($this->value, $search));
    }

    /**
     * @param string $from
     * @param string $to
     * @return static
     */
    public function between(string $from, string $to): static
    {
        return new static(Unicode::between($this->value, $from, $to));
    }

    /**
     * @return int
     */
    public function byteLength(): int
    {
        return Unicode::byteLength($this->value);
    }

    /**
     * @return static
     */
    public function capitalize(): static
    {
        return new static(Unicode::capitalize($this->value));
    }

    /**
     * @param string $needle
     * @return bool
     */
    public function contains(string $needle): bool
    {
        return Unicode::contains($this->value, $needle);
    }

    /**
     * @param array<string> $needles
     * @return bool
     */
    public function containsAll(array $needles): bool
    {
        return Unicode::containsAll($this->value, $needles);
    }

    /**
     * @param array<string> $needles
     * @return bool
     */
    public function containsAny(array $needles): bool
    {
        return Unicode::containsAny($this->value, $needles);
    }

    /**
     * @param string $pattern
     * @return bool
     */
    public function containsPattern(string $pattern): bool
    {
        return Unicode::containsPattern($this->value, $pattern);
    }
    /**
     * @param int $position
     * @param string $ellipsis
     * @return static
     */
    public function cut(int $position, string $ellipsis = ''): static
    {
        return new static(Unicode::cut($this->value, $position, $ellipsis));
    }

    /**
     * @return static
     */
    public function decapitalize(): static
    {
        return new static(Unicode::decapitalize($this->value));
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
        return Unicode::doesNotContain($this->value, $needle);
    }

    /**
     * @param string|iterable<array-key, string> $needle
     * @return bool
     */
    public function doesNotEndWith(string|iterable $needle): bool
    {
        return Unicode::doesNotEndWith($this->value, $needle);
    }

    /**
     * @param string|iterable<array-key, string> $needle
     * @return bool
     */
    public function doesNotStartWith(string|iterable $needle): bool
    {
        return Unicode::doesNotStartWith($this->value, $needle);
    }

    /**
     * @param int $position
     * @return static
     */
    public function dropFirst(int $position): static
    {
        return new static(Unicode::dropFirst($this->value, $position));
    }

    /**
     * @param string|iterable<array-key, string> $needle
     * @return bool
     */
    public function endsWith(string|iterable $needle): bool
    {
        return Unicode::endsWith($this->value, $needle);
    }

    /**
     * @param string $needle
     * @param int $offset
     * @return int|null
     */
    public function indexOfFirst(string $needle, int $offset = 0): ?int
    {
        return Unicode::indexOfFirst($this->value, $needle, $offset);
    }

    /**
     * @param string $needle
     * @param int $offset
     * @return int|null
     */
    public function indexOfLast(string $needle, int $offset = 0): ?int
    {
        return Unicode::indexOfLast($this->value, $needle, $offset);
    }

    /**
     * @param int $position
     * @param string $insert
     * @return static
     */
    public function insert(string $insert, int $position): static
    {
        return new static(Unicode::insert($this->value, $insert, $position));
    }

    /**
     * @return bool
     */
    public function isBlank(): bool
    {
        return Unicode::isBlank($this->value);
    }

    /**
     * @return bool
     */
    public function isNotBlank(): bool
    {
        return Unicode::isNotBlank($this->value);
    }

    /**
     * @return int
     */
    public function length(): int
    {
        return Unicode::length($this->value);
    }

    /**
     * @param string $pattern
     * @return array<int, array<string>>
     */
    public function matchAll(string $pattern): array
    {
        return Unicode::matchAll($this->value, $pattern);
    }

    /**
     * @param string $pattern
     * @return string
     */
    public function matchFirst(string $pattern): string
    {
        return Unicode::matchFirst($this->value, $pattern);
    }

    /**
     * @param int $length
     * @param string $pad
     * @return static
     */
    public function padBoth(int $length, string $pad = ' '): static
    {
        return new static(Unicode::padBoth($this->value, $length, $pad));
    }

    /**
     * @param int $length
     * @param string $pad
     * @return static
     */
    public function padStart(int $length, string $pad = ' '): static
    {
        return new static(Unicode::padStart($this->value, $length, $pad));
    }

    /**
     * @param int $length
     * @param string $pad
     * @return static
     */
    public function padEnd(int $length, string $pad = ' '): static
    {
        return new static(Unicode::padEnd($this->value, $length, $pad));
    }

    /**
     * @param string ...$string
     * @return static
     */
    public function prepend(string ...$string): static
    {
        $string[] = $this->value;
        return new static(Unicode::concat(...$string));
    }

    /**
     * @param string $search
     * @param int|null $limit
     * @param int $count
     * @return static
     */
    public function remove(string $search, ?int $limit = null, int &$count = 0): static
    {
        return new static(Unicode::remove($this->value, $search, $limit ?? -1, $count));
    }

    /**
     * @param int<0, max> $times
     * @return static
     */
    public function repeat(int $times): static
    {
        return new static(Unicode::repeat($this->value, $times));
    }

    /**
     * @param string $search
     * @param string $replace
     * @return static
     */
    public function replace(string $search, string $replace): static
    {
        return new static(Unicode::replace($this->value, $search, $replace));
    }

    /**
     * @param string $search
     * @param string $replace
     * @return static
     */
    public function replaceFirst(string $search, string $replace): static
    {
        return new static(Unicode::replaceFirst($this->value, $search, $replace));
    }

    /**
     * @param string $search
     * @param string $replace
     * @return static
     */
    public function replaceLast(string $search, string $replace): static
    {
        return new static(Unicode::replaceLast($this->value, $search, $replace));
    }

    /**
     * @param string $pattern
     * @param string $replace
     * @param int|null $limit
     * @return static
     */
    public function replaceMatch(string $pattern, string $replace, ?int $limit = null): static
    {
        return new static(Unicode::replaceMatch($this->value, $pattern, $replace, $limit ?? -1));
    }

    /**
     * @return static
     */
    public function reverse(): static
    {
        return new static(Unicode::reverse($this->value));
    }

    /**
     * @param non-empty-string $separator
     * @param int<0, max>|null $limit
     * @return array<int, string>
     */
    public function split(string $separator, ?int $limit = null): array
    {
        return Unicode::split($this->value, $separator, $limit);
    }

    /**
     * @param string|iterable<array-key, string> $needle
     * @return bool
     */
    public function startsWith(string|iterable $needle): bool
    {
        return Unicode::startsWith($this->value, $needle);
    }

    /**
     * @param int $offset
     * @param int|null $length
     * @return static
     */
    public function substring(int $offset, ?int $length = null): static
    {
        return new static(Unicode::substring($this->value, $offset, $length));
    }

    /**
     * @param int $position
     * @return static
     */
    public function takeFirst(int $position): static
    {
        return new static(Unicode::takeFirst($this->value, $position));
    }

    /**
     * @return static
     */
    public function toCamelCase(): static
    {
        return new static(Unicode::toCamelCase($this->value));
    }

    /**
     * @return static
     */
    public function toKebabCase(): static
    {
        return new static(Unicode::toKebabCase($this->value));
    }

    /**
     * @return static
     */
    public function toLowerCase(): static
    {
        return new static(Unicode::toLowerCase($this->value));
    }

    /**
     * @return static
     */
    public function toPascalCase(): static
    {
        $this->value = Unicode::toPascalCase($this->value);
        return $this;
    }

    /**
     * @return static
     */
    public function toUpperCase(): static
    {
        return new static(Unicode::toUpperCase($this->value));
    }

    /**
     * @return static
     */
    public function toSnakeCase(): static
    {
        return new static(Unicode::toSnakeCase($this->value));
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
        return new static(Unicode::trim($this->value, $characters));
    }

    /**
     * @param string $characters
     * @return static
     */
    public function trimStart(string $characters = '\s'): static
    {
        return new static(Unicode::trimStart($this->value, $characters));
    }

    /**
     * @param string $characters
     * @return static
     */
    public function trimEnd(string $characters = '\s'): static
    {
        return new static(Unicode::trimEnd($this->value, $characters));
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
