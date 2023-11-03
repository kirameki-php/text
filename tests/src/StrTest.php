<?php declare(strict_types=1);

namespace Tests\Kirameki\Text;

use Kirameki\Core\Testing\TestCase;
use Kirameki\Text\Str;

class StrTest extends TestCase
{
    public function test_afterFirst(): void
    {
        // match first
        $found = false;
        $this->assertSame('est', Str::afterFirst('test', 't', $found));
        $this->assertTrue($found);

        // match last
        $this->assertSame('', Str::afterFirst('test1', '1'));

        // match empty string
        $this->assertSame('test', Str::afterFirst('test', ''));

        // no match
        $found = true;
        $this->assertSame('test', Str::afterFirst('test', 'test2', $found));
        $this->assertFalse($found);

        // multi byte
        $this->assertSame('うえ', Str::afterFirst('ああいうえ', 'い'));

        // grapheme
        $this->assertSame('def', Str::afterFirst('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿def', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));

        // grapheme cluster
        $this->assertSame('🏿', Str::afterFirst('👋🏿', '👋'));
    }

    public function test_afterLast(): void
    {
        // match first (single occurrence)
        $found = false;
        self::assertSame('bc', Str::afterLast('abc', 'a', $found));
        $this->assertTrue($found);

        // match first (multiple occurrence)
        self::assertSame('1', Str::afterLast('test1', 't'));

        // match last
        self::assertSame('', Str::afterLast('test1', '1'));

        // should match the last string
        self::assertSame('Foo', Str::afterLast('----Foo', '---'));

        // match empty string
        self::assertSame('test', Str::afterLast('test', ''));

        // no match
        $found = true;
        self::assertSame('test', Str::afterLast('test', 'a', $found));
        $this->assertFalse($found);

        // multi byte
        self::assertSame('え', Str::afterLast('ああいういえ', 'い'));

        // grapheme
        self::assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿f', Str::afterLast('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', 'e'));

        // grapheme cluster
        self::assertSame('🏿', Str::afterLast('👋🏿', '👋'));
    }

    public function test_beforeFirst(): void
    {
        // match first (single occurrence)
        $found = false;
        $this->assertSame('a', Str::beforeFirst('abc', 'b', $found));
        $this->assertTrue($found);

        // match first (multiple occurrence)
        $this->assertSame('a', Str::beforeFirst('abc-abc', 'b'));

        // match last
        $this->assertSame('test', Str::beforeFirst('test1', '1'));

        // match multiple chars
        $this->assertSame('test', Str::beforeFirst('test123', '12'));

        // match empty string
        $this->assertSame('test', Str::beforeFirst('test', ''));

        // no match
        $found = true;
        $this->assertSame('test', Str::beforeFirst('test', 'a', $found));
        $this->assertFalse($found);

        // multi byte
        $this->assertSame('ああ', Str::beforeFirst('ああいういえ', 'い'));

        // grapheme
        $this->assertSame('abc', Str::beforeFirst('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
        $this->assertSame('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿', Str::beforeFirst('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', 'e'));

        // grapheme cluster
        $this->assertSame('👋', Str::beforeFirst('👋🏿', '🏿'));
    }

    public function test_beforeLast(): void
    {
        // match first (single occurrence)
        $found = false;
        $this->assertSame('a', Str::beforeLast('abc', 'b', $found));
        $this->assertTrue($found);

        // match first (multiple occurrence)
        $this->assertSame('abc-a', Str::beforeLast('abc-abc', 'b'));

        // match last
        $this->assertSame('test', Str::beforeLast('test1', '1'));

        // match empty string
        $this->assertSame('test', Str::beforeLast('test', ''));

        // no match
        $found = true;
        $this->assertSame('test', Str::beforeLast('test', 'a', $found));
        $this->assertFalse($found);

        // multi byte
        $this->assertSame('ああいう', Str::beforeLast('ああいういえ', 'い'));

        // grapheme
        $this->assertSame('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e', Str::beforeLast('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));

        // grapheme cluster
        $this->assertSame('👋', Str::beforeLast('👋🏿', '🏿'));
    }

}
