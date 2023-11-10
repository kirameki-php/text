<?php declare(strict_types=1);

namespace Kirameki\Text;

use Stringable;
use function basename;
use function dirname;
use function sprintf;

class UnicodeBuilder extends StringBuilder
{
    /**
     * @param string $value
     */
    public function __construct(string $value = '')
    {
        static::$ref ??= new Unicode();
        parent::__construct($value);
    }

    /**
     * @param int $position
     * @param string $ellipsis
     * @return static
     */
    public function cut(int $position, string $ellipsis = ''): static
    {
        return new static(Unicode::cut($this->value, $position, $ellipsis));
    }
}
