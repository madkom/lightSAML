<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Profile\Outbound\Message;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Protocol\NameIDPolicy;
use Psr\Log\LoggerInterface;

/**
 * Sets the NameIDPolicy of the outbound message to the value of own entityID.
 */
class NameIDPolicyAction extends AbstractProfileAction
{
    private $format;

    private $allowCreate;

    public function __construct(LoggerInterface $logger, $format, $allowCreate = false)
    {
        parent::__construct($logger);

        $this->format = $format;
        $this->allowCreate = $allowCreate;
    }

    /**
     * @param ProfileContext $context
     */
    protected function doExecute(ProfileContext $context)
    {
        $nameIDPolicy = new NameIDPolicy($this->format, $this->allowCreate);

        MessageContextHelper::asSamlMessage($context->getOutboundContext())
            ->setNameIDPolicy($nameIDPolicy);

        $this->logger->debug(
            sprintf('NameIDPolicy set with params: format: "%s", allowCreate: "%s"', $this->format, $this->allowCreate),
            LogHelper::getActionContext($context, $this)
        );
    }
}
