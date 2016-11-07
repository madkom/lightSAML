<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Profile\Outbound;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\ProfileContext;

class ForwardRelayStateAction extends AbstractProfileAction
{
    protected function doExecute(ProfileContext $context)
    {
        if (null == $context->getInboundContext()->getMessage()) {
            return;
        }

        if ($context->getInboundContext()->getRelayState()) {
            $this->logger->debug(sprintf('Forwarding relay state from inbound context: "%s"', $context->getInboundContext()->getRelayState()));
            $context->getOutboundContext()->setRelayState(
                $context->getInboundContext()->getRelayState()
            );
        }
    }
}
