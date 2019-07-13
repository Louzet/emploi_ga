<?php

namespace App\Inflector;

use FOS\RestBundle\Inflector\InflectorInterface;

class NoPluralizeInflector implements InflectorInterface
{
    /**
     * Pluralizes noun.
     *
     * @param string $word
     *
     * @return string
     */
    public function pluralize($word)
    {
        // don't pluralize (see FOSRestBundle doc (automatic route generation)
        return $word;
    }
}