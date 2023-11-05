<?php declare(strict_types=1);

namespace Tests\Kirameki\Text;

use Kirameki\Core\Testing\TestCase;
use Kirameki\Text\Exceptions\NotFoundException;
use Kirameki\Text\Str;
use Kirameki\Text\Unicode;
use function dump;
use function strlen;
use function substr;

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
        $this->assertSame('a', Str::beforeLast('abc', 'b'));

        // match first (multiple occurrence)
        $this->assertSame('abc-a', Str::beforeLast('abc-abc', 'b'));

        // match last
        $this->assertSame('test', Str::beforeLast('test1', '1'));

        // match empty string
        $this->assertSame('test', Str::beforeLast('test', ''));

        // no match
        $this->assertSame('test', Str::beforeLast('test', 'a'));

        // multi byte
        $this->assertSame('ああいう', Str::beforeLast('ああいういえ', 'い'));

        // grapheme
        $this->assertSame('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e', Str::beforeLast('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));

        // grapheme cluster
        $this->assertSame('👋', Str::beforeLast('👋🏿', '🏿'));
    }

    public function test_between(): void
    {
        // basic
        $this->assertSame('1', Str::between('test(1)', '(', ')'));

        // edge
        $this->assertSame('', Str::between('()', '(', ')'));
        $this->assertSame('1', Str::between('(1)', '(', ')'));

        // missing from
        $this->assertSame('test)', Str::between('test)', '(', ')'));

        // missing to
        $this->assertSame('test(', Str::between('test(', '(', ')'));

        // nested
        $this->assertSame('test(1', Str::between('(test(1))', '(', ')'));
        $this->assertSame('1', Str::between('(1) to (2)', '(', ')'));

        // multi char
        $this->assertSame('_ab_', Str::between('ab_ab_ba_ba', 'ab', 'ba'));

        // utf8
        $this->assertSame('い', Str::between('あいういう', 'あ', 'う'));

        // grapheme
        $this->assertSame('😃', Str::between('👋🏿😃👋🏿😃👋🏿', '👋🏿', '👋🏿'));

        // grapheme between codepoints
        $this->assertSame('', Str::between('👋🏿', '👋', '🏿'));
    }

    public function test_between_empty_from(): void
    {
        $this->expectExceptionMessage('$from must not be empty.');
        Str::between('test)', '', ')');
    }

    public function test_between_empty_to(): void
    {
        $this->expectExceptionMessage('$to must not be empty.');
        Str::between('test)', '(', '');
    }

    public function test_between_empty_from_and_to(): void
    {
        $this->expectExceptionMessage('$from must not be empty.');
        Str::between('test)', '', '');
    }

    public function test_betweenFurthest(): void
    {
        // basic
        $this->assertSame('1', Str::betweenFurthest('test(1)', '(', ')'));

        // edge
        $this->assertSame('', Str::betweenFurthest('()', '(', ')'));
        $this->assertSame('1', Str::betweenFurthest('(1)', '(', ')'));

        // missing from
        $this->assertSame('test)', Str::betweenFurthest('test)', '(', ')'));

        // missing to
        $this->assertSame('test(', Str::betweenFurthest('test(', '(', ')'));

        // nested
        $this->assertSame('test(1)', Str::betweenFurthest('(test(1))', '(', ')'));
        $this->assertSame('1) to (2', Str::betweenFurthest('(1) to (2)', '(', ')'));

        // multichar
        $this->assertSame('_', Str::betweenFurthest('ab_ba', 'ab', 'ba'));

        // utf8
        $this->assertSame('い', Str::betweenFurthest('あいう', 'あ', 'う'));

        // grapheme
        $this->assertSame('😃', Str::betweenFurthest('👋🏿😃👋🏿😃', '👋🏿', '👋🏿'));

        // grapheme between codepoints
        $this->assertSame('', Str::between('👋🏿', '👋', '🏿'));
    }

    public function test_betweenFurthest_empty_from(): void
    {
        $this->expectExceptionMessage('$from must not be empty.');
        Str::betweenFurthest('test)', '', ')');
    }

    public function test_betweenFurthest_empty_to(): void
    {
        $this->expectExceptionMessage('$to must not be empty.');
        Str::betweenFurthest('test)', '(', '');
    }

    public function test_betweenFurthest_empty_from_and_to(): void
    {
        $this->expectExceptionMessage('$from must not be empty.');
        Str::betweenFurthest('test)', '', '');
    }

    public function test_betweenLast(): void
    {
        // basic
        $this->assertSame('1', Str::betweenLast('test(1)', '(', ')'));

        // edge
        $this->assertSame('', Str::betweenLast('()', '(', ')'));
        $this->assertSame('1', Str::betweenLast('(1)', '(', ')'));

        // missing from
        $this->assertSame('test)', Str::between('test)', '(', ')'));

        // missing to
        $this->assertSame('test(', Str::between('test(', '(', ')'));

        // nested
        $this->assertSame('1)', Str::betweenLast('(test(1))', '(', ')'));
        $this->assertSame('2', Str::betweenLast('(1) to (2)', '(', ')'));

        // multi char
        $this->assertSame('_ba_', Str::betweenLast('ab_ab_ba_ba', 'ab', 'ba'));

        // utf8
        $this->assertSame('いうい', Str::betweenLast('あいういう', 'あ', 'う'));

        // grapheme
        $this->assertSame('🥹', Str::betweenLast('👋🏿😃👋🏿🥹👋', '👋🏿', '👋'));

        // grapheme between codepoints
        $this->assertSame('', Str::between('👋🏿', '👋', '🏿'));
    }

    public function test_betweenLast_empty_from(): void
    {
        $this->expectExceptionMessage('$from must not be empty.');
        Str::betweenFurthest('test)', '', ')');
    }

    public function test_betweenLast_empty_to(): void
    {
        $this->expectExceptionMessage('$to must not be empty.');
        Str::betweenFurthest('test)', '(', '');
    }

    public function test_betweenLast_empty_from_and_to(): void
    {
        $this->expectExceptionMessage('$from must not be empty.');
        Str::betweenFurthest('test)', '', '');
    }

    public function test_capitalize(): void
    {
        $this->assertSame('', Str::capitalize(''), 'empty');
        $this->assertSame('TT', Str::capitalize('TT'), 'all uppercase');
        $this->assertSame('Test', Str::capitalize('test'), 'lowercase');
        $this->assertSame('Test abc', Str::capitalize('test abc'), 'lowercase with spaces');
        $this->assertSame(' test abc', Str::capitalize(' test abc'), 'lowercase with spaces and leading space');
        $this->assertSame('àbc', Str::capitalize('àbc'), 'lowercase with accent');
        $this->assertSame('é', Str::capitalize('é'), 'lowercase with accent');
        $this->assertSame('ゅ', Str::capitalize('ゅ'), 'lowercase with hiragana');
        $this->assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿', Str::capitalize('🏴󠁧󠁢󠁳󠁣󠁴󠁿'), 'lowercase with emoji');
    }

    public function test_chunk(): void
    {
        $this->assertSame([], Str::chunk('', 5), 'empty');
        $this->assertSame(['ab'], Str::chunk('ab', 5), 'oversize');
        $this->assertSame(['ab'], Str::chunk('ab', 2), 'exact');
        $this->assertSame(['ab', 'c'], Str::chunk('abc', 2), 'fragment');
        $this->assertSame(['あ', 'い', 'う'], Str::chunk('あいう', 3), 'utf8');
        $this->assertSame(['ab', 'cd', 'efg'], Str::chunk('abcdefg', 2, 2), 'limit');

        $chunked = Str::chunk('あ', 2);
        $this->assertSame(2, strlen($chunked[0]), 'invalid');
        $this->assertSame(1, strlen($chunked[1]), 'invalid');
    }

    public function test_concat(): void
    {
        $this->assertSame('', Str::concat('', '', ''), 'empty');
        $this->assertSame(' ', Str::concat('', ' '), 'blank');
        $this->assertSame('', Str::concat(), 'no arg');
        $this->assertSame('a', Str::concat('a'), 'one arg');
        $this->assertSame('abc', Str::concat('a', 'b', 'c'), 'basic');
        $this->assertSame('あい', Str::concat('あ', 'い'), 'mb string');
        $this->assertSame('👋🏿', Str::concat('👋', '🏿'), 'mb string');
    }
}
