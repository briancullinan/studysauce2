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
    public function getFirstName()
    {
        return $this->getValueForPath('firstName');
    }

    /**
     * @return null|string
     */
    public function getLastName()
    {
        return $this->getValueForPath('lastName');
    }
}