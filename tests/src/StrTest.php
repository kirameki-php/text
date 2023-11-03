<?php declare(strict_types=1);

namespace Tests\Kirameki\Text;

use Kirameki\Core\Testing\TestCase;
use Kirameki\Text\Exceptions\NotFoundException;
use Kirameki\Text\Str;

class StrTest extends TestCase
{
    public function test_after_no_match(): void
    {
        $this->expectExceptionMessage('Substring "test2" does not exist in "test".');
        $this->expectException(NotFoundException::class);
        Str::after('test', 'test2');
    }

    public function test_afterOrNull(): void
    {
        // match first
        $this->assertSame('est', Str::afterOrNull('test', 't'));

        // match last
        $this->assertSame('', Str::afterOrNull('test1', '1'));

        // match empty string
        $this->assertSame('test', Str::afterOrNull('test', ''));

        // no match
        $this->assertSame(null, Str::afterOrNull('test', 'a'));

        // multi byte
        $this->assertSame('うえ', Str::afterOrNull('ああいうえ', 'い'));

        // grapheme
        $this->assertSame('def', Str::afterOrNull('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿def', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));

        // grapheme cluster
        $this->assertSame('🏿', Str::afterOrNull('👋🏿', '👋'));
    }

    public function test_afterOrSelf(): void
    {
        // no match
        $this->assertSame('test', Str::afterOrSelf('test', 'a'));
        $this->assertSame('🏿', Str::afterOrNull('👋🏿', '👋'));
    }

    public function test_afterLast_no_match(): void
    {
        $this->expectExceptionMessage('Substring "test2" does not exist in "test".');
        $this->expectException(NotFoundException::class);
        Str::afterLast('test', 'test2');
    }

    public function test_afterLastOrNull(): void
    {
        // match first (single occurrence)
        $this->assertSame('bc', Str::afterLastOrNull('abc', 'a'));

        // match first (multiple occurrence)
        $this->assertSame('1', Str::afterLastOrNull('test1', 't'));

        // match last
        $this->assertSame('', Str::afterLastOrNull('test1', '1'));

        // should match the last string
        $this->assertSame('Foo', Str::afterLastOrNull('----Foo', '---'));

        // match empty string
        $this->assertSame('test', Str::afterLastOrNull('test', ''));

        // no match
        $this->assertSame(null, Str::afterLastOrNull('test', 'a'));

        // multi byte
        $this->assertSame('え', Str::afterLastOrNull('ああいういえ', 'い'));

        // grapheme
        $this->assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿f', Str::afterLastOrNull('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', 'e'));

        // grapheme cluster
        $this->assertSame('🏿', Str::afterLastOrNull('👋🏿', '👋'));
    }

    public function test_afterLastOrSelf(): void
    {
        // no match
        $this->assertSame('test', Str::afterLastOrSelf('test', 'a'));
        $this->assertSame('🏿', Str::afterLastOrSelf('👋🏿', '👋'));
    }

    public function test_before_no_match(): void
    {
        $this->expectExceptionMessage('Substring "test2" does not exist in "test".');
        $this->expectException(NotFoundException::class);
        Str::before('test', 'test2');
    }

    public function test_beforeOrNull(): void
    {
        // match first (single occurrence)
        $this->assertSame('a', Str::beforeOrNull('abc', 'b'));

        // match first (multiple occurrence)
        $this->assertSame('a', Str::beforeOrNull('abc-abc', 'b'));

        // match last
        $this->assertSame('test', Str::beforeOrNull('test1', '1'));

        // match multiple chars
        $this->assertSame('test', Str::beforeOrNull('test123', '12'));

        // match empty string
        $this->assertSame('test', Str::beforeOrNull('test', ''));

        // no match
        $this->assertSame(null, Str::beforeOrNull('test', 'a'));

        // multi byte
        $this->assertSame('ああ', Str::beforeOrNull('ああいういえ', 'い'));

        // grapheme
        $this->assertSame('abc', Str::beforeOrNull('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
        $this->assertSame('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿', Str::beforeOrNull('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', 'e'));

        // grapheme cluster
        $this->assertSame('👋', Str::beforeOrNull('👋🏿', '🏿'));
    }

    public function test_beforeOrSelf(): void
    {
        // no match
        $this->assertSame('test', Str::beforeOrSelf('test', 'a'));
        $this->assertSame('👋', Str::beforeOrSelf('👋🏿', '🏿'));
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
