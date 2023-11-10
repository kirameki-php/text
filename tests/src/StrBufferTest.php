<?php declare(strict_types=1);

namespace Tests\Kirameki\Text;

use Kirameki\Core\Testing\TestCase;
use Kirameki\Text\StrBuffer;

class StrBufferTest extends TestCase
{
    protected function buffer(string $string): StrBuffer
    {
        return new StrBuffer($string);
    }

    public function test_from(): void
    {
        $sb = $this->buffer('a');
        self::assertInstanceOf(StrBuffer::class, $sb);
        self::assertSame('a', $sb->toString());
    }

    public function test_after(): void
    {
        $after = $this->buffer('buffer')->after('f');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('fer', $after->toString());

        $after = $this->buffer('abc')->after('d');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('abc', $after->toString());
    }

    public function test_afterLast(): void
    {
        $after = $this->buffer('abc abc')->afterLast('b');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('c', $after->toString());

        $after = $this->buffer('buffer')->afterLast('f');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('er', $after->toString());

        $after = $this->buffer('abc')->afterLast('d');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('abc', $after->toString());

    }

    public function test_append(): void
    {
        $after = $this->buffer('a')->append('1');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('a1', $after->toString());
    }

    public function test_appendFormat(): void
    {
        $after = $this->buffer('a')->appendFormat('%s %s', 'b', 0);
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('ab 0', $after->toString());
    }

    public function test_basename(): void
    {
        $sb = $this->buffer('/test/path/of.php');
        $after = $sb->basename();
        $afterSuffix = $sb->basename('.php');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertInstanceOf(StrBuffer::class, $afterSuffix);
        $this->assertSame('of.php', $after->toString());
        $this->assertSame('of', $afterSuffix->toString());
    }

    public function test_before(): void
    {
        $after = $this->buffer('abc')->before('b');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('a', $after->toString());

        $after = $this->buffer('abc')->before('d');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('abc', $after->toString());
    }

    public function test_beforeLast(): void
    {
        $after = $this->buffer('abbc')->beforeLast('b');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('ab', $after->toString());

        $after = $this->buffer('abbc')->beforeLast('d');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('abbc', $after->toString());
    }

    public function test_between(): void
    {
        $after = $this->buffer('abcd')->between('a', 'c');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('b', $after->toString());
    }

    public function test_betweenFurthest(): void
    {
        $after = $this->buffer('aa bb cc')->betweenFurthest('a', 'c');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('a bb c', $after->toString());
    }

    public function test_betweenLast(): void
    {
        $after = $this->buffer('aa bb cc')->betweenLast('a', 'c');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame(' bb c', $after->toString());
    }

    public function test_capitalize(): void
    {
        $after = $this->buffer('foo bar')->capitalize();
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('Foo bar', $after->toString());
        $after = $this->buffer('é')->capitalize();
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('é', $after->toString());
    }

    public function test_chunk(): void
    {
        $after = $this->buffer('foo bar')->chunk(2, 2);
        $this->assertSame(['fo', 'o ', 'bar'], $after);
    }

    public function test_contains(): void
    {
        $sb = $this->buffer('foo bar');
        self::assertFalse($sb->contains('baz'));
        self::assertTrue($sb->contains('foo'));
        self::assertTrue($sb->contains(''));
        self::assertFalse($sb->contains('  '));
    }

    public function test_containsAll(): void
    {
        $sb = $this->buffer('foo bar');
        self::assertFalse($sb->containsAll(['foo', 'bar', 'baz']));
        self::assertTrue($sb->containsAll(['foo', 'bar']));
        self::assertTrue($sb->containsAll(['', '']));
    }

    public function test_containsAny(): void
    {
        $sb = $this->buffer('foo bar');
        self::assertTrue($sb->containsAny(['foo', 'bar', 'baz']));
        self::assertFalse($sb->containsAny(['baz', '_']));
    }

    public function test_containsPattern(): void
    {
        $sb = $this->buffer('foo bar');
        self::assertTrue($sb->containsPattern('/[a-z]+/'));
        self::assertFalse($sb->containsPattern('/[0-9]+/'));
    }

