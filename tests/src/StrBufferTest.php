<?php declare(strict_types=1);

namespace Tests\Kirameki\Text;

use Kirameki\Core\Testing\TestCase;
use Kirameki\Text\StrBuffer;

class StrBufferTest extends TestCase
{
    public function test_from(): void
    {
        $sb = StrBuffer::from('a');
        self::assertInstanceOf(StrBuffer::class, $sb);
        self::assertSame('a', $sb->toString());
    }

    public function test_after(): void
    {
        $after = StrBuffer::from('buffer')->after('f');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('fer', $after->toString());

        $after = StrBuffer::from('abc')->after('d');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('abc', $after->toString());
    }

    public function test_afterLast(): void
    {
        $after = StrBuffer::from('abc abc')->afterLast('b');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('c', $after->toString());

        $after = StrBuffer::from('buffer')->afterLast('f');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('er', $after->toString());

        $after = StrBuffer::from('abc')->afterLast('d');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('abc', $after->toString());

    }

    public function test_append(): void
    {
        $after = StrBuffer::from('a')->append('1');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('a1', $after->toString());
    }

    public function test_appendFormat(): void
    {
        $after = StrBuffer::from('a')->appendFormat('%s %s', 'b', 0);
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('ab 0', $after->toString());
    }

    public function test_basename(): void
    {
        $sb = StrBuffer::from('/test/path/of.php');
        $after = $sb->basename();
        $afterSuffix = $sb->basename('.php');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertInstanceOf(StrBuffer::class, $afterSuffix);
        $this->assertSame('of.php', $after->toString());
        $this->assertSame('of', $afterSuffix->toString());
    }

    public function test_before(): void
    {
        $after = StrBuffer::from('abc')->before('b');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('a', $after->toString());

        $after = StrBuffer::from('abc')->before('d');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('abc', $after->toString());
    }

    public function test_beforeLast(): void
    {
        $after = StrBuffer::from('abbc')->beforeLast('b');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('ab', $after->toString());

        $after = StrBuffer::from('abbc')->beforeLast('d');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('abbc', $after->toString());
    }

    public function test_between(): void
    {
        $after = StrBuffer::from('abcd')->between('a', 'c');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('b', $after->toString());
    }

    public function test_betweenFurthest(): void
    {
        $after = StrBuffer::from('aa bb cc')->betweenFurthest('a', 'c');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('a bb c', $after->toString());
    }

    public function test_betweenLast(): void
    {
        $after = StrBuffer::from('aa bb cc')->betweenLast('a', 'c');
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame(' bb c', $after->toString());
    }

    public function test_capitalize(): void
    {
        $after = StrBuffer::from('foo bar')->capitalize();
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('Foo bar', $after->toString());
        $after = StrBuffer::from('é')->capitalize();
        $this->assertInstanceOf(StrBuffer::class, $after);
        $this->assertSame('é', $after->toString());
    }

    public function test_chunk(): void
    {
        $after = StrBuffer::from('foo bar')->chunk(2, 2);
        $this->assertSame(['fo', 'o ', 'bar'], $after);
    }

    public function test_contains(): void
    {
        $sb = StrBuffer::from('foo bar');
        self::assertFalse($sb->contains('baz'));
        self::assertTrue($sb->contains('foo'));
        self::assertTrue($sb->contains(''));
        self::assertFalse($sb->contains('  '));
    }

    public function test_containsAll(): void
    {
        $sb = StrBuffer::from('foo bar');
        self::assertFalse($sb->containsAll(['foo', 'bar', 'baz']));
        self::assertTrue($sb->containsAll(['foo', 'bar']));
        self::assertTrue($sb->containsAll(['', '']));
    }

    public function test_containsAny(): void
    {
        $sb = StrBuffer::from('foo bar');
        self::assertTrue($sb->containsAny(['foo', 'bar', 'baz']));
        self::assertFalse($sb->containsAny(['baz', '_']));
    }

    public function test_containsPattern(): void
    {
        $sb = StrBuffer::from('foo bar');
        self::assertTrue($sb->containsPattern('/[a-z]+/'));
        self::assertFalse($sb->containsPattern('/[0-9]+/'));
    }

