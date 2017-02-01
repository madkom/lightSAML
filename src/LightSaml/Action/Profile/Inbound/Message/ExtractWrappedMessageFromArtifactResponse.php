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
 * Date: 07.12.2016
 * Time: 15:45.
 */
namespace LightSaml\Action\Profile\Inbound\Message;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlActionException;
use LightSaml\Model\Protocol\ArtifactResponse;

class ExtractWrappedMessageFromArtifactResponse extends AbstractProfileAction
{
    /**
     * @param ProfileContext $context
     */
    protected function doExecute(ProfileContext $context)
    {
        /** @var ArtifactResponse $message */
        $message = $context->getInboundContext()->getMessage();

        if (!($message instanceof ArtifactResponse)) {
            throw new LightSamlActionException(sprintf('Message must by ArtifactResponse type, %s given', get_class($message)));
        }

        $samlMessage = $message->getWrappedMessage();
        $context->getInboundContext()->setMessage($samlMessage);

//        $samlMessage->serialize($context->getInboundContext()->getSerializationContext()->getDocument(), $context->getInboundContext()->getSerializationContext());

        $this->logger->info(
            'Extracted Saml message'//,
//            LogHelper::getActionContext($context, $this, array(
//                'message' => $context->getInboundContext()->getSerializationContext()->getDocument()->saveXML()
//            ))
        );
    }
}
