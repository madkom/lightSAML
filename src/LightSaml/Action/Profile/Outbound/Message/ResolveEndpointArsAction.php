<?php
/**
 * Created by PhpStorm.
 * User: mhytry
 * Date: 25.10.2016
 * Time: 12:05.
 */
namespace LightSaml\Action\Profile\Outbound\Message;

use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Metadata\ArtifactResolutionService;
use LightSaml\SamlConstants;

class ResolveEndpointArsAction extends ResolveEndpointBaseAction
{
    protected function getServiceType(ProfileContext $context)
    {
        return ArtifactResolutionService::class;
    }

    /**
     * @param ProfileContext $context
     *
     * @return string[]
     */
    protected function getBindings(ProfileContext $context)
    {
        return array(
            SamlConstants::BINDING_SAML2_SOAP,
        );
    }
}
