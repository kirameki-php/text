<?php declare(strict_types=1);

namespace Tests\Kirameki\Text;

use Kirameki\Core\Testing\TestCase;
use Kirameki\Text\Exceptions\NotFoundException;
use Kirameki\Text\Str;

class StrTest extends TestCase
{
    public function test_after(): void
    {
        // match first
        $this->assertSame('est', Str::after('test', 't'));

        // match last
        $this->assertSame('', Str::after('test1', '1'));

        // match empty string
        $this->assertSame('test', Str::after('test', ''));

        // no match
        $this->assertSame('test', Str::after('test', 'a'));

        // multi byte
        $this->assertSame('うえ', Str::after('ああいうえ', 'い'));

        // grapheme
        $this->assertSame('def', Str::after('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿def', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));

        // grapheme cluster
        $this->assertSame('🏿', Str::after('👋🏿', '👋'));
    }

    public function test_afterLast(): void
    {
        // match first (single occurrence)
        $this->assertSame('bc', Str::afterLast('abc', 'a'));

        // match first (multiple occurrence)
        $this->assertSame('1', Str::afterLast('test1', 't'));

        // match last
        $this->assertSame('', Str::afterLast('test1', '1'));

        // should match the last string
        $this->assertSame('Foo', Str::afterLast('----Foo', '---'));

        // match empty string
        $this->assertSame('test', Str::afterLast('test', ''));

        // no match
        $this->assertSame('test', Str::afterLast('test', 'a'));

        // multi byte
        $this->assertSame('え', Str::afterLast('ああいういえ', 'い'));

        // grapheme
        $this->assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿f', Str::afterLast('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', 'e'));

        // grapheme cluster
        $this->assertSame('🏿', Str::afterLast('👋🏿', '👋'));
    }

    public function test_before(): void
    {
        // match first (single occurrence)
        $this->assertSame('a', Str::before('abc', 'b'));

        // match first (multiple occurrence)
        $this->assertSame('a', Str::before('abc-abc', 'b'));

        // match last
        $this->assertSame('test', Str::before('test1', '1'));

        // match multiple chars
        $this->assertSame('test', Str::before('test123', '12'));

        // match empty string
        $this->assertSame('test', Str::before('test', ''));

        // no match
        $this->assertSame('test', Str::before('test', 'a'));

        // multi byte
        $this->assertSame('ああ', Str::before('ああいういえ', 'い'));

        // grapheme
        $this->assertSame('abc', Str::before('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
        $this->assertSame('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿', Str::before('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', 'e'));

        // grapheme cluster
        $this->assertSame('👋', Str::before('👋🏿', '🏿'));
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
