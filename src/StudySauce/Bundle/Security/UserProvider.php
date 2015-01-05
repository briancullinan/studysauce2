<?php

namespace StudySauce\Bundle\Security;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseUserProvider;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\EventListener\InviteListener;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserProvider
 * @package StudySauce\Bundle\Security
 */
class UserProvider extends BaseUserProvider
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
     */
    protected $encoderFactory;

    /**
     * Constructor
     *
     * @param UserManagerInterface $userManager
     * @param ContainerInterface $container
     * @param \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface $encoderFactory
     * @param array $properties
     */
    public function __construct(UserManagerInterface $userManager, ContainerInterface $container, EncoderFactoryInterface $encoderFactory, array $properties)
    {
        $this->container = $container;
        $this->encoderFactory = $encoderFactory;
        parent::__construct($userManager, $properties);
    }

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        /** @var EntityManager $orm */
        $orm = $this->container->get('doctrine')->getManager();
        /** @var Request $request */
        $request = $this->container->get('request');
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
        /** @var EntityManager $orm */
        $orm = $this->container->get('doctrine')->getManager();
        /** @var Request $request */
        $request = $this->container->get('request');
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
            // TODO: reconnect social users to adviser invites
            // https://bitbucket.org/StudySauce/studysauce2/src/e6387ffee6d036426699aed7cc7f10828aae62ef/src/StudySauce/Bundle/Controller/AccountController.php?at=master#cl-191

        }

        // these fields can always be updated and sync from the service
        $setter_id = $setter.'Id';
        $user->$setter_id($username);
        $setter_token = $setter.'AccessToken';
        $user->$setter_token($response->getAccessToken());
        $user->setEmail($response->getEmail() ?: ($username . '@example.org'));
        $user->setFirst($response->getFirst());
        $user->setLast($response->getLast());

        $this->userManager->updateUser($user);
        return $user;
    }

}