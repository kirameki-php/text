<?php declare(strict_types=1);

namespace Tests\Kirameki\Text;

use Error;
use Kirameki\Text\Exceptions\NotFoundException;
use Kirameki\Text\Str;
use Kirameki\Text\Unicode;
use PHPUnit\Framework\TestStatus\Warning;
use RuntimeException;
use Webmozart\Assert\InvalidArgumentException;
use function str_repeat;
use function substr;

class UnicodeTest extends TestCase
{
    public function test_after(): void
    {
        // match first
        $this->assertSame('est', Unicode::after('test', 't'));

        // match last
        $this->assertSame('', Unicode::after('test1', '1'));

        // match empty string
        $this->assertSame('test', Unicode::after('test', ''));

        // no match
        $this->assertSame('test', Unicode::after('test', 'test2'));

        // multi byte
        $this->assertSame('うえ', Unicode::after('ああいうえ', 'い'));

        // grapheme
        $this->assertSame('def', Unicode::after('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿def', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));

        // grapheme cluster
        $this->assertSame('👋🏿', Unicode::after('👋🏿', '👋'));
    }

    public function test_afterLast(): void
    {
        // match first (single occurrence)
        $this->assertSame('bc', Unicode::afterLast('abc', 'a'));

        // match first (multiple occurrence)
        $this->assertSame('1', Unicode::afterLast('test1', 't'));

        // match last
        $this->assertSame('', Unicode::afterLast('test1', '1'));

        // should match the last string
        $this->assertSame('Foo', Unicode::afterLast('----Foo', '---'));

        // match empty string
        $this->assertSame('test', Unicode::afterLast('test', ''));

        // no match
        $this->assertSame('test', Unicode::afterLast('test', 'a'));

        // multi byte
        $this->assertSame('え', Unicode::afterLast('ああいういえ', 'い'));

        // grapheme
        $this->assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿f', Unicode::afterLast('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', 'e'));

        // grapheme cluster
        $this->assertSame('👋🏿', Unicode::afterLast('👋🏿', '👋'));
    }

