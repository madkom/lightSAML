<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Created by PhpStorm.
 * User: mhytry
 * Date: 25.10.2016
 * Time: 11:45.
 */
namespace LightSaml\Builder\Action\Profile\ArtifactResolution;

use LightSaml\Action\DispatchEventAction;
use LightSaml\Action\Profile\Inbound\Message\ExtractWrappedMessageFromArtifactResponse;
use LightSaml\Action\Profile\Inbound\Message\IssuerValidatorAction;
use LightSaml\Action\Profile\Inbound\Message\MessageSignatureValidatorAction;
use LightSaml\Action\Profile\Inbound\Message\ReceiveArtifactAction;
use LightSaml\Action\Profile\Inbound\StatusResponse\InResponseToValidatorAction;
use LightSaml\Action\Profile\Inbound\StatusResponse\StatusAction;
use LightSaml\Action\Profile\Outbound\ArtifactResolution\CreateArtifactResolveAction;
use LightSaml\Action\Profile\Outbound\ArtifactResolution\DetectEntityDescriptor;
use LightSaml\Action\Profile\Outbound\ArtifactResolution\ExchangeArtifactAction;
use LightSaml\Action\Profile\Outbound\Message\CreateMessageIssuerAction;
use LightSaml\Action\Profile\Outbound\Message\MessageIdAction;
use LightSaml\Action\Profile\Outbound\Message\MessageIssueInstantAction;
use LightSaml\Action\Profile\Outbound\Message\MessageVersionAction;
use LightSaml\Action\Profile\Outbound\Message\ResolveEndpointArsAction;
use LightSaml\Action\Profile\Outbound\Message\SaveRequestStateAction;
use LightSaml\Action\Profile\Outbound\Message\SignMessageAction;
use LightSaml\Builder\Action\Profile\AbstractProfileActionBuilder;
use LightSaml\Event\Events;
use LightSaml\SamlConstants;

class SsoSpExchangeArtifactForResponseActionBuilder extends AbstractProfileActionBuilder
{
    protected function doInitialize()
    {
        $this->add(new ReceiveArtifactAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getBindingFactory()->create(SamlConstants::BINDING_SAML2_HTTP_ARTIFACT)
        ), 100);
        $this->add(new DetectEntityDescriptor(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getPartyContainer()->getIdpEntityDescriptorStore(),
            $this->buildContainer->getPartyContainer()->getTrustOptionsStore()
        ));
        $this->add(new ResolveEndpointArsAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getEndpointResolver()
        ));
        $this->add(new CreateArtifactResolveAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new MessageIdAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new MessageVersionAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            SamlConstants::VERSION_20
        ));
        $this->add(new MessageIssueInstantAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getSystemContainer()->getTimeProvider()
        ));
        $this->add(new CreateMessageIssuerAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new SaveRequestStateAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getStoreContainer()->getRequestStateStore()
        ));
        $this->add(new DispatchEventAction(
            $this->buildContainer->getSystemContainer()->getEventDispatcher(),
            Events::BEFORE_ENCRYPT
        ));
        $this->add(new SignMessageAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getSignatureResolver()
        ));

        // exchange the artifact for the actual protocol message
        $this->add(new ExchangeArtifactAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getBindingFactory()
        ), 400);

        //validation ArtifactResponse
        $this->add(new IssuerValidatorAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getNameIdValidator(),
            null
        ));
        $this->add(new StatusAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new InResponseToValidatorAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getStoreContainer()->getRequestStateStore()
        ));
        $this->add(new MessageSignatureValidatorAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getSignatureValidator()
        ));

        //extract Response from ArtifactResponse
        $this->add(new ExtractWrappedMessageFromArtifactResponse(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
    }
}
