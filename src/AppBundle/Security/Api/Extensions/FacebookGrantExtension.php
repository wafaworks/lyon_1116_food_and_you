<?php

namespace AppBundle\Security\Api\Extensions;

use AppBundle\Entity\Repository\AuthenticationRepository;
use FOS\OAuthServerBundle\Storage\GrantExtensionInterface;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\FacebookResourceOwner;
use OAuth2\Model\IOAuth2Client;
use OAuth2\OAuth2;
use OAuth2\OAuth2ServerException;

class FacebookGrantExtension implements GrantExtensionInterface
{
    const INVALID_FACEBOOK = 'invalid_facebook';

    /**
     * @var AuthenticationRepository|null
     */
    protected $authenticationRepository = null;

    /**
     * @var FacebookResourceOwner|null
     */
    protected $facebookResourceOwner = null;

    /**
     * FacebookGrantExtension constructor.
     *
     * @param AuthenticationRepository $authenticationRepository
     * @param FacebookResourceOwner $facebookResourceOwner
     */
    public function __construct(
        AuthenticationRepository $authenticationRepository,
        FacebookResourceOwner $facebookResourceOwner
    ) {
        $this->authenticationRepository = $authenticationRepository;
        $this->facebookResourceOwner = $facebookResourceOwner;
    }

    /**
     *
     * @param IOAuth2Client $client
     * @param array $inputData
     * @param array $authHeaders
     * @return array|bool
     *
     * @throws OAuth2ServerException
     */
    public function checkGrantExtension(IOAuth2Client $client, array $inputData, array $authHeaders)
    {
        if (!array_key_exists('access_token', $inputData)) {
            throw new OAuth2ServerException(
                OAuth2::HTTP_BAD_REQUEST,
                OAuth2::ERROR_INVALID_REQUEST,
                'Missing parameter. "access_token" is required'
            );
        }

        if (!array_key_exists('id', $inputData)) {
            throw new OAuth2ServerException(
                OAuth2::HTTP_BAD_REQUEST,
                OAuth2::ERROR_INVALID_REQUEST,
                'Missing parameter. "id" is required'
            );
        }

        $user = $this->authenticationRepository->findOneBy(
            array(
                'facebook_id' => $inputData['id'],
            )
        );

        $token = array("access_token" => $inputData['access_token']);
        $response = $this->facebookResourceOwner->getUserInformation($token)->getResponse();

        if ($user && !array_key_exists('error', $response) && $inputData['id'] == $response['id']) {
            return array(
                'data' => $user,
            );
        }

        throw new OAuth2ServerException(
            OAuth2::HTTP_BAD_REQUEST,
            self::INVALID_FACEBOOK,
            'User not registered on site or id/token is invalid.'
        );
    }
}
