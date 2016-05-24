<?php

/**
 * @author Ben Rowe <ben.rowe.83@gmail.com>
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

namespace Benrowe\Laravel\Config\Modifiers;

use Benrowe\Laravel\Config\Modifiers\Modifier;
use Illuminate\Support\Collection as BaseCollection;

/**
 *
 */
class Collection extends BaseCollection
{
    private $keys = [];
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
                $this->keys[] = $key;
                return $modifier->$method($value);
            }
        }
        return $value;
    }
}