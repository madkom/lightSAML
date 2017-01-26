<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Profile\Inbound\Message;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Binding\HttpArtifactBinding;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Context\Profile\ProfileContext;
use Psr\Log\LoggerInterface;

/**
 * Receives artifact from HTTP Request into inbound context.
 */
class ReceiveArtifactAction extends AbstractProfileAction
{
    /**
     * @var HttpArtifactBinding
     */
    private $binding;

    /**
     * @param LoggerInterface     $logger
     * @param HttpArtifactBinding $binding
     */
    public function __construct(LoggerInterface $logger, HttpArtifactBinding $binding)
    {
        parent::__construct($logger);

        $this->binding = $binding;
    }

    /**
     * @param ProfileContext $context
     */
    protected function doExecute(ProfileContext $context)
    {
        $this->binding->receive($context->getHttpRequest(), $context->getInboundContext());

        $this->logger->info(
            'Received artifact',
            LogHelper::getActionContext($context, $this, array(
                'artifact' => $context->getInboundContext()->getArtifact(),
            ))
        );
    }
}
