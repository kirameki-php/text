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
        $this->assertInstanceOf(StrBuffer::class, $sb);
        $this->assertSame('a', $sb->toString());
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
        $this->assertFalse($sb->contains('baz'));
        $this->assertTrue($sb->contains('foo'));
        $this->assertTrue($sb->contains(''));
        $this->assertFalse($sb->contains('  '));
    }

    public function test_containsAll(): void
    {
        $sb = $this->buffer('foo bar');
        $this->assertFalse($sb->containsAll(['foo', 'bar', 'baz']));
        $this->assertTrue($sb->containsAll(['foo', 'bar']));
        $this->assertTrue($sb->containsAll(['', '']));
    }

    public function test_containsAny(): void
    {
        $sb = $this->buffer('foo bar');
        $this->assertTrue($sb->containsAny(['foo', 'bar', 'baz']));
        $this->assertFalse($sb->containsAny(['baz', '_']));
    }

    public function test_containsPattern(): void
    {
        $sb = $this->buffer('foo bar');
        $this->assertTrue($sb->containsPattern('/[a-z]+/'));
        $this->assertFalse($sb->containsPattern('/[0-9]+/'));
    }

    public function test_count(): void
    {
        $sb = $this->buffer('foo bar');
        $this->assertSame(1, $sb->count('foo'));

        $sb = $this->buffer('あああ');
        $this->assertSame(1, $sb->count('ああ'));
        $this->assertSame(2, $sb->count('ああ', true));
    }

    public function test_decapitalize(): void
    {
        $after = $this->buffer('FOO Bar')->decapitalize();
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('fOO Bar', $after->toString());
    }

    public function test_dirname(): void
    {
        $sb = $this->buffer('/test/path/of.php');
        $after1 = $sb->dirname();
        $after2 = $sb->dirname(2);

        $this->assertInstanceOf(StrBuffer::class, $after1);
        $this->assertInstanceOf(StrBuffer::class, $after2);
        $this->assertSame('/test/path', $after1->toString());
        $this->assertSame('/test', $after2->toString());
    }

    public function test_doesNotEndWith(): void
    {
        $sb = $this->buffer('/test/path/of.php');
        $this->assertTrue($sb->doesNotEndWith('/test'));
        $this->assertFalse($sb->doesNotEndWith('.php'));
    }

    public function test_doesNotStartWith(): void
    {
        $sb = $this->buffer('/test/path/of.php');
        $this->assertFalse($sb->doesNotStartWith('/test'));
        $this->assertTrue($sb->doesNotStartWith('.php'));
    }

    public function test_dropFirst(): void
    {
        $after = $this->buffer('abc')->dropFirst(1);
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('bc', $after->toString());
    }

    public function test_dropLast(): void
    {
        $after = $this->buffer('abc')->dropLast(1);
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('ab', $after->toString());
    }

    public function test_endsWith(): void
    {
        $sb = $this->buffer('/test/path/of.php');
        $this->assertFalse($sb->endsWith('/test'));
        $this->assertTrue($sb->endsWith('.php'));
    }

    public function test_indexOfFirst(): void
    {
        $sb = $this->buffer('aabbcc');
        $this->assertSame(2, $sb->indexOfFirst('b'));
        $this->assertSame(3, $sb->indexOfFirst('b', 3));
    }

    public function test_indexOfLast(): void
    {
        $this->assertSame(3, $this->buffer('aabbcc')->indexOfLast('b'));
    }

    public function test_insertAt(): void
    {
        $after = $this->buffer('aaa')->insertAt('b', 1);
        $this->assertSame('abaa', $after->toString());
    }

    public function test_isBlank(): void
    {
        $this->assertTrue($this->buffer('')->isBlank());
        $this->assertFalse($this->buffer('a')->isBlank());
        $this->assertFalse($this->buffer("\n")->isBlank());
    }

    public function test_isNotBlank(): void
    {
        $this->assertFalse($this->buffer('')->isNotBlank());
        $this->assertTrue($this->buffer('a')->isNotBlank());
        $this->assertTrue($this->buffer("\n")->isNotBlank());
    }

    public function test_interpolate(): void
    {
        $buffer = $this->buffer(' <a> ')->interpolate(['a' => 1], '<', '>');
        $this->assertSame(' 1 ', $buffer->toString());
        $this->assertInstanceOf(StrBuffer::class, $buffer);
    }

    public function test_length(): void
    {
        $this->assertSame(9, $this->buffer('あいう')->length());
    }

    public function test_remove(): void
    {
        $after = $this->buffer('foooooo bar')->remove('oo', 2);
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('foo bar', $after->toString());
    }

    public function test_takeFirst(): void
    {
        $after = $this->buffer('abc')->takeFirst(1);
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('a', $after->toString());
    }

    public function test_toCamelCase(): void
    {
        $after = $this->buffer('foo bar')->toCamelCase();
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('fooBar', $after->toString());
    }

    public function test_toKebabCase(): void
    {
        $after = $this->buffer('foo barBaz')->toKebabCase();
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('foo-bar-baz', $after->toString());
    }

}
