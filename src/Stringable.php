<?php declare(strict_types=1);

namespace Kirameki\Text;

use Kirameki\Utils\Str;
use Stringable as StringableInterface;
use function basename;
use function dirname;
use function sprintf;

class Stringable implements StringableInterface
{
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
    public function after(string $search): static
    {
        return new static(Str::after($this->value, $search));
    }

    /**
     * @param int $position
     * @return static
     */
    public function afterIndex(int $position): static
    {
        return new static(Str::afterIndex($this->value, $position));
    }

    /**
     * @param string $search
     * @return static
     */
    public function afterLast(string $search): static
    {
        return new static(Str::afterLast($this->value, $search));
    }

    /**
     * @param string ...$string
     * @return static
     */
    public function append(string ...$string): static
    {
        return new static(Str::concat($this->value, ...$string));
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
    public function before(string $search): static
    {
        return new static(Str::before($this->value, $search));
    }

    /**
     * @param int $position
     * @return static
     */
    public function beforeIndex(int $position): static
    {
        return new static(Str::beforeIndex($this->value, $position));
    }

    /**
     * @param string $search
     * @return static
     */
    public function beforeLast(string $search): static
    {
        return new static(Str::beforeLast($this->value, $search));
    }

    /**
     * @param string $from
     * @param string $to
     * @return static
     */
    public function between(string $from, string $to): static
    {
        return new static(Str::between($this->value, $from, $to));
    }

    /**
     * @return int
     */
    public function bytes(): int
    {
        return Str::bytes($this->value);
    }

    /**
     * @return static
     */
    public function camelCase(): static
    {
        return new static(Str::camelCase($this->value));
    }

    /**
     * @return static
     */
    public function capitalize(): static
    {
        return new static(Str::capitalize($this->value));
    }

    /**
     * @param string $needle
     * @return bool
     */
    public function contains(string $needle): bool
    {
        return Str::contains($this->value, $needle);
    }

    /**
     * @param array<string> $needles
     * @return bool
     */
    public function containsAll(array $needles): bool
    {
        return Str::containsAll($this->value, $needles);
    }

    /**
     * @param array<string> $needles
     * @return bool
     */
    public function containsAny(array $needles): bool
    {
        return Str::containsAny($this->value, $needles);
    }

    /**
     * @param string $pattern
     * @return bool
     */
    public function containsPattern(string $pattern): bool
    {
        return Str::containsPattern($this->value, $pattern);
    }
    /**
     * @param int $position
     * @param string $ellipsis
     * @return static
     */
    public function cut(int $position, string $ellipsis = ''): static
    {
        return new static(Str::cut($this->value, $position, $ellipsis));
    }

    /**
     * @return static
     */
    public function decapitalize(): static
    {
        return new static(Str::decapitalize($this->value));
    }

    /**
     * @param string $search
     * @param int|null $limit
     * @return static
     */
    public function delete(string $search, ?int $limit = null): static
    {
        return new static(Str::delete($this->value, $search, $limit ?? -1));
    }

    /**
     * @param int<1, max> $levels
     * @return $this
     */
    public function dirname(int $levels = 1): static
    {
        return new static(dirname($this->value, $levels));
    }

    /**
     * @param string|iterable<array-key, string> $needle
     * @return bool
     */
    public function doesNotEndWith(string|iterable $needle): bool
    {
        return Str::doesNotEndWith($this->value, $needle);
    }

    /**
     * @param string|iterable<array-key, string> $needle
     * @return bool
     */
    public function doesNotStartWith(string|iterable $needle): bool
    {
        return Str::doesNotStartWith($this->value, $needle);
    }

    /**
     * @param string|iterable<array-key, string> $needle
     * @return bool
     */
    public function endsWith(string|iterable $needle): bool
    {
        return Str::endsWith($this->value, $needle);
    }

    /**
     * @param string $needle
     * @param int $offset
     * @return int|null
     */
    public function firstIndexOf(string $needle, int $offset = 0): ?int
    {
        return Str::firstIndexOf($this->value, $needle, $offset);
    }

    /**
     * @param int $position
     * @param string $insert
     * @return static
     */
    public function insert(string $insert, int $position): static
    {
        return new static(Str::insert($this->value, $insert, $position));
    }

    /**
     * @return bool
     */
    public function isBlank(): bool
    {
        return Str::isBlank($this->value);
    }

    /**
     * @return bool
     */
    public function isNotBlank(): bool
    {
        return Str::isNotBlank($this->value);
    }

    /**
     * @return static
     */
    public function kebabCase(): static
    {
        return new static(Str::kebabCase($this->value));
    }

    /**
     * @param string $needle
     * @param int $offset
     * @return int|null
     */
    public function lastIndexOf(string $needle, int $offset = 0): ?int
    {
        return Str::lastIndexOf($this->value, $needle, $offset);
    }

    /**
     * @return int
     */
    public function length(): int
    {
        return Str::length($this->value);
    }

    /**
     * @param string $pattern
     * @return array<int, array<string>>
     */
    public function match(string $pattern): array
    {
        return Str::match($this->value, $pattern);
    }

    /**
     * @param string $pattern
     * @return array<int, array<string>>
     */
    public function matchAll(string $pattern): array
    {
        return Str::matchAll($this->value, $pattern);
    }

    /**
     * @param string $needle
     * @return bool
     */
    public function notContains(string $needle): bool
    {
        return Str::notContains($this->value, $needle);
    }

    /**
     * @param int $length
     * @param string $pad
     * @return static
     */
    public function padBoth(int $length, string $pad = ' '): static
    {
        return new static(Str::padBoth($this->value, $length, $pad));
    }

    /**
     * @param int $length
     * @param string $pad
     * @return static
     */
    public function padLeft(int $length, string $pad = ' '): static
    {
        return new static(Str::padLeft($this->value, $length, $pad));
    }

    /**
     * @param int $length
     * @param string $pad
     * @return static
     */
    public function padRight(int $length, string $pad = ' '): static
    {
        return new static(Str::padRight($this->value, $length, $pad));
    }

    /**
     * @return static
     */
    public function pascalCase(): static
    {
        $this->value = Str::pascalCase($this->value);
        return $this;
    }

    /**
     * @param string ...$string
     * @return static
     */
    public function prepend(string ...$string): static
    {
        $string[] = $this->value;
        return new static(Str::concat(...$string));
    }

    /**
     * @param int<0, max> $times
     * @return static
     */
    public function repeat(int $times): static
    {
        return new static(Str::repeat($this->value, $times));
    }

    /**
     * @param string $search
     * @param string $replace
     * @return static
     */
    public function replace(string $search, string $replace): static
    {
        return new static(Str::replace($this->value, $search, $replace));
    }

    /**
     * @param string $search
     * @param string $replace
     * @return static
     */
    public function replaceFirst(string $search, string $replace): static
    {
        return new static(Str::replaceFirst($this->value, $search, $replace));
    }

    /**
     * @param string $search
     * @param string $replace
     * @return static
     */
    public function replaceLast(string $search, string $replace): static
    {
        return new static(Str::replaceLast($this->value, $search, $replace));
    }

    /**
     * @param string $pattern
     * @param string $replace
     * @param int|null $limit
     * @return static
     */
    public function replaceMatch(string $pattern, string $replace, ?int $limit = null): static
    {
        return new static(Str::replaceMatch($this->value, $pattern, $replace, $limit ?? -1));
    }

    /**
     * @return static
     */
    public function reverse(): static
    {
        return new static(Str::reverse($this->value));
    }

    /**
     * @return static
     */
    public function snakeCase(): static
    {
        return new static(Str::snakeCase($this->value));
    }

    /**
     * @param non-empty-string $separator
     * @param int<0, max>|null $limit
     * @return array<int, string>
     */
    public function split(string $separator, ?int $limit = null): array
    {
        return Str::split($this->value, $separator, $limit);
    }

    /**
     * @param string|iterable<array-key, string> $needle
     * @return bool
     */
    public function startsWith(string|iterable $needle): bool
    {
        return Str::startsWith($this->value, $needle);
    }

    /**
     * @param int $offset
     * @param int|null $length
     * @return static
     */
    public function substring(int $offset, ?int $length = null): static
    {
        return new static(Str::substring($this->value, $offset, $length));
    }

    /**
     * @return static
     */
    public function toLower(): static
    {
        return new static(Str::toLower($this->value));
    }

    /**
     * @return static
     */
    public function toUpper(): static
    {
        return new static(Str::toUpper($this->value));
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
        return new static(Str::trim($this->value, $characters));
    }

    /**
     * @param string $characters
     * @return static
     */
    public function trimStart(string $characters = '\s'): static
    {
        return new static(Str::trimStart($this->value, $characters));
    }

    /**
     * @param string $characters
     * @return static
     */
    public function trimEnd(string $characters = '\s'): static
    {
        return new static(Str::trimEnd($this->value, $characters));
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
