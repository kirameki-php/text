<?php declare(strict_types=1);

namespace Tests\Kirameki\Text;

use Error;
use Kirameki\Core\Testing\TestCase;
use Kirameki\Text\Str;
use RuntimeException;
use Webmozart\Assert\InvalidArgumentException;
use function str_repeat;
use function substr;

class StrTest extends TestCase
{
    public function test_afterFirst(): void
    {
        // match first
        self::assertSame('est', Str::afterFirst('test', 't'));

        // match last
        self::assertSame('', Str::afterFirst('test1', '1'));

        // match empty string
        self::assertSame('test', Str::afterFirst('test', ''));

        // no match
        self::assertSame('test', Str::afterFirst('test', 'test2'));

        // multi byte
        self::assertSame('うえ', Str::afterFirst('ああいうえ', 'い'));

        // grapheme
        self::assertSame('def', Str::afterFirst('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿def', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));

        // grapheme cluster
        self::assertSame('👋🏿', Str::afterFirst('👋🏿', '👋'));
    }

    public function test_afterLast(): void
    {
        // match first (single occurrence)
        self::assertSame('bc', Str::afterLast('abc', 'a'));

        // match first (multiple occurrence)
        self::assertSame('1', Str::afterLast('test1', 't'));

        // match last
        self::assertSame('', Str::afterLast('test1', '1'));

        // should match the last string
        self::assertSame('Foo', Str::afterLast('----Foo', '---'));

        // match empty string
        self::assertSame('test', Str::afterLast('test', ''));

        // no match
        self::assertSame('test', Str::afterLast('test', 'a'));

        // multi byte
        self::assertSame('え', Str::afterLast('ああいういえ', 'い'));

        // grapheme
        self::assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿f', Str::afterLast('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', 'e'));

        // grapheme cluster
        self::assertSame('👋🏿', Str::afterLast('👋🏿', '👋'));
    }

