<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Builder\Action\Profile\SingleSignOn\Sp;

use LightSaml\Action\DispatchEventAction;
use LightSaml\Action\Profile\Outbound\AuthnRequest\ACSAction;
use LightSaml\Action\Profile\Outbound\AuthnRequest\CreateAuthnRequestAction;
use LightSaml\Action\Profile\Outbound\Message\CreateMessageIssuerAction;
use LightSaml\Action\Profile\Outbound\Message\DestinationAction;
use LightSaml\Action\Profile\Outbound\Message\MessageIdAction;
use LightSaml\Action\Profile\Outbound\Message\MessageIssueInstantAction;
use LightSaml\Action\Profile\Outbound\Message\NameIDPolicyAction;
use LightSaml\Action\Profile\Outbound\Message\ResolveEndpointIdpSsoAction;
use LightSaml\Action\Profile\Outbound\Message\SaveRequestStateAction;
use LightSaml\Action\Profile\Outbound\Message\SendMessageAction;
use LightSaml\Action\Profile\Outbound\SetRelayStateAction;
use LightSaml\Action\Profile\Outbound\Message\SignMessageAction;
use LightSaml\Action\Profile\Outbound\Message\MessageVersionAction;
use LightSaml\Builder\Action\Profile\AbstractProfileActionBuilder;
use LightSaml\Event\Events;
use LightSaml\SamlConstants;

class SsoSpSendAuthnRequestActionBuilder extends AbstractProfileActionBuilder
{
    protected function doInitialize()
    {
        // Create AuthnRequest
        $this->add(new ResolveEndpointIdpSsoAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getEndpointResolver()
        ), 100);
        $this->add(new CreateAuthnRequestAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new SetRelayStateAction(
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
        $this->add(new DestinationAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new CreateMessageIssuerAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new NameIDPolicyAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            SamlConstants::NAME_ID_FORMAT_UNSPECIFIED //TODO move to SSODescriptor
        ));
        $this->add(new ACSAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getEndpointResolver(),
            [SamlConstants::BINDING_SAML2_HTTP_ARTIFACT, SamlConstants::BINDING_SAML2_HTTP_POST]
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

        // Send
        $this->add(new SendMessageAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getBindingFactory()
        ), 400);
    }
}
