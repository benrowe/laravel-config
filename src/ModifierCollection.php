<?php

namespace Benrowe\Laravel\Config;

use Illuminate\Support\Collection;
use Benrowe\Laravel\Config\Modifiers\Modifier;

/**
 *
 */
class ModifierCollection extends Collection
{
    /**
     * Convert the value based on the supplied modifiers
     *
     * @param  string $key       [description]
     * @param  mixed $value     [description]
     * @param  string $direction [description]
     * @return mixed            [description]
     */
    public function convert($key, $value, $direction = Modifier::DIRECTION_TO)
    {
        $method = 'convert'.ucfirst($direction);
        foreach ($this->items as $modifier) {
            if ($modifier->canHandle($value, $direction)) {
                return $modifier->$method($value);
            }
        }
        return $value;
    }
}
