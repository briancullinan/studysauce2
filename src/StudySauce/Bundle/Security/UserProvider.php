<?php

namespace StudySauce\Bundle\Security;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use StudySauce\Bundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserProvider
 * @package StudySauce\Bundle\Security
 */
class UserProvider extends BaseClass
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $entityManager;

    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
     */
    protected $encoderFactory;

    /**
     * Constructor
     *
     * @param UserManagerInterface $userManager
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface $encoderFactory
     * @param array $properties
     */
    public function __construct(UserManagerInterface $userManager, EntityManager $entityManager, EncoderFactoryInterface $encoderFactory, array $properties)
    {
        $this->entityManager = $entityManager;
        $this->encoderFactory = $encoderFactory;
        parent::__construct($userManager, $properties);
    }

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        /** @var User $user */
        /** @var PathUserResponse $response */
        $property = $this->getProperty($response);
        $username = $response->getUsername();

        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();

        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';

        //we "disconnect" previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy([$property => $username])) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $user->setFirst($response->getFirst());
            $user->setLast($response->getLast());
            $this->userManager->updateUser($previousUser);
        }

        //we connect current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());

        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        /** @var PathUserResponse $response */
        $username = $response->getUsername();
        /** @var User $user */
        $prop = $this->getProperty($response);
        $user = $this->userManager->findUserBy([$prop => $username]);
        // allow user with same email address to connect because we trust facebook and google auth
        if(empty($user))
            $user = $this->userManager->findUserBy(['email' => $response->getEmail()]);

        $service = $response->getResourceOwner()->getName();
        $setter = 'set'.ucfirst($service);

        // create new user here
        if (null === $user) {
            //I have set all requested data with the user's username
            //modify here with relevant data
            $user = $this->userManager->createUser();
            $user->setUsername($service.'.'.$username);
            $factory = $this->encoderFactory->getEncoder($user);
            $user->setPassword($factory->encodePassword(md5(uniqid(mt_rand(), true)), $user->getSalt()));
            $user->setEnabled(true);
        }

        // these fields can always be updates and sync from the service
        $setter_id = $setter.'Id';
        $user->$setter_id($username);
        $setter_token = $setter.'AccessToken';
        $user->$setter_token($response->getAccessToken());
        $user->setEmail($response->getEmail());
        $user->setFirst($response->getFirst());
        $user->setLast($response->getLast());
        $this->userManager->updateUser($user);
        return $user;
    }

}