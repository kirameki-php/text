<?php declare(strict_types=1);

namespace Tests\Kirameki\Text;

use Kirameki\Text\Exceptions\NotFoundException;
use Kirameki\Text\StringBuilder;
use PHPUnit\Framework\TestCase as BaseTestCase;

class StringBuilderTest extends BaseTestCase
{
    public function test_from(): void
    {
        $sb = StringBuilder::from('a');
        self::assertInstanceOf(StringBuilder::class, $sb);
        self::assertSame('a', $sb->toString());
    }

    public function test_afterFirst(): void
    {
        $after = StringBuilder::from('abc')->afterFirst('b');
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('c', $after->toString());
    }

    public function test_afterFirst_no_match(): void
    {
        $this->expectExceptionMessage('Substring "d" does not exist in "abc".');
        $this->expectException(NotFoundException::class);
        StringBuilder::from('abc')->afterFirst('d');
    }

    public function test_afterFirstOrSelf(): void
    {
        $after = StringBuilder::from('buffer')->afterFirstOrSelf('f');
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('fer', $after->toString());

        $after = StringBuilder::from('abc')->afterFirstOrSelf('d');
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('abc', $after->toString());
    }

    public function test_afterLast(): void
    {
        $after = StringBuilder::from('abc abc')->afterFirst('b');
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('c', $after->toString());
    }

    public function test_afterLast_no_match(): void
    {
        $this->expectExceptionMessage('Substring "d" does not exist in "abc".');
        $this->expectException(NotFoundException::class);
        StringBuilder::from('abc')->afterLast('d');
    }

    public function test_afterLastOrSelf(): void
    {
        $after = StringBuilder::from('buffer')->afterLastOrSelf('f');
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('er', $after->toString());

        $after = StringBuilder::from('abc')->afterLastOrSelf('d');
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('abc', $after->toString());
    }

    public function test_append(): void
    {
        $after = StringBuilder::from('a')->append('1');
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('a1', $after->toString());
    }

    public function test_appendFormat(): void
    {
        $after = StringBuilder::from('a')->appendFormat('%s %s', 'b', 0);
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('ab 0', $after->toString());
    }

    public function test_basename(): void
    {
        $sb = StringBuilder::from('/test/path/of.php');
        $after = $sb->basename();
        $afterSuffix = $sb->basename('.php');
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertInstanceOf(StringBuilder::class, $afterSuffix);
        $this->assertSame('of.php', $after->toString());
        $this->assertSame('of', $afterSuffix->toString());
    }

    public function test_beforeFirst(): void
    {
        $after = StringBuilder::from('abc')->beforeFirst('b');
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('a', $after->toString());
    }

    public function test_beforeFirst_not_found(): void
    {
        $this->expectExceptionMessage('Substring "d" does not exist in "abc".');
        $this->expectException(NotFoundException::class);
        StringBuilder::from('abc')->beforeFirst('d');
    }

    public function test_beforeFirstOrSelf(): void
    {
        $after = StringBuilder::from('abc')->beforeFirstOrSelf('d');
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('abc', $after->toString());
    }

    public function test_beforeLast(): void
    {
        $sb = StringBuilder::from('abbc');
        $found = false;
        $after = $sb->beforeLast('b', $found);
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('ab', $after->toString());
        $this->assertTrue($found);

        $sb = StringBuilder::from('abbc');
        $found = true;
        $after = $sb->beforeLast('d', $found);
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('abbc', $after->toString());
        $this->assertFalse($found);
    }

    public function test_between(): void
    {
        $stringable = StringBuilder::from('abcd');
        $after = $stringable->between('a', 'c');

        self::assertInstanceOf(StringBuilder::class, $after);
        self::assertSame('b', $after->toString());
    }

    public function test_bytes(): void
    {
        $stringable = StringBuilder::from('あいう');

        self::assertSame(9, $stringable->byteLength());
    }

    public function test_camelCase(): void
    {
        $stringable = StringBuilder::from('foo bar');
        $after = $stringable->toCamelCase();

        self::assertInstanceOf(StringBuilder::class, $after);
        self::assertSame('fooBar', $after->toString());
    }

    public function test_capitalize(): void
    {
        $stringable = StringBuilder::from('foo bar');
        $after = $stringable->capitalize();

        self::assertInstanceOf(StringBuilder::class, $after);
        self::assertSame('Foo bar', $after->toString());
    }

    public function test_contains(): void
    {
        $stringable = StringBuilder::from('foo bar');

        self::assertFalse($stringable->contains('baz'));
        self::assertTrue($stringable->contains('foo'));
        self::assertTrue($stringable->contains(''));
    }

    public function test_containsAll(): void
    {
        $stringable = StringBuilder::from('foo bar');

        self::assertFalse($stringable->containsAll(['foo', 'bar', 'baz']));
        self::assertTrue($stringable->containsAll(['foo', 'bar']));
        self::assertTrue($stringable->containsAll(['', '']));
    }

