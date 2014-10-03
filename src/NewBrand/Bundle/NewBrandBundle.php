<?php

namespace NewBrand\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class NewBrandBundle
 * @package NewBrand\Bundle
 */
class NewBrandBundle extends Bundle
{
    /**
     * Returns the bundle parent name.
     *
     * @return string The Bundle parent name it overrides or null if no parent
     *
     * @api
     */
    public function getParent()
    {
        return 'StudySauceBundle';
    }
}
