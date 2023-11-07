<?php declare(strict_types=1);

namespace Tests\Kirameki\Text;

use Kirameki\Core\Testing\TestCase;
use Kirameki\Text\Str;
use function strlen;

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
        // match first
        $this->assertSame('est', self::$ref::after('test', 't'));

        // match last
        $this->assertSame('', self::$ref::after('test1', '1'));

        // match empty string
        $this->assertSame('test', self::$ref::after('test', ''));

        // no match
        $this->assertSame('test', self::$ref::after('test', 'a'));

        // multi byte
        $this->assertSame('うえ', self::$ref::after('ああいうえ', 'い'));

        // grapheme
        $this->assertSame('def', self::$ref::after('abc🏴󠁧󠁢󠁳󠁣󠁴󠁿def', '🏴󠁧󠁢󠁳󠁣󠁴󠁿'));

        // grapheme cluster
        $this->assertSame('🏿', self::$ref::after('👋🏿', '👋'));
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
        $this->assertSame('🏿', self::$ref::afterLast('👋🏿', '👋'));
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
        $this->assertSame('👋', self::$ref::before('👋🏿', '🏿'));
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
        $this->assertSame('👋', self::$ref::beforeLast('👋🏿', '🏿'));
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
        $this->assertSame('', self::$ref::between('👋🏿', '👋', '🏿'));
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

        // multichar
        $this->assertSame('_', self::$ref::betweenFurthest('ab_ba', 'ab', 'ba'));

        // utf8
        $this->assertSame('い', self::$ref::betweenFurthest('あいう', 'あ', 'う'));

        // grapheme
        $this->assertSame('😃', self::$ref::betweenFurthest('👋🏿😃👋🏿😃', '👋🏿', '👋🏿'));

        // grapheme between codepoints
        $this->assertSame('', self::$ref::between('👋🏿', '👋', '🏿'));
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
        $this->assertSame('', self::$ref::between('👋🏿', '👋', '🏿'));
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
}