    public function test_containsAny(): void
    {
        $stringable = StringBuilder::from('foo bar');

        self::assertTrue($stringable->containsAny(['foo', 'bar', 'baz']));
        self::assertFalse($stringable->containsAny(['baz', '_']));
    }

    public function test_containsPattern(): void
    {
        $stringable = StringBuilder::from('foo bar');

        self::assertTrue($stringable->containsPattern('/[a-z]+/'));
        self::assertFalse($stringable->containsPattern('/[0-9]+/'));
    }

    public function test_cut(): void
    {
        $stringable = StringBuilder::from('あいう');
        $after = $stringable->cut(7, '...');

        self::assertInstanceOf(StringBuilder::class, $after);
        self::assertSame('あい...', $after->toString());
    }

    public function test_decapitalize(): void
    {
        $stringable = StringBuilder::from('FOO Bar');
        $after = $stringable->decapitalize();

        self::assertInstanceOf(StringBuilder::class, $after);
        self::assertSame('fOO Bar', $after->toString());
    }

    public function test_delete(): void
    {
        $stringable = StringBuilder::from('foooooo bar');
        $after = $stringable->remove('oo', 2);

        self::assertInstanceOf(StringBuilder::class, $after);
        self::assertSame('foo bar', $after->toString());
    }

    public function test_dirname(): void
    {
        $stringable = StringBuilder::from('/test/path/of.php');
        $after1 = $stringable->dirname();
        $after2 = $stringable->dirname(2);

        self::assertInstanceOf(StringBuilder::class, $after1);
        self::assertInstanceOf(StringBuilder::class, $after2);
        self::assertSame('/test/path', $after1->toString());
        self::assertSame('/test', $after2->toString());
    }

    public function test_doesNotEndWith(): void
    {
        $stringable = StringBuilder::from('/test/path/of.php');

        self::assertTrue($stringable->doesNotEndWith('/test'));
        self::assertFalse($stringable->doesNotEndWith('.php'));
    }

    public function test_doesNotStartWith(): void
    {
        $stringable = StringBuilder::from('/test/path/of.php');

        self::assertFalse($stringable->doesNotStartWith('/test'));
        self::assertTrue($stringable->doesNotStartWith('.php'));
    }

    public function test_dropFirst(): void
    {
        $stringable = StringBuilder::from('abc');
        $afterPos = $stringable->dropFirst(+1);
        $afterNeg = $stringable->dropFirst(-1);

        self::assertInstanceOf(StringBuilder::class, $afterPos);
        self::assertInstanceOf(StringBuilder::class, $afterNeg);
        self::assertSame('bc', $afterPos->toString());
        self::assertSame('c', $afterNeg->toString());
    }

    public function test_endsWith(): void
    {
        $stringable = StringBuilder::from('/test/path/of.php');

        self::assertFalse($stringable->endsWith('/test'));
        self::assertTrue($stringable->endsWith('.php'));
    }

    public function test_firstIndexOf(): void
    {
        $stringable = StringBuilder::from('aabbcc');

        self::assertSame(2, $stringable->indexOfFirst('b'));
    }

    public function test_insert(): void
    {
        $stringable = StringBuilder::from('aaa');
        $after = $stringable->insert('b', 1);

        self::assertSame('abaa', $after->toString());
    }

    public function test_isBlank(): void
    {
        self::assertTrue(StringBuilder::from('')->isBlank());
        self::assertFalse(StringBuilder::from('a')->isBlank());
        self::assertFalse(StringBuilder::from("\n")->isBlank());
    }

    public function test_isNotBlank(): void
    {
        self::assertFalse(StringBuilder::from('')->isNotBlank());
        self::assertTrue(StringBuilder::from('a')->isNotBlank());
        self::assertTrue(StringBuilder::from("\n")->isNotBlank());
    }

    public function test_kebabCase(): void
    {
        $stringable = StringBuilder::from('foo barBaz');
        $after = $stringable->toKebabCase();

        self::assertInstanceOf(StringBuilder::class, $after);
        self::assertSame('foo-bar-baz', $after->toString());
    }

    public function test_indexOfLast(): void
    {
        $stringable = StringBuilder::from('aabbcc');

        self::assertSame(3, $stringable->indexOfLast('b'));
    }

    public function test_length(): void
    {
        $stringable = StringBuilder::from('あいう');

        self::assertSame(3, $stringable->length());
    }

    public function test_takeFirst(): void
    {
        $stringable = StringBuilder::from('abc');
        $afterPos = $stringable->takeFirst(+1);
        $afterNeg = $stringable->takeFirst(-1);

        self::assertInstanceOf(StringBuilder::class, $afterPos);
        self::assertInstanceOf(StringBuilder::class, $afterNeg);
        self::assertSame('a', $afterPos->toString());
        self::assertSame('ab', $afterNeg->toString());
    }
}
