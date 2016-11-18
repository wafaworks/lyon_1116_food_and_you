<?php

namespace AppBundle\Security;

use AppBundle\Entity\Authentication;
use AppBundle\Entity\Media;
use AppBundle\Entity\Member;
use AppBundle\Notification\Event\RegistrationOauthEvent;
use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\User\UserInterface;

class OauthUserProvider extends FOSUBUserProvider
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var
     */
    private $rootDir;

    public function __construct(
        UserManagerInterface $userManager,
        array $properties,
        EventDispatcherInterface $dispatcher,
        $rootDir
    ) {
        parent::__construct($userManager, $properties);
        $this->dispatcher = $dispatcher;
        $this->rootDir = $rootDir;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $userData = $this->getUserData($response);
        /** @var Authentication $user */
        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $userData['userId']));

        if (null === $user) {
            $user = $this->findUserByUsernameOrEmail($userData);
            if (null === $user || !$user instanceof UserInterface) {
                $user = $this->createUser($response, $userData);
            } else {
                $this->updateTokens($user, $response);
                $this->userManager->updateUser($user);
            }
        } else {
            $checker = new UserChecker();
            $checker->checkPreAuth($user);
        }
        return $user;
    }

    /**
     * @param UserResponseInterface $response
     * @return array
     */
    private function getUserData(UserResponseInterface $response)
    {
        $rawResponse = $response->getResponse();
        if (is_array($rawResponse['picture']) &&
            count($rawResponse['picture']) === 1 &&
            is_array($rawResponse['picture']['data']) &&
            count($rawResponse['picture']['data']) === 2 &&
            array_key_exists('url', $rawResponse['picture']['data'])
        ) {
            $pictureUrl = $rawResponse['picture']['data']['url'];
        } else {
            $pictureUrl = false;
        }

        return array(
            'userId' => $response->getUsername(),
            'email' => $response->getEmail(),
            'username' => $response->getNickname() ?: $response->getRealName(),
            'first_name' => $response->getFirstName(),
            'last_name' => $response->getLastName(),
            'picture' => $pictureUrl,
        );
    }

    /**
     * @param array $userData
     * @return \FOS\UserBundle\Model\UserInterface
     */
    private function findUserByUsernameOrEmail(array $userData)
    {
        $user = $this->userManager->findUserByUsername($userData['username']);

        if (!$user) {
            $user = $this->userManager->findUserByEmail($userData['email']);
        }

        return $user;
    }

    /**
     * @param UserResponseInterface $response
     * @param array $userData
     * @return \FOS\UserBundle\Model\UserInterface
     */
    private function createUser(UserResponseInterface $response, array $userData)
    {
        /** @var Authentication $user */
        $user = $this->userManager->createUser();
        $user->setUsername(mb_strtolower(str_replace(' ', '', $userData['username'])));
        $user->setEmail($userData['email']);
        $user->setPassword('');
        $user->setEnabled(true);

        $this->updateTokens($user, $response);

        $member = new Member();
        $member->setFirstName($userData['first_name']);
        $member->setLastName($userData['last_name']);

        if ($userData['picture']) {
            $media = $this->getMedia($userData['picture']);
            $member->setPhoto($media);
        }

        $user->setMember($member);

        if(array_key_exists('email', $userData) && $userData['email']) {
            $this->dispatcher->dispatch(
                RegistrationOauthEvent::EVENT_NAME,
                new RegistrationOauthEvent($user)
            );
        } else {
            $user->setRoles(array(
                'ROLE_INCOMPLETE_USER' => 'ROLE_INCOMPLETE_USER'
            ));
            $user->setEmail($userData['userId'].'@facebook.tmp');
        }

        $this->userManager->updateUser($user);

        if (!empty($media) && $media instanceof Media && $media->getBinaryContent() instanceof File) {
            unlink($media->getBinaryContent()->getRealPath());
        }

        return $user;
    }

    private function updateTokens(Authentication $user, UserResponseInterface $response)
    {
        $user->setFacebookId($response->getUsername());
        $user->setFacebookAccessToken($response->getAccessToken());
    }

    /**
     * @param $picture
     * @return Media
     */
    private function getMedia($picture)
    {
        $tmpPicture = $this->downloadTemporaryImage($picture);

        $media = new Media();
        $media->setName('photo-user');
        $media->setEnabled(true);
        $media->setContext('user');
        $media->setProviderName('sonata.media.provider.image');
        $media->setAuthorName('Facebook');
        $media->setBinaryContent($tmpPicture);

        return $media;
    }

    /**
     * @param string $picture
     * @return File
     */
    private function downloadTemporaryImage($picture)
    {
        $image = file_get_contents($picture);

        $filePath = sprintf(
            '%s/tmp/fyou%s.%s',
            $this->rootDir,
            sha1(uniqid(mt_rand(), true)),
            pathinfo(parse_url($picture, PHP_URL_PATH), PATHINFO_EXTENSION)
        );

        file_put_contents($filePath, $image);

        return new File($filePath);
    }
}
