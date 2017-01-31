<?php
/**
 * Created by PhpStorm.
 * User: mhytry
 * Date: 10.11.2016
 * Time: 12:13.
 */
namespace LightSaml\Context\Profile\Helper;

use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlArtifactException;
use LightSaml\Error\LightSamlBindingException;
use LightSaml\Error\LightSamlProfileException;
use LightSaml\Model\Protocol\Artifact;
use LightSaml\SamlConstants;

class ArtifactHelper
{
    public static function generateForMessageContext(ProfileContext $profileContext)
    {
        $ssoDescriptor = self::ssoDescriptor($profileContext);

        $endpoint = $ssoDescriptor->getFirstArtifactResolutionService(SamlConstants::BINDING_SAML2_SOAP);

        if (empty($endpoint)) {
            throw new LightSamlBindingException('ArtifactResolutionService with SOAP binding is not defined');
        }

        $sourceId = sha1($profileContext->getOwnEntityDescriptor()->getEntityID());
        $messageHandle = bin2hex(openssl_random_pseudo_bytes(20)); //temporary

        return new Artifact($endpoint->getIndex(), $sourceId, $messageHandle);
    }

    public static function generateFromString($artifact)
    {
        $artifact = bin2hex(base64_decode($artifact));

        $typeCode = substr($artifact, 0, 4);

        if (Artifact::TYPE_CODE !== hexdec($typeCode)) {
            throw new LightSamlArtifactException(sprintf('Artifact format "%s" is not supported', $typeCode));
        }

        $endpointIndex = substr($artifact, 4, 4);
        $sourceId = substr($artifact, 8, 40);
        $messageHandle = substr($artifact, 48, 40);

        return new Artifact(hexdec($endpointIndex), $sourceId, $messageHandle);
    }

    /**
     * @param ProfileContext $profileContext
     *
     * @return \LightSaml\Model\Metadata\IdpSsoDescriptor|\LightSaml\Model\Metadata\SpSsoDescriptor|null
     */
    private static function ssoDescriptor(ProfileContext $profileContext)
    {
        $currentRole = $profileContext->getOwnRole();

        switch ($currentRole) {
            case ProfileContext::ROLE_SP:
                $ssoDescriptor = $profileContext->getOwnEntityDescriptor()->getFirstSpSsoDescriptor();
                break;
            case ProfileContext::ROLE_IDP:
                $ssoDescriptor = $profileContext->getOwnEntityDescriptor()->getFirstIdpSsoDescriptor();
                break;
            default:
                throw new LightSamlProfileException('Unspecified role');
        }

        return $ssoDescriptor;
    }
}
