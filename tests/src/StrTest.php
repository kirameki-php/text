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
}
