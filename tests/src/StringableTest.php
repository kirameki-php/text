<?php declare(strict_types=1);

namespace Tests\Kirameki\Text;

use Kirameki\Text\Stringable;
use PHPUnit\Framework\TestCase as BaseTestCase;

class StringableTest extends BaseTestCase
{
    public function test_from(): void
    {
        $stringable = Stringable::from('a');
        self::assertInstanceOf(Stringable::class, $stringable);
        self::assertSame('a', $stringable->toString());
    }

    public function test_after(): void
    {
        $stringable = Stringable::from('abc');
        $after = $stringable->after('b');

        self::assertInstanceOf(Stringable::class, $after);
        self::assertSame('c', $after->toString());
    }

    public function test_afterIndex(): void
    {
        $stringable = Stringable::from('abc');
        $afterPos = $stringable->afterIndex(+1);
        $afterNeg = $stringable->afterIndex(-1);

        self::assertInstanceOf(Stringable::class, $afterPos);
        self::assertInstanceOf(Stringable::class, $afterNeg);
        self::assertSame('bc', $afterPos->toString());
        self::assertSame('c', $afterNeg->toString());
    }

    public function test_afterLast(): void
    {
        $stringable = Stringable::from('aabb');
        $after = $stringable->afterLast('a');

        self::assertInstanceOf(Stringable::class, $after);
        self::assertSame('bb', $after->toString());
    }

    public function test_append(): void
    {
        $stringable = Stringable::from('a');
        $after = $stringable->append('1');

        self::assertInstanceOf(Stringable::class, $after);
        self::assertSame('a1', $after->toString());
    }

    public function test_appendFormat(): void
    {
        $stringable = Stringable::from('a');
        $after = $stringable->appendFormat('%s %s', 'b', 0);

        self::assertInstanceOf(Stringable::class, $after);
        self::assertSame('ab 0', $after->toString());
    }

    public function test_basename(): void
    {
        $stringable = Stringable::from('/test/path/of.php');
        $after = $stringable->basename();
        $afterSuffix = $stringable->basename('.php');

        self::assertInstanceOf(Stringable::class, $after);
        self::assertInstanceOf(Stringable::class, $afterSuffix);
        self::assertSame('of.php', $after->toString());
        self::assertSame('of', $afterSuffix->toString());
    }

    public function test_before(): void
    {
        $stringable = Stringable::from('abc');
        $after = $stringable->before('b');

        self::assertInstanceOf(Stringable::class, $after);
        self::assertSame('a', $after->toString());
    }

    public function test_beforeIndex(): void
    {
        $stringable = Stringable::from('abc');
        $afterPos = $stringable->beforeIndex(+1);
        $afterNeg = $stringable->beforeIndex(-1);

        self::assertInstanceOf(Stringable::class, $afterPos);
        self::assertInstanceOf(Stringable::class, $afterNeg);
        self::assertSame('a', $afterPos->toString());
        self::assertSame('ab', $afterNeg->toString());
    }

    public function test_beforeLast(): void
    {
        $stringable = Stringable::from('aabb');
        $after = $stringable->beforeLast('b');

        self::assertInstanceOf(Stringable::class, $after);
        self::assertSame('aab', $after->toString());
    }

    public function test_between(): void
    {
        $stringable = Stringable::from('abcd');
        $after = $stringable->between('a', 'c');

        self::assertInstanceOf(Stringable::class, $after);
        self::assertSame('b', $after->toString());
    }

    public function test_bytes(): void
    {
        $stringable = Stringable::from('あいう');

        self::assertSame(9, $stringable->bytes());
    }

    public function test_camelCase(): void
    {
        $stringable = Stringable::from('foo bar');
        $after = $stringable->camelCase();

        self::assertInstanceOf(Stringable::class, $after);
        self::assertSame('fooBar', $after->toString());
    }

    public function test_capitalize(): void
    {
        $stringable = Stringable::from('foo bar');
        $after = $stringable->capitalize();

        self::assertInstanceOf(Stringable::class, $after);
        self::assertSame('Foo bar', $after->toString());
    }

    public function test_contains(): void
    {
        $stringable = Stringable::from('foo bar');

        self::assertFalse($stringable->contains('baz'));
        self::assertTrue($stringable->contains('foo'));
        self::assertTrue($stringable->contains(''));
    }

    public function test_containsAll(): void
    {
        $stringable = Stringable::from('foo bar');

        self::assertFalse($stringable->containsAll(['foo', 'bar', 'baz']));
        self::assertTrue($stringable->containsAll(['foo', 'bar']));
        self::assertTrue($stringable->containsAll(['', '']));
    }

    public function test_containsAny(): void
    {
        $stringable = Stringable::from('foo bar');

        self::assertTrue($stringable->containsAny(['foo', 'bar', 'baz']));
        self::assertFalse($stringable->containsAny(['baz', '_']));
    }

    public function test_containsPattern(): void
    {
        $stringable = Stringable::from('foo bar');

        self::assertTrue($stringable->containsPattern('/[a-z]+/'));
        self::assertFalse($stringable->containsPattern('/[0-9]+/'));
    }

    public function test_decapitalize(): void
    {
        $stringable = Stringable::from('FOO Bar');
        $after = $stringable->decapitalize();

        self::assertInstanceOf(Stringable::class, $after);
        self::assertSame('fOO Bar', $after->toString());
    }

    public function test_delete(): void
    {
        $stringable = Stringable::from('foooooo bar');
        $after = $stringable->delete('oo', 2);

        self::assertInstanceOf(Stringable::class, $after);
        self::assertSame('foo bar', $after->toString());
    }
}
