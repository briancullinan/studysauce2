<?php

namespace StudySauce\Bundle\Security;

/**
 * Class PathUserResponse
 * @package StudySauce\Bundle\Security
 */
class PathUserResponse extends \HWI\Bundle\OAuthBundle\OAuth\Response\PathUserResponse
{
    /**
     * @return null|string
     */
    public function getFirst()
    {
        return $this->getValueForPath('first');
    }

    /**
     * @return null|string
     */
    public function getLast()
    {
        return $this->getValueForPath('last');
    }
}