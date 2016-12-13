<?php
/**
 * Created by PhpStorm.
 * User: mhytry
 * Date: 10.11.2016
 * Time: 12:13.
 */
namespace LightSaml\Model\Protocol;

use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlArtifactException;
use LightSaml\Error\LightSamlBindingException;
use LightSaml\Error\LightSamlProfileException;
use LightSaml\SamlConstants;

class ArtifactGenerator
{
    const ARTIFACT_FORMAT = 4;

    public function generateForMessageContext(ProfileContext $profileContext)
    {
        $ssoDescriptor = $this->ssoDescriptor($profileContext);

        $endpoint = $ssoDescriptor->getFirstArtifactResolutionService(SamlConstants::BINDING_SAML2_SOAP);

        if (empty($endpoint)) {
            throw new LightSamlBindingException('ArtifactResolutionService with SOAP binding is not defined');
        }

        $typeCode = str_pad(dechex(self::ARTIFACT_FORMAT), 4, 0, STR_PAD_LEFT);
        $endpointIndex = str_pad(dechex($endpoint->getIndex()), 4, 0, STR_PAD_LEFT);
        $sourceId = sha1($profileContext->getOwnEntityDescriptor()->getEntityID());
        $messageHandle = bin2hex(openssl_random_pseudo_bytes(20));

        return new Artifact($typeCode, $endpointIndex, $sourceId, $messageHandle);
    }

    public function generateFromString($artifact)
    {
        $artifact = bin2hex(base64_decode($artifact));

        $typeCode = substr($artifact, 0, 4);

        if (self::ARTIFACT_FORMAT !== hexdec($typeCode)) {
            throw new LightSamlArtifactException(sprintf('Artifact format "%s" is not supported', self::ARTIFACT_FORMAT));
        }

        $endpointIndex = substr($artifact, 4, 4);
        $sourceId = substr($artifact, 8, 40);
        $messageHandle = substr($artifact, 48, 40);

        return new Artifact($typeCode, $endpointIndex, $sourceId, $messageHandle);
    }

    /**
     * @param ProfileContext $profileContext
     *
     * @return \LightSaml\Model\Metadata\IdpSsoDescriptor|\LightSaml\Model\Metadata\SpSsoDescriptor|null
     */
    private function ssoDescriptor(ProfileContext $profileContext)
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
