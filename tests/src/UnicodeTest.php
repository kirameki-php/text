<?php declare(strict_types=1);

namespace Tests\Kirameki\Text;

use IntlException;
use Kirameki\Core\Testing\TestCase;
use Kirameki\Text\Unicode;
use PHPUnit\Framework\TestStatus\Warning;
use RuntimeException;
use Webmozart\Assert\InvalidArgumentException;
use function str_repeat;
use function substr;

class UnicodeTest extends TestCase
{
    protected static Unicode $ref;

    protected function setUp(): void
    {
        parent::setUp();
        self::$ref = new Unicode();
    }

    public function test_after(): void
    {
        // match first
        $this->assertSame('est', self::$ref::after('test', 't'));

        // match last
        $this->assertSame('', self::$ref::after('test1', '1'));

        // match empty string
        $this->assertSame('test', self::$ref::after('test', ''));

        // no match
        $this->assertSame('test', self::$ref::after('test', 'test2'));

        // multi byte
        $this->assertSame('うえ', self::$ref::after('ああいうえ', 'い'));

        // grapheme
        $this->assertSame('def', self::$ref::after('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿def', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));

        // grapheme cluster
        $this->assertSame('👋🏿', self::$ref::after('👋🏿', '👋'));
    }

    public function test_afterLast(): void
    {
        // match first (single occurrence)
        $this->assertSame('bc', self::$ref::afterLast('abc', 'a'));

        // match first (multiple occurrence)
        $this->assertSame('1', self::$ref::afterLast('test1', 't'));

        // match last
        $this->assertSame('', self::$ref::afterLast('test1', '1'));

        // should match the last string
        $this->assertSame('Foo', self::$ref::afterLast('----Foo', '---'));

        // match empty string
        $this->assertSame('test', self::$ref::afterLast('test', ''));

        // no match
        $this->assertSame('test', self::$ref::afterLast('test', 'a'));

        // multi byte
        $this->assertSame('え', self::$ref::afterLast('ああいういえ', 'い'));

        // grapheme
        $this->assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿f', self::$ref::afterLast('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', 'e'));

        // grapheme cluster
        $this->assertSame('👋🏿', self::$ref::afterLast('👋🏿', '👋'));
    }

