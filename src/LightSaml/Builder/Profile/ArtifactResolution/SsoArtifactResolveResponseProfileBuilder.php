<?php
/**
 * Created by PhpStorm.
 * User: mhytry
 * Date: 19.10.2016
 * Time: 14:43.
 */
namespace LightSaml\Builder\Profile\ArtifactResolution;

use LightSaml\Builder\Action\Profile\ArtifactResolution\SsoSpArtifactResolveResponseActionBuilder;
use LightSaml\Builder\Action\Profile\SingleSignOn\Sp\SsoSpResponseValidatorActionBuilder;
use LightSaml\Builder\Action\Profile\SingleSignOn\Sp\SsoSpValidateAssertionActionBuilder;
use LightSaml\Builder\Profile\AbstractProfileBuilder;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Profile\Profiles;

class SsoArtifactResolveResponseProfileBuilder extends AbstractProfileBuilder
{
    /**
     * @return string
     */
    protected function getProfileId()
    {
        return Profiles::SSO_SP_RECEIVE_ARTIFACT;
    }

    /**
     * @return string
     */
    protected function getProfileRole()
    {
        return ProfileContext::ROLE_SP;
    }

    public function buildContext()
    {
        $result = parent::buildContext();

        $result->getArtifactContext()->setGenerator($this->container->getServiceContainer()->getArtifactGenerator());

        return $result;
    }

    /**
     * @return \LightSaml\Builder\Action\ActionBuilderInterface
     */
    protected function getActionBuilder()
    {
        $result = new SsoSpArtifactResolveResponseActionBuilder(
            $this->container,
            new SsoSpResponseValidatorActionBuilder(
                $this->container,
                new SsoSpValidateAssertionActionBuilder($this->container)
            )
        );

        return $result;
    }
}