    public function test_before(): void
    {
        // match first (single occurrence)
        $this->assertSame('a', Unicode::before('abc', 'b'));

        // match first (multiple occurrence)
        $this->assertSame('a', Unicode::before('abc-abc', 'b'));

        // match last
        $this->assertSame('test', Unicode::before('test1', '1'));

        // match multiple chars
        $this->assertSame('test', Unicode::before('test123', '12'));

        // match empty string
        $this->assertSame('test', Unicode::before('test', ''));

        // no match
        $this->assertSame('test', Unicode::before('test', 'a'));

        // multi byte
        $this->assertSame('ああ', Unicode::before('ああいういえ', 'い'));

        // grapheme
        $this->assertSame('abc', Unicode::before('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
        $this->assertSame('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿', Unicode::before('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', 'e'));

        // grapheme cluster
        $this->assertSame('👋🏿', Unicode::before('👋🏿', '🏿'));
    }

    public function test_beforeLast(): void
    {
        // match first (single occurrence)
        $this->assertSame('a', Unicode::beforeLast('abc', 'b'));

        // match first (multiple occurrence)
        $this->assertSame('abc-a', Unicode::beforeLast('abc-abc', 'b'));

        // match last
        $this->assertSame('test', Unicode::beforeLast('test1', '1'));

        // match empty string
        $this->assertSame('test', Unicode::beforeLast('test', ''));

        // no match
        $this->assertSame('test', Unicode::beforeLast('test', 'a'));

        // multi byte
        $this->assertSame('ああいう', Unicode::beforeLast('ああいういえ', 'い'));

        // grapheme
        $this->assertSame('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e', Unicode::beforeLast('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));

        // grapheme cluster
        $this->assertSame('👋🏿', Unicode::beforeLast('👋🏿', '🏿'));
    }

    public function test_between(): void
    {
        // basic
        $this->assertSame('1', Unicode::between('test(1)', '(', ')'));

        // edge
        $this->assertSame('', Unicode::between('()', '(', ')'));
        $this->assertSame('1', Unicode::between('(1)', '(', ')'));

        // missing from
        $this->assertSame('test)', Unicode::between('test)', '(', ')'));

        // missing to
        $this->assertSame('test(', Unicode::between('test(', '(', ')'));

        // nested
        $this->assertSame('test(1', Unicode::between('(test(1))', '(', ')'));
        $this->assertSame('1', Unicode::between('(1) to (2)', '(', ')'));

        // multi char
        $this->assertSame('_ab_', Unicode::between('ab_ab_ba_ba', 'ab', 'ba'));

        // utf8
        $this->assertSame('い', Unicode::between('あいういう', 'あ', 'う'));

        // grapheme
        $this->assertSame('😃', Unicode::between('👋🏿😃👋🏿😃👋🏿', '👋🏿', '👋🏿'));

        // grapheme between codepoints
        $this->assertSame('👋🏿', Unicode::between('👋🏿', '👋', '🏿'));
    }

    public function test_between_empty_from(): void
    {
        $this->expectExceptionMessage('$from must not be empty.');
        Unicode::between('test)', '', ')');
    }

    public function test_between_empty_to(): void
    {
        $this->expectExceptionMessage('$to must not be empty.');
        Unicode::between('test)', '(', '');
    }

    public function test_between_empty_from_and_to(): void
    {
        $this->expectExceptionMessage('$from must not be empty.');
        Unicode::between('test)', '', '');
    }

    public function test_betweenFurthest(): void
    {
        // basic
        $this->assertSame('1', Unicode::betweenFurthest('test(1)', '(', ')'));

        // edge
        $this->assertSame('', Unicode::betweenFurthest('()', '(', ')'));
        $this->assertSame('1', Unicode::betweenFurthest('(1)', '(', ')'));

        // missing from
        $this->assertSame('test)', Unicode::betweenFurthest('test)', '(', ')'));

        // missing to
        $this->assertSame('test(', Unicode::betweenFurthest('test(', '(', ')'));

        // nested
        $this->assertSame('test(1)', Unicode::betweenFurthest('(test(1))', '(', ')'));
        $this->assertSame('1) to (2', Unicode::betweenFurthest('(1) to (2)', '(', ')'));

        // multi char
        $this->assertSame('_', Unicode::betweenFurthest('ab_ba', 'ab', 'ba'));

        // utf8
        $this->assertSame('い', Unicode::betweenFurthest('あいう', 'あ', 'う'));

        // grapheme
        $this->assertSame('😃', Unicode::betweenFurthest('👋🏿😃👋🏿😃', '👋🏿', '👋🏿'));

        // grapheme between codepoints
        $this->assertSame('👋🏿', Unicode::between('👋🏿', '👋', '🏿'));
    }

    public function test_betweenFurthest_empty_from(): void
    {
        $this->expectExceptionMessage('$from must not be empty.');
        Unicode::betweenFurthest('test)', '', ')');
    }

    public function test_betweenFurthest_empty_to(): void
    {
        $this->expectExceptionMessage('$to must not be empty.');
        Unicode::betweenFurthest('test)', '(', '');
    }

    public function test_betweenFurthest_empty_from_and_to(): void
    {
        $this->expectExceptionMessage('$from must not be empty.');
        Unicode::betweenFurthest('test)', '', '');
    }

    public function test_betweenLast(): void
    {
        // basic
        $this->assertSame('1', Unicode::betweenLast('test(1)', '(', ')'));

        // edge
        $this->assertSame('', Unicode::betweenLast('()', '(', ')'));
        $this->assertSame('1', Unicode::betweenLast('(1)', '(', ')'));

        // missing from
        $this->assertSame('test)', Unicode::between('test)', '(', ')'));

        // missing to
        $this->assertSame('test(', Unicode::between('test(', '(', ')'));

        // nested
        $this->assertSame('1)', Unicode::betweenLast('(test(1))', '(', ')'));
        $this->assertSame('2', Unicode::betweenLast('(1) to (2)', '(', ')'));

        // multi char
        $this->assertSame('_ba_', Unicode::betweenLast('ab_ab_ba_ba', 'ab', 'ba'));

        // utf8
        $this->assertSame('いうい', Unicode::betweenLast('あいういう', 'あ', 'う'));

        // grapheme
        $this->assertSame('🥹', Unicode::betweenLast('👋🏿😃👋🏿🥹👋', '👋🏿', '👋'));

        // grapheme between codepoints
        $this->assertSame('👋🏿', Unicode::between('👋🏿', '👋', '🏿'));
    }

    public function test_betweenLast_empty_from(): void
    {
        $this->expectExceptionMessage('$from must not be empty.');
        Unicode::betweenFurthest('test)', '', ')');
    }

    public function test_betweenLast_empty_to(): void
    {
        $this->expectExceptionMessage('$to must not be empty.');
        Unicode::betweenFurthest('test)', '(', '');
    }

    public function test_betweenLast_empty_from_and_to(): void
    {
        $this->expectExceptionMessage('$from must not be empty.');
        Unicode::betweenFurthest('test)', '', '');
    }

    public function test_byteLength(): void
    {
        // empty
        $this->assertSame(0, Unicode::byteLength(''));

        // ascii
        $this->assertSame(1, Unicode::byteLength('a'));

        // utf8
        $this->assertSame(3, Unicode::byteLength('あ'));

        // emoji
        $this->assertSame(25, Unicode::byteLength('👨‍👨‍👧‍👦'));
    }

    public function test_capitalize(): void
    {
        $this->assertSame('', Unicode::capitalize(''), 'empty');
        $this->assertSame('TT', Unicode::capitalize('TT'), 'all uppercase');
        $this->assertSame('Test', Unicode::capitalize('test'), 'lowercase');
        $this->assertSame('Test abc', Unicode::capitalize('test abc'), 'lowercase with spaces');
        $this->assertSame(' test abc', Unicode::capitalize(' test abc'), 'lowercase with spaces and leading space');
        $this->assertSame('Àbc', Unicode::capitalize('àbc'), 'lowercase with accent');
        $this->assertSame('É', Unicode::capitalize('é'), 'lowercase with accent');
        $this->assertSame('ゅ', Unicode::capitalize('ゅ'), 'lowercase with hiragana');
        $this->assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿', Unicode::capitalize('🏴󠁧󠁢󠁳󠁣󠁴󠁿'), 'lowercase with emoji');
    }

    public function test_chunk(): void
    {
        $this->assertSame([], Unicode::chunk('', 5), 'empty');
        $this->assertSame(['ab'], Unicode::chunk('ab', 5), 'oversize');
        $this->assertSame(['ab'], Unicode::chunk('ab', 2), 'exact');
        $this->assertSame(['ab', 'c'], Unicode::chunk('abc', 2), 'fragment');
        $this->assertSame(['あい', 'う'], Unicode::chunk('あいう', 2), 'utf8');
        $this->assertSame(['👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦'], Unicode::chunk('👨‍👨‍👧‍👦👨‍👨‍👧‍👦', 1), 'emoji');
        $this->assertSame(['あい', 'うえ', 'おかき'], Unicode::chunk('あいうえおかき', 2, 2), 'limit');
    }

    public function test_concat(): void
    {
        self::assertSame('', Unicode::concat());
        self::assertSame('test', Unicode::concat('test'));
        self::assertSame('testa ', Unicode::concat('test', 'a', '', ' '));
        self::assertSame('ゅゅ', Unicode::concat('ゅ', 'ゅ'));
        self::assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿🐌', Unicode::concat('🏴󠁧󠁢󠁳󠁣󠁴󠁿', '🐌'));
    }

    public function test_contains(): void
    {
        self::assertTrue(Unicode::contains('abcde', 'ab'));
        self::assertFalse(Unicode::contains('abcde', 'ac'));
        self::assertTrue(Unicode::contains('abcde', ''));
        self::assertTrue(Unicode::contains('', ''));
        self::assertFalse(Unicode::contains('👨‍👨‍👧‍👧‍', '👨'));
    }

    public function test_containsAll(): void
    {
        self::assertTrue(Unicode::containsAll('', []), 'empty substrings with blank');
        self::assertTrue(Unicode::containsAll('abc', []), 'empty substrings');
        self::assertTrue(Unicode::containsAll('', ['']), 'blank match blank');
        self::assertTrue(Unicode::containsAll('abcde', ['']), 'blank match string');
        self::assertFalse(Unicode::containsAll('abcde', ['a', 'z']), 'partial match first');
        self::assertFalse(Unicode::containsAll('abcde', ['z', 'a']), 'partial match last');
        self::assertTrue(Unicode::containsAll('abcde', ['a']), 'match single');
        self::assertFalse(Unicode::containsAll('abcde', ['z']), 'no match single');
        self::assertTrue(Unicode::containsAll('abcde', ['a', 'b']), 'match all first');
        self::assertTrue(Unicode::containsAll('abcde', ['c', 'b']), 'match all reversed');
        self::assertFalse(Unicode::containsAll('abcde', ['y', 'z']), 'no match all');
    }

    public function test_containsAny(): void
    {
        self::assertTrue(Unicode::containsAny('', []), 'blank and empty substrings');
        self::assertTrue(Unicode::containsAny('abcde', []), 'empty substrings');
        self::assertTrue(Unicode::containsAny('', ['']), 'blank match blank');
        self::assertTrue(Unicode::containsAny('abcde', ['']), 'blank matchs anything');
        self::assertTrue(Unicode::containsAny('abcde', ['a', 'z']), 'one match of many (first one matched)');
        self::assertTrue(Unicode::containsAny('abcde', ['z', 'a']), 'one match of many (last one matched)');
        self::assertTrue(Unicode::containsAny('abcde', ['a']), 'match single');
        self::assertFalse(Unicode::containsAny('abcde', ['z']), 'no match single');
        self::assertFalse(Unicode::containsAny('abcde', ['y', 'z']), 'no match all');
    }

    public function test_containsNone(): void
    {
        self::assertTrue(Unicode::containsNone('', []), 'blank and empty substrings');
        self::assertTrue(Unicode::containsNone('abcde', []), 'empty substrings');
        self::assertFalse(Unicode::containsNone('', ['']), 'blank match blank');
        self::assertFalse(Unicode::containsNone('abcde', ['']), 'blank matchs anything');
        self::assertFalse(Unicode::containsNone('abcde', ['a', 'z']), 'one match of many (first one matched)');
        self::assertFalse(Unicode::containsNone('abcde', ['z', 'a']), 'one match of many (last one matched)');
        self::assertFalse(Unicode::containsNone('abcde', ['a']), 'match single');
        self::assertTrue(Unicode::containsNone('abcde', ['z']), 'no match single');
        self::assertTrue(Unicode::containsNone('abcde', ['y', 'z']), 'no match all');
    }

    public function test_containsPattern(): void
    {
        self::assertTrue(Unicode::containsPattern('abc', '/b/'));
        self::assertTrue(Unicode::containsPattern('abc', '/ab/'));
        self::assertTrue(Unicode::containsPattern('abc', '/abc/'));
        self::assertTrue(Unicode::containsPattern('ABC', '/abc/i'));
        self::assertTrue(Unicode::containsPattern('aaaz', '/a{3}/'));
        self::assertTrue(Unicode::containsPattern('ABC1', '/[A-z\d]+/'));
        self::assertTrue(Unicode::containsPattern('ABC1]', '/\d]$/'));
        self::assertFalse(Unicode::containsPattern('AB1C', '/\d]$/'));
    }

    public function test_containsPattern_warning_as_error(): void
    {
        $this->expectExceptionMessage('preg_match(): Unknown modifier \'a\'');
        $this->expectException(Error::class);
        self::assertFalse(Unicode::containsPattern('', '/a/a'));
    }

    public function test_count(): void
    {
        // empty string
        self::assertSame(0, Unicode::count('', 'aaa'));

        // exact match
        self::assertSame(1, Unicode::count('abc', 'abc'));

        // no match
        self::assertSame(0, Unicode::count('ab', 'abc'));

        // simple
        self::assertSame(1, Unicode::count('This is a cat', ' is '));
        self::assertSame(2, Unicode::count('This is a cat', 'is'));

        // overlapping
        self::assertSame(2, Unicode::count('ababab', 'aba'));

        // utf8
        self::assertSame(2, Unicode::count('あいあ', 'あ'));

        // utf8 overlapping
        self::assertSame(2, Unicode::count('あああ', 'ああ'));

        // check half-width is not counted.
        self::assertSame(0, Unicode::count('ア', 'ｱ'));

        // grapheme
        self::assertSame(1, Unicode::count('👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦'));

        // grapheme subset should not match
        self::assertSame(0, Unicode::count('👨‍👨‍👧‍👦', '👨'));

        // grapheme overlapping
        self::assertSame(2, Unicode::count('👨‍👨‍👧‍👦👨‍👨‍👧‍👦👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦👨‍👨‍👧‍👦'));
    }

    public function test_count_with_empty_search(): void
    {
        $this->expectExceptionMessage('Search string must be non-empty');
        self::assertFalse(Unicode::count('a', ''));
    }

    public function test_cut(): void
    {
        // empty
        self::assertSame('', Unicode::cut('', 0));

        // basic
        self::assertSame('a', Unicode::cut('a', 1));
        self::assertSame('a', Unicode::cut('abc', 1));

        // utf-8
        self::assertSame('', Unicode::cut('あいう', 1));
        self::assertSame('あ', Unicode::cut('あいう', 3));

        // grapheme
        self::assertSame('', Unicode::cut('👋', 1));
        self::assertSame('', Unicode::cut('👋🏿', 1));
        self::assertSame('👋🏿', Unicode::cut('👋🏿', 8));

        // cut and replaced with ellipsis
        self::assertSame('a...', Unicode::cut('abc', 1, '...'));
        self::assertSame('...', Unicode::cut('あいう', 1, '...'));
        self::assertSame('あ...', Unicode::cut('あいう', 3, '...'));

        // cut and replaced with custom ellipsis
        self::assertSame('a$', Unicode::cut('abc', 1, '$'));
    }

    public function test_decapitalize(): void
    {
        self::assertSame('', Unicode::decapitalize(''));
        self::assertSame('test', Unicode::decapitalize('Test'));
        self::assertSame('t T', Unicode::decapitalize('T T'));
        self::assertSame(' T ', Unicode::decapitalize(' T '));
        self::assertSame('é', Unicode::decapitalize('É'));
        self::assertSame('🔡', Unicode::decapitalize('🔡'));
    }

    public function test_doesNotContain(): void
    {
        self::assertTrue(Unicode::doesNotContain('abcde', 'ac'));
        self::assertFalse(Unicode::doesNotContain('abcde', 'ab'));
        self::assertFalse(Unicode::doesNotContain('a', ''));
        self::assertTrue(Unicode::doesNotContain('', 'a'));
        self::assertTrue(Unicode::doesNotContain('👨‍👨‍👧‍👧‍', '👨'));
    }

    public function test_doesNotEndWith(): void
    {
        self::assertFalse(Unicode::doesNotEndWith('abc', 'c'));
        self::assertTrue(Unicode::doesNotEndWith('abc', 'b'));
        self::assertFalse(Unicode::doesNotEndWith('abc', ['c']));
        self::assertFalse(Unicode::doesNotEndWith('abc', ['a', 'b', 'c']));
        self::assertTrue(Unicode::doesNotEndWith('abc', ['a', 'b']));
        self::assertFalse(Unicode::doesNotEndWith('aabbcc', 'cc'));
        self::assertFalse(Unicode::doesNotEndWith('aabbcc' . PHP_EOL, PHP_EOL));
        self::assertFalse(Unicode::doesNotEndWith('abc0', '0'));
        self::assertFalse(Unicode::doesNotEndWith('abcfalse', 'false'));
        self::assertFalse(Unicode::doesNotEndWith('a', ''));
        self::assertFalse(Unicode::doesNotEndWith('', ''));
        self::assertFalse(Unicode::doesNotEndWith('あいう', 'う'));
        self::assertTrue(Unicode::doesNotEndWith("あ\n", 'あ'));
        self::assertTrue(Unicode::doesNotEndWith('👋🏻', '🏻'));
    }


    public function test_doesNotStartWith(): void
    {
        self::assertFalse(Unicode::doesNotStartWith('', ''));
        self::assertFalse(Unicode::doesNotStartWith('bb', ''));
        self::assertFalse(Unicode::doesNotStartWith('bb', 'b'));
        self::assertTrue(Unicode::doesNotStartWith('bb', 'ab'));
        self::assertFalse(Unicode::doesNotStartWith('あ-い-う', 'あ'));
        self::assertTrue(Unicode::doesNotStartWith('あ-い-う', 'え'));
        self::assertTrue(Unicode::doesNotStartWith('👨‍👨‍👧‍👦', '👨‍'));
        self::assertFalse(Unicode::doesNotStartWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿 👨‍👨‍👧‍👦', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
        self::assertTrue(Unicode::doesNotStartWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿 👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦'));
        self::assertFalse(Unicode::doesNotStartWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿', '🏴󠁧󠁢󠁳󠁣󠁴󠁿a'));
        self::assertTrue(Unicode::doesNotStartWith('ba', 'a'));
        self::assertTrue(Unicode::doesNotStartWith('', 'a'));
        self::assertTrue(Unicode::doesNotStartWith('abc', ['d', 'e']));
        self::assertFalse(Unicode::doesNotStartWith('abc', ['d', 'a']));
        self::assertTrue(Unicode::doesNotStartWith("\nあ", 'あ'));
    }

    public function test_drop(): void
    {
        // empty
        self::assertSame('', Unicode::dropFirst('', 1));

        // zero amount
        self::assertSame('a', Unicode::dropFirst('a', 0));

        // mid amount
        self::assertSame('e', Unicode::dropFirst('abcde', 4));

        // exact amount
        self::assertSame('', Unicode::dropFirst('abc', 3));

        // over overflow
        self::assertSame('', Unicode::dropFirst('abc', 4));

        // grapheme
        self::assertSame('def', Unicode::dropFirst('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿def', 4));

        // grapheme cluster (positive)
        self::assertSame('', Unicode::dropFirst('👋🏿', 1));
    }

    public function test_drop_negative_amount(): void
    {
        $this->expectExceptionMessage('Expected a value greater than or equal to 0. Got: -4');
        Unicode::dropFirst('abc', -4);
    }

    public function test_dropLast(): void
    {
        // empty
        self::assertSame('', Unicode::dropLast('', 1));

        // zero length
        self::assertSame('a', Unicode::dropLast('a', 0));

        // mid amount
        self::assertSame('ab', Unicode::dropLast('abc', 1));

        // exact amount
        self::assertSame('', Unicode::dropLast('abc', 3));

        // overflow
        self::assertSame('', Unicode::dropLast('abc', 4));

        // grapheme
        self::assertSame('abc', Unicode::dropLast('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿def', 4));

        // grapheme cluster (positive)
        self::assertSame('', Unicode::dropLast('👋🏿', 1));
    }

    public function test_dropLast_negative_amount(): void
    {
        $this->expectExceptionMessage('Expected a value greater than or equal to 0. Got: -4');
        Unicode::dropLast('abc', -4);
    }

    public function test_endsWith(): void
    {
        self::assertTrue(Unicode::endsWith('abc', 'c'));
        self::assertFalse(Unicode::endsWith('abc', 'b'));
        self::assertTrue(Unicode::endsWith('abc', ['c']));
        self::assertTrue(Unicode::endsWith('abc', ['a', 'b', 'c']));
        self::assertFalse(Unicode::endsWith('abc', ['a', 'b']));
        self::assertTrue(Unicode::endsWith('aabbcc', 'cc'));
        self::assertTrue(Unicode::endsWith('aabbcc' . PHP_EOL, PHP_EOL));
        self::assertTrue(Unicode::endsWith('abc0', '0'));
        self::assertTrue(Unicode::endsWith('abcfalse', 'false'));
        self::assertTrue(Unicode::endsWith('a', ''));
        self::assertTrue(Unicode::endsWith('', ''));
        self::assertTrue(Unicode::endsWith('あいう', 'う'));
        self::assertFalse(Unicode::endsWith("あ\n", 'あ'));
        self::assertFalse(Unicode::endsWith('👋🏻', '🏻'));
    }

    public function test_indexOfFirst(): void
    {
        // empty string
        self::assertNull(Unicode::indexOfFirst('', 'a'));

        // empty search
        self::assertSame(0, Unicode::indexOfFirst('ab', ''));

        // find at 0
        self::assertSame(0, Unicode::indexOfFirst('a', 'a'));

        // multiple matches
        self::assertSame(1, Unicode::indexOfFirst('abb', 'b'));

        // offset (within bound)
        self::assertSame(1, Unicode::indexOfFirst('abb', 'b', 1));
        self::assertSame(5, Unicode::indexOfFirst('aaaaaa', 'a', 5));

        // offset (out of bound)
        self::assertNull(Unicode::indexOfFirst('abb', 'b', 4));

        // offset (negative)
        self::assertSame(2, Unicode::indexOfFirst('abb', 'b', -1));

        // offset (negative)
        self::assertNull(Unicode::indexOfFirst('abb', 'b', -100));

        // offset utf-8
        self::assertSame(0, Unicode::indexOfFirst('👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦'));
        self::assertNull(Unicode::indexOfFirst('👨‍👨‍👧‍👦', '👨'));
        self::assertSame(1, Unicode::indexOfFirst('あいう', 'い', 1));
        self::assertSame(1, Unicode::indexOfFirst('🏴󠁧󠁢󠁳󠁣󠁴󠁿👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦', 1));
        self::assertNull(Unicode::indexOfFirst('🏴󠁧󠁢󠁳󠁣󠁴󠁿👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦', 2));
    }

    public function test_indexOfLast(): void
    {
        // empty string
        self::assertNull(Unicode::indexOfLast('', 'a'));

        // empty search
        self::assertSame(2, Unicode::indexOfLast('ab', ''));

        // find at 0
        self::assertSame(0, Unicode::indexOfLast('a', 'a'));

        // multiple matches
        self::assertSame(2, Unicode::indexOfLast('abb', 'b'));

        // offset (within bound)
        self::assertSame(2, Unicode::indexOfLast('abb', 'b', 1));
        self::assertSame(5, Unicode::indexOfLast('aaaaaa', 'a', 5));

        // offset (out of bound)
        self::assertNull(Unicode::indexOfLast('abb', 'b', 4));

        // offset (negative)
        self::assertSame(3, Unicode::indexOfLast('abbb', 'b', -1));

        // offset (negative)
        self::assertNull(Unicode::indexOfLast('abb', 'b', -100));

        // offset utf-8
        self::assertSame(0, Unicode::indexOfLast('👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦'));
        self::assertNull(Unicode::indexOfLast('👨‍👨‍👧‍👦', '👨'));
        self::assertSame(1, Unicode::indexOfLast('あいう', 'い', 1));
        self::assertSame(1, Unicode::indexOfLast('🏴󠁧󠁢󠁳󠁣󠁴󠁿👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦', 1));
        self::assertNull(Unicode::indexOfLast('🏴󠁧󠁢󠁳󠁣󠁴󠁿👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦', 2));
    }

    public function test_insert(): void
    {
        self::assertSame('xyzabc', Unicode::insert('abc', 'xyz', 0));
        self::assertSame('axyzbc', Unicode::insert('abc', 'xyz', 1));
        self::assertSame('xyzabc', Unicode::insert('abc', 'xyz', -1));
        self::assertSame('abcxyz', Unicode::insert('abc', 'xyz', 3));
        self::assertSame('あxyzい', Unicode::insert('あい', 'xyz', 1));
        self::assertSame('xyzあい', Unicode::insert('あい', 'xyz', -1));
    }

    public function test_isBlank(): void
    {
        self::assertTrue(Unicode::isBlank(''));
        self::assertFalse(Unicode::isBlank('0'));
        self::assertFalse(Unicode::isBlank(' '));
    }

    public function test_isNotBlank(): void
    {
        self::assertFalse(Unicode::isNotBlank(''));
        self::assertTrue(Unicode::isNotBlank('0'));
        self::assertTrue(Unicode::isNotBlank(' '));
    }

    public function test_kebabCase(): void
    {
        self::assertSame('test', Unicode::toKebabCase('test'));
        self::assertSame('test', Unicode::toKebabCase('Test'));
        self::assertSame('ttt', Unicode::toKebabCase('TTT'));
        self::assertSame('tt-test', Unicode::toKebabCase('TTTest'));
        self::assertSame('test-test', Unicode::toKebabCase('testTest'));
        self::assertSame('test-t-test', Unicode::toKebabCase('testTTest'));
        self::assertSame('test-test', Unicode::toKebabCase('test-test'));
        self::assertSame('test-test', Unicode::toKebabCase('test_test'));
        self::assertSame('test-test', Unicode::toKebabCase('test test'));
        self::assertSame('test-test-test', Unicode::toKebabCase('test test test'));
        self::assertSame('-test--test--', Unicode::toKebabCase(' test  test  '));
        self::assertSame('--test-test-test--', Unicode::toKebabCase("--test_test-test__"));
    }

    public function test_length(): void
    {
        // empty
        self::assertSame(0, Unicode::length(''));

        // ascii
        self::assertSame(4, Unicode::length('Test'));
        self::assertSame(9, Unicode::length(' T e s t '));

        // utf8
        self::assertSame(2, Unicode::length('あい'));
        self::assertSame(4, Unicode::length('あいzう'));

        // emoji
        self::assertSame(1, Unicode::length('👨‍👨‍👧‍👦'));
    }

    public function test_length_invalid_string(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Error converting input string to UTF-16: U_INVALID_CHAR_FOUND');
        Unicode::length(substr('あ', 1));
    }

    public function test_matchAll(): void
    {
        self::assertSame([['a', 'a']], Unicode::matchAll('abcabc', '/a/'));
        self::assertSame([['abc', 'abc'], 'p1' => ['a', 'a'], ['a', 'a']], Unicode::matchAll('abcabc', '/(?<p1>a)bc/'));
        self::assertSame([[]], Unicode::matchAll('abcabc', '/bcd/'));
        self::assertSame([['cd', 'c']], Unicode::matchAll('abcdxabc', '/c[^x]*/'));
        self::assertSame([[]], Unicode::matchAll('abcabcx', '/^abcx/'));
        self::assertSame([['cx']], Unicode::matchAll('abcabcx', '/cx$/'));
    }

    public function test_matchAll_without_slashes(): void
    {
        $this->expectWarning();
        $this->expectWarningMessage('preg_match_all(): Delimiter must not be alphanumeric, backslash, or NUL');
        Unicode::matchAll('abcabc', 'a');
    }

    public function test_matchFirst(): void
    {
        self::assertSame('a', Unicode::matchFirst('abcabc', '/a/'));
        self::assertSame('abc', Unicode::matchFirst('abcabc', '/(?<p1>a)bc/'));
        self::assertSame('cd', Unicode::matchFirst('abcdxabc', '/c[^x]*/'));
        self::assertSame('cx', Unicode::matchFirst('abcabcx', '/cx$/'));
    }

    public function test_matchFirst_no_match(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"aaa" does not match /z/');
        Unicode::matchFirst('aaa', '/z/');
    }

    public function test_matchFirst_without_slashes(): void
    {
        $this->expectException(Warning::class);
        $this->expectExceptionMessage('preg_match(): Delimiter must not be alphanumeric, backslash, or NUL');
        Unicode::matchFirst('abcabc', 'a');
    }

    public function test_matchFirstOrNull(): void
    {
        self::assertSame('a', Unicode::matchFirstOrNull('abcabc', '/a/'));
        self::assertSame('abc', Unicode::matchFirstOrNull('abcabc', '/(?<p1>a)bc/'));
        self::assertSame(null, Unicode::matchFirstOrNull('abcabc', '/bcd/'));
        self::assertSame('cd', Unicode::matchFirstOrNull('abcdxabc', '/c[^x]*/'));
        self::assertSame(null, Unicode::matchFirstOrNull('abcabcx', '/^abcx/'));
        self::assertSame('cx', Unicode::matchFirstOrNull('abcabcx', '/cx$/'));
    }

    public function test_matchFirstOrNull_without_slashes(): void
    {
        Unicode::matchFirstOrNull('abcabc', 'a');
    }

    public function test_pad(): void
    {
        // empty string
        self::assertSame('', Unicode::pad('', -1, '_'));

        // pad string
        self::assertSame('abc', Unicode::pad('abc', 3, ''));

        // defaults to pad right
        self::assertSame('a', Unicode::pad('a', -1, '_'));
        self::assertSame('a', Unicode::pad('a', 0, '_'));
        self::assertSame('a_', Unicode::pad('a', 2, '_'));
        self::assertSame('__', Unicode::pad('_', 2, '_'));
        self::assertSame('ab', Unicode::pad('ab', 1, '_'));

        // overflow padding
        self::assertSame('abcd', Unicode::pad('a', 4, 'bcde'));
    }

    public function test_pad_invalid_pad(): void
    {
        $this->expectExceptionMessage('Invalid padding type: 3');
        self::assertSame('ab', Unicode::pad('ab', 1, '_', 3));
    }

    public function test_padBoth(): void
    {
        self::assertSame('a', Unicode::padBoth('a', -1, '_'));
        self::assertSame('a', Unicode::padBoth('a', 0, '_'));
        self::assertSame('a_', Unicode::padBoth('a', 2, '_'));
        self::assertSame('__', Unicode::padBoth('_', 2, '_'));
        self::assertSame('_a_', Unicode::padBoth('a', 3, '_'));
        self::assertSame('__a__', Unicode::padBoth('a', 5, '_'));
        self::assertSame('__a___', Unicode::padBoth('a', 6, '_'));
        self::assertSame('12hello123', Unicode::padBoth('hello', 10, '123'));
        self::assertSame('いあい', Unicode::padBoth('あ', 3, 'い'));
    }

    public function test_padEnd(): void
    {
        self::assertSame('a', Unicode::padEnd('a', -1, '_'));
        self::assertSame('a', Unicode::padEnd('a', 0, '_'));
        self::assertSame('a_', Unicode::padEnd('a', 2, '_'));
        self::assertSame('__', Unicode::padEnd('_', 2, '_'));
        self::assertSame('ab', Unicode::padEnd('ab', 1, '_'));
        self::assertSame('あいういう', Unicode::padEnd('あ', 5, 'いう'), 'multi byte');
        self::assertSame('עִברִיתכן', Unicode::padEnd('עִברִית', 7, 'כן'), 'rtol languages');
    }

    public function test_padStart(): void
    {
        self::assertSame('a', Unicode::padStart('a', -1, '_'));
        self::assertSame('a', Unicode::padStart('a', 0, '_'));
        self::assertSame('_a', Unicode::padStart('a', 2, '_'));
        self::assertSame('__', Unicode::padStart('_', 2, '_'));
        self::assertSame('ab', Unicode::padStart('ab', 1, '_'));
        self::assertSame('いういうあ', Unicode::padStart('あ', 5, 'いう'), 'multi byte');
    }

    public function test_remove(): void
    {
        self::assertSame('', Unicode::remove('', ''), 'empty');
        self::assertSame('', Unicode::remove('aaa', 'a'), 'delete everything');
        self::assertSame('a  a', Unicode::remove('aaa aa a', 'aa'), 'no traceback check');
        self::assertSame('no match', Unicode::remove('no match', 'hctam on'), 'out of order chars');
        self::assertSame('aa', Unicode::remove('aa', 'a', 0), 'limit to 0');
        self::assertSame('a', Unicode::remove('aaa', 'a', 2), 'limit to 2');

        $count = 0;
        self::assertSame('aaa', Unicode::remove('aaa', 'a', 0, $count), 'count none');
        self::assertSame(0, $count);

        self::assertSame('a', Unicode::remove('aaa', 'a', 2, $count), 'count several');
        self::assertSame(2, $count);

        self::assertSame('', Unicode::remove('aaa', 'a', null, $count), 'count unlimited');
        self::assertSame(3, $count);
    }

    public function test_remove_with_negative_limit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a value greater than or equal to 0. Got: -1');
        Unicode::remove('', '', -1);
    }

    public function test_repeat(): void
    {
        self::assertSame('aaa', Unicode::repeat('a', 3));
        self::assertSame('', Unicode::repeat('a', 0));
    }

    public function test_repeat_negative_times(): void
    {
        $this->expectException(\Kirameki\Core\Exceptions\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected: $times >= 0. Got: -1.');
        Unicode::repeat('a', -1);
    }

    public function test_replace(): void
    {
        self::assertSame('', Unicode::replace('', '', ''));
        self::assertSame('b', Unicode::replace('b', '', 'a'));
        self::assertSame('aa', Unicode::replace('bb', 'b', 'a'));
        self::assertSame('', Unicode::replace('b', 'b', ''));
        self::assertSame('あえいえう', Unicode::replace('あ-い-う', '-', 'え'));
        self::assertSame('__🏴󠁧󠁢󠁳󠁣󠁴󠁿', Unicode::replace('🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿', '🏴󠁧󠁢󠁳󠁣󠁴󠁿a', '_'));

        // slash
        self::assertSame('abc', Unicode::replace('ab\c', '\\', ''));

        // dot
        self::assertSame('abc', Unicode::replace('abc.*', '.*', ''));

        // regex chars
        self::assertSame('a', Unicode::replace('[]/\\!?', '[]/\\!?', 'a'));

        // with limit and count
        $count = 0;
        self::assertSame('a', Unicode::replace('aaa', 'a', '', 2, $count));
        self::assertSame(2, $count);

        // 0 count for no match
        $count = 0;
        self::assertSame('', Unicode::replace('', '', '', null, $count));
        self::assertSame(0, $count);

        // should treat emoji cluster as one character
        self::assertSame('👋🏿', Unicode::replace('👋🏿', '👋', ''));
    }

    public function test_replace_with_negative_limit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a value greater than or equal to 0. Got: -1');
        Unicode::replace('', 'a', 'a', -1);
    }

    public function test_replaceEach(): void
    {
        // empty string
        self::assertSame('', Unicode::replaceEach('', ['?'], ['!']));

        // empty search string
        self::assertSame('test', Unicode::replaceEach('test', [''], ['a']));

        // replace each ?
        self::assertSame('x & y', Unicode::replaceEach('? & ?', ['?', '?'], ['x', 'y']));

        // utf-8
        self::assertSame('うえ', Unicode::replaceEach('あい', ['あ', 'い'], ['う', 'え']));

        // should treat emoji cluster as one character
        self::assertSame('👋🏿', Unicode::replaceEach('👋🏿', ['👋'], ['']));
    }

    public function test_replaceFirst(): void
    {
        self::assertSame('', Unicode::replaceFirst('', '', ''), 'empty string');
        self::assertSame('bb', Unicode::replaceFirst('bb', '', 'a'), 'empty search');
        self::assertSame('abb', Unicode::replaceFirst('bbb', 'b', 'a'), 'basic');
        self::assertSame('b', Unicode::replaceFirst('bb', 'b', ''), 'empty replacement');
        self::assertSame('あえい-う', Unicode::replaceFirst('あ-い-う', '-', 'え'), 'mbstring');
        self::assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿 a', Unicode::replaceFirst('🏴󠁧󠁢󠁳󠁣󠁴󠁿 👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦', 'a'), 'multiple codepoints');
        self::assertSame('_🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿', Unicode::replaceFirst('🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿', '🏴󠁧󠁢󠁳󠁣󠁴󠁿a', '_'));
        self::assertSame('👋🏿', Unicode::replaceFirst('👋🏿', '👋', ''), 'treat emoji cluster as one character');

        $replaced = false;
        Unicode::replaceFirst('bbb', 'b', 'a', $replaced);
        self::assertTrue($replaced, 'validate flag');

        $replaced = true;
        Unicode::replaceFirst('b', 'z', '', $replaced);
        self::assertFalse($replaced, 'flag is overridden with false');
    }

    public function test_replaceLast(): void
    {
        self::assertSame('', Unicode::replaceLast('', '', ''), 'empty string');
        self::assertSame('bb', Unicode::replaceLast('bb', '', 'a'), 'empty search');
        self::assertSame('bba', Unicode::replaceLast('bbb', 'b', 'a'), 'basic');
        self::assertSame('b', Unicode::replaceLast('bb', 'b', ''), 'empty replacement');
        self::assertSame('あ-いえう', Unicode::replaceLast('あ-い-う', '-', 'え'), 'mbstring');
        self::assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿 a', Unicode::replaceLast('🏴󠁧󠁢󠁳󠁣󠁴󠁿 👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦', 'a'), 'multiple codepoints');
        self::assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿a_🏴󠁧󠁢󠁳󠁣󠁴󠁿', Unicode::replaceLast('🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿', '🏴󠁧󠁢󠁳󠁣󠁴󠁿a', '_'));
        self::assertSame('👋🏿', Unicode::replaceLast('👋🏿', '👋', ''), 'treat emoji cluster as one character');

        $replaced = false;
        Unicode::replaceLast('bbb', 'b', 'a', $replaced);
        self::assertTrue($replaced, 'validate flag');

        $replaced = true;
        Unicode::replaceLast('b', 'z', '', $replaced);
        self::assertFalse($replaced, 'flag is overridden with false');
    }

    public function test_replaceMatch(): void
    {
        self::assertSame('', Unicode::replaceMatch('', '', ''));
        self::assertSame('abb', Unicode::replaceMatch('abc', '/c/', 'b'));
        self::assertSame('abbb', Unicode::replaceMatch('abcc', '/c/', 'b'));
        self::assertSame('あいい', Unicode::replaceMatch('あいう', '/う/', 'い'));
        self::assertSame('x', Unicode::replaceMatch('abcde', '/[A-Za-z]+/', 'x'));
        self::assertSame('a-b', Unicode::replaceMatch('a🏴󠁧󠁢󠁳󠁣󠁴󠁿b', '/🏴󠁧󠁢󠁳󠁣󠁴󠁿/', '-'));

        // with null count no match
        $count = 0;
        self::assertSame('', Unicode::replaceMatch('', '', '', null, $count));
        self::assertSame(0, $count);

        // with null count
        $count = 0;
        self::assertSame('', Unicode::replaceMatch('aaa', '/a/', '', null, $count));
        self::assertSame(3, $count);

        // with counter reset
        $count = 1;
        self::assertSame('', Unicode::replaceMatch('aaa', '/a/', '', null, $count));
        self::assertSame(3, $count);

        // with limit
        self::assertSame('a', Unicode::replaceMatch('aaa', '/a/', '', 2));
    }

    public function test_replaceMatch_with_negative_limit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a value greater than or equal to 0. Got: -1');
        Unicode::replaceMatch('', '/a/', 'a', -1);
    }

    public function test_reverse(): void
    {
        self::assertSame('', Unicode::reverse(''));
        self::assertSame('ba', Unicode::reverse('ab'));
        self::assertSame('ういあ', Unicode::reverse('あいう'));
        self::assertSame('cbあ🏴󠁧󠁢󠁳󠁣󠁴󠁿', Unicode::reverse('🏴󠁧󠁢󠁳󠁣󠁴󠁿あbc'));
    }

    public function test_startsWith(): void
    {
        self::assertTrue(Unicode::startsWith('', ''));
        self::assertTrue(Unicode::startsWith('bb', ''));
        self::assertTrue(Unicode::startsWith('bb', 'b'));
        self::assertTrue(Unicode::startsWith('あ-い-う', 'あ'));
        self::assertFalse(Unicode::startsWith('👨‍👨‍👧‍👦', '👨‍'));
        self::assertTrue(Unicode::startsWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿 👨‍👨‍👧‍👦', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
        self::assertFalse(Unicode::startsWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿 👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦'));
        self::assertTrue(Unicode::startsWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿', '🏴󠁧󠁢󠁳󠁣󠁴󠁿a'));
        self::assertFalse(Unicode::startsWith('ba', 'a'));
        self::assertFalse(Unicode::startsWith('', 'a'));
    }

    public function test_split(): void
    {
        // empty
        self::assertSame(['', ''], Unicode::split(' ', ' '));

        // no match
        self::assertSame(['abc'], Unicode::split('abc', '_'));

        // match
        self::assertSame(['a', 'c', 'd'], Unicode::split('abcbd', 'b'));

        // match utf-8
        self::assertSame(['あ', 'う'], Unicode::split('あいう', 'い'));

        // match with limit
        self::assertSame(['a', 'cbd'], Unicode::split('abcbd', 'b', 2));

        // match with limit
        self::assertSame(['a', 'b', 'c'], Unicode::split('abc', ''));

        // match emoji
        self::assertSame(['👨‍👨‍👧‍👦'], Unicode::split('👨‍👨‍👧‍👦', '‍👦'));
    }

    public function test_split_with_negative_limit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a value greater than or equal to 0. Got: -1');
        Unicode::split('a', 'b', -1);
    }

    public function test_substring(): void
    {
        // empty
        self::assertSame('', Unicode::substring('', 0));
        self::assertSame('', Unicode::substring('', 0, 1));

        // ascii
        self::assertSame('abc', Unicode::substring('abc', 0));
        self::assertSame('bc', Unicode::substring('abc', 1));
        self::assertSame('c', Unicode::substring('abc', -1));
        self::assertSame('a', Unicode::substring('abc', 0, 1));
        self::assertSame('b', Unicode::substring('abc', 1, 1));
        self::assertSame('b', Unicode::substring('abc', -2, 1));
        self::assertSame('bc', Unicode::substring('abc', -2, 2));
        self::assertSame('ab', Unicode::substring('abc', -9999, 2));
        self::assertSame('ab', Unicode::substring('abc', 0, -1));
        self::assertSame('a', Unicode::substring('abc', 0, -2));
        self::assertSame('', Unicode::substring('abc', 0, -3));
        self::assertSame('', Unicode::substring('abc', 2, -1));

        // utf-8
        self::assertSame('あいう', Unicode::substring('あいう', 0));
        self::assertSame('いう', Unicode::substring('あいう', 1));
        self::assertSame('う', Unicode::substring('あいう', -1));
        self::assertSame('い', Unicode::substring('あいう', -2, 1));
        self::assertSame('いう', Unicode::substring('あいう', -2, 2));
        self::assertSame('あい', Unicode::substring('あいう', -9999, 2));

        // grapheme
        self::assertSame('👨‍👨‍👧‍👦', Unicode::substring('👨‍👨‍👧‍👦', 0));
        self::assertSame('', Unicode::substring('👨‍👨‍👧‍👦', 1));
        self::assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿', Unicode::substring('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿', 1));
        self::assertSame('👨‍👨‍👧‍👦', Unicode::substring('👨‍👨‍👧‍👦👨‍👨‍👧‍👦', 1, 1));
        self::assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿', Unicode::substring('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿', -1, 1));
    }

    public function test_substring_invalid_input(): void
    {
        $this->expectExceptionMessage('Error converting input string to UTF-16: U_INVALID_CHAR_FOUND');
        self::assertSame('', Unicode::substring(substr('あ', 1), 0, 2));
    }

    public function test_takeFirst(): void
    {
        // empty string
        self::assertSame('', Unicode::takeFirst('', 1));

        // empty string
        self::assertSame('', Unicode::takeFirst('', 1));

        // zero amount
        self::assertSame('', Unicode::takeFirst('a', 0));

        // mid amount
        self::assertSame('abcd', Unicode::takeFirst('abcde', 4));

        // exact length
        self::assertSame('abc', Unicode::takeFirst('abc', 3));

        // grapheme
        self::assertSame('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿', Unicode::takeFirst('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', 4));

        // grapheme cluster
        self::assertSame('👋🏿', Unicode::takeFirst('👋🏿', 1));
    }

    public function test_takeFirst_out_of_range_negative(): void
    {
        $this->expectExceptionMessage('Expected a value greater than or equal to 0. Got: -4');
        Unicode::takeFirst('abc', -4);
    }

    public function test_takeLast(): void
    {
        // empty string
        self::assertSame('', Unicode::takeLast('', 1));

        // empty string
        self::assertSame('', Unicode::takeLast('', 1));

        // zero amount
        self::assertSame('a', Unicode::takeLast('a', 0));

        // mid amount
        self::assertSame('bcde', Unicode::takeLast('abcde', 4));

        // exact length
        self::assertSame('abc', Unicode::takeLast('abc', 3));

        // over length
        self::assertSame('abc', Unicode::takeLast('abc', 4));

        // grapheme
        self::assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', Unicode::takeLast('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', 4));

        // grapheme cluster
        self::assertSame('👋🏿', Unicode::takeLast('👋🏿', 1));
    }

    public function test_takeLast_out_of_range_negative(): void
    {
        $this->expectExceptionMessage('Expected a value greater than or equal to 0. Got: -4');
        Unicode::takeLast('abc', -4);
    }

    public function test_toBool(): void
    {
        self::assertTrue(Unicode::toBool('true'), 'true as string');
        self::assertTrue(Unicode::toBool('TRUE'), 'TRUE as string');
        self::assertFalse(Unicode::toBool('false'), 'false as string');
        self::assertFalse(Unicode::toBool('FALSE'), 'FALSE as string');
        self::assertTrue(Unicode::toBool('1'), 'empty as string');
    }

    public function test_toBool_empty(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"" is not a valid boolean string.');
        // empty as string
        Unicode::toBool('');
    }

    public function test_toBool_with_negative(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"-2" is not a valid boolean string.');
        // invalid boolean (number)
        Unicode::toBool('-2');
    }

    public function test_toBool_with_yes(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"yes" is not a valid boolean string.');
        // truthy will fail
        Unicode::toBool('yes');
    }

    public function test_toBoolOrNull(): void
    {
        self::assertTrue(Unicode::toBoolOrNull('true'), 'true as string');
        self::assertTrue(Unicode::toBoolOrNull('TRUE'), 'TRUE as string');
        self::assertFalse(Unicode::toBoolOrNull('false'), 'false as string');
        self::assertFalse(Unicode::toBoolOrNull('FALSE'), 'FALSE as string');
        self::assertTrue(Unicode::toBoolOrNull('1'), 'empty as string');
        self::assertNull(Unicode::toBoolOrNull(''), 'empty as string');
        self::assertNull(Unicode::toBoolOrNull('-2'), 'invalid boolean (number)');
        self::assertNull(Unicode::toBoolOrNull('yes'), 'truthy will fail');
    }

    public function test_toCamelCase(): void
    {
        self::assertSame('test', Unicode::toCamelCase('test'));
        self::assertSame('test', Unicode::toCamelCase('Test'));
        self::assertSame('testTest', Unicode::toCamelCase('test-test'));
        self::assertSame('testTest', Unicode::toCamelCase('test_test'));
        self::assertSame('testTest', Unicode::toCamelCase('test test'));
        self::assertSame('testTestTest', Unicode::toCamelCase('test test test'));
        self::assertSame('testTest', Unicode::toCamelCase(' test  test  '));
        self::assertSame('testTestTest', Unicode::toCamelCase("--test_test-test__"));
    }

    public function test_toFloat(): void
    {
        self::assertSame(1.0, Unicode::toFloat('1'), 'positive int');
        self::assertSame(-1.0, Unicode::toFloat('-1'), 'negative int');
        self::assertSame(1.23, Unicode::toFloat('1.23'), 'positive float');
        self::assertSame(-1.23, Unicode::toFloat('-1.23'), 'negative float');
        self::assertSame(0.0, Unicode::toFloat('0'), 'zero int');
        self::assertSame(0.0, Unicode::toFloat('0.0'), 'zero float');
        self::assertSame(0.0, Unicode::toFloat('-0'), 'negative zero int');
        self::assertSame(0.0, Unicode::toFloat('-0.0'), 'negative zero float');
        self::assertSame(0.123, Unicode::toFloat('0.123'), 'start from zero');
        self::assertSame(123.456, Unicode::toFloat('123.456'), 'multiple digits');
        self::assertSame(1230.0, Unicode::toFloat('1.23e3'), 'scientific notation with e');
        self::assertSame(1230.0, Unicode::toFloat('1.23E3'), 'scientific notation with E');
        self::assertSame(-1230.0, Unicode::toFloat('-1.23e3'), 'scientific notation as negative');
        self::assertSame(1.234, Unicode::toFloatOrNull('123.4E-2'), 'scientific notation irregular');
        self::assertSame(1230.0, Unicode::toFloat('1.23e+3'), 'with +e');
        self::assertSame(1230.0, Unicode::toFloat('1.23E+3'), 'with +E');
        self::assertSame(0.012, Unicode::toFloat('1.2e-2'), 'with -e');
        self::assertSame(0.012, Unicode::toFloat('1.2E-2'), 'with -E');
        self::assertNan(Unicode::toFloat('NAN'), 'NAN');
        self::assertNan(Unicode::toFloat('-NAN'), 'Negative NAN');
        self::assertNan(Unicode::toFloat('NaN'), 'NaN from Javascript');
        self::assertNan(Unicode::toFloat('-NaN'), 'Negative NaN');
        self::assertInfinite(Unicode::toFloat('INF'), 'upper case INF');
        self::assertInfinite(Unicode::toFloat('Infinity'), 'INF from Javascript');
    }

    public function test_toFloat_overflow_e_notation(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Float precision lost for "1e20"');
        Unicode::toFloat('1e20');
    }

    public function test_toFloat_empty_string(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"" is not a valid float.');
        Unicode::toFloat('');
    }

    public function test_toFloat_invalid_string(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"1a" is not a valid float.');
        Unicode::toFloat('1a');
    }

    public function test_toFloat_dot_start(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('".1" is not a valid float.');
        Unicode::toFloat('.1');
    }

    public function test_toFloat_zero_start(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"00.1" is not a valid float.');
        Unicode::toFloat('00.1');
    }

    public function test_toFloat_overflow_number(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Float precision lost for "1.11111111111111"');
        Unicode::toFloat('1.' . str_repeat('1', 14));
    }

    public function test_toFloatOrNull(): void
    {
        self::assertSame(1.0, Unicode::toFloatOrNull('1'), 'positive int');
        self::assertSame(-1.0, Unicode::toFloatOrNull('-1'), 'negative int');
        self::assertSame(1.23, Unicode::toFloatOrNull('1.23'), 'positive float');
        self::assertSame(-1.23, Unicode::toFloatOrNull('-1.23'), 'negative float');
        self::assertSame(0.0, Unicode::toFloatOrNull('0'), 'zero int');
        self::assertSame(0.0, Unicode::toFloatOrNull('0.0'), 'zero float');
        self::assertSame(0.0, Unicode::toFloatOrNull('-0'), 'negative zero int');
        self::assertSame(0.0, Unicode::toFloatOrNull('-0.0'), 'negative zero float');
        self::assertSame(0.123, Unicode::toFloatOrNull('0.123'), 'start from zero');
        self::assertSame(123.456, Unicode::toFloatOrNull('123.456'), 'multiple digits');
        self::assertSame(1230.0, Unicode::toFloatOrNull('1.23e3'), 'scientific notation with e');
        self::assertSame(1230.0, Unicode::toFloatOrNull('1.23E3'), 'scientific notation with E');
        self::assertSame(-1230.0, Unicode::toFloatOrNull('-1.23e3'), 'scientific notation as negative');
        self::assertSame(1230.0, Unicode::toFloatOrNull('1.23e+3'), 'with +e');
        self::assertSame(1230.0, Unicode::toFloatOrNull('1.23E+3'), 'with +E');
        self::assertSame(0.012, Unicode::toFloatOrNull('1.2e-2'), 'with -e');
        self::assertSame(0.012, Unicode::toFloatOrNull('1.2E-2'), 'with -E');
        self::assertSame(1.234, Unicode::toFloatOrNull('123.4E-2'), 'scientific notation irregular');
        self::assertNull(Unicode::toFloatOrNull('1e+20'), 'overflowing +e notation');
        self::assertNull(Unicode::toFloatOrNull('1e-20'), 'overflowing -e notation');
        self::assertNull(Unicode::toFloatOrNull('nan'), 'Lowercase nan is not NAN');
        self::assertNan(Unicode::toFloatOrNull('NAN'), 'NAN');
        self::assertNan(Unicode::toFloatOrNull('-NAN'), 'Negative NAN');
        self::assertNan(Unicode::toFloatOrNull('NaN'), 'NaN from Javascript');
        self::assertNan(Unicode::toFloatOrNull('-NaN'), 'Negative NaN');
        self::assertNull(Unicode::toFloatOrNull('inf'), 'Lowercase inf is not INF');
        self::assertInfinite(Unicode::toFloatOrNull('INF'), 'upper case INF');
        self::assertInfinite(Unicode::toFloatOrNull('Infinity'), 'INF from Javascript');
        self::assertNull(Unicode::toFloatOrNull(''), 'empty');
        self::assertNull(Unicode::toFloatOrNull('a1'), 'invalid string');
        self::assertNull(Unicode::toFloatOrNull('01.1'), 'zero start');
        self::assertNull(Unicode::toFloatOrNull('.1'), 'dot start');
        self::assertNull(Unicode::toFloatOrNull('1.' . str_repeat('1', 100)), 'overflow');
    }

    public function test_toInt(): void
    {
        self::assertSame(123, Unicode::toIntOrNull('123'));
    }

    public function test_toInt_blank(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"" is not a valid integer.');
        Unicode::toInt('');
    }

    public function test_toInt_float(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"1.0" is not a valid integer.');
        Unicode::toInt('1.0');
    }

    public function test_toInt_with_e_notation(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"1.23E+3" is not a valid integer.');
        Unicode::toInt('1.23E+3');
    }

    public function test_toInt_float_with_e_notation(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"1.0e-2" is not a valid integer.');
        Unicode::toInt('1.0e-2');
    }

    public function test_toInt_zero_start(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"01" is not a valid integer.');
        Unicode::toInt('01');
    }

    public function test_toInt_not_compatible(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"a1" is not a valid integer.');
        Unicode::toInt('a1');
    }

    public function test_toInt_positive_overflow(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"11111111111111111111" is not a valid integer.');
        Unicode::toInt(str_repeat('1', 20));
    }

    public function test_toInt_negative_overflow(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"-11111111111111111111" is not a valid integer.');
        Unicode::toInt('-' . str_repeat('1', 20));
    }

    public function test_toIntOrNull(): void
    {
        self::assertSame(123, Unicode::toIntOrNull('123'));
        self::assertNull(Unicode::toIntOrNull(str_repeat('1', 20)), 'overflow positive');
        self::assertNull(Unicode::toIntOrNull('-' . str_repeat('1', 20)), 'overflow positive');
        self::assertNull(Unicode::toIntOrNull(''), 'blank');
        self::assertNull(Unicode::toIntOrNull('1.0'), 'float value');
        self::assertNull(Unicode::toIntOrNull('1.0e-2'), 'float value with e notation');
        self::assertNull(Unicode::toIntOrNull('a1'), 'invalid string');
        self::assertNull(Unicode::toIntOrNull('01'), 'zero start');
    }

    public function test_toLowerCase(): void
    {
        // empty (nothing happens)
        self::assertSame('', Unicode::toLowerCase(''));

        // basic
        self::assertSame('abc', Unicode::toLowerCase('ABC'));

        // utf-8 chars (nothing happens)
        self::assertSame('あいう', Unicode::toLowerCase('あいう'));

        // utf-8 special chars
        self::assertSame('çği̇öşü', Unicode::toLowerCase('ÇĞİÖŞÜ'));

        // grapheme (nothing happens)
        self::assertSame('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿', Unicode::toLowerCase('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
    }

    public function test_toPascalCase(): void
    {
        self::assertSame('A', Unicode::toPascalCase('a'));
        self::assertSame('TestMe', Unicode::toPascalCase('test_me'));
        self::assertSame('TestMe', Unicode::toPascalCase('test-me'));
        self::assertSame('TestMe', Unicode::toPascalCase('test me'));
        self::assertSame('TestMe', Unicode::toPascalCase('testMe'));
        self::assertSame('TestMe', Unicode::toPascalCase('TestMe'));
        self::assertSame('TestMe', Unicode::toPascalCase(' test_me '));
        self::assertSame('TestMeNow!', Unicode::toPascalCase('test_me now-!'));
    }

    public function test_toSnakeCase(): void
    {
        // empty
        self::assertSame('', Unicode::toSnakeCase(''));

        // no-change
        self::assertSame('abc', Unicode::toSnakeCase('abc'));

        // case
        self::assertSame('the_test_for_case', Unicode::toSnakeCase('the test for case'));
        self::assertSame('the_test_for_case', Unicode::toSnakeCase('the-test-for-case'));
        self::assertSame('the_test_for_case', Unicode::toSnakeCase('theTestForCase'));
        self::assertSame('ttt', Unicode::toSnakeCase('TTT'));
        self::assertSame('tt_t', Unicode::toSnakeCase('TtT'));
        self::assertSame('tt_t', Unicode::toSnakeCase('TtT'));
        self::assertSame('the__test', Unicode::toSnakeCase('the  test'));
        self::assertSame('__test', Unicode::toSnakeCase('  test'));
        self::assertSame("test\nabc", Unicode::toSnakeCase("test\nabc"));
        self::assertSame('__test_test_test__', Unicode::toSnakeCase("--test_test-test__"));
    }

    public function test_toUpperCase(): void
    {
        // empty (nothing happens)
        self::assertSame('', Unicode::toUpperCase(''));

        // basic
        self::assertSame('ABC', Unicode::toUpperCase('abc'));

        // utf-8 chars (nothing happens)
        self::assertSame('あいう', Unicode::toUpperCase('あいう'));

        // utf-8 special chars
        self::assertSame('ÇĞİÖŞÜ', Unicode::toUpperCase('çği̇öşü'));

        // grapheme (nothing happens)
        self::assertSame('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿', Unicode::toUpperCase('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
    }

    public function test_trim(): void
    {
        // empty (nothing happens)
        self::assertSame('', Unicode::trim(''));

        // left only
        self::assertSame('a', Unicode::trim("\ta"));

        // right only
        self::assertSame('a', Unicode::trim("a\t"));

        // new line on both ends
        self::assertSame('abc', Unicode::trim("\nabc\n"));

        // tab and mixed line on both ends
        self::assertSame('abc', Unicode::trim("\t\nabc\n\t"));

        // tab and mixed line on both ends
        self::assertSame('abc', Unicode::trim("\t\nabc\n\t"));

        // multibyte spaces (https://3v4l.org/s16FF)
        self::assertSame('abc', Unicode::trim("\u{2000}\u{2001}abc\u{2002}\u{2003}"));

        // grapheme (nothing happens)
        self::assertSame('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿', Unicode::trim('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿'));

        // custom
        self::assertSame('b', Unicode::trim('aba', 'a'));

        // custom empty
        self::assertSame('a', Unicode::trim('a', ''));

        // custom overrides delimiter
        self::assertSame("\nb\n", Unicode::trim("a\nb\na", 'a'));

        // custom multiple
        self::assertSame('b', Unicode::trim("_ab_a_", 'a_'));
    }

    public function test_trimEnd(): void
    {
        // empty (nothing happens)
        self::assertSame('', Unicode::trimEnd(''));

        // left only
        self::assertSame("\ta", Unicode::trimEnd("\ta"));

        // right only
        self::assertSame('a', Unicode::trimEnd("a\t"));

        // new line on both ends
        self::assertSame("\nabc", Unicode::trimEnd("\nabc\n"));

        // tab and mixed line on both ends
        self::assertSame('abc', Unicode::trimEnd("abc\n\t"));

        // multibyte spaces (https://3v4l.org/s16FF)
        self::assertSame(' abc', Unicode::trimEnd(" abc\n\t\u{0009}\u{2028}\u{2029}\v "));

        // grapheme (nothing happens)
        self::assertSame('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿', Unicode::trimEnd('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿'));

        // custom
        self::assertSame('ab', Unicode::trimEnd('aba', 'a'));

        // custom empty
        self::assertSame('a', Unicode::trimEnd('a', ''));

        // custom overrides delimiter
        self::assertSame("ab\n", Unicode::trimEnd("ab\na", 'a'));

        // custom multiple
        self::assertSame('_ab', Unicode::trimEnd("_ab_a_", 'a_'));
    }

    public function test_trimStart(): void
    {
        // empty (nothing happens)
        self::assertSame('', Unicode::trimStart(''));

        // left only
        self::assertSame("a", Unicode::trimStart("\ta"));

        // right only
        self::assertSame("a\t", Unicode::trimStart("a\t"));

        // new line on both ends
        self::assertSame("abc\n", Unicode::trimStart("\nabc\n"));

        // tab and new line
        self::assertSame('abc', Unicode::trimStart("\n\tabc"));

        // multibyte spaces (https://3v4l.org/s16FF)
        self::assertSame('abc ', Unicode::trimStart("\n\t\u{0009}\u{2028}\u{2029}\v abc "));

        // grapheme (nothing happens)
        self::assertSame('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿', Unicode::trimStart('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿'));

        // custom
        self::assertSame('ba', Unicode::trimStart('aba', 'a'));

        // custom empty
        self::assertSame('a', Unicode::trimStart('a', ''));

        // custom overrides delimiter
        self::assertSame("\nba", Unicode::trimStart("a\nba", 'a'));

        // custom multiple
        self::assertSame('b_a_', Unicode::trimStart("_ab_a_", 'a_'));
    }

    public function test_withPrefix(): void
    {
        // empty string always adds
        self::assertSame('foo', Unicode::withPrefix('', 'foo'));

        // empty start does nothing
        self::assertSame('foo', Unicode::withPrefix('foo', ''));

        // has match
        self::assertSame('foo', Unicode::withPrefix('foo', 'f'));

        // no match
        self::assertSame('_foo', Unicode::withPrefix('foo', '_'));

        // partial matching doesn't count
        self::assertSame('___foo', Unicode::withPrefix('_foo', '__'));

        // repeats handled properly
        self::assertSame('__foo', Unicode::withPrefix('__foo', '_'));

        // try escape chars
        self::assertSame('\s foo', Unicode::withPrefix(' foo', "\s"));

        // new line
        self::assertSame("\n foo", Unicode::withPrefix(' foo', "\n"));

        // slashes
        self::assertSame('/foo', Unicode::withPrefix('foo', '/'));

        // utf8 match
        self::assertSame('あい', Unicode::withPrefix('あい', 'あ'));

        // utf8 no match
        self::assertSame('うえあい', Unicode::withPrefix('あい', 'うえ'));

        // grapheme (treats combined grapheme as 1 whole character)
        self::assertSame('👨👨‍👨‍👧‍👧', Unicode::withPrefix('👨‍👨‍👧‍👧', '👨'));
    }

    public function test_withSuffix(): void
    {
        // empty string always adds
        self::assertSame('foo', Unicode::withSuffix('', 'foo'));

        // empty start does nothing
        self::assertSame('foo', Unicode::withSuffix('foo', ''));

        // has match
        self::assertSame('foo', Unicode::withSuffix('foo', 'oo'));

        // no match
        self::assertSame('foo bar', Unicode::withSuffix('foo', ' bar'));

        // partial matching doesn't count
        self::assertSame('foo___', Unicode::withSuffix('foo_', '__'));

        // repeats handled properly
        self::assertSame('foo__', Unicode::withSuffix('foo__', '_'));

        // try escape chars
        self::assertSame('foo \s', Unicode::withSuffix('foo ', "\s"));

        // new line
        self::assertSame("foo \n", Unicode::withSuffix('foo ', "\n"));

        // slashes
        self::assertSame('foo/', Unicode::withSuffix('foo', '/'));

        // utf8 match
        self::assertSame('あい', Unicode::withSuffix('あい', 'い'));

        // utf8 no match
        self::assertSame('あいうえ', Unicode::withSuffix('あい', 'うえ'));

        // grapheme (treats combined grapheme as 1 whole character)
        self::assertSame('👨‍👨‍👧‍👧‍👧‍', Unicode::withSuffix('👨‍👨‍👧‍👧‍', '👧‍'));
    }

    public function test_wrap(): void
    {
        // blanks
        self::assertSame('', Unicode::wrap('', '', ''));

        // simple case
        self::assertSame('[a]', Unicode::wrap('a', '[', ']'));

        // multibyte
        self::assertSame('１a２', Unicode::wrap('a', '１', '２'));

        // grapheme
        self::assertSame('👨‍👨‍👧‍a🏴󠁧󠁢󠁳󠁣󠁴󠁿', Unicode::wrap('a', '👨‍👨‍👧‍', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
    }
}
