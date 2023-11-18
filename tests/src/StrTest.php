<?php declare(strict_types=1);

namespace Tests\Kirameki\Text;

use Kirameki\Core\Exceptions\InvalidArgumentException;
use Kirameki\Core\Testing\TestCase;
use Kirameki\Text\Exceptions\NoMatchException;
use Kirameki\Text\Str;
use function array_pop;
use function array_shift;
use function dump;
use function strlen;
use const PHP_EOL;
use const STR_PAD_LEFT;

class StrTest extends TestCase
{
    protected static Str $ref;

    protected function setUp(): void
    {
        parent::setUp();
        self::$ref = new Str();
    }

    public function test_after(): void
    {
        $this->assertSame('est', self::$ref::after('test', 't'), 'match first');
        $this->assertSame('', self::$ref::after('test1', '1'), 'match last');
        $this->assertSame('test', self::$ref::after('test', ''), 'match empty string');
        $this->assertSame('test', self::$ref::after('test', 'a'), 'no match');
        $this->assertSame('うえ', self::$ref::after('ああいうえ', 'い'), 'multi byte');
        $this->assertSame('def', self::$ref::after('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿def', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'), 'grapheme');
        $this->assertSame('🏿', self::$ref::after('👋🏿', '👋'), 'grapheme cluster');
    }

    public function test_afterLast(): void
    {
        $this->assertSame('bc', self::$ref::afterLast('abc', 'a'), 'match first (single occurrence)');
        $this->assertSame('1', self::$ref::afterLast('test1', 't'), 'match first (multiple occurrence)');
        $this->assertSame('', self::$ref::afterLast('test1', '1'), 'match last');
        $this->assertSame('Foo', self::$ref::afterLast('----Foo', '---'), 'should match the last string');
        $this->assertSame('test', self::$ref::afterLast('test', ''), 'match empty string');
        $this->assertSame('test', self::$ref::afterLast('test', 'a'), 'no match');
        $this->assertSame('え', self::$ref::afterLast('ああいういえ', 'い'), 'multi byte');
        $this->assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿f', self::$ref::afterLast('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', 'e'), 'grapheme');
        $this->assertSame('🏿', self::$ref::afterLast('👋🏿', '👋'), 'grapheme cluster');
    }

    public function test_before(): void
    {
        $this->assertSame('a', self::$ref::before('abc', 'b'), 'match first (single occurrence)');
        $this->assertSame('a', self::$ref::before('abc-abc', 'b'), 'match first (multiple occurrence)');
        $this->assertSame('test', self::$ref::before('test1', '1'), 'match last');
        $this->assertSame('test', self::$ref::before('test123', '12'), 'match multiple chars');
        $this->assertSame('test', self::$ref::before('test', ''), 'match empty string');
        $this->assertSame('test', self::$ref::before('test', 'a'), 'no match');
        $this->assertSame('ああ', self::$ref::before('ああいういえ', 'い'), 'multi byte');
        $this->assertSame('abc', self::$ref::before('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'), 'grapheme substring');
        $this->assertSame('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿', self::$ref::before('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', 'e'), 'grapheme string');
        $this->assertSame('👋', self::$ref::before('👋🏿', '🏿'), 'substring is grapheme codepoint');
    }

    public function test_beforeLast(): void
    {
        $this->assertSame('a', self::$ref::beforeLast('abc', 'b'), 'match first (single occurrence)');
        $this->assertSame('abc-a', self::$ref::beforeLast('abc-abc', 'b'), 'match first (multiple occurrence)');
        $this->assertSame('test', self::$ref::beforeLast('test1', '1'), 'match last');
        $this->assertSame('test', self::$ref::beforeLast('test', ''), 'match empty string');
        $this->assertSame('test', self::$ref::beforeLast('test', 'a'), 'no match');
        $this->assertSame('ああいう', self::$ref::beforeLast('ああいういえ', 'い'), 'multi byte');
        $this->assertSame('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e', self::$ref::beforeLast('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿d🏴󠁧󠁢󠁳󠁣󠁴󠁿e🏴󠁧󠁢󠁳󠁣󠁴󠁿f', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'), 'substring is grapheme');
        $this->assertSame('👋', self::$ref::beforeLast('👋🏿', '🏿'), 'substring is grapheme codepoint');
    }

    public function test_between(): void
    {
        $this->assertSame('1', self::$ref::between('test(1)', '(', ')'), 'basic');
        $this->assertSame('', self::$ref::between('()', '(', ')'), 'match edge: nothing in between');
        $this->assertSame('1', self::$ref::between('(1)', '(', ')'), 'match edge: char in between');
        $this->assertSame('test)', self::$ref::between('test)', '(', ')'), 'missing from');
        $this->assertSame('test(', self::$ref::between('test(', '(', ')'), 'missing to');
        $this->assertSame('test(1', self::$ref::between('(test(1))', '(', ')'), 'nested');
        $this->assertSame('1', self::$ref::between('(1) to (2)', '(', ')'), 'multi occurrence');
        $this->assertSame('_ab_', self::$ref::between('ab_ab_ba_ba', 'ab', 'ba'), 'multi char');
        $this->assertSame('い', self::$ref::between('あいういう', 'あ', 'う'), 'utf8');
        $this->assertSame('😃', self::$ref::between('👋🏿😃👋🏿😃👋🏿', '👋🏿', '👋🏿'), 'substring is grapheme');
        $this->assertSame('', self::$ref::between('👋🏿', '👋', '🏿'), 'grapheme between codepoints');
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
        $this->assertSame('1', self::$ref::betweenFurthest('test(1)', '(', ')'), 'basic');
        $this->assertSame('', self::$ref::betweenFurthest('()', '(', ')'), 'match edge: nothing in between');
        $this->assertSame('1', self::$ref::betweenFurthest('(1)', '(', ')'), 'match edge: char in between');
        $this->assertSame('test)', self::$ref::betweenFurthest('test)', '(', ')'), 'missing from');
        $this->assertSame('test(', self::$ref::betweenFurthest('test(', '(', ')'), 'missing to');
        $this->assertSame('test(1)', self::$ref::betweenFurthest('(test(1))', '(', ')'), 'nested');
        $this->assertSame('1) to (2', self::$ref::betweenFurthest('(1) to (2)', '(', ')'), 'multi occurrence');
        $this->assertSame('_', self::$ref::betweenFurthest('ab_ba', 'ab', 'ba'), 'multi char');
        $this->assertSame('い', self::$ref::betweenFurthest('あいう', 'あ', 'う'), 'utf8');
        $this->assertSame('😃', self::$ref::betweenFurthest('👋🏿😃👋🏿😃', '👋🏿', '👋🏿'), 'grapheme');
        $this->assertSame('', self::$ref::between('👋🏿', '👋', '🏿'), 'grapheme between codepoints');
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
        $this->assertSame('1', self::$ref::betweenLast('test(1)', '(', ')'), 'basic');
        $this->assertSame('', self::$ref::betweenLast('()', '(', ')'), 'match edge: nothing in between');
        $this->assertSame('1', self::$ref::betweenLast('(1)', '(', ')'), 'match edge: char in between');
        $this->assertSame('test)', self::$ref::between('test)', '(', ')'), 'missing from');
        $this->assertSame('test(', self::$ref::between('test(', '(', ')'), 'missing to');
        $this->assertSame('1)', self::$ref::betweenLast('(test(1))', '(', ')'), 'nested');
        $this->assertSame('2', self::$ref::betweenLast('(1) to (2)', '(', ')'), 'multi occurrence');
        $this->assertSame('_ba_', self::$ref::betweenLast('ab_ab_ba_ba', 'ab', 'ba'), 'multi char');
        $this->assertSame('いうい', self::$ref::betweenLast('あいういう', 'あ', 'う'), 'utf8');
        $this->assertSame('🥹', self::$ref::betweenLast('👋🏿😃👋🏿🥹👋', '👋🏿', '👋'), 'grapheme');
        $this->assertSame('', self::$ref::between('👋🏿', '👋', '🏿'), 'grapheme between codepoints');
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

    public function test_capitalize(): void
    {
        $this->assertSame('', self::$ref::capitalize(''), 'empty');
        $this->assertSame('TT', self::$ref::capitalize('TT'), 'all uppercase');
        $this->assertSame('Test', self::$ref::capitalize('test'), 'lowercase');
        $this->assertSame('Test abc', self::$ref::capitalize('test abc'), 'lowercase with spaces');
        $this->assertSame(' test abc', self::$ref::capitalize(' test abc'), 'lowercase with spaces and leading space');
        $this->assertSame('àbc', self::$ref::capitalize('àbc'), 'lowercase with accent');
        $this->assertSame('é', self::$ref::capitalize('é'), 'lowercase with accent');
        $this->assertSame('ゅ', self::$ref::capitalize('ゅ'), 'lowercase with hiragana');
        $this->assertSame('🏴󠁧󠁢󠁳󠁣󠁴󠁿', self::$ref::capitalize('🏴󠁧󠁢󠁳󠁣󠁴󠁿'), 'lowercase with emoji');
    }

    public function test_chunk(): void
    {
        $this->assertSame([], self::$ref::chunk('', 5), 'empty');
        $this->assertSame(['ab'], self::$ref::chunk('ab', 5), 'oversize');
        $this->assertSame(['ab'], self::$ref::chunk('ab', 2), 'exact');
        $this->assertSame(['ab', 'c'], self::$ref::chunk('abc', 2), 'fragment');
        $this->assertSame(['あ', 'い', 'う'], self::$ref::chunk('あいう', 3), 'utf8');
        $this->assertSame(['ab', 'cd', 'efg'], self::$ref::chunk('abcdefg', 2, 2), 'limit');

        $chunked = self::$ref::chunk('あ', 2);
        $this->assertSame(2, strlen($chunked[0]), 'invalid');
        $this->assertSame(1, strlen($chunked[1]), 'invalid');
    }

    public function test_chunk_with_invalid_size(): void
    {
        $this->expectExceptionMessage('Expected: $size >= 1. Got: 0.');
        self::$ref::chunk('abc', 0);
    }

    public function test_chunk_with_invalid_limit(): void
    {
        $this->expectExceptionMessage('Expected: $limit >= 0. Got: -1.');
        self::$ref::chunk('abc', 2, -1);
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
        $this->assertTrue(self::$ref::contains('👨‍👨‍👧‍👧‍', '👨'), 'grapheme partial');
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
        $this->assertTrue(self::$ref::containsAll('👨‍👨‍👧‍👧‍', ['👨', '👧']), 'grapheme partial');
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
        $this->assertTrue(self::$ref::containsAny('👨‍👨‍👧‍👧‍', ['👨', '🐌']), 'grapheme partial');
        $this->assertFalse(self::$ref::containsAny('👨‍👨‍👧‍👧‍', ['👀', '🐌']), 'grapheme no match');
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
        $this->assertFalse(self::$ref::containsNone('👨‍👨‍👧‍👧‍', ['👀', '👨']), 'grapheme partial');
        $this->assertTrue(self::$ref::containsNone('👨‍👨‍👧‍👧‍', ['👀', '🐌']), 'grapheme no match');
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
        $this->assertTrue(self::$ref::containsPattern('👨‍👨‍👧‍👧‍', '/👨/'));
    }

    public function test_containsPattern_warning_as_error(): void
    {
        $this->expectWarningMessage('preg_match(): Unknown modifier \'a\'');
        $this->assertFalse(self::$ref::containsPattern('', '/a/a'));
    }

    public function test_count(): void
    {
        $this->assertSame(0, self::$ref::count('', 'aaa'), 'empty string');
        $this->assertSame(1, self::$ref::count('abc', 'abc'), 'exact match');
        $this->assertSame(0, self::$ref::count('ab', 'abc'), 'no match');
        $this->assertSame(1, self::$ref::count('This is a cat', ' is '), 'single match');
        $this->assertSame(2, self::$ref::count('This is a cat', 'is'), 'multi match');
        $this->assertSame(2, self::$ref::count('abababa', 'aba'), 'no overlapping');
        $this->assertSame(2, self::$ref::count('あいあ', 'あ'), 'utf8');
        $this->assertSame(1, self::$ref::count('あああ', 'ああ'), 'utf8 no overlapping');
        $this->assertSame(0, self::$ref::count('ア', 'ｱ'), 'check half-width is not counted.');
        $this->assertSame(1, self::$ref::count('👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦'), 'grapheme');
        $this->assertSame(2, self::$ref::count('👨‍👨‍👧‍👦', '👨'), 'grapheme subset will match');
        $this->assertSame(3, self::$ref::count('abababa', 'aba', true), 'overlapping');
        $this->assertSame(2, self::$ref::count('あああ', 'ああ', true), 'utf8 overlapping');
        $this->assertSame(2, self::$ref::count('👨‍👨‍👧‍👦👨‍👨‍👧‍👦👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦👨‍👨‍👧‍👦', true), 'grapheme overlapping');
    }

    public function test_count_with_empty_search(): void
    {
        $this->expectExceptionMessage('$substring must not be empty.');
        $this->assertFalse(self::$ref::count('a', ''));
    }

    public function test_decapitalize(): void
    {
        $this->assertSame('', self::$ref::decapitalize(''));
        $this->assertSame('test', self::$ref::decapitalize('Test'));
        $this->assertSame('t T', self::$ref::decapitalize('T T'));
        $this->assertSame(' T ', self::$ref::decapitalize(' T '));
        $this->assertSame('Éé', self::$ref::decapitalize('Éé'));
        $this->assertSame('🔡', self::$ref::decapitalize('🔡'));
    }

    public function test_doesNotContain(): void
    {
        $this->assertTrue(self::$ref::doesNotContain('abcde', 'ac'));
        $this->assertFalse(self::$ref::doesNotContain('abcde', 'ab'));
        $this->assertFalse(self::$ref::doesNotContain('a', ''));
        $this->assertTrue(self::$ref::doesNotContain('', 'a'));
        $this->assertFalse(self::$ref::doesNotContain('👨‍👨‍👧‍👧‍', '👨'));
    }

    public function test_doesNotEndWith(): void
    {
        $this->assertFalse(self::$ref::doesNotEndWith('abc', 'c'));
        $this->assertTrue(self::$ref::doesNotEndWith('abc', 'b'));
        $this->assertFalse(self::$ref::doesNotEndWith('aabbcc', 'cc'));
        $this->assertFalse(self::$ref::doesNotEndWith('aabbcc' . PHP_EOL, PHP_EOL));
        $this->assertFalse(self::$ref::doesNotEndWith('abc0', '0'));
        $this->assertFalse(self::$ref::doesNotEndWith('abcfalse', 'false'));
        $this->assertFalse(self::$ref::doesNotEndWith('a', ''));
        $this->assertFalse(self::$ref::doesNotEndWith('', ''));
        $this->assertFalse(self::$ref::doesNotEndWith('あいう', 'う'));
        $this->assertTrue(self::$ref::doesNotEndWith("あ\n", 'あ'));
        $this->assertFalse(self::$ref::doesNotEndWith('👋🏻', '🏻'));
    }

    public function test_doesNotStartWith(): void
    {
        $this->assertFalse(self::$ref::doesNotStartWith('', ''));
        $this->assertFalse(self::$ref::doesNotStartWith('bb', ''));
        $this->assertFalse(self::$ref::doesNotStartWith('bb', 'b'));
        $this->assertTrue(self::$ref::doesNotStartWith('bb', 'ab'));
        $this->assertFalse(self::$ref::doesNotStartWith('あ-い-う', 'あ'));
        $this->assertTrue(self::$ref::doesNotStartWith('あ-い-う', 'え'));
        $this->assertFalse(self::$ref::doesNotStartWith('👨‍👨‍👧‍👦', '👨‍'));
        $this->assertFalse(self::$ref::doesNotStartWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿 👨‍👨‍👧‍👦', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
        $this->assertTrue(self::$ref::doesNotStartWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿 👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦'));
        $this->assertFalse(self::$ref::doesNotStartWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿', '🏴󠁧󠁢󠁳󠁣󠁴󠁿a'));
        $this->assertTrue(self::$ref::doesNotStartWith('ba', 'a'));
        $this->assertTrue(self::$ref::doesNotStartWith('', 'a'));
        $this->assertTrue(self::$ref::doesNotStartWith("\nあ", 'あ'));
    }

    public function test_dropFirst(): void
    {
        $this->assertSame('', self::$ref::dropFirst('', 1), 'empty');
        $this->assertSame('a', self::$ref::dropFirst('a', 0), 'zero amount');
        $this->assertSame('e', self::$ref::dropFirst('abcde', 4), 'mid amount');
        $this->assertSame('', self::$ref::dropFirst('abc', 3), 'exact amount');
        $this->assertSame('', self::$ref::dropFirst('abc', 4), 'over overflow');
        $this->assertSame('👦', self::$ref::dropFirst('👨‍👨‍👧‍👦', 21), 'grapheme');
        $this->assertSame('🏿', self::$ref::dropFirst('👋🏿', 4), 'grapheme cluster (positive)');
    }

    public function test_dropFirst_negative_amount(): void
    {
        $this->expectExceptionMessage('Expected: $amount >= 0. Got: -4.');
        self::$ref::dropFirst('abc', -4);
    }

    public function test_dropLast(): void
    {
        $this->assertSame('', self::$ref::dropLast('', 1), 'empty');
        $this->assertSame('a', self::$ref::dropLast('a', 0), 'zero length');
        $this->assertSame('ab', self::$ref::dropLast('abc', 1), 'mid amount');
        $this->assertSame('', self::$ref::dropLast('abc', 3), 'exact amount');
        $this->assertSame('', self::$ref::dropLast('abc', 4), 'overflow');
        $this->assertSame('👨‍👨‍👧‍', self::$ref::dropLast('👨‍👨‍👧‍👦', 4), 'grapheme');
        $this->assertSame('👋', self::$ref::dropLast('👋🏿', 4), 'grapheme cluster (positive)');
    }

    public function test_dropLast_negative_amount(): void
    {
        $this->expectExceptionMessage('Expected: $amount >= 0. Got: -4.');
        self::$ref::dropLast('abc', -4);
    }

    public function test_endsWith(): void
    {
        $this->assertTrue(self::$ref::endsWith('abc', 'c'), 'single hit');
        $this->assertFalse(self::$ref::endsWith('abc', 'b'), 'single miss');
        $this->assertTrue(self::$ref::endsWith('aabbcc', 'cc'), 'multiple occurrence string');
        $this->assertTrue(self::$ref::endsWith('aabbcc' . PHP_EOL, PHP_EOL), 'newline');
        $this->assertTrue(self::$ref::endsWith('abc0', '0'), 'zero');
        $this->assertTrue(self::$ref::endsWith('abcfalse', 'false'), 'false');
        $this->assertTrue(self::$ref::endsWith('a', ''), 'empty needle');
        $this->assertTrue(self::$ref::endsWith('', ''), 'empty haystack and needle');
        $this->assertTrue(self::$ref::endsWith('あいう', 'う'), 'utf8');
        $this->assertFalse(self::$ref::endsWith("あ\n", 'あ'), 'utf8 newline');
        $this->assertTrue(self::$ref::endsWith('👋🏻', '🏻'), 'grapheme');
    }

    public function test_endsWithAny(): void
    {
        $this->assertTrue(self::$ref::endsWithAny('abc', ['c']), 'array hit');
        $this->assertTrue(self::$ref::endsWithAny('abc', ['a', 'b', 'c']), 'array hit with misses');
        $this->assertFalse(self::$ref::endsWithAny('abc', ['a', 'b']), 'array miss');
        $this->assertTrue(self::$ref::endsWithAny('👋🏿', ['🏿', 'a']), 'array miss');
    }

    public function test_endsWithNone(): void
    {
        $this->assertFalse(self::$ref::endsWithNone('abc', ['c']));
        $this->assertFalse(self::$ref::endsWithNone('abc', ['a', 'b', 'c']));
        $this->assertTrue(self::$ref::endsWithNone('abc', ['a', 'b']));
        $this->assertfalse(self::$ref::endsWithNone('👋🏿', ['🏿', 'a']));
    }

    public function test_equals(): void
    {
        $this->assertTrue(self::$ref::equals('', ''), 'empty');
        $this->assertTrue(self::$ref::equals('abc', 'abc'), 'basic');
        $this->assertFalse(self::$ref::equals('abc', 'ABC'), 'case sensitive');
        $this->assertFalse(self::$ref::equals('abc', 'ab'), 'shorter');
        $this->assertFalse(self::$ref::equals('abc', 'abcd'), 'longer');
        $this->assertFalse(self::$ref::equals('abc', 'abc '), 'space');
    }

    public function test_equalsAny(): void
    {
        $this->assertTrue(self::$ref::equalsAny('abc', ['abc']), 'basic');
        $this->assertTrue(self::$ref::equalsAny('abc', ['abc', 'abc']), 'all hit');
        $this->assertTrue(self::$ref::equalsAny('abc', ['abc', 'def']), 'basic with miss');
        $this->assertFalse(self::$ref::equalsAny('abc', ['ABC']), 'case sensitive');
        $this->assertFalse(self::$ref::equalsAny('abc', ['ab']), 'shorter');
        $this->assertFalse(self::$ref::equalsAny('abc', ['abcd']), 'longer');
        $this->assertFalse(self::$ref::equalsAny('abc', ['abc ']), 'space');
    }

    public function test_indexOfFirst(): void
    {
        $this->assertNull(self::$ref::indexOfFirst('', 'a'), 'empty string');
        $this->assertSame(0, self::$ref::indexOfFirst('ab', ''), 'empty search');
        $this->assertSame(0, self::$ref::indexOfFirst('a', 'a'), 'find at 0');
        $this->assertSame(1, self::$ref::indexOfFirst('abb', 'b'), 'multiple matches');
        $this->assertSame(1, self::$ref::indexOfFirst('abb', 'b', 1), 'offset (within bound)');
        $this->assertSame(5, self::$ref::indexOfFirst('aaaaaa', 'a', 5), 'offset (within bound)');
        $this->assertNull(self::$ref::indexOfFirst('abb', 'b', 4), 'offset (out of bound)');
        $this->assertSame(2, self::$ref::indexOfFirst('abb', 'b', -1), 'offset (negative)');
        $this->assertNull(self::$ref::indexOfFirst('abb', 'b', -100), 'offset (negative)');
        $this->assertSame(0, self::$ref::indexOfFirst('👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦'), 'grapheme hit');
        $this->assertSame(0, self::$ref::indexOfFirst('👨‍👨‍👧‍👦', '👨'), 'grapheme hit subset');
        $this->assertSame(3, self::$ref::indexOfFirst('あいう', 'い', 1), 'utf8');
        $this->assertSame(28, self::$ref::indexOfFirst('🏴󠁧󠁢󠁳󠁣󠁴󠁿👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦', 1), 'grapheme hit with offset');
    }

    public function test_indexOfLast(): void
    {
        $this->assertNull(self::$ref::indexOfLast('', 'a'), 'empty string');
        $this->assertSame(2, self::$ref::indexOfLast('ab', ''), 'empty search');
        $this->assertSame(0, self::$ref::indexOfLast('a', 'a'), 'find at 0');
        $this->assertSame(2, self::$ref::indexOfLast('abb', 'b'), 'multiple matches');
        $this->assertSame(2, self::$ref::indexOfLast('abb', 'b', 1), 'offset (within bound)');
        $this->assertSame(5, self::$ref::indexOfLast('aaaaaa', 'a', 5), 'offset (within bound)');
        $this->assertNull(self::$ref::indexOfLast('abb', 'b', 4), 'offset (out of bound)');
        $this->assertSame(3, self::$ref::indexOfLast('abbb', 'b', -1), 'offset (negative)');
        $this->assertNull(self::$ref::indexOfLast('abb', 'b', -100), 'offset (negative)');
        $this->assertSame(0, self::$ref::indexOfLast('👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦'), 'utf-8');
        $this->assertSame(7, self::$ref::indexOfLast('👨‍👨‍👧‍👦', '👨'), 'utf-8');
        $this->assertSame(3, self::$ref::indexOfLast('あいう', 'い', 1), 'offset utf-8');
        $this->assertSame(28, self::$ref::indexOfLast('🏴󠁧󠁢󠁳󠁣󠁴󠁿👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦', 1), 'offset utf-8');
        $this->assertSame(null, self::$ref::indexOfLast('🏴󠁧󠁢󠁳󠁣󠁴󠁿👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦', 29), 'offset utf-8');
    }

    public function test_insertAt(): void
    {
        $this->assertSame('xyzabc', self::$ref::insertAt('abc', 'xyz', 0), 'at zero');
        $this->assertSame('axyzbc', self::$ref::insertAt('abc', 'xyz', 1), 'basic');
        $this->assertSame('xyzabc', self::$ref::insertAt('abc', 'xyz', -1), 'negative');
        $this->assertSame('abcxyz', self::$ref::insertAt('abc', 'xyz', 3), 'edge');
        $this->assertSame('abcxyz', self::$ref::insertAt('abc', 'xyz', 4), 'overflow');
        $this->assertSame('あxyzい', self::$ref::insertAt('あい', 'xyz', 3), 'utf8');
        $this->assertSame('xyzあい', self::$ref::insertAt('あい', 'xyz', -1), 'utf8 negative');
        $this->assertSame('👨x👨', self::$ref::insertAt('👨👨', 'x', 4), 'grapheme');
    }

    public function test_interpolate(): void
    {
        $this->assertSame('', self::$ref::interpolate('', ['a' => 1]), 'empty string');
        $this->assertSame('abc', self::$ref::interpolate('abc', []), 'no placeholder');
        $this->assertSame('{a}', self::$ref::interpolate('{a}', []), 'no match');
        $this->assertSame('1{b}', self::$ref::interpolate('{a}{b}', ['a' => 1]), 'one match');
        $this->assertSame('1 hi', self::$ref::interpolate('{a} hi', ['a' => 1]), 'replace edge');
        $this->assertSame('1 1', self::$ref::interpolate('{a} {a}', ['a' => 1]), 'replace twice');
        $this->assertSame('{b} 1', self::$ref::interpolate('{a} {b}', ['a' => '{b}', 'b' => 1]), 'replace multiple');
        $this->assertSame('{a1}', self::$ref::interpolate('{a{a}}', ['a' => 1]), 'nested v1');
        $this->assertSame('{1}', self::$ref::interpolate('{{a}}', ['a' => 1]), 'nested v2');
        $this->assertSame('\\{a}', self::$ref::interpolate('\\{a}', ['a' => 1]), 'escape start');
        $this->assertSame('{a\\}', self::$ref::interpolate('{a\\}', ['a' => 1]), 'escape end');
        $this->assertSame('\\1', self::$ref::interpolate('\\\\{a}', ['a' => 1]), 'don\'t escape double escape char');
        $this->assertSame('\\\\{a}', self::$ref::interpolate('\\\\\\{a}', ['a' => 1]), 'escape mixed with no escape');
        $this->assertSame('{a!}', self::$ref::interpolate('{a!}', ['a!' => 1]), 'only match ascii placeholder');
        $this->assertSame(' 1 ', self::$ref::interpolate(' {_a_b} ', ['_a_b' => 1]), 'allow under score');
        $this->assertSame('1', self::$ref::interpolate('<a>', ['a' => 1], '<', '>'), 'different delimiters');
        $this->assertSame('1.23', self::$ref::interpolate('{a:%.2f}', ['a' => 1.2345]), 'with formatting');
        $this->assertSame('005', self::$ref::interpolate('{a:%1$03d}', ['a' => 5]), 'with formatting');
        $this->assertSame('...5', self::$ref::interpolate('{a:%\'.4d}', ['a' => 5]), 'with formatting');
        $this->assertSame('{a:}', self::$ref::interpolate('{a:}', ['a' => 5]), 'empty formatting');
    }

    public function test_interpolate_non_list(): void
    {
        $this->expectExceptionMessage('Expected $replace to be a map. List given.');
        $this->expectException(InvalidArgumentException::class);
        self::$ref::interpolate('', [1, 2]);
    }

    public function test_interpolate_empty_delimiterStart(): void
    {
        $this->expectExceptionMessage('$delimiterStart and $delimiterEnd must not be empty.');
        $this->expectException(InvalidArgumentException::class);
        self::$ref::interpolate('', [1, 2], '');
    }

    public function test_interpolate_empty_delimiterEnd(): void
    {
        $this->expectExceptionMessage('$delimiterStart and $delimiterEnd must not be empty.');
        $this->expectException(InvalidArgumentException::class);
        self::$ref::interpolate('', [1, 2], '{', '');
    }

    public function test_isBlank(): void
    {
        $this->assertTrue(self::$ref::isBlank(''));
        $this->assertFalse(self::$ref::isBlank('0'));
        $this->assertFalse(self::$ref::isBlank(' '));
    }

    public function test_isNotBlank(): void
    {
        $this->assertFalse(self::$ref::isNotBlank(''));
        $this->assertTrue(self::$ref::isNotBlank('0'));
        $this->assertTrue(self::$ref::isNotBlank(' '));
    }

    public function test_length(): void
    {
        $this->assertSame(0, self::$ref::length(''), 'empty');
        $this->assertSame(4, self::$ref::length('Test'), 'ascii');
        $this->assertSame(9, self::$ref::length(' T e s t '), 'ascii');
        $this->assertSame(6, self::$ref::length('あい'), 'utf8');
        $this->assertSame(10, self::$ref::length('あいzう'), 'utf8');
        $this->assertSame(25, self::$ref::length('👨‍👨‍👧‍👦'), 'emoji');
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
        $this->expectException(NoMatchException::class);
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

    public function test_matchLast(): void
    {
        $this->assertSame('34', self::$ref::matchLast('12a34a', '/\d+/'));
        $this->assertSame('13', self::$ref::matchLast('1213', '/(?<p1>1)\d/'));
        $this->assertSame('c', self::$ref::matchLast('abcdxabc', '/c[^x]*/'));
        $this->assertSame('cx', self::$ref::matchLast('abcabcx', '/cx$/'));
    }

    public function test_matchLast_no_match(): void
    {
        $this->expectException(NoMatchException::class);
        $this->expectExceptionMessage('"aaa" does not match /z/');
        self::$ref::matchLast('aaa', '/z/');
    }

    public function test_matchLast_without_slashes(): void
    {
        $this->expectWarningMessage('preg_match_all(): Delimiter must not be alphanumeric, backslash, or NUL');
        self::$ref::matchLast('abcabc', 'a');
    }

    public function test_matchLastOrNull(): void
    {
        $this->assertSame('34', self::$ref::matchLastOrNull('12a34a', '/\d+/'));
        $this->assertSame('13', self::$ref::matchLastOrNull('1213', '/(?<p1>1)\d/'));
        $this->assertSame(null, self::$ref::matchLastOrNull('abcabc', '/bcd/'));
        $this->assertSame('c', self::$ref::matchLastOrNull('abcdxabc', '/c[^x]*/'));
        $this->assertSame(null, self::$ref::matchLastOrNull('abcabcx', '/^abcx/'));
        $this->assertSame('cx', self::$ref::matchLastOrNull('abcabcx', '/cx$/'));
    }

    public function test_matchLastOrNull_without_slashes(): void
    {
        $this->expectWarningMessage('preg_match_all(): Delimiter must not be alphanumeric, backslash, or NUL');
        self::$ref::matchLastOrNull('abcabc', 'a');
    }

    public function test_pad(): void
    {
        $this->assertSame('', self::$ref::pad('', -1, '_'), 'empty string');
        $this->assertSame('abc', self::$ref::pad('abc', 3, ''), 'pad string');
        $this->assertSame('a', self::$ref::pad('a', -1, '_'), 'defaults to pad right');
        $this->assertSame('a', self::$ref::pad('a', 0, '_'), 'zero length');
        $this->assertSame('a_', self::$ref::pad('a', 2, '_'), 'pad right');
        $this->assertSame('__', self::$ref::pad('_', 2, '_'), 'pad same char as given');
        $this->assertSame('ab', self::$ref::pad('ab', 1, '_'), 'length < string size');
        $this->assertSame('abcd', self::$ref::pad('a', 4, 'bcde'), 'overflow padding');
        $this->assertSame('あ_', self::$ref::pad('あ', 4, '_'), 'multi byte');
        $this->assertSame('👋🏿_', self::$ref::pad('👋🏿', 9, '_'), 'grapheme');
        $this->assertSame('_👋🏿', self::$ref::pad('👋🏿', 9, '_', STR_PAD_LEFT), 'Set type');
    }

    public function test_pad_invalid_pad(): void
    {
        $this->expectExceptionMessage('Unknown padding type: 3.');
        $this->expectException(InvalidArgumentException::class);
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
        $this->assertSame('あ', self::$ref::padEnd('あ', 3, '_'), 'multi byte no pad');
        $this->assertSame('あ_', self::$ref::padBoth('あ', 4, '_'), 'multi byte');
        $this->assertSame('👋🏿_', self::$ref::padBoth('👋🏿', 9, '_'), 'grapheme');
    }

    public function test_padEnd(): void
    {
        $this->assertSame('a', self::$ref::padEnd('a', -1, '_'));
        $this->assertSame('a', self::$ref::padEnd('a', 0, '_'));
        $this->assertSame('a_', self::$ref::padEnd('a', 2, '_'));
        $this->assertSame('__', self::$ref::padEnd('_', 2, '_'));
        $this->assertSame('ab', self::$ref::padEnd('ab', 1, '_'));
        $this->assertSame('あ', self::$ref::padEnd('あ', 3, '_'), 'multi byte no pad');
        $this->assertSame('あ_', self::$ref::padEnd('あ', 4, '_'), 'multi byte');
        $this->assertSame('👋🏿_', self::$ref::padEnd('👋🏿', 9, '_'), 'grapheme');
    }

    public function test_padStart(): void
    {
        $this->assertSame('a', self::$ref::padStart('a', -1, '_'));
        $this->assertSame('a', self::$ref::padStart('a', 0, '_'));
        $this->assertSame('_a', self::$ref::padStart('a', 2, '_'));
        $this->assertSame('__', self::$ref::padStart('_', 2, '_'));
        $this->assertSame('ab', self::$ref::padStart('ab', 1, '_'));
        $this->assertSame('あ', self::$ref::padStart('あ', 3, '_'), 'multi byte no pad');
        $this->assertSame('_あ', self::$ref::padStart('あ', 4, '_'), 'multi byte');
        $this->assertSame('_👋🏿', self::$ref::padStart('👋🏿', 9, '_'), 'grapheme');
    }

    public function test_range(): void
    {
        // TODO fix
//        $this->assertSame('', self::$ref::range('', 0, 1), 'empty string');
//        $this->assertSame('', self::$ref::range('abc', 0, 0), 'zero length');
//        $this->assertSame('', self::$ref::range('abc', 0, -1), 'negative length');
    }

    public function test_remove(): void
    {
        $this->assertSame('', self::$ref::remove('', ''), 'empty');
        $this->assertSame('', self::$ref::remove('aaa', 'a'), 'delete everything');
        $this->assertSame('a  a', self::$ref::remove('aaa aa a', 'aa'), 'no traceback check');
        $this->assertSame('no match', self::$ref::remove('no match', 'hctam on'), 'out of order chars');
        $this->assertSame('👋👋', self::$ref::remove('👋🏿👋🏿', '🏿'), 'dont delete grapheme code point');
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
        $this->expectExceptionMessage('Expected: $limit >= 0. Got: -1.');
        self::$ref::remove('', '', -1);
    }

    public function test_removeFirst(): void
    {
        $this->assertSame('', self::$ref::removeFirst('', ''), 'empty');
        $this->assertSame('', self::$ref::removeFirst('', 'abc'), 'empty string');
        $this->assertSame('abc', self::$ref::removeFirst('abc', ''), 'empty substring');
        $this->assertSame('bac', self::$ref::removeFirst('abac', 'a'), 'delete first');
        $this->assertSame('👋👋🏿', self::$ref::removeFirst('👋🏿👋🏿', '🏿'), 'dont delete grapheme code point');
    }

    public function test_removeLast(): void
    {
        $this->assertSame('', self::$ref::removeLast('', ''), 'empty');
        $this->assertSame('', self::$ref::removeLast('', 'abc'), 'empty string');
        $this->assertSame('abc', self::$ref::removeLast('abc', ''), 'empty substring');
        $this->assertSame('abc', self::$ref::removeLast('abac', 'a'), 'delete last');
        $this->assertSame('👋🏿👋', self::$ref::removeLast('👋🏿👋🏿', '🏿'), 'dont delete grapheme code point');
    }

    public function test_repeat(): void
    {
        $this->assertSame('aaa', self::$ref::repeat('a', 3), 'ascii');
        $this->assertSame('あああ', self::$ref::repeat('あ', 3), 'multi byte');
        $this->assertSame('👋🏿👋🏿👋🏿', self::$ref::repeat('👋🏿', 3), 'grapheme');
        $this->assertSame('', self::$ref::repeat('a', 0), 'zero');
    }

    public function test_repeat_negative_times(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected: $times >= 0. Got: -1.');
        self::$ref::repeat('a', -1);
    }

    public function test_replace(): void
    {
        $this->assertSame('', self::$ref::replace('', '', ''), 'empty string');
        $this->assertSame('b', self::$ref::replace('b', '', 'a'), 'empty search');
        $this->assertSame('aa', self::$ref::replace('bb', 'b', 'a'), 'basic');
        $this->assertSame('', self::$ref::replace('b', 'b', ''), 'empty replacement');
        $this->assertSame('あえいえう', self::$ref::replace('あ-い-う', '-', 'え'), 'mbstring');
        $this->assertSame('__🏴󠁧󠁢󠁳󠁣󠁴󠁿', self::$ref::replace('🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿', '🏴󠁧󠁢󠁳󠁣󠁴󠁿a', '_'), 'multiple codepoints');
        $this->assertSame('abc', self::$ref::replace('ab\c', '\\', ''), 'escape char');
        $this->assertSame('abc', self::$ref::replace('abc.*', '.*', ''), 'regex chars');
        $this->assertSame('a', self::$ref::replace('[]/\\!?', '[]/\\!?', 'a'), 'regex chars');

        $count = 0;
        $this->assertSame('a', self::$ref::replace('aaa', 'a', '', 2, $count), 'with limit and count');
        $this->assertSame(2, $count, 'with limit and count');

        $count = 0;
        $this->assertSame('', self::$ref::replace('', '', '', null, $count), '0 count for no match');
        $this->assertSame(0, $count, '0 count for no match');

        $this->assertSame('🏿', self::$ref::replace('👋🏿', '👋', ''), 'grapheme');
    }

    public function test_replace_with_negative_limit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected: $limit >= 0. Got: -1.');
        self::$ref::replace('', 'a', 'a', -1);
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
        $this->assertSame('🏿', self::$ref::replaceFirst('👋🏿', '👋', ''), 'grapheme');

        $replaced = false;
        self::$ref::replaceFirst('bbb', 'b', 'a', $replaced);
        $this->assertTrue($replaced, 'validate flag');

        $replaced = true;
        self::$ref::replaceFirst('b', 'z', '', $replaced);
        $this->assertFalse($replaced, 'flag is overridden with false');
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
        $this->assertSame('🏿', self::$ref::replaceLast('👋🏿', '👋', ''), 'grapheme');

        $replaced = false;
        self::$ref::replaceLast('bbb', 'b', 'a', $replaced);
        $this->assertTrue($replaced, 'validate flag');

        $replaced = true;
        self::$ref::replaceLast('b', 'z', '', $replaced);
        $this->assertFalse($replaced, 'flag is overridden with false');
    }

    public function test_replaceMatch(): void
    {
        $this->assertSame('', self::$ref::replaceMatch('', '', ''));
        $this->assertSame('abb', self::$ref::replaceMatch('abc', '/c/', 'b'));
        $this->assertSame('abbb', self::$ref::replaceMatch('abcc', '/c/', 'b'));
        $this->assertSame('あいい', self::$ref::replaceMatch('あいう', '/う/', 'い'));
        $this->assertSame('x', self::$ref::replaceMatch('abcde', '/[A-Za-z]+/', 'x'));
        $this->assertSame('a👋-b', self::$ref::replaceMatch('a👋-b', '/🏿/', '-'), 'grapheme');

        $count = 0;
        $this->assertSame('', self::$ref::replaceMatch('', '', '', null, $count), 'check count: no match');
        $this->assertSame(0, $count, 'check count: no match');

        $count = 0;
        $this->assertSame('', self::$ref::replaceMatch('aaa', '/a/', '', null, $count), 'unlimited match');
        $this->assertSame(3, $count, 'unlimited match');

        $count = 1;
        $this->assertSame('', self::$ref::replaceMatch('aaa', '/a/', '', null, $count), 'counter is reset');
        $this->assertSame(3, $count, 'counter is reset');

        $this->assertSame('a', self::$ref::replaceMatch('aaa', '/a/', '', 2), 'limit to 2');
    }

    public function test_replaceMatchWithCallback(): void
    {
        $this->assertSame('', Str::replaceMatchWithCallback('', '/./', fn() => 'b'));
        $this->assertSame('bbb', Str::replaceMatchWithCallback('abc', '/[ac]/', fn() => 'b'));

        $list = ['a', 'b', 'c'];
        $this->assertSame('a b c', self::$ref::replaceMatchWithCallback('? ? ?', '/\?/', function (array $m) use (&$list) {
            return array_shift($list) ?? '';
        }), 'with callback');
    }

    public function test_replaceMatch_with_negative_limit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected: $limit >= 0. Got: -1.');
        self::$ref::replaceMatch('', '/a/', 'a', -1);
    }

    public function test_reverse(): void
    {
        $this->assertSame('', self::$ref::reverse(''));
        $this->assertSame('ba', self::$ref::reverse('ab'));
        $this->assertSame("\x82\x81\xE3", self::$ref::reverse('あ'));
    }

    public function test_split(): void
    {
        $this->assertSame(['', ''], self::$ref::split(' ', ' '), 'empty');
        $this->assertSame(['abc'], self::$ref::split('abc', '_'), 'no match');
        $this->assertSame(['a', 'c', 'd'], self::$ref::split('abcbd', 'b'), 'match');
        $this->assertSame(['あ', 'う'], self::$ref::split('あいう', 'い'), 'match utf-8');
        $this->assertSame(['a', 'cbd'], self::$ref::split('abcbd', 'b', 2), 'match with limit');
        $this->assertSame(['a', 'b', 'c'], self::$ref::split('abc', ''), 'match with limit');
        $this->assertSame(['👨‍👨‍👧', ''], self::$ref::split('👨‍👨‍👧‍👦', '‍👦'), 'match emoji');
    }

    public function test_split_with_negative_limit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected: $limit >= 0. Got: -1.');
        self::$ref::split('a', 'b', -1);
    }

    public function test_startsWith(): void
    {
        $this->assertTrue(self::$ref::startsWith('', ''));
        $this->assertTrue(self::$ref::startsWith('bb', ''));
        $this->assertTrue(self::$ref::startsWith('bb', 'b'));
        $this->assertTrue(self::$ref::startsWith('あ-い-う', 'あ'));
        $this->assertTrue(self::$ref::startsWith('👨‍👨‍👧‍👦', '👨‍'));
        $this->assertTrue(self::$ref::startsWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿 👨‍👨‍👧‍👦', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));
        $this->assertFalse(self::$ref::startsWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿 👨‍👨‍👧‍👦', '👨‍👨‍👧‍👦'));
        $this->assertTrue(self::$ref::startsWith('🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿a🏴󠁧󠁢󠁳󠁣󠁴󠁿', '🏴󠁧󠁢󠁳󠁣󠁴󠁿a'));
        $this->assertFalse(self::$ref::startsWith('ba', 'a'));
        $this->assertFalse(self::$ref::startsWith('', 'a'));
    }

    public function test_startsWithAny(): void
    {
        $this->assertFalse(self::$ref::startsWithAny('abc', ['d', 'e']));
        $this->assertTrue(self::$ref::startsWithAny('abc', ['d', 'a']));
        $this->assertTrue(self::$ref::startsWithAny('👋🏿', ['👋', 'a']));
    }

    public function test_startsWithNone(): void
    {
        $this->assertTrue(self::$ref::startsWithNone('abc', ['d', 'e']));
        $this->assertFalse(self::$ref::startsWithNone('abc', ['d', 'a']));
        $this->assertFalse(self::$ref::startsWithNone('👋🏿', ['👋', 'a']));
    }
}
