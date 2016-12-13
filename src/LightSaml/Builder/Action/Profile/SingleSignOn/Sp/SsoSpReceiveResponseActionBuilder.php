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

use LightSaml\Action\Profile\Inbound\Message\AssertBindingTypeAction;
use LightSaml\Action\Profile\Inbound\Message\ReceiveMessageAction;
use LightSaml\Action\Profile\Inbound\Response\ValidatorAction;
use LightSaml\Build\Container\BuildContainerInterface;
use LightSaml\Builder\Action\ActionBuilderInterface;
use LightSaml\Builder\Action\Profile\AbstractProfileActionBuilder;
use LightSaml\SamlConstants;

class SsoSpReceiveResponseActionBuilder extends AbstractProfileActionBuilder
{
    /** @var ActionBuilderInterface */
    private $responseValidatorActionBuilder;

    /**
     * @param BuildContainerInterface $buildContainer
     * @param ActionBuilderInterface  $responseValidatorActionBuilder
     */
    public function __construct(BuildContainerInterface $buildContainer, ActionBuilderInterface $responseValidatorActionBuilder)
    {
        parent::__construct($buildContainer);

        $this->responseValidatorActionBuilder = $responseValidatorActionBuilder;
    }

    protected function doInitialize()
    {
        // Receive
        $this->add(new ReceiveMessageAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getBindingFactory()
        ), 100);
        $this->add(new AssertBindingTypeAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            array(SamlConstants::BINDING_SAML2_HTTP_POST)
        ));

        // Response validation
        $this->add(new ValidatorAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->responseValidatorActionBuilder->build()
        ));
    }
}