    public function test_count(): void
    {
        $sb = StrBuffer::from('foo bar');
        self::assertSame(1, $sb->count('foo'));

        $sb = StrBuffer::from('あああ');
        self::assertSame(1, $sb->count('ああ'));
        self::assertSame(2, $sb->count('ああ', true));
    }

    public function test_decapitalize(): void
    {
        $after = StrBuffer::from('FOO Bar')->decapitalize();
        self::assertInstanceOf(StrBuffer::class, $after);
        self::assertSame('fOO Bar', $after->toString());
    }

    public function test_delete(): void
    {
        $after = StrBuffer::from('foooooo bar')->remove('oo', 2);
        self::assertInstanceOf(StrBuffer::class, $after);
        self::assertSame('foo bar', $after->toString());
    }

    public function test_dirname(): void
    {
        $sb = StrBuffer::from('/test/path/of.php');
        $after1 = $sb->dirname();
        $after2 = $sb->dirname(2);

        self::assertInstanceOf(StrBuffer::class, $after1);
        self::assertInstanceOf(StrBuffer::class, $after2);
        self::assertSame('/test/path', $after1->toString());
        self::assertSame('/test', $after2->toString());
    }

    public function test_doesNotEndWith(): void
    {
        $sb = StrBuffer::from('/test/path/of.php');
        self::assertTrue($sb->doesNotEndWith('/test'));
        self::assertFalse($sb->doesNotEndWith('.php'));
    }

    public function test_doesNotStartWith(): void
    {
        $sb = StrBuffer::from('/test/path/of.php');
        self::assertFalse($sb->doesNotStartWith('/test'));
        self::assertTrue($sb->doesNotStartWith('.php'));
    }

    public function test_dropFirst(): void
    {
        $after = StrBuffer::from('abc')->dropFirst(1);
        self::assertInstanceOf(StrBuffer::class, $after);
        self::assertSame('bc', $after->toString());
    }

    public function test_endsWith(): void
    {
        $sb = StrBuffer::from('/test/path/of.php');
        self::assertFalse($sb->endsWith('/test'));
        self::assertTrue($sb->endsWith('.php'));
    }

    public function test_firstIndexOf(): void
    {
        $sb = StrBuffer::from('aabbcc');
        self::assertSame(2, $sb->indexOfFirst('b'));
    }

    public function test_insert(): void
    {
        $after = StrBuffer::from('aaa')->insert('b', 1);
        self::assertSame('abaa', $after->toString());
    }

    public function test_isBlank(): void
    {
        self::assertTrue(StrBuffer::from('')->isBlank());
        self::assertFalse(StrBuffer::from('a')->isBlank());
        self::assertFalse(StrBuffer::from("\n")->isBlank());
    }

    public function test_isNotBlank(): void
    {
        self::assertFalse(StrBuffer::from('')->isNotBlank());
        self::assertTrue(StrBuffer::from('a')->isNotBlank());
        self::assertTrue(StrBuffer::from("\n")->isNotBlank());
    }

    public function test_kebabCase(): void
    {
        $after = StrBuffer::from('foo barBaz')->toKebabCase();
        self::assertInstanceOf(StrBuffer::class, $after);
        self::assertSame('foo-bar-baz', $after->toString());
    }

    public function test_indexOfLast(): void
    {
        self::assertSame(3, StrBuffer::from('aabbcc')->indexOfLast('b'));
    }

    public function test_length(): void
    {
        self::assertSame(3, StrBuffer::from('あいう')->length());
    }

    public function test_takeFirst(): void
    {
        $after = StrBuffer::from('abc')->takeFirst(1);
        self::assertInstanceOf(StrBuffer::class, $after);
        self::assertSame('a', $after->toString());
    }

    public function test_toCamelCase(): void
    {
        $after = StrBuffer::from('foo bar')->toCamelCase();
        self::assertInstanceOf(StrBuffer::class, $after);
        self::assertSame('fooBar', $after->toString());
    }
}
