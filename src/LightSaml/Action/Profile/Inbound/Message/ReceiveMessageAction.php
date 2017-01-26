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

use LightSaml\Action\ActionInterface;
use LightSaml\Action\DebugPrintTreeActionInterface;
use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Binding\BindingFactoryInterface;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlBindingException;
use LightSaml\SamlConstants;
use Psr\Log\LoggerInterface;

/**
 * Receives message from HTTP Request into inbound context,
 * optionally enforces biding type to the one specified in the inbound context.
 */
class ReceiveMessageAction extends AbstractProfileAction implements DebugPrintTreeActionInterface
{
    /** @var BindingFactoryInterface */
    protected $bindingFactory;

    /**
     * @var ActionInterface
     */
    private $exchengeArtifactForResponseAction;

    /**
     * @param LoggerInterface         $logger
     * @param BindingFactoryInterface $bindingFactory
     * @param ActionInterface         $exchengeArtifactForResponseAction
     */
    public function __construct(LoggerInterface $logger, BindingFactoryInterface $bindingFactory, ActionInterface $exchengeArtifactForResponseAction)
    {
        parent::__construct($logger);

        $this->bindingFactory = $bindingFactory;
        $this->exchengeArtifactForResponseAction = $exchengeArtifactForResponseAction;
    }

    /**
     * @param ProfileContext $context
     */
    protected function doExecute(ProfileContext $context)
    {
        $bindingType = $this->bindingFactory->detectBindingType($context->getHttpRequest());
        if (null == $bindingType) {
            $message = 'Unable to resolve binding type, invalid or unsupported http request';
            $this->logger->critical($message, LogHelper::getActionErrorContext($context, $this));
            throw new LightSamlBindingException($message);
        }

        $this->logger->debug(sprintf('Detected binding type: %s', $bindingType), LogHelper::getActionContext($context, $this));

        if ($bindingType === SamlConstants::BINDING_SAML2_HTTP_ARTIFACT) {
            $this->exchengeArtifactForResponseAction->execute($context);
        } else {
            $binding = $this->bindingFactory->create($bindingType);
            $binding->receive($context->getHttpRequest(), $context->getInboundContext());
        }

        $context->getInboundContext()->setBindingType($bindingType);

        $this->logger->info(
            'Received message',
            LogHelper::getActionContext($context, $this, array(
                'message' => $context->getInboundContext()->getDeserializationContext()->getDocument()->saveXML(),
            ))
        );
    }

    /**
     * @param int $depth
     *
     * @return array
     */
    public function debugPrintTree($depth = 0)
    {
        $arr = array();
        if ($this->exchengeArtifactForResponseAction instanceof DebugPrintTreeActionInterface) {
            $arr = array_merge($arr, $this->exchengeArtifactForResponseAction->debugPrintTree());
        } else {
            $arr[get_class($this->exchengeArtifactForResponseAction)] = array();
        }

        $result = array(
            static::class => $arr,
        );

        return $result;
    }
}