    public function test_before(): void
    {
        // match first (single occurrence)
        $this->assertSame('a', self::$ref::before('abc', 'b'));

        // match first (multiple occurrence)
        $this->assertSame('a', self::$ref::before('abc-abc', 'b'));

        // match last
        $this->assertSame('test', self::$ref::before('test1', '1'));

        // match multiple chars
        $this->assertSame('test', self::$ref::before('test123', '12'));

        // match empty string
        $this->assertSame('test', self::$ref::before('test', ''));

        // no match
        $this->assertSame('test', self::$ref::before('test', 'a'));

        // multi byte
        $this->assertSame('ああ', self::$ref::before('ああいういえ', 'い'));

        // grapheme
        $this->assertSame('abc', self::$ref::before('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
        $this->assertSame('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿', self::$ref::before('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', 'e'));

        // grapheme cluster
        $this->assertSame('👋🏿', self::$ref::before('👋🏿', '🏿'));
    }

    public function test_beforeLast(): void
    {
        // match first (single occurrence)
        $this->assertSame('a', self::$ref::beforeLast('abc', 'b'));

        // match first (multiple occurrence)
        $this->assertSame('abc-a', self::$ref::beforeLast('abc-abc', 'b'));

        // match last
        $this->assertSame('test', self::$ref::beforeLast('test1', '1'));

        // match empty string
        $this->assertSame('test', self::$ref::beforeLast('test', ''));

        // no match
        $this->assertSame('test', self::$ref::beforeLast('test', 'a'));

        // multi byte
        $this->assertSame('ああいう', self::$ref::beforeLast('ああいういえ', 'い'));

        // grapheme
        $this->assertSame('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e', self::$ref::beforeLast('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));

        // grapheme cluster
        $this->assertSame('👋🏿', self::$ref::beforeLast('👋🏿', '🏿'));
    }

    public function test_between(): void
    {
        // basic
        $this->assertSame('1', self::$ref::between('test(1)', '(', ')'));

        // edge
        $this->assertSame('', self::$ref::between('()', '(', ')'));
        $this->assertSame('1', self::$ref::between('(1)', '(', ')'));

        // missing from
        $this->assertSame('test)', self::$ref::between('test)', '(', ')'));

        // missing to
        $this->assertSame('test(', self::$ref::between('test(', '(', ')'));

        // nested
        $this->assertSame('test(1', self::$ref::between('(test(1))', '(', ')'));
        $this->assertSame('1', self::$ref::between('(1) to (2)', '(', ')'));

        // multi char
        $this->assertSame('_ab_', self::$ref::between('ab_ab_ba_ba', 'ab', 'ba'));

        // utf8
        $this->assertSame('い', self::$ref::between('あいういう', 'あ', 'う'));

        // grapheme
        $this->assertSame('😃', self::$ref::between('👋🏿😃👋🏿😃👋🏿', '👋🏿', '👋🏿'));

        // grapheme between codepoints
        $this->assertSame('👋🏿', self::$ref::between('👋🏿', '👋', '🏿'));
    }

    public function test_between_empty_from(): void
    {
        $this->expectExceptionMessage('$from must not be empty.');
        self::$ref::between('test)', '', ')');
    }

    public function test_between_empty_to(): void
    {
        $this->expectExceptionMessage('$to must not be empty.');
        self::$ref::between('test)', '(', '');
    }

    public function test_between_empty_from_and_to(): void
    {
        $this->expectExceptionMessage('$from must not be empty.');
        self::$ref::between('test)', '', '');
    }

    public function test_betweenFurthest(): void
    {
        // basic
        $this->assertSame('1', self::$ref::betweenFurthest('test(1)', '(', ')'));

        // edge
        $this->assertSame('', self::$ref::betweenFurthest('()', '(', ')'));
        $this->assertSame('1', self::$ref::betweenFurthest('(1)', '(', ')'));

        // missing from
        $this->assertSame('test)', self::$ref::betweenFurthest('test)', '(', ')'));

        // missing to
        $this->assertSame('test(', self::$ref::betweenFurthest('test(', '(', ')'));

        // nested
        $this->assertSame('test(1)', self::$ref::betweenFurthest('(test(1))', '(', ')'));
        $this->assertSame('1) to (2', self::$ref::betweenFurthest('(1) to (2)', '(', ')'));

        // multi char
        $this->assertSame('_', self::$ref::betweenFurthest('ab_ba', 'ab', 'ba'));

        // utf8
        $this->assertSame('い', self::$ref::betweenFurthest('あいう', 'あ', 'う'));

        // grapheme
        $this->assertSame('😃', self::$ref::betweenFurthest('👋🏿😃👋🏿😃', '👋🏿', '👋🏿'));

        // grapheme between codepoints
        $this->assertSame('👋🏿', self::$ref::between('👋🏿', '👋', '🏿'));
    }

    public function test_betweenFurthest_empty_from(): void
    {
        $this->expectExceptionMessage('$from must not be empty.');
        self::$ref::betweenFurthest('test)', '', ')');
    }

    public function test_betweenFurthest_empty_to(): void
    {
        $this->expectExceptionMessage('$to must not be empty.');
        self::$ref::betweenFurthest('test)', '(', '');
    }

    public function test_betweenFurthest_empty_from_and_to(): void
    {
        $this->expectExceptionMessage('$from must not be empty.');
        self::$ref::betweenFurthest('test)', '', '');
    }

    public function test_betweenLast(): void
    {
        // basic
        $this->assertSame('1', self::$ref::betweenLast('test(1)', '(', ')'));

        // edge
        $this->assertSame('', self::$ref::betweenLast('()', '(', ')'));
        $this->assertSame('1', self::$ref::betweenLast('(1)', '(', ')'));

        // missing from
        $this->assertSame('test)', self::$ref::between('test)', '(', ')'));

        // missing to
        $this->assertSame('test(', self::$ref::between('test(', '(', ')'));

        // nested
        $this->assertSame('1)', self::$ref::betweenLast('(test(1))', '(', ')'));
        $this->assertSame('2', self::$ref::betweenLast('(1) to (2)', '(', ')'));

        // multi char
        $this->assertSame('_ba_', self::$ref::betweenLast('ab_ab_ba_ba', 'ab', 'ba'));

        // utf8
        $this->assertSame('いうい', self::$ref::betweenLast('あいういう', 'あ', 'う'));

        // grapheme
        $this->assertSame('🥹', self::$ref::betweenLast('👋🏿😃👋🏿🥹👋', '👋🏿', '👋'));

        // grapheme between codepoints
        $this->assertSame('👋🏿', self::$ref::between('👋🏿', '👋', '🏿'));
    }

    public function test_betweenLast_empty_from(): void
    {
        $this->expectExceptionMessage('$from must not be empty.');
        self::$ref::betweenFurthest('test)', '', ')');
    }

    public function test_betweenLast_empty_to(): void
    {
        $this->expectExceptionMessage('$to must not be empty.');
        self::$ref::betweenFurthest('test)', '(', '');
    }

    public function test_betweenLast_empty_from_and_to(): void
    {
        $this->expectExceptionMessage('$from must not be empty.');
        self::$ref::betweenFurthest('test)', '', '');
    }

    public function test_byteLength(): void
    {
        // empty
        $this->assertSame(0, self::$ref::byteLength(''));

        // ascii
        $this->assertSame(1, self::$ref::byteLength('a'));

        // utf8
        $this->assertSame(3, self::$ref::byteLength('あ'));

        // emoji
        $this->assertSame(25, self::$ref::byteLength('👨‍👨‍👧‍👦'));
    }

    public function test_capitalize(): void
    {
        $this->assertSame('', self::$ref::capitalize(''), 'empty');
        $this->assertSame('TT', self::$ref::capitalize('TT'), 'all uppercase');
        $this->assertSame('Test', self::$ref::capitalize('test'), 'lowercase');
        $this->assertSame('Test abc', self::$ref::capitalize('test abc'), 'lowercase with spaces');
        $this->assertSame(' test abc', self::$ref::capitalize(' test abc'), 'lowercase with spaces and leading space');
        $this->assertSame('Àbc', self::$ref::capitalize('àbc'), 'lowercase with accent');
        $this->assertSame('É', self::$ref::capitalize('é'), 'lowercase with accent');
        $this->assertSame('ゅ', self::$ref::capitalize('ゅ'), 'lowercase with hiragana');
        $this->assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿', self::$ref::capitalize('🏴󠁧󠁢󠁳󠁣󠁴󠁿'), 'lowercase with emoji');
    }

    public function test_chunk(): void
    {
        $this->assertSame([], self::$ref::chunk('', 5), 'empty');
        $this->assertSame(['ab'], self::$ref::chunk('ab', 5), 'oversize');
        $this->assertSame(['ab'], self::$ref::chunk('ab', 2), 'exact');
        $this->assertSame(['ab', 'c'], self::$ref::chunk('abc', 2), 'fragment');
        $this->assertSame(['あい', 'う'], self::$ref::chunk('あいう', 2), 'utf8');
        $this->assertSame(['👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦'], self::$ref::chunk('👨‍👨‍👧‍👦👨‍👨‍👧‍👦', 1), 'emoji');
        $this->assertSame(['あい', 'うえ', 'おかき'], self::$ref::chunk('あいうえおかき', 2, 2), 'limit');
    }

    public function test_concat(): void
    {
        $this->assertSame('', self::$ref::concat('', '', ''), 'empty');
        $this->assertSame(' ', self::$ref::concat('', ' '), 'blank');
        $this->assertSame('', self::$ref::concat(), 'no arg');
        $this->assertSame('a', self::$ref::concat('a'), 'one arg');
        $this->assertSame('abc', self::$ref::concat('a', 'b', 'c'), 'basic');
        $this->assertSame('あい', self::$ref::concat('あ', 'い'), 'mb string');
        $this->assertSame('👋🏿', self::$ref::concat('👋', '🏿'), 'mb string');
    }

    public function test_contains(): void
    {
        $this->assertTrue(self::$ref::contains('abcde', ''), 'empty needle');
        $this->assertTrue(self::$ref::contains('', ''), 'empty haystack and needle');
        $this->assertTrue(self::$ref::contains('abcde', 'ab'), 'partial first');
        $this->assertTrue(self::$ref::contains('abcde', 'cd'), 'partial mid');
        $this->assertTrue(self::$ref::contains('abcde', 'de'), 'partial last');
        $this->assertFalse(self::$ref::contains('abc', ' a'), 'space pad left');
        $this->assertFalse(self::$ref::contains('abc', 'a '), 'space pad right');
        $this->assertTrue(self::$ref::contains('abc', 'abc'), 'full');
        $this->assertFalse(self::$ref::contains('ab', 'abc'), 'needle is longer');
        $this->assertFalse(self::$ref::contains('👨‍👨‍👧‍👧‍', '👨'), 'grapheme partial');
        $this->assertFalse(self::$ref::contains('👨‍👨‍👧‍👧‍abc', '👨‍👨‍👧‍👧‍ abc'), 'grapheme');
    }

    public function test_containsAll(): void
    {
        $this->assertTrue(self::$ref::containsAll('', []), 'empty substrings with blank');
        $this->assertTrue(self::$ref::containsAll('abc', []), 'empty substrings');
        $this->assertTrue(self::$ref::containsAll('', ['']), 'blank match blank');
        $this->assertTrue(self::$ref::containsAll('abcde', ['']), 'blank match string');
        $this->assertFalse(self::$ref::containsAll('abcde', ['a', 'z']), 'partial match first');
        $this->assertFalse(self::$ref::containsAll('abcde', ['z', 'a']), 'partial match last');
        $this->assertTrue(self::$ref::containsAll('abcde', ['a']), 'match single');
        $this->assertFalse(self::$ref::containsAll('abcde', ['z']), 'no match single');
        $this->assertTrue(self::$ref::containsAll('abcde', ['a', 'b']), 'match all first');
        $this->assertTrue(self::$ref::containsAll('abcde', ['c', 'b']), 'match all reversed');
        $this->assertFalse(self::$ref::containsAll('abcde', ['y', 'z']), 'no match all');
    }

    public function test_containsAny(): void
    {
        $this->assertTrue(self::$ref::containsAny('', []), 'blank and empty substrings');
        $this->assertTrue(self::$ref::containsAny('abcde', []), 'empty substrings');
        $this->assertTrue(self::$ref::containsAny('', ['']), 'blank match blank');
        $this->assertTrue(self::$ref::containsAny('abcde', ['']), 'blank matchs anything');
        $this->assertTrue(self::$ref::containsAny('abcde', ['a', 'z']), 'one match of many (first one matched)');
        $this->assertTrue(self::$ref::containsAny('abcde', ['z', 'a']), 'one match of many (last one matched)');
        $this->assertTrue(self::$ref::containsAny('abcde', ['a']), 'match single');
        $this->assertFalse(self::$ref::containsAny('abcde', ['z']), 'no match single');
        $this->assertFalse(self::$ref::containsAny('abcde', ['y', 'z']), 'no match all');
    }

    public function test_containsNone(): void
    {
        $this->assertTrue(self::$ref::containsNone('', []), 'blank and empty substrings');
        $this->assertTrue(self::$ref::containsNone('abcde', []), 'empty substrings');
        $this->assertFalse(self::$ref::containsNone('', ['']), 'blank match blank');
        $this->assertFalse(self::$ref::containsNone('abcde', ['']), 'blank matchs anything');
        $this->assertFalse(self::$ref::containsNone('abcde', ['a', 'z']), 'one match of many (first one matched)');
        $this->assertFalse(self::$ref::containsNone('abcde', ['z', 'a']), 'one match of many (last one matched)');
        $this->assertFalse(self::$ref::containsNone('abcde', ['a']), 'match single');
        $this->assertTrue(self::$ref::containsNone('abcde', ['z']), 'no match single');
        $this->assertTrue(self::$ref::containsNone('abcde', ['y', 'z']), 'no match all');
    }

    public function test_containsPattern(): void
    {
        $this->assertTrue(self::$ref::containsPattern('abc', '/b/'));
        $this->assertTrue(self::$ref::containsPattern('abc', '/ab/'));
        $this->assertTrue(self::$ref::containsPattern('abc', '/abc/'));
        $this->assertTrue(self::$ref::containsPattern('ABC', '/abc/i'));
        $this->assertTrue(self::$ref::containsPattern('aaaz', '/a{3}/'));
        $this->assertTrue(self::$ref::containsPattern('ABC1', '/[A-z\d]+/'));
        $this->assertTrue(self::$ref::containsPattern('ABC1]', '/\d]$/'));
        $this->assertFalse(self::$ref::containsPattern('AB1C', '/\d]$/'));
    }

    public function test_containsPattern_warning_as_error(): void
    {
        $this->expectWarningMessage('preg_match(): Unknown modifier \'a\'');
        $this->assertFalse(self::$ref::containsPattern('', '/a/a'));
    }

    public function test_count(): void
    {
        // empty string
        $this->assertSame(0, self::$ref::count('', 'aaa'));

        // exact match
        $this->assertSame(1, self::$ref::count('abc', 'abc'));

        // no match
        $this->assertSame(0, self::$ref::count('ab', 'abc'));

        // simple
        $this->assertSame(1, self::$ref::count('This is a cat', ' is '));
        $this->assertSame(2, self::$ref::count('This is a cat', 'is'));

        // overlapping
        $this->assertSame(2, self::$ref::count('ababab', 'aba'));

        // utf8
        $this->assertSame(2, self::$ref::count('あいあ', 'あ'));

        // utf8 overlapping
        $this->assertSame(2, self::$ref::count('あああ', 'ああ'));

        // check half-width is not counted.
        $this->assertSame(0, self::$ref::count('ア', 'ｱ'));

        // grapheme
        $this->assertSame(1, self::$ref::count('👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦'));

        // grapheme subset should not match
        $this->assertSame(0, self::$ref::count('👨‍👨‍👧‍👦', '👨'));

        // grapheme overlapping
        $this->assertSame(2, self::$ref::count('👨‍👨‍👧‍👦👨‍👨‍👧‍👦👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦👨‍👨‍👧‍👦'));
    }

    public function test_count_with_empty_search(): void
    {
        $this->expectExceptionMessage('Search string must be non-empty');
        self::assertFalse(self::$ref::count('a', ''));
    }

    public function test_cut(): void
    {
        // empty
        $this->assertSame('', self::$ref::cut('', 0));

        // basic
        $this->assertSame('a', self::$ref::cut('a', 1));
        $this->assertSame('a', self::$ref::cut('abc', 1));

        // utf-8
        $this->assertSame('', self::$ref::cut('あいう', 1));
        $this->assertSame('あ', self::$ref::cut('あいう', 3));

        // grapheme
        $this->assertSame('', self::$ref::cut('👋', 1));
        $this->assertSame('', self::$ref::cut('👋🏿', 1));
        $this->assertSame('👋🏿', self::$ref::cut('👋🏿', 8));

        // cut and replaced with ellipsis
        $this->assertSame('a...', self::$ref::cut('abc', 1, '...'));
        $this->assertSame('...', self::$ref::cut('あいう', 1, '...'));
        $this->assertSame('あ...', self::$ref::cut('あいう', 3, '...'));

        // cut and replaced with custom ellipsis
        $this->assertSame('a$', self::$ref::cut('abc', 1, '$'));
    }

    public function test_decapitalize(): void
    {
        $this->assertSame('', self::$ref::decapitalize(''));
        $this->assertSame('test', self::$ref::decapitalize('Test'));
        $this->assertSame('t T', self::$ref::decapitalize('T T'));
        $this->assertSame(' T ', self::$ref::decapitalize(' T '));
        $this->assertSame('é', self::$ref::decapitalize('É'));
        $this->assertSame('🔡', self::$ref::decapitalize('🔡'));
    }

    public function test_doesNotContain(): void
    {
        self::assertTrue(self::$ref::doesNotContain('abcde', 'ac'));
        self::assertFalse(self::$ref::doesNotContain('abcde', 'ab'));
        self::assertFalse(self::$ref::doesNotContain('a', ''));
        self::assertTrue(self::$ref::doesNotContain('', 'a'));
        self::assertTrue(self::$ref::doesNotContain('👨‍👨‍👧‍👧‍', '👨'));
    }

    public function test_doesNotEndWith(): void
    {
        self::assertFalse(self::$ref::doesNotEndWith('abc', 'c'));
        self::assertTrue(self::$ref::doesNotEndWith('abc', 'b'));
        self::assertFalse(self::$ref::doesNotEndWith('abc', ['c']));
        self::assertFalse(self::$ref::doesNotEndWith('abc', ['a', 'b', 'c']));
        self::assertTrue(self::$ref::doesNotEndWith('abc', ['a', 'b']));
        self::assertFalse(self::$ref::doesNotEndWith('aabbcc', 'cc'));
        self::assertFalse(self::$ref::doesNotEndWith('aabbcc' . PHP_EOL, PHP_EOL));
        self::assertFalse(self::$ref::doesNotEndWith('abc0', '0'));
        self::assertFalse(self::$ref::doesNotEndWith('abcfalse', 'false'));
        self::assertFalse(self::$ref::doesNotEndWith('a', ''));
        self::assertFalse(self::$ref::doesNotEndWith('', ''));
        self::assertFalse(self::$ref::doesNotEndWith('あいう', 'う'));
        self::assertTrue(self::$ref::doesNotEndWith("あ\n", 'あ'));
        self::assertTrue(self::$ref::doesNotEndWith('👋🏻', '🏻'));
    }


    public function test_doesNotStartWith(): void
    {
        self::assertFalse(self::$ref::doesNotStartWith('', ''));
        self::assertFalse(self::$ref::doesNotStartWith('bb', ''));
        self::assertFalse(self::$ref::doesNotStartWith('bb', 'b'));
        self::assertTrue(self::$ref::doesNotStartWith('bb', 'ab'));
        self::assertFalse(self::$ref::doesNotStartWith('あ-い-う', 'あ'));
        self::assertTrue(self::$ref::doesNotStartWith('あ-い-う', 'え'));
        self::assertTrue(self::$ref::doesNotStartWith('👨‍👨‍👧‍👦', '👨‍'));
        self::assertFalse(self::$ref::doesNotStartWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿 👨‍👨‍👧‍👦', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
        self::assertTrue(self::$ref::doesNotStartWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿 👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦'));
        self::assertFalse(self::$ref::doesNotStartWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿', '🏴󠁧󠁢󠁳󠁣󠁴󠁿a'));
        self::assertTrue(self::$ref::doesNotStartWith('ba', 'a'));
        self::assertTrue(self::$ref::doesNotStartWith('', 'a'));
        self::assertTrue(self::$ref::doesNotStartWith('abc', ['d', 'e']));
        self::assertFalse(self::$ref::doesNotStartWith('abc', ['d', 'a']));
        self::assertTrue(self::$ref::doesNotStartWith("\nあ", 'あ'));
    }

    public function test_drop(): void
    {
        // empty
        $this->assertSame('', self::$ref::dropFirst('', 1));

        // zero amount
        $this->assertSame('a', self::$ref::dropFirst('a', 0));

        // mid amount
        $this->assertSame('e', self::$ref::dropFirst('abcde', 4));

        // exact amount
        $this->assertSame('', self::$ref::dropFirst('abc', 3));

        // over overflow
        $this->assertSame('', self::$ref::dropFirst('abc', 4));

        // grapheme
        $this->assertSame('def', self::$ref::dropFirst('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿def', 4));

        // grapheme cluster (positive)
        $this->assertSame('', self::$ref::dropFirst('👋🏿', 1));
    }

    public function test_drop_negative_amount(): void
    {
        $this->expectExceptionMessage('Expected a value greater than or equal to 0. Got: -4');
        self::$ref::dropFirst('abc', -4);
    }

    public function test_dropLast(): void
    {
        // empty
        $this->assertSame('', self::$ref::dropLast('', 1));

        // zero length
        $this->assertSame('a', self::$ref::dropLast('a', 0));

        // mid amount
        $this->assertSame('ab', self::$ref::dropLast('abc', 1));

        // exact amount
        $this->assertSame('', self::$ref::dropLast('abc', 3));

        // overflow
        $this->assertSame('', self::$ref::dropLast('abc', 4));

        // grapheme
        $this->assertSame('abc', self::$ref::dropLast('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿def', 4));

        // grapheme cluster (positive)
        $this->assertSame('', self::$ref::dropLast('👋🏿', 1));
    }

    public function test_dropLast_negative_amount(): void
    {
        $this->expectExceptionMessage('Expected a value greater than or equal to 0. Got: -4');
        self::$ref::dropLast('abc', -4);
    }

    public function test_endsWith(): void
    {
        self::assertTrue(self::$ref::endsWith('abc', 'c'));
        self::assertFalse(self::$ref::endsWith('abc', 'b'));
        self::assertTrue(self::$ref::endsWith('abc', ['c']));
        self::assertTrue(self::$ref::endsWith('abc', ['a', 'b', 'c']));
        self::assertFalse(self::$ref::endsWith('abc', ['a', 'b']));
        self::assertTrue(self::$ref::endsWith('aabbcc', 'cc'));
        self::assertTrue(self::$ref::endsWith('aabbcc' . PHP_EOL, PHP_EOL));
        self::assertTrue(self::$ref::endsWith('abc0', '0'));
        self::assertTrue(self::$ref::endsWith('abcfalse', 'false'));
        self::assertTrue(self::$ref::endsWith('a', ''));
        self::assertTrue(self::$ref::endsWith('', ''));
        self::assertTrue(self::$ref::endsWith('あいう', 'う'));
        self::assertFalse(self::$ref::endsWith("あ\n", 'あ'));
        self::assertFalse(self::$ref::endsWith('👋🏻', '🏻'));
    }

    public function test_indexOfFirst(): void
    {
        // empty string
        self::assertNull(self::$ref::indexOfFirst('', 'a'));

        // empty search
        $this->assertSame(0, self::$ref::indexOfFirst('ab', ''));

        // find at 0
        $this->assertSame(0, self::$ref::indexOfFirst('a', 'a'));

        // multiple matches
        $this->assertSame(1, self::$ref::indexOfFirst('abb', 'b'));

        // offset (within bound)
        $this->assertSame(1, self::$ref::indexOfFirst('abb', 'b', 1));
        $this->assertSame(5, self::$ref::indexOfFirst('aaaaaa', 'a', 5));

        // offset (out of bound)
        self::assertNull(self::$ref::indexOfFirst('abb', 'b', 4));

        // offset (negative)
        $this->assertSame(2, self::$ref::indexOfFirst('abb', 'b', -1));

        // offset (negative)
        self::assertNull(self::$ref::indexOfFirst('abb', 'b', -100));

        // offset utf-8
        $this->assertSame(0, self::$ref::indexOfFirst('👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦'));
        self::assertNull(self::$ref::indexOfFirst('👨‍👨‍👧‍👦', '👨'));
        $this->assertSame(1, self::$ref::indexOfFirst('あいう', 'い', 1));
        $this->assertSame(1, self::$ref::indexOfFirst('🏴󠁧󠁢󠁳󠁣󠁴󠁿👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦', 1));
        self::assertNull(self::$ref::indexOfFirst('🏴󠁧󠁢󠁳󠁣󠁴󠁿👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦', 2));
    }

    public function test_indexOfLast(): void
    {
        // empty string
        self::assertNull(self::$ref::indexOfLast('', 'a'));

        // empty search
        $this->assertSame(2, self::$ref::indexOfLast('ab', ''));

        // find at 0
        $this->assertSame(0, self::$ref::indexOfLast('a', 'a'));

        // multiple matches
        $this->assertSame(2, self::$ref::indexOfLast('abb', 'b'));

        // offset (within bound)
        $this->assertSame(2, self::$ref::indexOfLast('abb', 'b', 1));
        $this->assertSame(5, self::$ref::indexOfLast('aaaaaa', 'a', 5));

        // offset (out of bound)
        self::assertNull(self::$ref::indexOfLast('abb', 'b', 4));

        // offset (negative)
        $this->assertSame(3, self::$ref::indexOfLast('abbb', 'b', -1));

        // offset (negative)
        self::assertNull(self::$ref::indexOfLast('abb', 'b', -100));

        // offset utf-8
        $this->assertSame(0, self::$ref::indexOfLast('👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦'));
        self::assertNull(self::$ref::indexOfLast('👨‍👨‍👧‍👦', '👨'));
        $this->assertSame(1, self::$ref::indexOfLast('あいう', 'い', 1));
        $this->assertSame(1, self::$ref::indexOfLast('🏴󠁧󠁢󠁳󠁣󠁴󠁿👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦', 1));
        self::assertNull(self::$ref::indexOfLast('🏴󠁧󠁢󠁳󠁣󠁴󠁿👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦', 2));
    }

    public function test_insert(): void
    {
        $this->assertSame('xyzabc', self::$ref::insert('abc', 'xyz', 0));
        $this->assertSame('axyzbc', self::$ref::insert('abc', 'xyz', 1));
        $this->assertSame('xyzabc', self::$ref::insert('abc', 'xyz', -1));
        $this->assertSame('abcxyz', self::$ref::insert('abc', 'xyz', 3));
        $this->assertSame('あxyzい', self::$ref::insert('あい', 'xyz', 1));
        $this->assertSame('xyzあい', self::$ref::insert('あい', 'xyz', -1));
    }

    public function test_isBlank(): void
    {
        self::assertTrue(self::$ref::isBlank(''));
        self::assertFalse(self::$ref::isBlank('0'));
        self::assertFalse(self::$ref::isBlank(' '));
    }

    public function test_isNotBlank(): void
    {
        self::assertFalse(self::$ref::isNotBlank(''));
        self::assertTrue(self::$ref::isNotBlank('0'));
        self::assertTrue(self::$ref::isNotBlank(' '));
    }

    public function test_kebabCase(): void
    {
        $this->assertSame('test', self::$ref::toKebabCase('test'));
        $this->assertSame('test', self::$ref::toKebabCase('Test'));
        $this->assertSame('ttt', self::$ref::toKebabCase('TTT'));
        $this->assertSame('tt-test', self::$ref::toKebabCase('TTTest'));
        $this->assertSame('test-test', self::$ref::toKebabCase('testTest'));
        $this->assertSame('test-t-test', self::$ref::toKebabCase('testTTest'));
        $this->assertSame('test-test', self::$ref::toKebabCase('test-test'));
        $this->assertSame('test-test', self::$ref::toKebabCase('test_test'));
        $this->assertSame('test-test', self::$ref::toKebabCase('test test'));
        $this->assertSame('test-test-test', self::$ref::toKebabCase('test test test'));
        $this->assertSame('-test--test--', self::$ref::toKebabCase(' test  test  '));
        $this->assertSame('--test-test-test--', self::$ref::toKebabCase("--test_test-test__"));
    }

    public function test_length(): void
    {
        // empty
        $this->assertSame(0, self::$ref::length(''));

        // ascii
        $this->assertSame(4, self::$ref::length('Test'));
        $this->assertSame(9, self::$ref::length(' T e s t '));

        // utf8
        $this->assertSame(2, self::$ref::length('あい'));
        $this->assertSame(4, self::$ref::length('あいzう'));

        // emoji
        $this->assertSame(1, self::$ref::length('👨‍👨‍👧‍👦'));
    }

    public function test_length_invalid_string(): void
    {
        $this->expectExceptionMessage('Error converting input string to UTF-16');
        $this->expectException(IntlException::class);
        self::$ref::length(substr('あ', 1));
    }

    public function test_matchAll(): void
    {
        $this->assertSame([['a', 'a']], self::$ref::matchAll('abcabc', '/a/'));
        $this->assertSame([['abc', 'abc'], 'p1' => ['a', 'a'], ['a', 'a']], self::$ref::matchAll('abcabc', '/(?<p1>a)bc/'));
        $this->assertSame([[]], self::$ref::matchAll('abcabc', '/bcd/'));
        $this->assertSame([['cd', 'c']], self::$ref::matchAll('abcdxabc', '/c[^x]*/'));
        $this->assertSame([[]], self::$ref::matchAll('abcabcx', '/^abcx/'));
        $this->assertSame([['cx']], self::$ref::matchAll('abcabcx', '/cx$/'));
    }

    public function test_matchAll_without_slashes(): void
    {
        $this->expectWarningMessage('preg_match_all(): Delimiter must not be alphanumeric, backslash, or NUL');
        self::$ref::matchAll('abcabc', 'a');
    }

    public function test_matchFirst(): void
    {
        $this->assertSame('a', self::$ref::matchFirst('abcabc', '/a/'));
        $this->assertSame('abc', self::$ref::matchFirst('abcabc', '/(?<p1>a)bc/'));
        $this->assertSame('cd', self::$ref::matchFirst('abcdxabc', '/c[^x]*/'));
        $this->assertSame('cx', self::$ref::matchFirst('abcabcx', '/cx$/'));
    }

    public function test_matchFirst_no_match(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"aaa" does not match /z/');
        self::$ref::matchFirst('aaa', '/z/');
    }

    public function test_matchFirst_without_slashes(): void
    {
        $this->expectWarningMessage('preg_match(): Delimiter must not be alphanumeric, backslash, or NUL');
        self::$ref::matchFirst('abcabc', 'a');
    }

    public function test_matchFirstOrNull(): void
    {
        $this->assertSame('a', self::$ref::matchFirstOrNull('abcabc', '/a/'));
        $this->assertSame('abc', self::$ref::matchFirstOrNull('abcabc', '/(?<p1>a)bc/'));
        $this->assertSame(null, self::$ref::matchFirstOrNull('abcabc', '/bcd/'));
        $this->assertSame('cd', self::$ref::matchFirstOrNull('abcdxabc', '/c[^x]*/'));
        $this->assertSame(null, self::$ref::matchFirstOrNull('abcabcx', '/^abcx/'));
        $this->assertSame('cx', self::$ref::matchFirstOrNull('abcabcx', '/cx$/'));
    }

    public function test_matchFirstOrNull_without_slashes(): void
    {
        $this->expectWarningMessage('preg_match(): Delimiter must not be alphanumeric, backslash, or NUL');
        self::$ref::matchFirstOrNull('abcabc', 'a');
    }

    public function test_pad(): void
    {
        // empty string
        $this->assertSame('', self::$ref::pad('', -1, '_'));

        // pad string
        $this->assertSame('abc', self::$ref::pad('abc', 3, ''));

        // defaults to pad right
        $this->assertSame('a', self::$ref::pad('a', -1, '_'));
        $this->assertSame('a', self::$ref::pad('a', 0, '_'));
        $this->assertSame('a_', self::$ref::pad('a', 2, '_'));
        $this->assertSame('__', self::$ref::pad('_', 2, '_'));
        $this->assertSame('ab', self::$ref::pad('ab', 1, '_'));

        // overflow padding
        $this->assertSame('abcd', self::$ref::pad('a', 4, 'bcde'));
    }

    public function test_pad_invalid_pad(): void
    {
        $this->expectExceptionMessage('Invalid padding type: 3');
        $this->assertSame('ab', self::$ref::pad('ab', 1, '_', 3));
    }

    public function test_padBoth(): void
    {
        $this->assertSame('a', self::$ref::padBoth('a', -1, '_'));
        $this->assertSame('a', self::$ref::padBoth('a', 0, '_'));
        $this->assertSame('a_', self::$ref::padBoth('a', 2, '_'));
        $this->assertSame('__', self::$ref::padBoth('_', 2, '_'));
        $this->assertSame('_a_', self::$ref::padBoth('a', 3, '_'));
        $this->assertSame('__a__', self::$ref::padBoth('a', 5, '_'));
        $this->assertSame('__a___', self::$ref::padBoth('a', 6, '_'));
        $this->assertSame('12hello123', self::$ref::padBoth('hello', 10, '123'));
        $this->assertSame('いあい', self::$ref::padBoth('あ', 3, 'い'));
    }

    public function test_padEnd(): void
    {
        $this->assertSame('a', self::$ref::padEnd('a', -1, '_'));
        $this->assertSame('a', self::$ref::padEnd('a', 0, '_'));
        $this->assertSame('a_', self::$ref::padEnd('a', 2, '_'));
        $this->assertSame('__', self::$ref::padEnd('_', 2, '_'));
        $this->assertSame('ab', self::$ref::padEnd('ab', 1, '_'));
        $this->assertSame('あいういう', self::$ref::padEnd('あ', 5, 'いう'), 'multi byte');
        $this->assertSame('עִברִיתכן', self::$ref::padEnd('עִברִית', 7, 'כן'), 'rtol languages');
    }

    public function test_padStart(): void
    {
        $this->assertSame('a', self::$ref::padStart('a', -1, '_'));
        $this->assertSame('a', self::$ref::padStart('a', 0, '_'));
        $this->assertSame('_a', self::$ref::padStart('a', 2, '_'));
        $this->assertSame('__', self::$ref::padStart('_', 2, '_'));
        $this->assertSame('ab', self::$ref::padStart('ab', 1, '_'));
        $this->assertSame('いういうあ', self::$ref::padStart('あ', 5, 'いう'), 'multi byte');
    }

    public function test_remove(): void
    {
        $this->assertSame('', self::$ref::remove('', ''), 'empty');
        $this->assertSame('', self::$ref::remove('aaa', 'a'), 'delete everything');
        $this->assertSame('a  a', self::$ref::remove('aaa aa a', 'aa'), 'no traceback check');
        $this->assertSame('no match', self::$ref::remove('no match', 'hctam on'), 'out of order chars');
        $this->assertSame('aa', self::$ref::remove('aa', 'a', 0), 'limit to 0');
        $this->assertSame('a', self::$ref::remove('aaa', 'a', 2), 'limit to 2');

        $count = 0;
        $this->assertSame('aaa', self::$ref::remove('aaa', 'a', 0, $count), 'count none');
        $this->assertSame(0, $count);

        $this->assertSame('a', self::$ref::remove('aaa', 'a', 2, $count), 'count several');
        $this->assertSame(2, $count);

        $this->assertSame('', self::$ref::remove('aaa', 'a', null, $count), 'count unlimited');
        $this->assertSame(3, $count);
    }

    public function test_remove_with_negative_limit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a value greater than or equal to 0. Got: -1');
        self::$ref::remove('', '', -1);
    }

    public function test_repeat(): void
    {
        $this->assertSame('aaa', self::$ref::repeat('a', 3));
        $this->assertSame('', self::$ref::repeat('a', 0));
    }

    public function test_repeat_negative_times(): void
    {
        $this->expectException(\Kirameki\Core\Exceptions\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected: $times >= 0. Got: -1.');
        self::$ref::repeat('a', -1);
    }

    public function test_replace(): void
    {
        $this->assertSame('', self::$ref::replace('', '', ''));
        $this->assertSame('b', self::$ref::replace('b', '', 'a'));
        $this->assertSame('aa', self::$ref::replace('bb', 'b', 'a'));
        $this->assertSame('', self::$ref::replace('b', 'b', ''));
        $this->assertSame('あえいえう', self::$ref::replace('あ-い-う', '-', 'え'));
        $this->assertSame('__🏴󠁧󠁢󠁳󠁣󠁴󠁿', self::$ref::replace('🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿', '🏴󠁧󠁢󠁳󠁣󠁴󠁿a', '_'));

        // slash
        $this->assertSame('abc', self::$ref::replace('ab\c', '\\', ''));

        // dot
        $this->assertSame('abc', self::$ref::replace('abc.*', '.*', ''));

        // regex chars
        $this->assertSame('a', self::$ref::replace('[]/\\!?', '[]/\\!?', 'a'));

        // with limit and count
        $count = 0;
        $this->assertSame('a', self::$ref::replace('aaa', 'a', '', 2, $count));
        $this->assertSame(2, $count);

        // 0 count for no match
        $count = 0;
        $this->assertSame('', self::$ref::replace('', '', '', null, $count));
        $this->assertSame(0, $count);

        // should treat emoji cluster as one character
        $this->assertSame('👋🏿', self::$ref::replace('👋🏿', '👋', ''));
    }

    public function test_replace_with_negative_limit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a value greater than or equal to 0. Got: -1');
        self::$ref::replace('', 'a', 'a', -1);
    }

    public function test_replaceEach(): void
    {
        // empty string
        $this->assertSame('', self::$ref::replaceEach('', ['?'], ['!']));

        // empty search string
        $this->assertSame('test', self::$ref::replaceEach('test', [''], ['a']));

        // replace each ?
        $this->assertSame('x & y', self::$ref::replaceEach('? & ?', ['?', '?'], ['x', 'y']));

        // utf-8
        $this->assertSame('うえ', self::$ref::replaceEach('あい', ['あ', 'い'], ['う', 'え']));

        // should treat emoji cluster as one character
        $this->assertSame('👋🏿', self::$ref::replaceEach('👋🏿', ['👋'], ['']));
    }

    public function test_replaceFirst(): void
    {
        $this->assertSame('', self::$ref::replaceFirst('', '', ''), 'empty string');
        $this->assertSame('bb', self::$ref::replaceFirst('bb', '', 'a'), 'empty search');
        $this->assertSame('abb', self::$ref::replaceFirst('bbb', 'b', 'a'), 'basic');
        $this->assertSame('b', self::$ref::replaceFirst('bb', 'b', ''), 'empty replacement');
        $this->assertSame('あえい-う', self::$ref::replaceFirst('あ-い-う', '-', 'え'), 'mbstring');
        $this->assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿 a', self::$ref::replaceFirst('🏴󠁧󠁢󠁳󠁣󠁴󠁿 👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦', 'a'), 'multiple codepoints');
        $this->assertSame('_🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿', self::$ref::replaceFirst('🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿', '🏴󠁧󠁢󠁳󠁣󠁴󠁿a', '_'));
        $this->assertSame('👋🏿', self::$ref::replaceFirst('👋🏿', '👋', ''), 'treat emoji cluster as one character');

        $replaced = false;
        self::$ref::replaceFirst('bbb', 'b', 'a', $replaced);
        self::assertTrue($replaced, 'validate flag');

        $replaced = true;
        self::$ref::replaceFirst('b', 'z', '', $replaced);
        self::assertFalse($replaced, 'flag is overridden with false');
    }

    public function test_replaceLast(): void
    {
        $this->assertSame('', self::$ref::replaceLast('', '', ''), 'empty string');
        $this->assertSame('bb', self::$ref::replaceLast('bb', '', 'a'), 'empty search');
        $this->assertSame('bba', self::$ref::replaceLast('bbb', 'b', 'a'), 'basic');
        $this->assertSame('b', self::$ref::replaceLast('bb', 'b', ''), 'empty replacement');
        $this->assertSame('あ-いえう', self::$ref::replaceLast('あ-い-う', '-', 'え'), 'mbstring');
        $this->assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿 a', self::$ref::replaceLast('🏴󠁧󠁢󠁳󠁣󠁴󠁿 👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦', 'a'), 'multiple codepoints');
        $this->assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿a_🏴󠁧󠁢󠁳󠁣󠁴󠁿', self::$ref::replaceLast('🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿', '🏴󠁧󠁢󠁳󠁣󠁴󠁿a', '_'));
        $this->assertSame('👋🏿', self::$ref::replaceLast('👋🏿', '👋', ''), 'treat emoji cluster as one character');

        $replaced = false;
        self::$ref::replaceLast('bbb', 'b', 'a', $replaced);
        self::assertTrue($replaced, 'validate flag');

        $replaced = true;
        self::$ref::replaceLast('b', 'z', '', $replaced);
        self::assertFalse($replaced, 'flag is overridden with false');
    }

    public function test_replaceMatch(): void
    {
        $this->assertSame('', self::$ref::replaceMatch('', '', ''));
        $this->assertSame('abb', self::$ref::replaceMatch('abc', '/c/', 'b'));
        $this->assertSame('abbb', self::$ref::replaceMatch('abcc', '/c/', 'b'));
        $this->assertSame('あいい', self::$ref::replaceMatch('あいう', '/う/', 'い'));
        $this->assertSame('x', self::$ref::replaceMatch('abcde', '/[A-Za-z]+/', 'x'));
        $this->assertSame('a-b', self::$ref::replaceMatch('a🏴󠁧󠁢󠁳󠁣󠁴󠁿b', '/🏴󠁧󠁢󠁳󠁣󠁴󠁿/', '-'));

        // with null count no match
        $count = 0;
        $this->assertSame('', self::$ref::replaceMatch('', '', '', null, $count));
        $this->assertSame(0, $count);

        // with null count
        $count = 0;
        $this->assertSame('', self::$ref::replaceMatch('aaa', '/a/', '', null, $count));
        $this->assertSame(3, $count);

        // with counter reset
        $count = 1;
        $this->assertSame('', self::$ref::replaceMatch('aaa', '/a/', '', null, $count));
        $this->assertSame(3, $count);

        // with limit
        $this->assertSame('a', self::$ref::replaceMatch('aaa', '/a/', '', 2));
    }

    public function test_replaceMatch_with_negative_limit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a value greater than or equal to 0. Got: -1');
        self::$ref::replaceMatch('', '/a/', 'a', -1);
    }

    public function test_reverse(): void
    {
        $this->assertSame('', self::$ref::reverse(''));
        $this->assertSame('ba', self::$ref::reverse('ab'));
        $this->assertSame('ういあ', self::$ref::reverse('あいう'));
        $this->assertSame('cbあ🏴󠁧󠁢󠁳󠁣󠁴󠁿', self::$ref::reverse('🏴󠁧󠁢󠁳󠁣󠁴󠁿あbc'));
    }

    public function test_startsWith(): void
    {
        self::assertTrue(self::$ref::startsWith('', ''));
        self::assertTrue(self::$ref::startsWith('bb', ''));
        self::assertTrue(self::$ref::startsWith('bb', 'b'));
        self::assertTrue(self::$ref::startsWith('あ-い-う', 'あ'));
        self::assertFalse(self::$ref::startsWith('👨‍👨‍👧‍👦', '👨‍'));
        self::assertTrue(self::$ref::startsWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿 👨‍👨‍👧‍👦', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
        self::assertFalse(self::$ref::startsWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿 👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦'));
        self::assertTrue(self::$ref::startsWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿', '🏴󠁧󠁢󠁳󠁣󠁴󠁿a'));
        self::assertFalse(self::$ref::startsWith('ba', 'a'));
        self::assertFalse(self::$ref::startsWith('', 'a'));
    }

    public function test_split(): void
    {
        // empty
        $this->assertSame(['', ''], self::$ref::split(' ', ' '));

        // no match
        $this->assertSame(['abc'], self::$ref::split('abc', '_'));

        // match
        $this->assertSame(['a', 'c', 'd'], self::$ref::split('abcbd', 'b'));

        // match utf-8
        $this->assertSame(['あ', 'う'], self::$ref::split('あいう', 'い'));

        // match with limit
        $this->assertSame(['a', 'cbd'], self::$ref::split('abcbd', 'b', 2));

        // match with limit
        $this->assertSame(['a', 'b', 'c'], self::$ref::split('abc', ''));

        // match emoji
        $this->assertSame(['👨‍👨‍👧‍👦'], self::$ref::split('👨‍👨‍👧‍👦', '‍👦'));
    }

    public function test_split_with_negative_limit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a value greater than or equal to 0. Got: -1');
        self::$ref::split('a', 'b', -1);
    }

    public function test_substring(): void
    {
        // empty
        $this->assertSame('', self::$ref::substring('', 0));
        $this->assertSame('', self::$ref::substring('', 0, 1));

        // ascii
        $this->assertSame('abc', self::$ref::substring('abc', 0));
        $this->assertSame('bc', self::$ref::substring('abc', 1));
        $this->assertSame('c', self::$ref::substring('abc', -1));
        $this->assertSame('a', self::$ref::substring('abc', 0, 1));
        $this->assertSame('b', self::$ref::substring('abc', 1, 1));
        $this->assertSame('b', self::$ref::substring('abc', -2, 1));
        $this->assertSame('bc', self::$ref::substring('abc', -2, 2));
        $this->assertSame('ab', self::$ref::substring('abc', -9999, 2));
        $this->assertSame('ab', self::$ref::substring('abc', 0, -1));
        $this->assertSame('a', self::$ref::substring('abc', 0, -2));
        $this->assertSame('', self::$ref::substring('abc', 0, -3));
        $this->assertSame('', self::$ref::substring('abc', 2, -1));

        // utf-8
        $this->assertSame('あいう', self::$ref::substring('あいう', 0));
        $this->assertSame('いう', self::$ref::substring('あいう', 1));
        $this->assertSame('う', self::$ref::substring('あいう', -1));
        $this->assertSame('い', self::$ref::substring('あいう', -2, 1));
        $this->assertSame('いう', self::$ref::substring('あいう', -2, 2));
        $this->assertSame('あい', self::$ref::substring('あいう', -9999, 2));

        // grapheme
        $this->assertSame('👨‍👨‍👧‍👦', self::$ref::substring('👨‍👨‍👧‍👦', 0));
        $this->assertSame('', self::$ref::substring('👨‍👨‍👧‍👦', 1));
        $this->assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿', self::$ref::substring('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿', 1));
        $this->assertSame('👨‍👨‍👧‍👦', self::$ref::substring('👨‍👨‍👧‍👦👨‍👨‍👧‍👦', 1, 1));
        $this->assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿', self::$ref::substring('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿', -1, 1));
    }

    public function test_substring_invalid_input(): void
    {
        $this->expectExceptionMessage('Error converting input string to UTF-16');
        $this->expectException(IntlException::class);
        $this->assertSame('', self::$ref::substring(substr('あ', 1), 0, 2));
    }

    public function test_takeFirst(): void
    {
        // empty string
        $this->assertSame('', self::$ref::takeFirst('', 1));

        // empty string
        $this->assertSame('', self::$ref::takeFirst('', 1));

        // zero amount
        $this->assertSame('', self::$ref::takeFirst('a', 0));

        // mid amount
        $this->assertSame('abcd', self::$ref::takeFirst('abcde', 4));

        // exact length
        $this->assertSame('abc', self::$ref::takeFirst('abc', 3));

        // grapheme
        $this->assertSame('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿', self::$ref::takeFirst('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', 4));

        // grapheme cluster
        $this->assertSame('👋🏿', self::$ref::takeFirst('👋🏿', 1));
    }

    public function test_takeFirst_out_of_range_negative(): void
    {
        $this->expectExceptionMessage('Expected a value greater than or equal to 0. Got: -4');
        self::$ref::takeFirst('abc', -4);
    }

    public function test_takeLast(): void
    {
        // empty string
        $this->assertSame('', self::$ref::takeLast('', 1));

        // empty string
        $this->assertSame('', self::$ref::takeLast('', 1));

        // zero amount
        $this->assertSame('a', self::$ref::takeLast('a', 0));

        // mid amount
        $this->assertSame('bcde', self::$ref::takeLast('abcde', 4));

        // exact length
        $this->assertSame('abc', self::$ref::takeLast('abc', 3));

        // over length
        $this->assertSame('abc', self::$ref::takeLast('abc', 4));

        // grapheme
        $this->assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', self::$ref::takeLast('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', 4));

        // grapheme cluster
        $this->assertSame('👋🏿', self::$ref::takeLast('👋🏿', 1));
    }

    public function test_takeLast_out_of_range_negative(): void
    {
        $this->expectExceptionMessage('Expected a value greater than or equal to 0. Got: -4');
        self::$ref::takeLast('abc', -4);
    }

    public function test_toBool(): void
    {
        self::assertTrue(self::$ref::toBool('true'), 'true as string');
        self::assertTrue(self::$ref::toBool('TRUE'), 'TRUE as string');
        self::assertFalse(self::$ref::toBool('false'), 'false as string');
        self::assertFalse(self::$ref::toBool('FALSE'), 'FALSE as string');
        self::assertTrue(self::$ref::toBool('1'), 'empty as string');
    }

    public function test_toBool_empty(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"" is not a valid boolean string.');
        // empty as string
        self::$ref::toBool('');
    }

    public function test_toBool_with_negative(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"-2" is not a valid boolean string.');
        // invalid boolean (number)
        self::$ref::toBool('-2');
    }

    public function test_toBool_with_yes(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"yes" is not a valid boolean string.');
        // truthy will fail
        self::$ref::toBool('yes');
    }

    public function test_toBoolOrNull(): void
    {
        self::assertTrue(self::$ref::toBoolOrNull('true'), 'true as string');
        self::assertTrue(self::$ref::toBoolOrNull('TRUE'), 'TRUE as string');
        self::assertFalse(self::$ref::toBoolOrNull('false'), 'false as string');
        self::assertFalse(self::$ref::toBoolOrNull('FALSE'), 'FALSE as string');
        self::assertTrue(self::$ref::toBoolOrNull('1'), 'empty as string');
        self::assertNull(self::$ref::toBoolOrNull(''), 'empty as string');
        self::assertNull(self::$ref::toBoolOrNull('-2'), 'invalid boolean (number)');
        self::assertNull(self::$ref::toBoolOrNull('yes'), 'truthy will fail');
    }

    public function test_toCamelCase(): void
    {
        $this->assertSame('test', self::$ref::toCamelCase('test'));
        $this->assertSame('test', self::$ref::toCamelCase('Test'));
        $this->assertSame('testTest', self::$ref::toCamelCase('test-test'));
        $this->assertSame('testTest', self::$ref::toCamelCase('test_test'));
        $this->assertSame('testTest', self::$ref::toCamelCase('test test'));
        $this->assertSame('testTestTest', self::$ref::toCamelCase('test test test'));
        $this->assertSame('testTest', self::$ref::toCamelCase(' test  test  '));
        $this->assertSame('testTestTest', self::$ref::toCamelCase("--test_test-test__"));
    }

    public function test_toFloat(): void
    {
        $this->assertSame(1.0, self::$ref::toFloat('1'), 'positive int');
        $this->assertSame(-1.0, self::$ref::toFloat('-1'), 'negative int');
        $this->assertSame(1.23, self::$ref::toFloat('1.23'), 'positive float');
        $this->assertSame(-1.23, self::$ref::toFloat('-1.23'), 'negative float');
        $this->assertSame(0.0, self::$ref::toFloat('0'), 'zero int');
        $this->assertSame(0.0, self::$ref::toFloat('0.0'), 'zero float');
        $this->assertSame(0.0, self::$ref::toFloat('-0'), 'negative zero int');
        $this->assertSame(0.0, self::$ref::toFloat('-0.0'), 'negative zero float');
        $this->assertSame(0.123, self::$ref::toFloat('0.123'), 'start from zero');
        $this->assertSame(123.456, self::$ref::toFloat('123.456'), 'multiple digits');
        $this->assertSame(1230.0, self::$ref::toFloat('1.23e3'), 'scientific notation with e');
        $this->assertSame(1230.0, self::$ref::toFloat('1.23E3'), 'scientific notation with E');
        $this->assertSame(-1230.0, self::$ref::toFloat('-1.23e3'), 'scientific notation as negative');
        $this->assertSame(1.234, self::$ref::toFloatOrNull('123.4E-2'), 'scientific notation irregular');
        $this->assertSame(1230.0, self::$ref::toFloat('1.23e+3'), 'with +e');
        $this->assertSame(1230.0, self::$ref::toFloat('1.23E+3'), 'with +E');
        $this->assertSame(0.012, self::$ref::toFloat('1.2e-2'), 'with -e');
        $this->assertSame(0.012, self::$ref::toFloat('1.2E-2'), 'with -E');
        self::assertNan(self::$ref::toFloat('NAN'), 'NAN');
        self::assertNan(self::$ref::toFloat('-NAN'), 'Negative NAN');
        self::assertNan(self::$ref::toFloat('NaN'), 'NaN from Javascript');
        self::assertNan(self::$ref::toFloat('-NaN'), 'Negative NaN');
        self::assertInfinite(self::$ref::toFloat('INF'), 'upper case INF');
        self::assertInfinite(self::$ref::toFloat('Infinity'), 'INF from Javascript');
    }

    public function test_toFloat_overflow_e_notation(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Float precision lost for "1e20"');
        self::$ref::toFloat('1e20');
    }

    public function test_toFloat_empty_string(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"" is not a valid float.');
        self::$ref::toFloat('');
    }

    public function test_toFloat_invalid_string(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"1a" is not a valid float.');
        self::$ref::toFloat('1a');
    }

    public function test_toFloat_dot_start(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('".1" is not a valid float.');
        self::$ref::toFloat('.1');
    }

    public function test_toFloat_zero_start(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"00.1" is not a valid float.');
        self::$ref::toFloat('00.1');
    }

    public function test_toFloat_overflow_number(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Float precision lost for "1.11111111111111"');
        self::$ref::toFloat('1.' . str_repeat('1', 14));
    }

    public function test_toFloatOrNull(): void
    {
        $this->assertSame(1.0, self::$ref::toFloatOrNull('1'), 'positive int');
        $this->assertSame(-1.0, self::$ref::toFloatOrNull('-1'), 'negative int');
        $this->assertSame(1.23, self::$ref::toFloatOrNull('1.23'), 'positive float');
        $this->assertSame(-1.23, self::$ref::toFloatOrNull('-1.23'), 'negative float');
        $this->assertSame(0.0, self::$ref::toFloatOrNull('0'), 'zero int');
        $this->assertSame(0.0, self::$ref::toFloatOrNull('0.0'), 'zero float');
        $this->assertSame(0.0, self::$ref::toFloatOrNull('-0'), 'negative zero int');
        $this->assertSame(0.0, self::$ref::toFloatOrNull('-0.0'), 'negative zero float');
        $this->assertSame(0.123, self::$ref::toFloatOrNull('0.123'), 'start from zero');
        $this->assertSame(123.456, self::$ref::toFloatOrNull('123.456'), 'multiple digits');
        $this->assertSame(1230.0, self::$ref::toFloatOrNull('1.23e3'), 'scientific notation with e');
        $this->assertSame(1230.0, self::$ref::toFloatOrNull('1.23E3'), 'scientific notation with E');
        $this->assertSame(-1230.0, self::$ref::toFloatOrNull('-1.23e3'), 'scientific notation as negative');
        $this->assertSame(1230.0, self::$ref::toFloatOrNull('1.23e+3'), 'with +e');
        $this->assertSame(1230.0, self::$ref::toFloatOrNull('1.23E+3'), 'with +E');
        $this->assertSame(0.012, self::$ref::toFloatOrNull('1.2e-2'), 'with -e');
        $this->assertSame(0.012, self::$ref::toFloatOrNull('1.2E-2'), 'with -E');
        $this->assertSame(1.234, self::$ref::toFloatOrNull('123.4E-2'), 'scientific notation irregular');
        self::assertNull(self::$ref::toFloatOrNull('1e+20'), 'overflowing +e notation');
        self::assertNull(self::$ref::toFloatOrNull('1e-20'), 'overflowing -e notation');
        self::assertNull(self::$ref::toFloatOrNull('nan'), 'Lowercase nan is not NAN');
        self::assertNan(self::$ref::toFloatOrNull('NAN'), 'NAN');
        self::assertNan(self::$ref::toFloatOrNull('-NAN'), 'Negative NAN');
        self::assertNan(self::$ref::toFloatOrNull('NaN'), 'NaN from Javascript');
        self::assertNan(self::$ref::toFloatOrNull('-NaN'), 'Negative NaN');
        self::assertNull(self::$ref::toFloatOrNull('inf'), 'Lowercase inf is not INF');
        self::assertInfinite(self::$ref::toFloatOrNull('INF'), 'upper case INF');
        self::assertInfinite(self::$ref::toFloatOrNull('Infinity'), 'INF from Javascript');
        self::assertNull(self::$ref::toFloatOrNull(''), 'empty');
        self::assertNull(self::$ref::toFloatOrNull('a1'), 'invalid string');
        self::assertNull(self::$ref::toFloatOrNull('01.1'), 'zero start');
        self::assertNull(self::$ref::toFloatOrNull('.1'), 'dot start');
        self::assertNull(self::$ref::toFloatOrNull('1.' . str_repeat('1', 100)), 'overflow');
    }

    public function test_toInt(): void
    {
        $this->assertSame(123, self::$ref::toIntOrNull('123'));
    }

    public function test_toInt_blank(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"" is not a valid integer.');
        self::$ref::toInt('');
    }

    public function test_toInt_float(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"1.0" is not a valid integer.');
        self::$ref::toInt('1.0');
    }

    public function test_toInt_with_e_notation(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"1.23E+3" is not a valid integer.');
        self::$ref::toInt('1.23E+3');
    }

    public function test_toInt_float_with_e_notation(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"1.0e-2" is not a valid integer.');
        self::$ref::toInt('1.0e-2');
    }

    public function test_toInt_zero_start(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"01" is not a valid integer.');
        self::$ref::toInt('01');
    }

    public function test_toInt_not_compatible(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"a1" is not a valid integer.');
        self::$ref::toInt('a1');
    }

    public function test_toInt_positive_overflow(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"11111111111111111111" is not a valid integer.');
        self::$ref::toInt(str_repeat('1', 20));
    }

    public function test_toInt_negative_overflow(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('"-11111111111111111111" is not a valid integer.');
        self::$ref::toInt('-' . str_repeat('1', 20));
    }

    public function test_toIntOrNull(): void
    {
        $this->assertSame(123, self::$ref::toIntOrNull('123'));
        self::assertNull(self::$ref::toIntOrNull(str_repeat('1', 20)), 'overflow positive');
        self::assertNull(self::$ref::toIntOrNull('-' . str_repeat('1', 20)), 'overflow positive');
        self::assertNull(self::$ref::toIntOrNull(''), 'blank');
        self::assertNull(self::$ref::toIntOrNull('1.0'), 'float value');
        self::assertNull(self::$ref::toIntOrNull('1.0e-2'), 'float value with e notation');
        self::assertNull(self::$ref::toIntOrNull('a1'), 'invalid string');
        self::assertNull(self::$ref::toIntOrNull('01'), 'zero start');
    }

    public function test_toLowerCase(): void
    {
        // empty (nothing happens)
        $this->assertSame('', self::$ref::toLowerCase(''));

        // basic
        $this->assertSame('abc', self::$ref::toLowerCase('ABC'));

        // utf-8 chars (nothing happens)
        $this->assertSame('あいう', self::$ref::toLowerCase('あいう'));

        // utf-8 special chars
        $this->assertSame('çği̇öşü', self::$ref::toLowerCase('ÇĞİÖŞÜ'));

        // grapheme (nothing happens)
        $this->assertSame('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿', self::$ref::toLowerCase('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
    }

    public function test_toPascalCase(): void
    {
        $this->assertSame('A', self::$ref::toPascalCase('a'));
        $this->assertSame('TestMe', self::$ref::toPascalCase('test_me'));
        $this->assertSame('TestMe', self::$ref::toPascalCase('test-me'));
        $this->assertSame('TestMe', self::$ref::toPascalCase('test me'));
        $this->assertSame('TestMe', self::$ref::toPascalCase('testMe'));
        $this->assertSame('TestMe', self::$ref::toPascalCase('TestMe'));
        $this->assertSame('TestMe', self::$ref::toPascalCase(' test_me '));
        $this->assertSame('TestMeNow!', self::$ref::toPascalCase('test_me now-!'));
    }

    public function test_toSnakeCase(): void
    {
        // empty
        $this->assertSame('', self::$ref::toSnakeCase(''));

        // no-change
        $this->assertSame('abc', self::$ref::toSnakeCase('abc'));

        // case
        $this->assertSame('the_test_for_case', self::$ref::toSnakeCase('the test for case'));
        $this->assertSame('the_test_for_case', self::$ref::toSnakeCase('the-test-for-case'));
        $this->assertSame('the_test_for_case', self::$ref::toSnakeCase('theTestForCase'));
        $this->assertSame('ttt', self::$ref::toSnakeCase('TTT'));
        $this->assertSame('tt_t', self::$ref::toSnakeCase('TtT'));
        $this->assertSame('tt_t', self::$ref::toSnakeCase('TtT'));
        $this->assertSame('the__test', self::$ref::toSnakeCase('the  test'));
        $this->assertSame('__test', self::$ref::toSnakeCase('  test'));
        $this->assertSame("test\nabc", self::$ref::toSnakeCase("test\nabc"));
        $this->assertSame('__test_test_test__', self::$ref::toSnakeCase("--test_test-test__"));
    }

    public function test_toUpperCase(): void
    {
        // empty (nothing happens)
        $this->assertSame('', self::$ref::toUpperCase(''));

        // basic
        $this->assertSame('ABC', self::$ref::toUpperCase('abc'));

        // utf-8 chars (nothing happens)
        $this->assertSame('あいう', self::$ref::toUpperCase('あいう'));

        // utf-8 special chars
        $this->assertSame('ÇĞİÖŞÜ', self::$ref::toUpperCase('çği̇öşü'));

        // grapheme (nothing happens)
        $this->assertSame('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿', self::$ref::toUpperCase('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
    }

    public function test_trim(): void
    {
        // empty (nothing happens)
        $this->assertSame('', self::$ref::trim(''));

        // left only
        $this->assertSame('a', self::$ref::trim("\ta"));

        // right only
        $this->assertSame('a', self::$ref::trim("a\t"));

        // new line on both ends
        $this->assertSame('abc', self::$ref::trim("\nabc\n"));

        // tab and mixed line on both ends
        $this->assertSame('abc', self::$ref::trim("\t\nabc\n\t"));

        // tab and mixed line on both ends
        $this->assertSame('abc', self::$ref::trim("\t\nabc\n\t"));

        // multibyte spaces (https://3v4l.org/s16FF)
        $this->assertSame('abc', self::$ref::trim("\u{2000}\u{2001}abc\u{2002}\u{2003}"));

        // grapheme (nothing happens)
        $this->assertSame('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿', self::$ref::trim('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿'));

        // custom
        $this->assertSame('b', self::$ref::trim('aba', 'a'));

        // custom empty
        $this->assertSame('a', self::$ref::trim('a', ''));

        // custom overrides delimiter
        $this->assertSame("\nb\n", self::$ref::trim("a\nb\na", 'a'));

        // custom multiple
        $this->assertSame('b', self::$ref::trim("_ab_a_", 'a_'));
    }

    public function test_trimEnd(): void
    {
        // empty (nothing happens)
        $this->assertSame('', self::$ref::trimEnd(''));

        // left only
        $this->assertSame("\ta", self::$ref::trimEnd("\ta"));

        // right only
        $this->assertSame('a', self::$ref::trimEnd("a\t"));

        // new line on both ends
        $this->assertSame("\nabc", self::$ref::trimEnd("\nabc\n"));

        // tab and mixed line on both ends
        $this->assertSame('abc', self::$ref::trimEnd("abc\n\t"));

        // multibyte spaces (https://3v4l.org/s16FF)
        $this->assertSame(' abc', self::$ref::trimEnd(" abc\n\t\u{0009}\u{2028}\u{2029}\v "));

        // grapheme (nothing happens)
        $this->assertSame('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿', self::$ref::trimEnd('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿'));

        // custom
        $this->assertSame('ab', self::$ref::trimEnd('aba', 'a'));

        // custom empty
        $this->assertSame('a', self::$ref::trimEnd('a', ''));

        // custom overrides delimiter
        $this->assertSame("ab\n", self::$ref::trimEnd("ab\na", 'a'));

        // custom multiple
        $this->assertSame('_ab', self::$ref::trimEnd("_ab_a_", 'a_'));
    }

    public function test_trimStart(): void
    {
        // empty (nothing happens)
        $this->assertSame('', self::$ref::trimStart(''));

        // left only
        $this->assertSame("a", self::$ref::trimStart("\ta"));

        // right only
        $this->assertSame("a\t", self::$ref::trimStart("a\t"));

        // new line on both ends
        $this->assertSame("abc\n", self::$ref::trimStart("\nabc\n"));

        // tab and new line
        $this->assertSame('abc', self::$ref::trimStart("\n\tabc"));

        // multibyte spaces (https://3v4l.org/s16FF)
        $this->assertSame('abc ', self::$ref::trimStart("\n\t\u{0009}\u{2028}\u{2029}\v abc "));

        // grapheme (nothing happens)
        $this->assertSame('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿', self::$ref::trimStart('👨‍👨‍👧‍👦🏴󠁧󠁢󠁳󠁣󠁴󠁿'));

        // custom
        $this->assertSame('ba', self::$ref::trimStart('aba', 'a'));

        // custom empty
        $this->assertSame('a', self::$ref::trimStart('a', ''));

        // custom overrides delimiter
        $this->assertSame("\nba", self::$ref::trimStart("a\nba", 'a'));

        // custom multiple
        $this->assertSame('b_a_', self::$ref::trimStart("_ab_a_", 'a_'));
    }

    public function test_withPrefix(): void
    {
        // empty string always adds
        $this->assertSame('foo', self::$ref::withPrefix('', 'foo'));

        // empty start does nothing
        $this->assertSame('foo', self::$ref::withPrefix('foo', ''));

        // has match
        $this->assertSame('foo', self::$ref::withPrefix('foo', 'f'));

        // no match
        $this->assertSame('_foo', self::$ref::withPrefix('foo', '_'));

        // partial matching doesn't count
        $this->assertSame('___foo', self::$ref::withPrefix('_foo', '__'));

        // repeats handled properly
        $this->assertSame('__foo', self::$ref::withPrefix('__foo', '_'));

        // try escape chars
        $this->assertSame('\s foo', self::$ref::withPrefix(' foo', "\s"));

        // new line
        $this->assertSame("\n foo", self::$ref::withPrefix(' foo', "\n"));

        // slashes
        $this->assertSame('/foo', self::$ref::withPrefix('foo', '/'));

        // utf8 match
        $this->assertSame('あい', self::$ref::withPrefix('あい', 'あ'));

        // utf8 no match
        $this->assertSame('うえあい', self::$ref::withPrefix('あい', 'うえ'));

        // grapheme (treats combined grapheme as 1 whole character)
        $this->assertSame('👨👨‍👨‍👧‍👧', self::$ref::withPrefix('👨‍👨‍👧‍👧', '👨'));
    }

    public function test_withSuffix(): void
    {
        // empty string always adds
        $this->assertSame('foo', self::$ref::withSuffix('', 'foo'));

        // empty start does nothing
        $this->assertSame('foo', self::$ref::withSuffix('foo', ''));

        // has match
        $this->assertSame('foo', self::$ref::withSuffix('foo', 'oo'));

        // no match
        $this->assertSame('foo bar', self::$ref::withSuffix('foo', ' bar'));

        // partial matching doesn't count
        $this->assertSame('foo___', self::$ref::withSuffix('foo_', '__'));

        // repeats handled properly
        $this->assertSame('foo__', self::$ref::withSuffix('foo__', '_'));

        // try escape chars
        $this->assertSame('foo \s', self::$ref::withSuffix('foo ', "\s"));

        // new line
        $this->assertSame("foo \n", self::$ref::withSuffix('foo ', "\n"));

        // slashes
        $this->assertSame('foo/', self::$ref::withSuffix('foo', '/'));

        // utf8 match
        $this->assertSame('あい', self::$ref::withSuffix('あい', 'い'));

        // utf8 no match
        $this->assertSame('あいうえ', self::$ref::withSuffix('あい', 'うえ'));

        // grapheme (treats combined grapheme as 1 whole character)
        $this->assertSame('👨‍👨‍👧‍👧‍👧‍', self::$ref::withSuffix('👨‍👨‍👧‍👧‍', '👧‍'));
    }

    public function test_wrap(): void
    {
        // blanks
        $this->assertSame('', self::$ref::wrap('', '', ''));

        // simple case
        $this->assertSame('[a]', self::$ref::wrap('a', '[', ']'));

        // multibyte
        $this->assertSame('１a２', self::$ref::wrap('a', '１', '２'));

        // grapheme
        $this->assertSame('👨‍👨‍👧‍a🏴󠁧󠁢󠁳󠁣󠁴󠁿', self::$ref::wrap('a', '👨‍👨‍👧‍', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
    }
}
