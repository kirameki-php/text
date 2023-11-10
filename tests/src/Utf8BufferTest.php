<?php declare(strict_types=1);

namespace Tests\Kirameki\Text;

use Kirameki\Core\Testing\TestCase;
use Kirameki\Text\StrBuffer;
use Kirameki\Text\Utf8Buffer;

class Utf8BufferTest extends TestCase
{
    public function test_cut(): void
    {
        $after = Utf8Buffer::from('あいう')->cut(7, '...');
        self::assertInstanceOf(StrBuffer::class, $after);
        self::assertSame('あい...', $after->toString());
    }
}
