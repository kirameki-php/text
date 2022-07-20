<?php declare(strict_types=1);

namespace Kirameki\Text;

use Closure;
use Kirameki\Utils\Str as Util;
use Stringable;
use function basename;
use function dirname;
use function sprintf;

class Str implements Stringable
{
    /**
     * @var string
     */
    protected string $value;

    /**
     * @param string $value
     */
    public function __construct(string $value = '')
    {
        $this->value = $value;
    }

    /**
     * @param string $search
     * @return $this
     */
    public function after(string $search): static
    {
        $this->value = Util::after($this->value, $search);
        return $this;
    }

    /**
     * @param int $position
     * @return $this
     */
    public function afterIndex(int $position): static
    {
        $this->value = Util::afterIndex($this->value, $position);
        return $this;
    }

    /**
     * @param string $search
     * @return $this
     */
    public function afterLast(string $search): static
    {
        $this->value = Util::afterLast($this->value, $search);
        return $this;
    }

    /**
     * @param string ...$string
     * @return $this
     */
    public function append(string ...$string): static
    {
        $this->value = Util::concat($this->value, ...$string);
        return $this;
    }

    /**
     * @param string $format
     * @param float|int|string ...$values
     * @return $this
     */
    public function appendFormat(string $format, float|int|string ...$values): static
    {
        $this->value.= sprintf($format, ...$values);
        return $this;
    }

    /**
     * @param string $suffix
     * @return $this
     */
    public function basename(string $suffix = ''): static
    {
        $this->value = basename($this->value, $suffix);
        return $this;
    }

    /**
     * @param string $search
     * @return $this
     */
    public function before(string $search): static
    {
        $this->value = Util::before($this->value, $search);
        return $this;
    }

    /**
     * @param int $position
     * @return $this
     */
    public function beforeIndex(int $position): static
    {
        $this->value = Util::beforeIndex($this->value, $position);
        return $this;
    }

    /**
     * @param string $search
     * @return $this
     */
    public function beforeLast(string $search): static
    {
        $this->value = Util::beforeLast($this->value, $search);
        return $this;
    }

    /**
     * @param string $from
     * @param string $to
     * @return $this
     */
    public function between(string $from, string $to): static
    {
        $this->value = Util::between($this->value, $from, $to);
        return $this;
    }

    /**
     * @return int
     */
    public function bytes(): int
    {
        return Util::bytes($this->value);
    }

    /**
     * @return $this
     */
    public function camelCase(): static
    {
        $this->value = Util::camelCase($this->value);
        return $this;
    }

    /**
     * @return $this
     */
    public function capitalize(): static
    {
        $this->value = Util::capitalize($this->value);
        return $this;
    }

    /**
     * @param string $needle
     * @return bool
     */
    public function contains(string $needle): bool
    {
        return Util::contains($this->value, $needle);
    }

    /**
     * @param array<string> $needles
     * @return bool
     */
    public function containsAll(array $needles): bool
    {
        return Util::containsAll($this->value, $needles);
    }

    /**
     * @param array<string> $needles
     * @return bool
     */
    public function containsAny(array $needles): bool
    {
        return Util::containsAny($this->value, $needles);
    }

    /**
     * @param string $pattern
     * @return bool
     */
    public function containsPattern(string $pattern): bool
    {
        return Util::containsPattern($this->value, $pattern);
    }
    /**
     * @param int $position
     * @param string $ellipsis
     * @return $this
     */
    public function cut(int $position, string $ellipsis = ''): static
    {
        $this->value = Util::cut($this->value, $position, $ellipsis);
        return $this;
    }

    /**
     * @return $this
     */
    public function decapitalize(): static
    {
        $this->value = Util::decapitalize($this->value);
        return $this;
    }

    /**
     * @param string $search
     * @param int|null $limit
     * @return $this
     */
    public function delete(string $search, ?int $limit = null): static
    {
        $this->value = Util::delete($this->value, $search, $limit ?? -1);
        return $this;
    }

    /**
     * @param int<1, max> $levels
     * @return $this
     */
    public function dirname(int $levels = 1): static
    {
        $this->value = dirname($this->value, $levels);
        return $this;
    }

    /**
     * @param string|iterable<array-key, string> $needle
     * @return bool
     */
    public function doesNotEndWith(string|iterable $needle): bool
    {
        return Util::doesNotEndWith($this->value, $needle);
    }

    /**
     * @param string|iterable<array-key, string> $needle
     * @return bool
     */
    public function doesNotStartWith(string|iterable $needle): bool
    {
        return Util::doesNotStartWith($this->value, $needle);
    }

    /**
     * @param string|iterable<array-key, string> $needle
     * @return bool
     */
    public function endsWith(string|iterable $needle): bool
    {
        return Util::endsWith($this->value, $needle);
    }

    /**
     * @param string $needle
     * @param int $offset
     * @return int|null
     */
    public function firstIndexOf(string $needle, int $offset = 0): ?int
    {
        return Util::firstIndexOf($this->value, $needle, $offset);
    }

    /**
     * @param int $position
     * @param string $insert
     * @return $this
     */
    public function insert(string $insert, int $position): static
    {
        $this->value = Util::insert($this->value, $insert, $position);
        return $this;
    }

    /**
     * @return bool
     */
    public function isBlank(): bool
    {
        return Util::isBlank($this->value);
    }

