<?php

namespace Benrowe\Laravel\Config;

use Illuminate\Support\Collection;

/**
 *
 */
class ModifierCollection extends Collection
{
    /**
     * Convert the value based on the supplied modifiers
     *
     * @param  [type] $key       [description]
     * @param  [type] $value     [description]
     * @param  string $direction [description]
     * @return [type]            [description]
     */
    public function convert($key, $value, $direction = 'to')
    {
        foreach ($this->items as $modifier) {
            if ($modifier->canHandle($value)) {
                return $modifier->convertto($value);
            }
        }
        return $value;
    }
}
