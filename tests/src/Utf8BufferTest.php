<?php declare(strict_types=1);

namespace Tests\Kirameki\Text;

use Kirameki\Core\Testing\TestCase;
use Kirameki\Text\Utf8Buffer;

class Utf8BufferTest extends TestCase
{
    protected function buffer(string $string): Utf8Buffer
    {
        return new Utf8Buffer($string);
    }

    public function test_byteLength(): void
    {
        $this->assertSame(0, $this->buffer('')->byteLength(), 'empty');
        $this->assertSame(3, $this->buffer('123')->byteLength(), 'ascii');
        $this->assertSame(9, $this->buffer('あいう')->byteLength(), 'utf8');
        $this->assertSame(28, $this->buffer('🏴󠁧󠁢󠁳󠁣󠁴󠁿')->byteLength(), 'grapheme');
    }

    public function test_cut(): void
    {
        $after = $this->buffer('あいう')->cut(7, '...');
        $this->assertInstanceOf(Utf8Buffer::class, $after);
        $this->assertSame('あい...', $after->toString());
    }

    public function test_interpolate(): void
    {
        $buffer = $this->buffer(' <a> ')->interpolate(['a' => 1], '<', '>');
        $this->assertSame(' 1 ', $buffer->toString());
        $this->assertInstanceOf(Utf8Buffer::class, $buffer);
    }

    public function test_isBlank(): void
    {
        $this->assertTrue($this->buffer('')->isBlank());
        $this->assertFalse($this->buffer('a')->isBlank());
        $this->assertFalse($this->buffer("\n")->isBlank());
    }

    public function test_isNotBlank(): void
    {
        $this->assertFalse($this->buffer('')->isNotBlank());
        $this->assertTrue($this->buffer('a')->isNotBlank());
        $this->assertTrue($this->buffer("\n")->isNotBlank());
    }

    public function test_length(): void
    {
        $this->assertSame(3, $this->buffer('あいう')->length());
    }

}
