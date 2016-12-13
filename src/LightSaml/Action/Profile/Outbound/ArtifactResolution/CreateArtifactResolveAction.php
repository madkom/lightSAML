<?php
/**
 * Created by PhpStorm.
 * User: mhytry
 * Date: 25.10.2016
 * Time: 12:41.
 */
namespace LightSaml\Action\Profile\Outbound\ArtifactResolution;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Protocol\ArtifactResolve;

class CreateArtifactResolveAction extends AbstractProfileAction
{
    protected function doExecute(ProfileContext $context)
    {
        $artifactResolve = new ArtifactResolve();
        $artifactResolve->setArtifact((string) $context->getInboundContext()->getArtifact());

        $context->getOutboundContext()->setMessage($artifactResolve);
    }
}
