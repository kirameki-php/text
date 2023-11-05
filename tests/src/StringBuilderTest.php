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

    public function test_after(): void
    {
        $after = StringBuilder::from('buffer')->after('f');
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('fer', $after->toString());

        $after = StringBuilder::from('abc')->after('d');
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('abc', $after->toString());
    }

    public function test_afterLast(): void
    {
        $after = StringBuilder::from('abc abc')->afterLast('b');
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('c', $after->toString());

        $after = StringBuilder::from('buffer')->afterLast('f');
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('er', $after->toString());

        $after = StringBuilder::from('abc')->afterLast('d');
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

    public function test_before(): void
    {
        $after = StringBuilder::from('abc')->before('b');
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('a', $after->toString());

        $after = StringBuilder::from('abc')->before('d');
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('abc', $after->toString());
    }

    public function test_beforeLast(): void
    {
        $after = StringBuilder::from('abbc')->beforeLast('b');
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('ab', $after->toString());

        $after = StringBuilder::from('abbc')->beforeLast('d');
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('abbc', $after->toString());
    }

    public function test_between(): void
    {
        $after = StringBuilder::from('abcd')->between('a', 'c');
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('b', $after->toString());
    }

    public function test_betweenFurthest(): void
    {
        $after = StringBuilder::from('aa bb cc')->betweenFurthest('a', 'c');
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('a bb c', $after->toString());
    }

    public function test_betweenLast(): void
    {
        $after = StringBuilder::from('aa bb cc')->betweenLast('a', 'c');
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame(' bb c', $after->toString());
    }

    public function test_capitalize(): void
    {
        $after = StringBuilder::from('foo bar')->capitalize();
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('Foo bar', $after->toString());
        $after = StringBuilder::from('é')->capitalize();
        $this->assertInstanceOf(StringBuilder::class, $after);
        $this->assertSame('é', $after->toString());
    }

    public function test_chunk(): void
    {
        $after = StringBuilder::from('foo bar')->chunk(2, 2);
        $this->assertSame(['fo', 'o ', 'bar'], $after);
    }

    public function test_contains(): void
    {
        $sb = StringBuilder::from('foo bar');
        self::assertFalse($sb->contains('baz'));
        self::assertTrue($sb->contains('foo'));
        self::assertTrue($sb->contains(''));
    }

    public function test_containsAll(): void
    {
        $sb = StringBuilder::from('foo bar');
        self::assertFalse($sb->containsAll(['foo', 'bar', 'baz']));
        self::assertTrue($sb->containsAll(['foo', 'bar']));
        self::assertTrue($sb->containsAll(['', '']));
    }

    public function test_containsAny(): void
    {
        $sb = StringBuilder::from('foo bar');
        self::assertTrue($sb->containsAny(['foo', 'bar', 'baz']));
        self::assertFalse($sb->containsAny(['baz', '_']));
    }

    public function test_containsPattern(): void
    {
        $sb = StringBuilder::from('foo bar');
        self::assertTrue($sb->containsPattern('/[a-z]+/'));
        self::assertFalse($sb->containsPattern('/[0-9]+/'));
    }

    public function test_cut(): void
    {
        $after = StringBuilder::from('あいう')->cut(7, '...');
        self::assertInstanceOf(StringBuilder::class, $after);
        self::assertSame('あい...', $after->toString());
    }

    public function test_decapitalize(): void
    {
        $after = StringBuilder::from('FOO Bar')->decapitalize();
        self::assertInstanceOf(StringBuilder::class, $after);
        self::assertSame('fOO Bar', $after->toString());
    }

    public function test_delete(): void
    {
        $after = StringBuilder::from('foooooo bar')->remove('oo', 2);
        self::assertInstanceOf(StringBuilder::class, $after);
        self::assertSame('foo bar', $after->toString());
    }

    public function test_dirname(): void
    {
        $sb = StringBuilder::from('/test/path/of.php');
        $after1 = $sb->dirname();
        $after2 = $sb->dirname(2);

        self::assertInstanceOf(StringBuilder::class, $after1);
        self::assertInstanceOf(StringBuilder::class, $after2);
        self::assertSame('/test/path', $after1->toString());
        self::assertSame('/test', $after2->toString());
    }

    public function test_doesNotEndWith(): void
    {
        $sb = StringBuilder::from('/test/path/of.php');
        self::assertTrue($sb->doesNotEndWith('/test'));
        self::assertFalse($sb->doesNotEndWith('.php'));
    }

    public function test_doesNotStartWith(): void
    {
        $sb = StringBuilder::from('/test/path/of.php');
        self::assertFalse($sb->doesNotStartWith('/test'));
        self::assertTrue($sb->doesNotStartWith('.php'));
    }

    public function test_dropFirst(): void
    {
        $sb = StringBuilder::from('abc');
        $afterPos = $sb->dropFirst(+1);
        $afterNeg = $sb->dropFirst(-1);

        self::assertInstanceOf(StringBuilder::class, $afterPos);
        self::assertInstanceOf(StringBuilder::class, $afterNeg);
        self::assertSame('bc', $afterPos->toString());
        self::assertSame('c', $afterNeg->toString());
    }

    public function test_endsWith(): void
    {
        $sb = StringBuilder::from('/test/path/of.php');
        self::assertFalse($sb->endsWith('/test'));
        self::assertTrue($sb->endsWith('.php'));
    }

    public function test_firstIndexOf(): void
    {
        $sb = StringBuilder::from('aabbcc');
        self::assertSame(2, $sb->indexOfFirst('b'));
    }

    public function test_insert(): void
    {
        $after = StringBuilder::from('aaa')->insert('b', 1);
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
        $after = StringBuilder::from('foo barBaz')->toKebabCase();
        self::assertInstanceOf(StringBuilder::class, $after);
        self::assertSame('foo-bar-baz', $after->toString());
    }

    public function test_indexOfLast(): void
    {
        self::assertSame(3, StringBuilder::from('aabbcc')->indexOfLast('b'));
    }

    public function test_length(): void
    {
        self::assertSame(3, StringBuilder::from('あいう')->length());
    }

    public function test_takeFirst(): void
    {
        $sb = StringBuilder::from('abc');
        $afterPos = $sb->takeFirst(+1);
        $afterNeg = $sb->takeFirst(-1);
        self::assertInstanceOf(StringBuilder::class, $afterPos);
        self::assertInstanceOf(StringBuilder::class, $afterNeg);
        self::assertSame('a', $afterPos->toString());
        self::assertSame('ab', $afterNeg->toString());
    }

    public function test_toCamelCase(): void
    {
        $after = StringBuilder::from('foo bar')->toCamelCase();
        self::assertInstanceOf(StringBuilder::class, $after);
        self::assertSame('fooBar', $after->toString());
    }
}