    public function test_beforeFirst(): void
    {
        // match first (single occurrence)
        self::assertSame('a', Str::beforeFirst('abc', 'b'));

        // match first (multiple occurrence)
        self::assertSame('a', Str::beforeFirst('abc-abc', 'b'));

        // match last
        self::assertSame('test', Str::beforeFirst('test1', '1'));

        // match multiple chars
        self::assertSame('test', Str::beforeFirst('test123', '12'));

        // match empty string
        self::assertSame('test', Str::beforeFirst('test', ''));

        // no match
        self::assertSame('test', Str::beforeFirst('test', 'a'));

        // multi byte
        self::assertSame('ああ', Str::beforeFirst('ああいういえ', 'い'));

        // grapheme
        self::assertSame('abc', Str::beforeFirst('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
        self::assertSame('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿', Str::beforeFirst('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', 'e'));

        // grapheme cluster
        self::assertSame('👋🏿', Str::beforeFirst('👋🏿', '🏿'));
    }

    public function test_beforeLast(): void
    {
        // match first (single occurrence)
        self::assertSame('a', Str::beforeLast('abc', 'b'));

        // match first (multiple occurrence)
        self::assertSame('abc-a', Str::beforeLast('abc-abc', 'b'));

        // match last
        self::assertSame('test', Str::beforeLast('test1', '1'));

        // match empty string
        self::assertSame('test', Str::beforeLast('test', ''));

        // no match
        self::assertSame('test', Str::beforeLast('test', 'a'));

        // multi byte
        self::assertSame('ああいう', Str::beforeLast('ああいういえ', 'い'));

        // grapheme
        self::assertSame('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e', Str::beforeLast('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));

        // grapheme cluster
        self::assertSame('👋🏿', Str::beforeLast('👋🏿', '🏿'));
    }

    public function test_between(): void
    {
        // basic
        self::assertSame('1', Str::between('test(1)', '(', ')'));

        // edge
        self::assertSame('', Str::between('()', '(', ')'));
        self::assertSame('1', Str::between('(1)', '(', ')'));

        // nested
        self::assertSame('test(1)', Str::between('(test(1))', '(', ')'));
        self::assertSame('1) to (2', Str::between('(1) to (2)', '(', ')'));

        // multichar
        self::assertSame('_', Str::between('ab_ba', 'ab', 'ba'));

        // utf8
        self::assertSame('い', Str::between('あいう', 'あ', 'う'));

        // grapheme
        self::assertSame('😃', Str::between('👋🏿😃👋🏿😃', '👋🏿', '👋🏿'));
    }

    public function test_between_empty_from(): void
    {
        $this->expectExceptionMessage('Expected a non-empty value. Got: ""');
        Str::between('test)', '', ')');
    }

    public function test_between_empty_to(): void
    {
        $this->expectExceptionMessage('Expected a non-empty value. Got: ""');
        Str::between('test)', '(', '');
    }

    public function test_between_empty_from_and_to(): void
    {
        $this->expectExceptionMessage('Expected a non-empty value. Got: ""');
        Str::between('test)', '', '');
    }

    public function test_between_missing_from(): void
    {
        $this->expectExceptionMessage('$from: "(" does not exist in "test)"');
        Str::between('test)', '(', ')');
    }

    public function test_between_missing_to(): void
    {
        $this->expectExceptionMessage('$to: ")" does not exist after $from in "test("');
        Str::between('test(', '(', ')');
    }

    public function test_between_grapheme_substring(): void
    {
        $this->expectExceptionMessage('$from: "👋" does not exist in "👋🏿"');
        Str::between('👋🏿', '👋', '🏿');
    }

    public function test_betweenFirst(): void
    {
        // basic
        self::assertSame('1', Str::betweenFirst('test(1)', '(', ')'));

        // edge
        self::assertSame('', Str::betweenFirst('()', '(', ')'));
        self::assertSame('1', Str::betweenFirst('(1)', '(', ')'));

        // nested
        self::assertSame('test(1', Str::betweenFirst('(test(1))', '(', ')'));
        self::assertSame('1', Str::betweenFirst('(1) to (2)', '(', ')'));

        // multichar
        self::assertSame('_ab_', Str::betweenFirst('ab_ab_ba_ba', 'ab', 'ba'));

        // utf8
        self::assertSame('い', Str::betweenFirst('あいういう', 'あ', 'う'));

        // grapheme
        self::assertSame('😃', Str::betweenFirst('👋🏿😃👋🏿😃👋🏿', '👋🏿', '👋🏿'));
    }

    public function test_betweenFirst_empty_from(): void
    {
        $this->expectExceptionMessage('Expected a non-empty value. Got: ""');
        Str::between('test)', '', ')');
    }

    public function test_betweenFirst_empty_to(): void
    {
        $this->expectExceptionMessage('Expected a non-empty value. Got: ""');
        Str::between('test)', '(', '');
    }

    public function test_betweenFirst_empty_from_and_to(): void
    {
        $this->expectExceptionMessage('Expected a non-empty value. Got: ""');
        Str::between('test)', '', '');
    }

    public function test_betweenFirst_missing_from(): void
    {
        $this->expectExceptionMessage('$from: "(" does not exist in "test)"');
        Str::between('test)', '(', ')');
    }

    public function test_betweenFirst_missing_to(): void
    {
        $this->expectExceptionMessage('$to: ")" does not exist after $from in "test("');
        Str::between('test(', '(', ')');
    }

    public function test_betweenFirst_grapheme_substring(): void
    {
        $this->expectExceptionMessage('$from: "👋" does not exist in "👋🏿"');
        Str::between('👋🏿', '👋', '🏿');
    }

    public function test_betweenLast(): void
    {
        // basic
        self::assertSame('1', Str::betweenLast('test(1)', '(', ')'));

        // edge
        self::assertSame('', Str::betweenLast('()', '(', ')'));
        self::assertSame('1', Str::betweenLast('(1)', '(', ')'));

        // nested
        self::assertSame('1)', Str::betweenLast('(test(1))', '(', ')'));
        self::assertSame('2', Str::betweenLast('(1) to (2)', '(', ')'));

        // multichar
        self::assertSame('_ba_', Str::betweenLast('ab_ab_ba_ba', 'ab', 'ba'));

        // utf8
        self::assertSame('いうい', Str::betweenLast('あいういう', 'あ', 'う'));

        // grapheme
        self::assertSame('🥹', Str::betweenLast('👋🏿😃👋🏿🥹👋', '👋🏿', '👋'));
    }

    public function test_betweenLast_empty_from(): void
    {
        $this->expectExceptionMessage('Expected a non-empty value. Got: ""');
        Str::between('test)', '', ')');
    }

    public function test_betweenLast_empty_to(): void
    {
        $this->expectExceptionMessage('Expected a non-empty value. Got: ""');
        Str::between('test)', '(', '');
    }

    public function test_betweenLast_empty_from_and_to(): void
    {
        $this->expectExceptionMessage('Expected a non-empty value. Got: ""');
        Str::between('test)', '', '');
    }

    public function test_betweenLast_missing_from(): void
    {
        $this->expectExceptionMessage('$from: "(" does not exist in "test)"');
        Str::between('test)', '(', ')');
    }

    public function test_betweenLast_missing_to(): void
    {
        $this->expectExceptionMessage('$to: ")" does not exist after $from in "test("');
        Str::between('test(', '(', ')');
    }

    public function test_betweenLast_grapheme_substring(): void
    {
        $this->expectExceptionMessage('$from: "👋" does not exist in "👋🏿"');
        Str::between('👋🏿', '👋', '🏿');
    }

    public function test_byteLength(): void
    {
        // empty
        self::assertSame(0, Str::byteLength(''));

        // ascii
        self::assertSame(1, Str::byteLength('a'));

        // utf8
        self::assertSame(3, Str::byteLength('あ'));

        // emoji
        self::assertSame(25, Str::byteLength('👨‍👨‍👧‍👦'));
    }

    public function test_capitalize(): void
    {
        // empty
        self::assertSame('', Str::capitalize(''));

        // only the first character is changed
        self::assertSame('TT', Str::capitalize('TT'));

        self::assertSame('Test', Str::capitalize('test'));
        self::assertSame('Test abc', Str::capitalize('test abc'));
        self::assertSame(' test abc', Str::capitalize(' test abc'));
        self::assertSame('Àbc', Str::capitalize('àbc'));
        self::assertSame('É', Str::capitalize('é'));
        self::assertSame('ゅ', Str::capitalize('ゅ'));
        self::assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿', Str::capitalize('🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
    }

    public function test_chunk(): void
    {
        self::assertSame([], Str::chunk('', 5), 'empty');
        self::assertSame(['ab'], Str::chunk('ab', 5), 'oversize');
        self::assertSame(['ab'], Str::chunk('ab', 2), 'exact');
        self::assertSame(['ab', 'c'], Str::chunk('abc', 2), 'fragment');
        self::assertSame(['あい', 'う'], Str::chunk('あいう', 2), 'utf8');
        self::assertSame(['👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦'], Str::chunk('👨‍👨‍👧‍👦👨‍👨‍👧‍👦', 1), 'emoji');
    }

    public function test_concat(): void
    {
        self::assertSame('', Str::concat());
        self::assertSame('test', Str::concat('test'));
        self::assertSame('testa ', Str::concat('test', 'a', '', ' '));
        self::assertSame('ゅゅ', Str::concat('ゅ', 'ゅ'));
        self::assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿🐌', Str::concat('🏴󠁧󠁢󠁳󠁣󠁴󠁿', '🐌'));
    }

    public function test_contains(): void
    {
        self::assertTrue(Str::contains('abcde', 'ab'));
        self::assertFalse(Str::contains('abcde', 'ac'));
        self::assertTrue(Str::contains('abcde', ''));
        self::assertTrue(Str::contains('', ''));
        self::assertFalse(Str::contains('👨‍👨‍👧‍👧‍', '👨'));
    }

    public function test_containsAll(): void
    {
        self::assertTrue(Str::containsAll('', []), 'empty substrings with blank');
        self::assertTrue(Str::containsAll('abc', []), 'empty substrings');
        self::assertTrue(Str::containsAll('', ['']), 'blank match blank');
        self::assertTrue(Str::containsAll('abcde', ['']), 'blank match string');
        self::assertFalse(Str::containsAll('abcde', ['a', 'z']), 'partial match first');
        self::assertFalse(Str::containsAll('abcde', ['z', 'a']), 'partial match last');
        self::assertTrue(Str::containsAll('abcde', ['a']), 'match single');
        self::assertFalse(Str::containsAll('abcde', ['z']), 'no match single');
        self::assertTrue(Str::containsAll('abcde', ['a', 'b']), 'match all first');
        self::assertTrue(Str::containsAll('abcde', ['c', 'b']), 'match all reversed');
        self::assertFalse(Str::containsAll('abcde', ['y', 'z']), 'no match all');
    }

    public function test_containsAny(): void
    {
        self::assertTrue(Str::containsAny('', []), 'blank and empty substrings');
        self::assertTrue(Str::containsAny('abcde', []), 'empty substrings');
        self::assertTrue(Str::containsAny('', ['']), 'blank match blank');
        self::assertTrue(Str::containsAny('abcde', ['']), 'blank matchs anything');
        self::assertTrue(Str::containsAny('abcde', ['a', 'z']), 'one match of many (first one matched)');
        self::assertTrue(Str::containsAny('abcde', ['z', 'a']), 'one match of many (last one matched)');
        self::assertTrue(Str::containsAny('abcde', ['a']), 'match single');
        self::assertFalse(Str::containsAny('abcde', ['z']), 'no match single');
        self::assertFalse(Str::containsAny('abcde', ['y', 'z']), 'no match all');
    }

    public function test_containsNone(): void
    {
        self::assertTrue(Str::containsNone('', []), 'blank and empty substrings');
        self::assertTrue(Str::containsNone('abcde', []), 'empty substrings');
        self::assertFalse(Str::containsNone('', ['']), 'blank match blank');
        self::assertFalse(Str::containsNone('abcde', ['']), 'blank matchs anything');
        self::assertFalse(Str::containsNone('abcde', ['a', 'z']), 'one match of many (first one matched)');
        self::assertFalse(Str::containsNone('abcde', ['z', 'a']), 'one match of many (last one matched)');
        self::assertFalse(Str::containsNone('abcde', ['a']), 'match single');
        self::assertTrue(Str::containsNone('abcde', ['z']), 'no match single');
        self::assertTrue(Str::containsNone('abcde', ['y', 'z']), 'no match all');
    }

    public function test_containsPattern(): void
    {
        self::assertTrue(Str::containsPattern('abc', '/b/'));
        self::assertTrue(Str::containsPattern('abc', '/ab/'));
        self::assertTrue(Str::containsPattern('abc', '/abc/'));
        self::assertTrue(Str::containsPattern('ABC', '/abc/i'));
        self::assertTrue(Str::containsPattern('aaaz', '/a{3}/'));
        self::assertTrue(Str::containsPattern('ABC1', '/[A-z\d]+/'));
        self::assertTrue(Str::containsPattern('ABC1]', '/\d]$/'));
        self::assertFalse(Str::containsPattern('AB1C', '/\d]$/'));
    }

    public function test_containsPattern_warning_as_error(): void
    {
        $this->expectExceptionMessage('preg_match(): Unknown modifier \'a\'');
        $this->expectException(Error::class);
        self::assertFalse(Str::containsPattern('', '/a/a'));
    }

    public function test_count(): void
    {
        // empty string
        self::assertSame(0, Str::count('', 'aaa'));

        // exact match
        self::assertSame(1, Str::count('abc', 'abc'));

        // no match
        self::assertSame(0, Str::count('ab', 'abc'));

        // simple
        self::assertSame(1, Str::count('This is a cat', ' is '));
        self::assertSame(2, Str::count('This is a cat', 'is'));

        // overlapping
        self::assertSame(2, Str::count('ababab', 'aba'));

        // utf8
        self::assertSame(2, Str::count('あいあ', 'あ'));

        // utf8 overlapping
        self::assertSame(2, Str::count('あああ', 'ああ'));

        // check half-width is not counted.
        self::assertSame(0, Str::count('ア', 'ｱ'));

        // grapheme
        self::assertSame(1, Str::count('👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦'));

        // grapheme subset should not match
        self::assertSame(0, Str::count('👨‍👨‍👧‍👦', '👨'));

        // grapheme overlapping
        self::assertSame(2, Str::count('👨‍👨‍👧‍👦👨‍👨‍👧‍👦👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦👨‍👨‍👧‍👦'));
    }

    public function test_count_with_empty_search(): void
    {
        $this->expectExceptionMessage('Search string must be non-empty');
        self::assertFalse(Str::count('a', ''));
    }

    public function test_cut(): void
    {
        // empty
        self::assertSame('', Str::cut('', 0));

        // basic
        self::assertSame('a', Str::cut('a', 1));
        self::assertSame('a', Str::cut('abc', 1));

        // utf-8
        self::assertSame('', Str::cut('あいう', 1));
        self::assertSame('あ', Str::cut('あいう', 3));

        // grapheme
        self::assertSame('', Str::cut('👋', 1));
        self::assertSame('', Str::cut('👋🏿', 1));
        self::assertSame('👋🏿', Str::cut('👋🏿', 8));

        // cut and replaced with ellipsis
        self::assertSame('a...', Str::cut('abc', 1, '...'));
        self::assertSame('...', Str::cut('あいう', 1, '...'));
        self::assertSame('あ...', Str::cut('あいう', 3, '...'));

        // cut and replaced with custom ellipsis
        self::assertSame('a$', Str::cut('abc', 1, '$'));
    }

    public function test_decapitalize(): void
    {
        self::assertSame('', Str::decapitalize(''));
        self::assertSame('test', Str::decapitalize('Test'));
        self::assertSame('t T', Str::decapitalize('T T'));
        self::assertSame(' T ', Str::decapitalize(' T '));
        self::assertSame('é', Str::decapitalize('É'));
        self::assertSame('🔡', Str::decapitalize('🔡'));
    }

    public function test_doesNotContain(): void
    {
        self::assertTrue(Str::doesNotContain('abcde', 'ac'));
        self::assertFalse(Str::doesNotContain('abcde', 'ab'));
        self::assertFalse(Str::doesNotContain('a', ''));
        self::assertTrue(Str::doesNotContain('', 'a'));
        self::assertTrue(Str::doesNotContain('👨‍👨‍👧‍👧‍', '👨'));
    }

    public function test_doesNotEndWith(): void
    {
        self::assertFalse(Str::doesNotEndWith('abc', 'c'));
        self::assertTrue(Str::doesNotEndWith('abc', 'b'));
        self::assertFalse(Str::doesNotEndWith('abc', ['c']));
        self::assertFalse(Str::doesNotEndWith('abc', ['a', 'b', 'c']));
        self::assertTrue(Str::doesNotEndWith('abc', ['a', 'b']));
        self::assertFalse(Str::doesNotEndWith('aabbcc', 'cc'));
        self::assertFalse(Str::doesNotEndWith('aabbcc' . PHP_EOL, PHP_EOL));
        self::assertFalse(Str::doesNotEndWith('abc0', '0'));
        self::assertFalse(Str::doesNotEndWith('abcfalse', 'false'));
        self::assertFalse(Str::doesNotEndWith('a', ''));
        self::assertFalse(Str::doesNotEndWith('', ''));
        self::assertFalse(Str::doesNotEndWith('あいう', 'う'));
        self::assertTrue(Str::doesNotEndWith("あ\n", 'あ'));
        self::assertTrue(Str::doesNotEndWith('👋🏻', '🏻'));
    }


    public function test_doesNotStartWith(): void
    {
        self::assertFalse(Str::doesNotStartWith('', ''));
        self::assertFalse(Str::doesNotStartWith('bb', ''));
        self::assertFalse(Str::doesNotStartWith('bb', 'b'));
        self::assertTrue(Str::doesNotStartWith('bb', 'ab'));
        self::assertFalse(Str::doesNotStartWith('あ-い-う', 'あ'));
        self::assertTrue(Str::doesNotStartWith('あ-い-う', 'え'));
        self::assertTrue(Str::doesNotStartWith('👨‍👨‍👧‍👦', '👨‍'));
        self::assertFalse(Str::doesNotStartWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿 👨‍👨‍👧‍👦', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
        self::assertTrue(Str::doesNotStartWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿 👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦'));
        self::assertFalse(Str::doesNotStartWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿', '🏴󠁧󠁢󠁳󠁣󠁴󠁿a'));
        self::assertTrue(Str::doesNotStartWith('ba', 'a'));
        self::assertTrue(Str::doesNotStartWith('', 'a'));
        self::assertTrue(Str::doesNotStartWith('abc', ['d', 'e']));
        self::assertFalse(Str::doesNotStartWith('abc', ['d', 'a']));
        self::assertTrue(Str::doesNotStartWith("\nあ", 'あ'));
    }

    public function test_drop(): void
    {
        // empty
        self::assertSame('', Str::dropFirst('', 1));

        // zero amount
        self::assertSame('a', Str::dropFirst('a', 0));

        // mid amount
        self::assertSame('e', Str::dropFirst('abcde', 4));

        // exact amount
        self::assertSame('', Str::dropFirst('abc', 3));

        // over overflow
        self::assertSame('', Str::dropFirst('abc', 4));

        // grapheme
        self::assertSame('def', Str::dropFirst('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿def', 4));

        // grapheme cluster (positive)
        self::assertSame('', Str::dropFirst('👋🏿', 1));
    }

    public function test_drop_negative_amount(): void
    {
        $this->expectExceptionMessage('Expected a value greater than or equal to 0. Got: -4');
        Str::dropFirst('abc', -4);
    }

    public function test_dropLast(): void
    {
        // empty
        self::assertSame('', Str::dropLast('', 1));

        // zero length
        self::assertSame('a', Str::dropLast('a', 0));

        // mid amount
        self::assertSame('ab', Str::dropLast('abc', 1));

        // exact amount
        self::assertSame('', Str::dropLast('abc', 3));

        // overflow
        self::assertSame('', Str::dropLast('abc', 4));

        // grapheme
        self::assertSame('abc', Str::dropLast('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿def', 4));

        // grapheme cluster (positive)
        self::assertSame('', Str::dropLast('👋🏿', 1));
    }

    public function test_dropLast_negative_amount(): void
    {
        $this->expectExceptionMessage('Expected a value greater than or equal to 0. Got: -4');
        Str::dropLast('abc', -4);
    }

    public function test_endsWith(): void
    {
        self::assertTrue(Str::endsWith('abc', 'c'));
        self::assertFalse(Str::endsWith('abc', 'b'));
        self::assertTrue(Str::endsWith('abc', ['c']));
        self::assertTrue(Str::endsWith('abc', ['a', 'b', 'c']));
        self::assertFalse(Str::endsWith('abc', ['a', 'b']));
        self::assertTrue(Str::endsWith('aabbcc', 'cc'));
        self::assertTrue(Str::endsWith('aabbcc' . PHP_EOL, PHP_EOL));
        self::assertTrue(Str::endsWith('abc0', '0'));
        self::assertTrue(Str::endsWith('abcfalse', 'false'));
        self::assertTrue(Str::endsWith('a', ''));
        self::assertTrue(Str::endsWith('', ''));
        self::assertTrue(Str::endsWith('あいう', 'う'));
        self::assertFalse(Str::endsWith("あ\n", 'あ'));
        self::assertFalse(Str::endsWith('👋🏻', '🏻'));
    }

    public function test_indexOfFirst(): void
    {
        // empty string
        self::assertNull(Str::indexOfFirst('', 'a'));

        // empty search
        self::assertSame(0, Str::indexOfFirst('ab', ''));

        // find at 0
        self::assertSame(0, Str::indexOfFirst('a', 'a'));

        // multiple matches
        self::assertSame(1, Str::indexOfFirst('abb', 'b'));

        // offset (within bound)
        self::assertSame(1, Str::indexOfFirst('abb', 'b', 1));
        self::assertSame(5, Str::indexOfFirst('aaaaaa', 'a', 5));

        // offset (out of bound)
        self::assertNull(Str::indexOfFirst('abb', 'b', 4));

        // offset (negative)
        self::assertSame(2, Str::indexOfFirst('abb', 'b', -1));

        // offset (negative)
        self::assertNull(Str::indexOfFirst('abb', 'b', -100));

        // offset utf-8
        self::assertSame(0, Str::indexOfFirst('👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦'));
        self::assertNull(Str::indexOfFirst('👨‍👨‍👧‍👦', '👨'));
        self::assertSame(1, Str::indexOfFirst('あいう', 'い', 1));
        self::assertSame(1, Str::indexOfFirst('🏴󠁧󠁢󠁳󠁣󠁴󠁿👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦', 1));
        self::assertNull(Str::indexOfFirst('🏴󠁧󠁢󠁳󠁣󠁴󠁿👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦', 2));
    }

    public function test_indexOfLast(): void
    {
        // empty string
        self::assertNull(Str::indexOfLast('', 'a'));

        // empty search
        self::assertSame(2, Str::indexOfLast('ab', ''));

        // find at 0
        self::assertSame(0, Str::indexOfLast('a', 'a'));

        // multiple matches
        self::assertSame(2, Str::indexOfLast('abb', 'b'));

        // offset (within bound)
        self::assertSame(2, Str::indexOfLast('abb', 'b', 1));
        self::assertSame(5, Str::indexOfLast('aaaaaa', 'a', 5));

        // offset (out of bound)
        self::assertNull(Str::indexOfLast('abb', 'b', 4));

        // offset (negative)
        self::assertSame(3, Str::indexOfLast('abbb', 'b', -1));

        // offset (negative)
        self::assertNull(Str::indexOfLast('abb', 'b', -100));

        // offset utf-8
        self::assertSame(0, Str::indexOfLast('👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦'));
        self::assertNull(Str::indexOfLast('👨‍👨‍👧‍👦', '👨'));
        self::assertSame(1, Str::indexOfLast('あいう', 'い', 1));
        self::assertSame(1, Str::indexOfLast('🏴󠁧󠁢󠁳󠁣󠁴󠁿👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦', 1));
        self::assertNull(Str::indexOfLast('🏴󠁧󠁢󠁳󠁣󠁴󠁿👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦', 2));
    }

    public function test_insert(): void
    {
        self::assertSame('xyzabc', Str::insert('abc', 'xyz', 0));
        self::assertSame('axyzbc', Str::insert('abc', 'xyz', 1));
        self::assertSame('xyzabc', Str::insert('abc', 'xyz', -1));
        self::assertSame('abcxyz', Str::insert('abc', 'xyz', 3));
        self::assertSame('あxyzい', Str::insert('あい', 'xyz', 1));
        self::assertSame('xyzあい', Str::insert('あい', 'xyz', -1));
    }

    public function test_isBlank(): void
    {
        self::assertTrue(Str::isBlank(''));
        self::assertFalse(Str::isBlank('0'));
        self::assertFalse(Str::isBlank(' '));
    }

    public function test_isNotBlank(): void
    {
        self::assertFalse(Str::isNotBlank(''));
        self::assertTrue(Str::isNotBlank('0'));
        self::assertTrue(Str::isNotBlank(' '));
    }

    public function test_kebabCase(): void
    {
        self::assertSame('test', Str::toKebabCase('test'));
        self::assertSame('test', Str::toKebabCase('Test'));
        self::assertSame('ttt', Str::toKebabCase('TTT'));
        self::assertSame('tt-test', Str::toKebabCase('TTTest'));
        self::assertSame('test-test', Str::toKebabCase('testTest'));
        self::assertSame('test-t-test', Str::toKebabCase('testTTest'));
        self::assertSame('test-test', Str::toKebabCase('test-test'));
        self::assertSame('test-test', Str::toKebabCase('test_test'));
        self::assertSame('test-test', Str::toKebabCase('test test'));
        self::assertSame('test-test-test', Str::toKebabCase('test test test'));
        self::assertSame('-test--test--', Str::toKebabCase(' test  test  '));
        self::assertSame('--test-test-test--', Str::toKebabCase("--test_test-test__"));
    }

    public function test_length(): void
    {
        // empty
        self::assertSame(0, Str::length(''));

        // ascii
        self::assertSame(4, Str::length('Test'));
        self::assertSame(9, Str::length(' T e s t '));

        // utf8
        self::assertSame(2, Str::length('あい'));
        self::assertSame(4, Str::length('あいzう'));

        // emoji
        self::assertSame(1, Str::length('👨‍👨‍👧‍👦'));
    }

    public function test_length_invalid_string(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Error converting input string to UTF-16: U_INVALID_CHAR_FOUND');
        Str::length(substr('あ', 1));
    }

    public function test_matchAll(): void
    {
        self::assertSame([['a', 'a']], Str::matchAll('abcabc', '/a/'));
        self::assertSame([['abc', 'abc'], 'p1' => ['a', 'a'], ['a', 'a']], Str::matchAll('abcabc', '/(?<p1>a)bc/'));
        self::assertSame([[]], Str::matchAll('abcabc', '/bcd/'));
        self::assertSame([['cd', 'c']], Str::matchAll('abcdxabc', '/c[^x]*/'));
        self::assertSame([[]], Str::matchAll('abcabcx', '/^abcx/'));
        self::assertSame([['cx']], Str::matchAll('abcabcx', '/cx$/'));
    }

    public function test_matchAll_without_slashes(): void
    {
        $this->expectWarning();
        $this->expectWarningMessage('preg_match_all(): Delimiter must not be alphanumeric, backslash, or NUL');
        Str::matchAll('abcabc', 'a');
    }

    public function test_matchFirst(): void
    {
        self::assertSame('a', Str::matchFirst('abcabc', '/a/'));
        self::assertSame('abc', Str::matchFirst('abcabc', '/(?<p1>a)bc/'));
        self::assertSame('cd', Str::matchFirst('abcdxabc', '/c[^x]*/'));
        self::assertSame('cx', Str::matchFirst('abcabcx', '/cx$/'));
    }

    public function test_matchFirst_no_match(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"aaa" does not match /z/');
        Str::matchFirst('aaa', '/z/');
    }

    public function test_matchFirst_without_slashes(): void
    {
        $this->expectWarning();
        $this->expectWarningMessage('preg_match(): Delimiter must not be alphanumeric, backslash, or NUL');
        Str::matchFirst('abcabc', 'a');
    }

    public function test_matchFirstOrNull(): void
    {
        self::assertSame('a', Str::matchFirstOrNull('abcabc', '/a/'));
        self::assertSame('abc', Str::matchFirstOrNull('abcabc', '/(?<p1>a)bc/'));
        self::assertSame(null, Str::matchFirstOrNull('abcabc', '/bcd/'));
        self::assertSame('cd', Str::matchFirstOrNull('abcdxabc', '/c[^x]*/'));
        self::assertSame(null, Str::matchFirstOrNull('abcabcx', '/^abcx/'));
        self::assertSame('cx', Str::matchFirstOrNull('abcabcx', '/cx$/'));
    }

    public function test_matchFirstOrNull_without_slashes(): void
    {
        $this->expectWarning();
        $this->expectWarningMessage('preg_match(): Delimiter must not be alphanumeric, backslash, or NUL');
        Str::matchFirstOrNull('abcabc', 'a');
    }

    public function test_pad(): void
    {
        // empty string
        self::assertSame('', Str::pad('', -1, '_'));

        // pad string
        self::assertSame('abc', Str::pad('abc', 3, ''));

        // defaults to pad right
        self::assertSame('a', Str::pad('a', -1, '_'));
        self::assertSame('a', Str::pad('a', 0, '_'));
        self::assertSame('a_', Str::pad('a', 2, '_'));
        self::assertSame('__', Str::pad('_', 2, '_'));
        self::assertSame('ab', Str::pad('ab', 1, '_'));

        // overflow padding
        self::assertSame('abcd', Str::pad('a', 4, 'bcde'));
    }

    public function test_pad_invalid_pad(): void
    {
        $this->expectExceptionMessage('Invalid padding type: 3');
        self::assertSame('ab', Str::pad('ab', 1, '_', 3));
    }

    public function test_padBoth(): void
    {
        self::assertSame('a', Str::padBoth('a', -1, '_'));
        self::assertSame('a', Str::padBoth('a', 0, '_'));
        self::assertSame('a_', Str::padBoth('a', 2, '_'));
        self::assertSame('__', Str::padBoth('_', 2, '_'));
        self::assertSame('_a_', Str::padBoth('a', 3, '_'));
        self::assertSame('__a__', Str::padBoth('a', 5, '_'));
        self::assertSame('__a___', Str::padBoth('a', 6, '_'));
        self::assertSame('12hello123', Str::padBoth('hello', 10, '123'));
        self::assertSame('いあい', Str::padBoth('あ', 3, 'い'));
    }

    public function test_padEnd(): void
    {
        self::assertSame('a', Str::padEnd('a', -1, '_'));
        self::assertSame('a', Str::padEnd('a', 0, '_'));
        self::assertSame('a_', Str::padEnd('a', 2, '_'));
        self::assertSame('__', Str::padEnd('_', 2, '_'));
        self::assertSame('ab', Str::padEnd('ab', 1, '_'));
        self::assertSame('あいういう', Str::padEnd('あ', 5, 'いう'), 'multi byte');
        self::assertSame('עִברִיתכן', Str::padEnd('עִברִית', 7, 'כן'), 'rtol languages');
    }

    public function test_padStart(): void
    {
        self::assertSame('a', Str::padStart('a', -1, '_'));
        self::assertSame('a', Str::padStart('a', 0, '_'));
        self::assertSame('_a', Str::padStart('a', 2, '_'));
        self::assertSame('__', Str::padStart('_', 2, '_'));
        self::assertSame('ab', Str::padStart('ab', 1, '_'));
        self::assertSame('いういうあ', Str::padStart('あ', 5, 'いう'), 'multi byte');
    }

    public function test_remove(): void
    {
        self::assertSame('', Str::remove('', ''), 'empty');
        self::assertSame('', Str::remove('aaa', 'a'), 'delete everything');
        self::assertSame('a  a', Str::remove('aaa aa a', 'aa'), 'no traceback check');
        self::assertSame('no match', Str::remove('no match', 'hctam on'), 'out of order chars');
        self::assertSame('aa', Str::remove('aa', 'a', 0), 'limit to 0');
        self::assertSame('a', Str::remove('aaa', 'a', 2), 'limit to 2');

        $count = 0;
        self::assertSame('aaa', Str::remove('aaa', 'a', 0, $count), 'count none');
        self::assertSame(0, $count);

        self::assertSame('a', Str::remove('aaa', 'a', 2, $count), 'count several');
        self::assertSame(2, $count);

        self::assertSame('', Str::remove('aaa', 'a', null, $count), 'count unlimited');
        self::assertSame(3, $count);
    }

    public function test_remove_with_negative_limit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a value greater than or equal to 0. Got: -1');
        Str::remove('', '', -1);
    }

    public function test_repeat(): void
    {
        self::assertSame('aaa', Str::repeat('a', 3));
        self::assertSame('', Str::repeat('a', 0));
    }

    public function test_repeat_negative_times(): void
    {
        $this->expectError();
        $this->expectErrorMessage('str_repeat(): Argument #2 ($times) must be greater than or equal to 0');
        /** @noinspection PhpExpressionResultUnusedInspection */
        Str::repeat('a', -1);
    }

    public function test_replace(): void
    {
        self::assertSame('', Str::replace('', '', ''));
        self::assertSame('b', Str::replace('b', '', 'a'));
        self::assertSame('aa', Str::replace('bb', 'b', 'a'));
        self::assertSame('', Str::replace('b', 'b', ''));
        self::assertSame('あえいえう', Str::replace('あ-い-う', '-', 'え'));
        self::assertSame('__🏴󠁧󠁢󠁳󠁣󠁴󠁿', Str::replace('🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿', '🏴󠁧󠁢󠁳󠁣󠁴󠁿a', '_'));

        // slash
        self::assertSame('abc', Str::replace('ab\c', '\\', ''));

        // dot
        self::assertSame('abc', Str::replace('abc.*', '.*', ''));

        // regex chars
        self::assertSame('a', Str::replace('[]/\\!?', '[]/\\!?', 'a'));

        // with limit and count
        $count = 0;
        self::assertSame('a', Str::replace('aaa', 'a', '', 2, $count));
        self::assertSame(2, $count);

        // 0 count for no match
        $count = 0;
        self::assertSame('', Str::replace('', '', '', null, $count));
        self::assertSame(0, $count);

        // should treat emoji cluster as one character
        self::assertSame('👋🏿', Str::replace('👋🏿', '👋', ''));
    }

    public function test_replace_with_negative_limit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a value greater than or equal to 0. Got: -1');
        Str::replace('', 'a', 'a', -1);
    }

    public function test_replaceEach(): void
    {
        // empty string
        self::assertSame('', Str::replaceEach('', ['?'], ['!']));

        // empty search string
        self::assertSame('test', Str::replaceEach('test', [''], ['a']));

        // replace each ?
        self::assertSame('x & y', Str::replaceEach('? & ?', ['?', '?'], ['x', 'y']));

        // utf-8
        self::assertSame('うえ', Str::replaceEach('あい', ['あ', 'い'], ['う', 'え']));

        // should treat emoji cluster as one character
        self::assertSame('👋🏿', Str::replaceEach('👋🏿', ['👋'], ['']));
    }

    public function test_replaceFirst(): void
    {
        self::assertSame('', Str::replaceFirst('', '', ''), 'empty string');
        self::assertSame('bb', Str::replaceFirst('bb', '', 'a'), 'empty search');
        self::assertSame('abb', Str::replaceFirst('bbb', 'b', 'a'), 'basic');
        self::assertSame('b', Str::replaceFirst('bb', 'b', ''), 'empty replacement');
        self::assertSame('あえい-う', Str::replaceFirst('あ-い-う', '-', 'え'), 'mbstring');
        self::assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿 a', Str::replaceFirst('🏴󠁧󠁢󠁳󠁣󠁴󠁿 👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦', 'a'), 'multiple codepoints');
        self::assertSame('_🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿', Str::replaceFirst('🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿', '🏴󠁧󠁢󠁳󠁣󠁴󠁿a', '_'));
        self::assertSame('👋🏿', Str::replaceFirst('👋🏿', '👋', ''), 'treat emoji cluster as one character');

        $replaced = false;
        Str::replaceFirst('bbb', 'b', 'a', $replaced);
        self::assertTrue($replaced, 'validate flag');

        $replaced = true;
        Str::replaceFirst('b', 'z', '', $replaced);
        self::assertFalse($replaced, 'flag is overridden with false');
    }

    public function test_replaceLast(): void
    {
        self::assertSame('', Str::replaceLast('', '', ''), 'empty string');
        self::assertSame('bb', Str::replaceLast('bb', '', 'a'), 'empty search');
        self::assertSame('bba', Str::replaceLast('bbb', 'b', 'a'), 'basic');
        self::assertSame('b', Str::replaceLast('bb', 'b', ''), 'empty replacement');
        self::assertSame('あ-いえう', Str::replaceLast('あ-い-う', '-', 'え'), 'mbstring');
        self::assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿 a', Str::replaceLast('🏴󠁧󠁢󠁳󠁣󠁴󠁿 👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦', 'a'), 'multiple codepoints');
        self::assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿a_🏴󠁧󠁢󠁳󠁣󠁴󠁿', Str::replaceLast('🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿', '🏴󠁧󠁢󠁳󠁣󠁴󠁿a', '_'));
        self::assertSame('👋🏿', Str::replaceLast('👋🏿', '👋', ''), 'treat emoji cluster as one character');

        $replaced = false;
        Str::replaceLast('bbb', 'b', 'a', $replaced);
        self::assertTrue($replaced, 'validate flag');

        $replaced = true;
        Str::replaceLast('b', 'z', '', $replaced);
        self::assertFalse($replaced, 'flag is overridden with false');
    }

    public function test_replaceMatch(): void
    {
        self::assertSame('', Str::replaceMatch('', '', ''));
        self::assertSame('abb', Str::replaceMatch('abc', '/c/', 'b'));
        self::assertSame('abbb', Str::replaceMatch('abcc', '/c/', 'b'));
        self::assertSame('あいい', Str::replaceMatch('あいう', '/う/', 'い'));
        self::assertSame('x', Str::replaceMatch('abcde', '/[A-Za-z]+/', 'x'));
        self::assertSame('a-b', Str::replaceMatch('a🏴󠁧󠁢󠁳󠁣󠁴󠁿b', '/🏴󠁧󠁢󠁳󠁣󠁴󠁿/', '-'));

        // with null count no match
        $count = 0;
        self::assertSame('', Str::replaceMatch('', '', '', null, $count));
        self::assertSame(0, $count);

        // with null count
        $count = 0;
        self::assertSame('', Str::replaceMatch('aaa', '/a/', '', null, $count));
        self::assertSame(3, $count);

        // with counter reset
        $count = 1;
        self::assertSame('', Str::replaceMatch('aaa', '/a/', '', null, $count));
        self::assertSame(3, $count);

        // with limit
        self::assertSame('a', Str::replaceMatch('aaa', '/a/', '', 2));
    }

    public function test_replaceMatch_with_negative_limit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a value greater than or equal to 0. Got: -1');
        Str::replaceMatch('', '/a/', 'a', -1);
    }

    public function test_reverse(): void
    {
        self::assertSame('', Str::reverse(''));
        self::assertSame('ba', Str::reverse('ab'));
        self::assertSame('ういあ', Str::reverse('あいう'));
        self::assertSame('cbあ🏴󠁧󠁢󠁳󠁣󠁴󠁿', Str::reverse('🏴󠁧󠁢󠁳󠁣󠁴󠁿あbc'));
    }

    public function test_startsWith(): void
    {
        self::assertTrue(Str::startsWith('', ''));
        self::assertTrue(Str::startsWith('bb', ''));
        self::assertTrue(Str::startsWith('bb', 'b'));
        self::assertTrue(Str::startsWith('あ-い-う', 'あ'));
        self::assertFalse(Str::startsWith('👨‍👨‍👧‍👦', '👨‍'));
        self::assertTrue(Str::startsWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿 👨‍👨‍👧‍👦', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
        self::assertFalse(Str::startsWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿 👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦'));
        self::assertTrue(Str::startsWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿', '🏴󠁧󠁢󠁳󠁣󠁴󠁿a'));
        self::assertFalse(Str::startsWith('ba', 'a'));
        self::assertFalse(Str::startsWith('', 'a'));
    }

    public function test_split(): void
    {
        // empty
        self::assertSame(['', ''], Str::split(' ', ' '));

        // no match
        self::assertSame(['abc'], Str::split('abc', '_'));

        // match
        self::assertSame(['a', 'c', 'd'], Str::split('abcbd', 'b'));

        // match utf-8
        self::assertSame(['あ', 'う'], Str::split('あいう', 'い'));

        // match with limit
        self::assertSame(['a', 'cbd'], Str::split('abcbd', 'b', 2));

        // match with limit
        self::assertSame(['a', 'b', 'c'], Str::split('abc', ''));

        // match emoji
        self::assertSame(['👨‍👨‍👧‍👦'], Str::split('👨‍👨‍👧‍👦', '‍👦'));
    }

    public function test_split_with_negative_limit(): void
    {
        $this->expectErrorMessage('Expected a value greater than or equal to 0. Got: -1');
        Str::split('a', 'b', -1);
    }

    public function test_substring(): void
    {
        // empty
        self::assertSame('', Str::substring('', 0));
        self::assertSame('', Str::substring('', 0, 1));

        // ascii
        self::assertSame('abc', Str::substring('abc', 0));
        self::assertSame('bc', Str::substring('abc', 1));
        self::assertSame('c', Str::substring('abc', -1));
        self::assertSame('a', Str::substring('abc', 0, 1));
        self::assertSame('b', Str::substring('abc', 1, 1));
        self::assertSame('b', Str::substring('abc', -2, 1));
        self::assertSame('bc', Str::substring('abc', -2, 2));
        self::assertSame('ab', Str::substring('abc', -9999, 2));
        self::assertSame('ab', Str::substring('abc', 0, -1));
        self::assertSame('a', Str::substring('abc', 0, -2));
        self::assertSame('', Str::substring('abc', 0, -3));
        self::assertSame('', Str::substring('abc', 2, -1));

        // utf-8
        self::assertSame('あいう', Str::substring('あいう', 0));
        self::assertSame('いう', Str::substring('あいう', 1));
        self::assertSame('う', Str::substring('あいう', -1));
        self::assertSame('い', Str::substring('あいう', -2, 1));
        self::assertSame('いう', Str::substring('あいう', -2, 2));
        self::assertSame('あい', Str::substring('あいう', -9999, 2));

        // grapheme
        self::assertSame('👨‍👨‍👧‍👦', Str::substring('👨‍👨‍👧‍👦', 0));
        self::assertSame('', Str::substring('👨‍👨‍👧‍👦', 1));
        self::assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿', Str::substring('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿', 1));
        self::assertSame('👨‍👨‍👧‍👦', Str::substring('👨‍👨‍👧‍👦👨‍👨‍👧‍👦', 1, 1));
        self::assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿', Str::substring('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿', -1, 1));
    }

    public function test_substring_invalid_input(): void
    {
        $this->expectExceptionMessage('Error converting input string to UTF-16: U_INVALID_CHAR_FOUND');
        self::assertSame('', Str::substring(substr('あ', 1), 0, 2));
    }

    public function test_takeFirst(): void
    {
        // empty string
        self::assertSame('', Str::takeFirst('', 1));

        // empty string
        self::assertSame('', Str::takeFirst('', 1));

        // zero amount
        self::assertSame('', Str::takeFirst('a', 0));

        // mid amount
        self::assertSame('abcd', Str::takeFirst('abcde', 4));

        // exact length
        self::assertSame('abc', Str::takeFirst('abc', 3));

        // grapheme
        self::assertSame('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿', Str::takeFirst('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', 4));

        // grapheme cluster
        self::assertSame('👋🏿', Str::takeFirst('👋🏿', 1));
    }

    public function test_takeFirst_out_of_range_negative(): void
    {
        $this->expectExceptionMessage('Expected a value greater than or equal to 0. Got: -4');
        Str::takeFirst('abc', -4);
    }

    public function test_takeLast(): void
    {
        // empty string
        self::assertSame('', Str::takeLast('', 1));

        // empty string
        self::assertSame('', Str::takeLast('', 1));

        // zero amount
        self::assertSame('a', Str::takeLast('a', 0));

        // mid amount
        self::assertSame('bcde', Str::takeLast('abcde', 4));

        // exact length
        self::assertSame('abc', Str::takeLast('abc', 3));

        // over length
        self::assertSame('abc', Str::takeLast('abc', 4));

        // grapheme
        self::assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', Str::takeLast('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', 4));

        // grapheme cluster
        self::assertSame('👋🏿', Str::takeLast('👋🏿', 1));
    }

    public function test_takeLast_out_of_range_negative(): void
    {
        $this->expectExceptionMessage('Expected a value greater than or equal to 0. Got: -4');
        Str::takeLast('abc', -4);
    }

    public function test_toBool(): void
    {
        self::assertTrue(Str::toBool('true'), 'true as string');
        self::assertTrue(Str::toBool('TRUE'), 'TRUE as string');
        self::assertFalse(Str::toBool('false'), 'false as string');
        self::assertFalse(Str::toBool('FALSE'), 'FALSE as string');
        self::assertTrue(Str::toBool('1'), 'empty as string');
    }

    public function test_toBool_empty(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"" is not a valid boolean string.');
        // empty as string
        Str::toBool('');
    }

    public function test_toBool_with_negative(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"-2" is not a valid boolean string.');
        // invalid boolean (number)
        Str::toBool('-2');
    }

    public function test_toBool_with_yes(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"yes" is not a valid boolean string.');
        // truthy will fail
        Str::toBool('yes');
    }

    public function test_toBoolOrNull(): void
    {
        self::assertTrue(Str::toBoolOrNull('true'), 'true as string');
        self::assertTrue(Str::toBoolOrNull('TRUE'), 'TRUE as string');
        self::assertFalse(Str::toBoolOrNull('false'), 'false as string');
        self::assertFalse(Str::toBoolOrNull('FALSE'), 'FALSE as string');
        self::assertTrue(Str::toBoolOrNull('1'), 'empty as string');
        self::assertNull(Str::toBoolOrNull(''), 'empty as string');
        self::assertNull(Str::toBoolOrNull('-2'), 'invalid boolean (number)');
        self::assertNull(Str::toBoolOrNull('yes'), 'truthy will fail');
    }

    public function test_toCamelCase(): void
    {
        self::assertSame('test', Str::toCamelCase('test'));
        self::assertSame('test', Str::toCamelCase('Test'));
        self::assertSame('testTest', Str::toCamelCase('test-test'));
        self::assertSame('testTest', Str::toCamelCase('test_test'));
        self::assertSame('testTest', Str::toCamelCase('test test'));
        self::assertSame('testTestTest', Str::toCamelCase('test test test'));
        self::assertSame('testTest', Str::toCamelCase(' test  test  '));
        self::assertSame('testTestTest', Str::toCamelCase("--test_test-test__"));
    }

    public function test_toFloat(): void
    {
        self::assertSame(1.0, Str::toFloat('1'), 'positive int');
        self::assertSame(-1.0, Str::toFloat('-1'), 'negative int');
        self::assertSame(1.23, Str::toFloat('1.23'), 'positive float');
        self::assertSame(-1.23, Str::toFloat('-1.23'), 'negative float');
        self::assertSame(0.0, Str::toFloat('0'), 'zero int');
        self::assertSame(0.0, Str::toFloat('0.0'), 'zero float');
        self::assertSame(0.0, Str::toFloat('-0'), 'negative zero int');
        self::assertSame(0.0, Str::toFloat('-0.0'), 'negative zero float');
        self::assertSame(0.123, Str::toFloat('0.123'), 'start from zero');
        self::assertSame(123.456, Str::toFloat('123.456'), 'multiple digits');
        self::assertSame(1230.0, Str::toFloat('1.23e3'), 'scientific notation with e');
        self::assertSame(1230.0, Str::toFloat('1.23E3'), 'scientific notation with E');
        self::assertSame(-1230.0, Str::toFloat('-1.23e3'), 'scientific notation as negative');
        self::assertSame(1.234, Str::toFloatOrNull('123.4E-2'), 'scientific notation irregular');
        self::assertSame(1230.0, Str::toFloat('1.23e+3'), 'with +e');
        self::assertSame(1230.0, Str::toFloat('1.23E+3'), 'with +E');
        self::assertSame(0.012, Str::toFloat('1.2e-2'), 'with -e');
        self::assertSame(0.012, Str::toFloat('1.2E-2'), 'with -E');
        self::assertNan(Str::toFloat('NAN'), 'NAN');
        self::assertNan(Str::toFloat('-NAN'), 'Negative NAN');
        self::assertNan(Str::toFloat('NaN'), 'NaN from Javascript');
        self::assertNan(Str::toFloat('-NaN'), 'Negative NaN');
        self::assertInfinite(Str::toFloat('INF'), 'upper case INF');
        self::assertInfinite(Str::toFloat('Infinity'), 'INF from Javascript');
    }

    public function test_toFloat_overflow_e_notation(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Float precision lost for "1e20"');
        Str::toFloat('1e20');
    }

    public function test_toFloat_empty_string(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"" is not a valid float.');
        Str::toFloat('');
    }

    public function test_toFloat_invalid_string(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"1a" is not a valid float.');
        Str::toFloat('1a');
    }

    public function test_toFloat_dot_start(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('".1" is not a valid float.');
        Str::toFloat('.1');
    }

    public function test_toFloat_zero_start(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"00.1" is not a valid float.');
        Str::toFloat('00.1');
    }

    public function test_toFloat_overflow_number(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Float precision lost for "1.11111111111111"');
        Str::toFloat('1.' . str_repeat('1', 14));
    }

    public function test_toFloatOrNull(): void
    {
        self::assertSame(1.0, Str::toFloatOrNull('1'), 'positive int');
        self::assertSame(-1.0, Str::toFloatOrNull('-1'), 'negative int');
        self::assertSame(1.23, Str::toFloatOrNull('1.23'), 'positive float');
        self::assertSame(-1.23, Str::toFloatOrNull('-1.23'), 'negative float');
        self::assertSame(0.0, Str::toFloatOrNull('0'), 'zero int');
        self::assertSame(0.0, Str::toFloatOrNull('0.0'), 'zero float');
        self::assertSame(0.0, Str::toFloatOrNull('-0'), 'negative zero int');
        self::assertSame(0.0, Str::toFloatOrNull('-0.0'), 'negative zero float');
        self::assertSame(0.123, Str::toFloatOrNull('0.123'), 'start from zero');
        self::assertSame(123.456, Str::toFloatOrNull('123.456'), 'multiple digits');
        self::assertSame(1230.0, Str::toFloatOrNull('1.23e3'), 'scientific notation with e');
        self::assertSame(1230.0, Str::toFloatOrNull('1.23E3'), 'scientific notation with E');
        self::assertSame(-1230.0, Str::toFloatOrNull('-1.23e3'), 'scientific notation as negative');
        self::assertSame(1230.0, Str::toFloatOrNull('1.23e+3'), 'with +e');
        self::assertSame(1230.0, Str::toFloatOrNull('1.23E+3'), 'with +E');
        self::assertSame(0.012, Str::toFloatOrNull('1.2e-2'), 'with -e');
        self::assertSame(0.012, Str::toFloatOrNull('1.2E-2'), 'with -E');
        self::assertSame(1.234, Str::toFloatOrNull('123.4E-2'), 'scientific notation irregular');
        self::assertNull(Str::toFloatOrNull('1e+20'), 'overflowing +e notation');
        self::assertNull(Str::toFloatOrNull('1e-20'), 'overflowing -e notation');
        self::assertNull(Str::toFloatOrNull('nan'), 'Lowercase nan is not NAN');
        self::assertNan(Str::toFloatOrNull('NAN'), 'NAN');
        self::assertNan(Str::toFloatOrNull('-NAN'), 'Negative NAN');
        self::assertNan(Str::toFloatOrNull('NaN'), 'NaN from Javascript');
        self::assertNan(Str::toFloatOrNull('-NaN'), 'Negative NaN');
        self::assertNull(Str::toFloatOrNull('inf'), 'Lowercase inf is not INF');
        self::assertInfinite(Str::toFloatOrNull('INF'), 'upper case INF');
        self::assertInfinite(Str::toFloatOrNull('Infinity'), 'INF from Javascript');
        self::assertNull(Str::toFloatOrNull(''), 'empty');
        self::assertNull(Str::toFloatOrNull('a1'), 'invalid string');
        self::assertNull(Str::toFloatOrNull('01.1'), 'zero start');
        self::assertNull(Str::toFloatOrNull('.1'), 'dot start');
        self::assertNull(Str::toFloatOrNull('1.' . str_repeat('1', 100)), 'overflow');
    }

    public function test_toInt(): void
    {
        self::assertSame(123, Str::toIntOrNull('123'));
    }

    public function test_toInt_blank(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"" is not a valid integer.');
        Str::toInt('');
    }

    public function test_toInt_float(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"1.0" is not a valid integer.');
        Str::toInt('1.0');
    }

    public function test_toInt_with_e_notation(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"1.23E+3" is not a valid integer.');
        Str::toInt('1.23E+3');
    }

    public function test_toInt_float_with_e_notation(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"1.0e-2" is not a valid integer.');
        Str::toInt('1.0e-2');
    }

    public function test_toInt_zero_start(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"01" is not a valid integer.');
        Str::toInt('01');
    }

    public function test_toInt_not_compatible(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"a1" is not a valid integer.');
        Str::toInt('a1');
    }

    public function test_toInt_positive_overflow(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"11111111111111111111" is not a valid integer.');
        Str::toInt(str_repeat('1', 20));
    }

    public function test_toInt_negative_overflow(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"-11111111111111111111" is not a valid integer.');
        Str::toInt('-' . str_repeat('1', 20));
    }

    public function test_toIntOrNull(): void
    {
        self::assertSame(123, Str::toIntOrNull('123'));
        self::assertNull(Str::toIntOrNull(str_repeat('1', 20)), 'overflow positive');
        self::assertNull(Str::toIntOrNull('-' . str_repeat('1', 20)), 'overflow positive');
        self::assertNull(Str::toIntOrNull(''), 'blank');
        self::assertNull(Str::toIntOrNull('1.0'), 'float value');
        self::assertNull(Str::toIntOrNull('1.0e-2'), 'float value with e notation');
        self::assertNull(Str::toIntOrNull('a1'), 'invalid string');
        self::assertNull(Str::toIntOrNull('01'), 'zero start');
    }

    public function test_toLowerCase(): void
    {
        // empty (nothing happens)
        self::assertSame('', Str::toLowerCase(''));

        // basic
        self::assertSame('abc', Str::toLowerCase('ABC'));

        // utf-8 chars (nothing happens)
        self::assertSame('あいう', Str::toLowerCase('あいう'));

        // utf-8 special chars
        self::assertSame('çği̇öşü', Str::toLowerCase('ÇĞİÖŞÜ'));

        // grapheme (nothing happens)
        self::assertSame('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿', Str::toLowerCase('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
    }

    public function test_toPascalCase(): void
    {
        self::assertSame('A', Str::toPascalCase('a'));
        self::assertSame('TestMe', Str::toPascalCase('test_me'));
        self::assertSame('TestMe', Str::toPascalCase('test-me'));
        self::assertSame('TestMe', Str::toPascalCase('test me'));
        self::assertSame('TestMe', Str::toPascalCase('testMe'));
        self::assertSame('TestMe', Str::toPascalCase('TestMe'));
        self::assertSame('TestMe', Str::toPascalCase(' test_me '));
        self::assertSame('TestMeNow!', Str::toPascalCase('test_me now-!'));
    }

    public function test_toSnakeCase(): void
    {
        // empty
        self::assertSame('', Str::toSnakeCase(''));

        // no-change
        self::assertSame('abc', Str::toSnakeCase('abc'));

        // case
        self::assertSame('the_test_for_case', Str::toSnakeCase('the test for case'));
        self::assertSame('the_test_for_case', Str::toSnakeCase('the-test-for-case'));
        self::assertSame('the_test_for_case', Str::toSnakeCase('theTestForCase'));
        self::assertSame('ttt', Str::toSnakeCase('TTT'));
        self::assertSame('tt_t', Str::toSnakeCase('TtT'));
        self::assertSame('tt_t', Str::toSnakeCase('TtT'));
        self::assertSame('the__test', Str::toSnakeCase('the  test'));
        self::assertSame('__test', Str::toSnakeCase('  test'));
        self::assertSame("test\nabc", Str::toSnakeCase("test\nabc"));
        self::assertSame('__test_test_test__', Str::toSnakeCase("--test_test-test__"));
    }

    public function test_toUpperCase(): void
    {
        // empty (nothing happens)
        self::assertSame('', Str::toUpperCase(''));

        // basic
        self::assertSame('ABC', Str::toUpperCase('abc'));

        // utf-8 chars (nothing happens)
        self::assertSame('あいう', Str::toUpperCase('あいう'));

        // utf-8 special chars
        self::assertSame('ÇĞİÖŞÜ', Str::toUpperCase('çği̇öşü'));

        // grapheme (nothing happens)
        self::assertSame('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿', Str::toUpperCase('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
    }

    public function test_trim(): void
    {
        // empty (nothing happens)
        self::assertSame('', Str::trim(''));

        // left only
        self::assertSame('a', Str::trim("\ta"));

        // right only
        self::assertSame('a', Str::trim("a\t"));

        // new line on both ends
        self::assertSame('abc', Str::trim("\nabc\n"));

        // tab and mixed line on both ends
        self::assertSame('abc', Str::trim("\t\nabc\n\t"));

        // tab and mixed line on both ends
        self::assertSame('abc', Str::trim("\t\nabc\n\t"));

        // multibyte spaces (https://3v4l.org/s16FF)
        self::assertSame('abc', Str::trim("\u{2000}\u{2001}abc\u{2002}\u{2003}"));

        // grapheme (nothing happens)
        self::assertSame('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿', Str::trim('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿'));

        // custom
        self::assertSame('b', Str::trim('aba', 'a'));

        // custom empty
        self::assertSame('a', Str::trim('a', ''));

        // custom overrides delimiter
        self::assertSame("\nb\n", Str::trim("a\nb\na", 'a'));

        // custom multiple
        self::assertSame('b', Str::trim("_ab_a_", 'a_'));
    }

    public function test_trimEnd(): void
    {
        // empty (nothing happens)
        self::assertSame('', Str::trimEnd(''));

        // left only
        self::assertSame("\ta", Str::trimEnd("\ta"));

        // right only
        self::assertSame('a', Str::trimEnd("a\t"));

        // new line on both ends
        self::assertSame("\nabc", Str::trimEnd("\nabc\n"));

        // tab and mixed line on both ends
        self::assertSame('abc', Str::trimEnd("abc\n\t"));

        // multibyte spaces (https://3v4l.org/s16FF)
        self::assertSame(' abc', Str::trimEnd(" abc\n\t\u{0009}\u{2028}\u{2029}\v "));

        // grapheme (nothing happens)
        self::assertSame('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿', Str::trimEnd('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿'));

        // custom
        self::assertSame('ab', Str::trimEnd('aba', 'a'));

        // custom empty
        self::assertSame('a', Str::trimEnd('a', ''));

        // custom overrides delimiter
        self::assertSame("ab\n", Str::trimEnd("ab\na", 'a'));

        // custom multiple
        self::assertSame('_ab', Str::trimEnd("_ab_a_", 'a_'));
    }

    public function test_trimStart(): void
    {
        // empty (nothing happens)
        self::assertSame('', Str::trimStart(''));

        // left only
        self::assertSame("a", Str::trimStart("\ta"));

        // right only
        self::assertSame("a\t", Str::trimStart("a\t"));

        // new line on both ends
        self::assertSame("abc\n", Str::trimStart("\nabc\n"));

        // tab and new line
        self::assertSame('abc', Str::trimStart("\n\tabc"));

        // multibyte spaces (https://3v4l.org/s16FF)
        self::assertSame('abc ', Str::trimStart("\n\t\u{0009}\u{2028}\u{2029}\v abc "));

        // grapheme (nothing happens)
        self::assertSame('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿', Str::trimStart('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿'));

        // custom
        self::assertSame('ba', Str::trimStart('aba', 'a'));

        // custom empty
        self::assertSame('a', Str::trimStart('a', ''));

        // custom overrides delimiter
        self::assertSame("\nba", Str::trimStart("a\nba", 'a'));

        // custom multiple
        self::assertSame('b_a_', Str::trimStart("_ab_a_", 'a_'));
    }

    public function test_withPrefix(): void
    {
        // empty string always adds
        self::assertSame('foo', Str::withPrefix('', 'foo'));

        // empty start does nothing
        self::assertSame('foo', Str::withPrefix('foo', ''));

        // has match
        self::assertSame('foo', Str::withPrefix('foo', 'f'));

        // no match
        self::assertSame('_foo', Str::withPrefix('foo', '_'));

        // partial matching doesn't count
        self::assertSame('___foo', Str::withPrefix('_foo', '__'));

        // repeats handled properly
        self::assertSame('__foo', Str::withPrefix('__foo', '_'));

        // try escape chars
        self::assertSame('\s foo', Str::withPrefix(' foo', "\s"));

        // new line
        self::assertSame("\n foo", Str::withPrefix(' foo', "\n"));

        // slashes
        self::assertSame('/foo', Str::withPrefix('foo', '/'));

        // utf8 match
        self::assertSame('あい', Str::withPrefix('あい', 'あ'));

        // utf8 no match
        self::assertSame('うえあい', Str::withPrefix('あい', 'うえ'));

        // grapheme (treats combined grapheme as 1 whole character)
        self::assertSame('👨👨‍👨‍👧‍👧', Str::withPrefix('👨‍👨‍👧‍👧', '👨'));
    }

    public function test_withSuffix(): void
    {
        // empty string always adds
        self::assertSame('foo', Str::withSuffix('', 'foo'));

        // empty start does nothing
        self::assertSame('foo', Str::withSuffix('foo', ''));

        // has match
        self::assertSame('foo', Str::withSuffix('foo', 'oo'));

        // no match
        self::assertSame('foo bar', Str::withSuffix('foo', ' bar'));

        // partial matching doesn't count
        self::assertSame('foo___', Str::withSuffix('foo_', '__'));

        // repeats handled properly
        self::assertSame('foo__', Str::withSuffix('foo__', '_'));

        // try escape chars
        self::assertSame('foo \s', Str::withSuffix('foo ', "\s"));

        // new line
        self::assertSame("foo \n", Str::withSuffix('foo ', "\n"));

        // slashes
        self::assertSame('foo/', Str::withSuffix('foo', '/'));

        // utf8 match
        self::assertSame('あい', Str::withSuffix('あい', 'い'));

        // utf8 no match
        self::assertSame('あいうえ', Str::withSuffix('あい', 'うえ'));

        // grapheme (treats combined grapheme as 1 whole character)
        self::assertSame('👨‍👨‍👧‍👧‍👧‍', Str::withSuffix('👨‍👨‍👧‍👧‍', '👧‍'));
    }

    public function test_wrap(): void
    {
        // blanks
        self::assertSame('', Str::wrap('', '', ''));

        // simple case
        self::assertSame('[a]', Str::wrap('a', '[', ']'));

        // multibyte
        self::assertSame('１a２', Str::wrap('a', '１', '２'));

        // grapheme
        self::assertSame('👨‍👨‍👧‍a🏴󠁧󠁢󠁳󠁣󠁴󠁿', Str::wrap('a', '👨‍👨‍👧‍', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
    }
}
