<?php declare(strict_types=1);

namespace Kirameki\Text;

use Kirameki\Utils\Str as Base;

class Str extends Base
{
    /**
     * @param string $string
     * @return Stringable
     */
    public static function of(string $string): Stringable
    {
        return new Stringable($string);
    }
}