    public function test_count(): void
    {
        $sb = $this->buffer('foo bar');
        self::assertSame(1, $sb->count('foo'));

        $sb = $this->buffer('あああ');
        self::assertSame(1, $sb->count('ああ'));
        self::assertSame(2, $sb->count('ああ', true));
    }

    public function test_decapitalize(): void
    {
        $after = $this->buffer('FOO Bar')->decapitalize();
        self::assertInstanceOf(StrBuffer::class, $after);
        self::assertSame('fOO Bar', $after->toString());
    }

    public function test_dirname(): void
    {
        $sb = $this->buffer('/test/path/of.php');
        $after1 = $sb->dirname();
        $after2 = $sb->dirname(2);

        self::assertInstanceOf(StrBuffer::class, $after1);
        self::assertInstanceOf(StrBuffer::class, $after2);
        self::assertSame('/test/path', $after1->toString());
        self::assertSame('/test', $after2->toString());
    }

    public function test_doesNotEndWith(): void
    {
        $sb = $this->buffer('/test/path/of.php');
        self::assertTrue($sb->doesNotEndWith('/test'));
        self::assertFalse($sb->doesNotEndWith('.php'));
    }

    public function test_doesNotStartWith(): void
    {
        $sb = $this->buffer('/test/path/of.php');
        self::assertFalse($sb->doesNotStartWith('/test'));
        self::assertTrue($sb->doesNotStartWith('.php'));
    }

    public function test_dropFirst(): void
    {
        $after = $this->buffer('abc')->dropFirst(1);
        self::assertInstanceOf(StrBuffer::class, $after);
        self::assertSame('bc', $after->toString());
    }

    public function test_dropLast(): void
    {
        $after = $this->buffer('abc')->dropLast(1);
        self::assertInstanceOf(StrBuffer::class, $after);
        self::assertSame('ab', $after->toString());
    }

    public function test_endsWith(): void
    {
        $sb = $this->buffer('/test/path/of.php');
        self::assertFalse($sb->endsWith('/test'));
        self::assertTrue($sb->endsWith('.php'));
    }

    public function test_firstIndexOf(): void
    {
        $sb = $this->buffer('aabbcc');
        self::assertSame(2, $sb->indexOfFirst('b'));
    }

    public function test_insert(): void
    {
        $after = $this->buffer('aaa')->insert('b', 1);
        self::assertSame('abaa', $after->toString());
    }

    public function test_isBlank(): void
    {
        self::assertTrue($this->buffer('')->isBlank());
        self::assertFalse($this->buffer('a')->isBlank());
        self::assertFalse($this->buffer("\n")->isBlank());
    }

    public function test_isNotBlank(): void
    {
        self::assertFalse($this->buffer('')->isNotBlank());
        self::assertTrue($this->buffer('a')->isNotBlank());
        self::assertTrue($this->buffer("\n")->isNotBlank());
    }

    public function test_kebabCase(): void
    {
        $after = $this->buffer('foo barBaz')->toKebabCase();
        self::assertInstanceOf(StrBuffer::class, $after);
        self::assertSame('foo-bar-baz', $after->toString());
    }

    public function test_indexOfLast(): void
    {
        self::assertSame(3, $this->buffer('aabbcc')->indexOfLast('b'));
    }

    public function test_length(): void
    {
        self::assertSame(9, $this->buffer('あいう')->length());
    }

    public function test_remove(): void
    {
        $after = $this->buffer('foooooo bar')->remove('oo', 2);
        self::assertInstanceOf(StrBuffer::class, $after);
        self::assertSame('foo bar', $after->toString());
    }

    public function test_takeFirst(): void
    {
        $after = $this->buffer('abc')->takeFirst(1);
        self::assertInstanceOf(StrBuffer::class, $after);
        self::assertSame('a', $after->toString());
    }

    public function test_toCamelCase(): void
    {
        $after = $this->buffer('foo bar')->toCamelCase();
        self::assertInstanceOf(StrBuffer::class, $after);
        self::assertSame('fooBar', $after->toString());
    }
}
