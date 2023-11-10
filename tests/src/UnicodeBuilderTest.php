<?php declare(strict_types=1);

namespace Tests\Kirameki\Text;

use Kirameki\Core\Testing\TestCase;
use Kirameki\Text\StringBuilder;
use Kirameki\Text\UnicodeBuilder;

class UnicodeBuilderTest extends TestCase
{
    public function test_cut(): void
    {
        $after = UnicodeBuilder::from('あいう')->cut(7, '...');
        self::assertInstanceOf(StringBuilder::class, $after);
        self::assertSame('あい...', $after->toString());
    }
}
