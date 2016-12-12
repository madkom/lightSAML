<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Profile\Outbound\AuthnRequest;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Error\LightSamlContextException;
use LightSaml\Model\Metadata\AssertionConsumerService;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\Resolver\Endpoint\Criteria\BindingCriteria;
use LightSaml\Resolver\Endpoint\Criteria\DescriptorTypeCriteria;
use LightSaml\Resolver\Endpoint\Criteria\ServiceTypeCriteria;
use LightSaml\Resolver\Endpoint\EndpointResolverInterface;
use Psr\Log\LoggerInterface;

class ACSAction extends AbstractProfileAction
{
    /** @var EndpointResolverInterface */
    private $endpointResolver;

    /**
     * @var array
     */
    private $bindingCriteria;

    public function __construct(LoggerInterface $logger, EndpointResolverInterface $endpointResolver, array $bindingCriteria)
    {
        parent::__construct($logger);

        $this->endpointResolver = $endpointResolver;
        $this->bindingCriteria = $bindingCriteria;
    }

    protected function doExecute(ProfileContext $context)
    {
        $ownEntityDescriptor = $context->getOwnEntityDescriptor();

        $criteriaSet = new CriteriaSet([
            new DescriptorTypeCriteria(SpSsoDescriptor::class),
            new ServiceTypeCriteria(AssertionConsumerService::class),
            new BindingCriteria($this->bindingCriteria),
        ]);

        $endpoints = $this->endpointResolver->resolve($criteriaSet, $ownEntityDescriptor->getAllEndpoints());
        if (empty($endpoints)) {
            $message = sprintf('Missing ACS Service with %s binding in own SP SSO Descriptor', implode(', ', $this->bindingCriteria));
            $this->logger->error($message, LogHelper::getActionErrorContext($context, $this));
            throw new LightSamlContextException($context, $message);
        }

        MessageContextHelper::asAuthnRequest($context->getOutboundContext())
            ->setAssertionConsumerServiceURL($endpoints[0]->getEndpoint()->getLocation())
            ->setProtocolBinding($endpoints[0]->getEndpoint()->getBinding());
    }
}
