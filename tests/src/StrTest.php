<?php declare(strict_types=1);

namespace Tests\Kirameki\Text;

use Kirameki\Core\Testing\TestCase;
use Kirameki\Text\Str;
use function strlen;
use const PHP_EOL;

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
        self::assertFalse(self::$ref::count('a', ''));
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
        $this->assertFalse(self::$ref::doesNotEndWith('abc', ['c']));
        $this->assertFalse(self::$ref::doesNotEndWith('abc', ['a', 'b', 'c']));
        $this->assertTrue(self::$ref::doesNotEndWith('abc', ['a', 'b']));
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
        $this->assertTrue(self::$ref::doesNotStartWith('abc', ['d', 'e']));
        $this->assertFalse(self::$ref::doesNotStartWith('abc', ['d', 'a']));
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
        self::assertTrue(self::$ref::endsWith('abc', 'c'), 'single hit');
        self::assertFalse(self::$ref::endsWith('abc', 'b'), 'single miss');
        self::assertTrue(self::$ref::endsWith('abc', ['c']), 'array hit');
        self::assertTrue(self::$ref::endsWith('abc', ['a', 'b', 'c']), 'array hit with misses');
        self::assertFalse(self::$ref::endsWith('abc', ['a', 'b']), 'array miss');
        self::assertTrue(self::$ref::endsWith('aabbcc', 'cc'), 'multiple occurrence string');
        self::assertTrue(self::$ref::endsWith('aabbcc' . PHP_EOL, PHP_EOL), 'newline');
        self::assertTrue(self::$ref::endsWith('abc0', '0'), 'zero');
        self::assertTrue(self::$ref::endsWith('abcfalse', 'false'), 'false');
        self::assertTrue(self::$ref::endsWith('a', ''), 'empty needle');
        self::assertTrue(self::$ref::endsWith('', ''), 'empty haystack and needle');
        self::assertTrue(self::$ref::endsWith('あいう', 'う'), 'utf8');
        self::assertFalse(self::$ref::endsWith("あ\n", 'あ'), 'utf8 newline');
        self::assertTrue(self::$ref::endsWith('👋🏻', '🏻'), 'grapheme');
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

}
