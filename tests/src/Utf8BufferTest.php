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
        self::assertSame(0, $this->buffer('')->byteLength(), 'empty');
        self::assertSame(3, $this->buffer('123')->byteLength(), 'ascii');
        self::assertSame(9, $this->buffer('あいう')->byteLength(), 'utf8');
        self::assertSame(28, $this->buffer('🏴󠁧󠁢󠁳󠁣󠁴󠁿')->byteLength(), 'grapheme');
    }

    public function test_cut(): void
    {
        $after = $this->buffer('あいう')->cut(7, '...');
        self::assertInstanceOf(Utf8Buffer::class, $after);
        self::assertSame('あい...', $after->toString());
    }

    public function test_length(): void
    {
        self::assertSame(3, $this->buffer('あいう')->length());
    }
}