    /**
     * @return bool
     */
    public function isNotBlank(): bool
    {
        return Util::isNotBlank($this->value);
    }

    /**
     * @return $this
     */
    public function kebabCase(): static
    {
        $this->value = Util::kebabCase($this->value);
        return $this;
    }

    /**
     * @param string $needle
     * @param int $offset
     * @return int|null
     */
    public function lastIndexOf(string $needle, int $offset = 0): ?int
    {
        return Util::lastIndexOf($this->value, $needle, $offset);
    }

    /**
     * @return int
     */
    public function length(): int
    {
        return Util::length($this->value);
    }

    /**
     * @param string $pattern
     * @return array<int, array<string>>
     */
    public function match(string $pattern): array
    {
        return Util::match($this->value, $pattern);
    }

    /**
     * @param string $pattern
     * @return array<int, array<string>>
     */
    public function matchAll(string $pattern): array
    {
        return Util::matchAll($this->value, $pattern);
    }

    /**
     * @param string $needle
     * @return bool
     */
    public function notContains(string $needle): bool
    {
        return Util::notContains($this->value, $needle);
    }

    /**
     * @param int $length
     * @param string $pad
     * @return $this
     */
    public function padBoth(int $length, string $pad = ' '): static
    {
        $this->value = Util::padBoth($this->value, $length, $pad);
        return $this;
    }

    /**
     * @param int $length
     * @param string $pad
     * @return $this
     */
    public function padLeft(int $length, string $pad = ' '): static
    {
        $this->value = Util::padLeft($this->value, $length, $pad);
        return $this;
    }

    /**
     * @param int $length
     * @param string $pad
     * @return $this
     */
    public function padRight(int $length, string $pad = ' '): static
    {
        $this->value = Util::padRight($this->value, $length, $pad);
        return $this;
    }

    /**
     * @return $this
     */
    public function pascalCase(): static
    {
        $this->value = Util::pascalCase($this->value);
        return $this;
    }

    /**
     * @param string ...$string
     * @return $this
     */
    public function prepend(string ...$string): static
    {
        $string[] = $this->value;
        $this->value = Util::concat(...$string);
        return $this;
    }

    /**
     * @param int<0, max> $times
     * @return $this
     */
    public function repeat(int $times): static
    {
        $this->value = Util::repeat($this->value, $times);
        return $this;
    }

    /**
     * @param string $search
     * @param string $replace
     * @return $this
     */
    public function replace(string $search, string $replace): static
    {
        $this->value = Util::replace($this->value, $search, $replace);
        return $this;
    }

    /**
     * @param string $search
     * @param string $replace
     * @return $this
     */
    public function replaceFirst(string $search, string $replace): static
    {
        $this->value = Util::replaceFirst($this->value, $search, $replace);
        return $this;
    }

    /**
     * @param string $search
     * @param string $replace
     * @return $this
     */
    public function replaceLast(string $search, string $replace): static
    {
        $this->value = Util::replaceLast($this->value, $search, $replace);
        return $this;
    }

    /**
     * @param string $pattern
     * @param string $replace
     * @param int $limit
     * @return $this
     */
    public function replaceMatch(string $pattern, string $replace, ?int $limit = null): static
    {
        $this->value = Util::replaceMatch($this->value, $pattern, $replace, $limit ?? -1);
        return $this;
    }

    /**
     * @return $this
     */
    public function reverse(): static
    {
        $this->value = Util::reverse($this->value);
        return $this;
    }

    /**
     * @return $this
     */
    public function snakeCase(): static
    {
        $this->value = Util::snakeCase($this->value);
        return $this;
    }

    /**
     * @param non-empty-string $separator
     * @param int<0, max>|null $limit
     * @return array<int, string>
     */
    public function split(string $separator, ?int $limit = null): array
    {
        return Util::split($this->value, $separator, $limit);
    }

    /**
     * @param string|iterable<array-key, string> $needle
     * @return bool
     */
    public function startsWith(string|iterable $needle): bool
    {
        return Util::startsWith($this->value, $needle);
    }

    /**
     * @param int $offset
     * @param int|null $length
     * @return $this
     */
    public function substring(int $offset, ?int $length = null): static
    {
        $this->value = Util::substring($this->value, $offset, $length);
        return $this;
    }

    /**
     * @return $this
     */
    public function toLower(): static
    {
        $this->value = Util::toLower($this->value);
        return $this;
    }

    /**
     * @return $this
     */
    public function toUpper(): static
    {
        $this->value = Util::toUpper($this->value);
        return $this;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * @param Closure(string): static $callback
     * @return $this
     */
    public function transform(Closure $callback): static
    {
        $this->value = (string) $callback($this->value);
        return $this;
    }

    /**
     * @param string $characters
     * @return $this
     */
    public function trim(string $characters = " \t\n\r\0\x0B"): static
    {
        $this->value = Util::trim($this->value, $characters);
        return $this;
    }

    /**
     * @param string $characters
     * @return $this
     */
    public function trimStart(string $characters = " \t\n\r\0\x0B"): static
    {
        $this->value = Util::trimStart($this->value, $characters);
        return $this;
    }

    /**
     * @param string $characters
     * @return $this
     */
    public function trimEnd(string $characters = " \t\n\r\0\x0B"): static
    {
        $this->value = Util::trimEnd($this->value, $characters);
        return $this;
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
