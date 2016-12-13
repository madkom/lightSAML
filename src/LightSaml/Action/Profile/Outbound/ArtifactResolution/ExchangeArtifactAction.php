<?php
/**
 * Created by PhpStorm.
 * User: mhytry
 * Date: 14.11.2016
 * Time: 14:52.
 */
namespace LightSaml\Action\Profile\Outbound\ArtifactResolution;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Binding\BindingFactoryInterface;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlBindingException;
use LightSaml\Model\Protocol\ArtifactResponse;
use LightSaml\Model\Protocol\SamlMessage;
use LightSaml\SamlConstants;
use Psr\Log\LoggerInterface;

class ExchangeArtifactAction extends AbstractProfileAction
{
    /** @var BindingFactoryInterface */
    protected $bindingFactory;

    /**
     * @param LoggerInterface         $logger
     * @param BindingFactoryInterface $bindingFactory
     */
    public function __construct(LoggerInterface $logger, BindingFactoryInterface $bindingFactory)
    {
        parent::__construct($logger);

        $this->bindingFactory = $bindingFactory;
    }

    protected function doExecute(ProfileContext $context)
    {
        if ($this->validateSynchronousBinding($context->getEndpoint()->getBinding()) === false) {
            throw new LightSamlBindingException(sprintf('Binding %s is not synchronous', $context->getEndpoint()->getBinding()));
        }

        $binding = $this->bindingFactory->create($context->getEndpoint()->getBinding());

        $binding->setSoapClient(new \SoapClient(null, [
            'uri' => 'some-uri',
            'location' => $context->getEndpoint()->getLocation(),
            'trace' => true,
            'exceptions' => true,
        ]));
        $binding->setAction('');

        $outboundContext = $context->getOutboundContext();

        $this->logger->info(
            'Sending message',
            LogHelper::getActionContext($context, $this, array(
                'message' => $outboundContext->getSerializationContext()->getDocument()->saveXML(),
            ))
        );

        $synchronousResponse = $binding->send($outboundContext, $context->getEndpoint()->getLocation());

        if ($synchronousResponse->isOk() === false) {
            throw new LightSamlBindingException(sprintf('An error occured while exchanging artifact: %s', $synchronousResponse->getContent()));
        }

        /** @var ArtifactResponse $samlMessage */
        $samlMessage = SamlMessage::fromXML($synchronousResponse->getContent(), $context->getInboundContext()->getDeserializationContext());
        $context->getInboundContext()->setMessage($samlMessage);

        $this->logger->info(
            'Received message',
            LogHelper::getActionContext($context, $this, array(
                'message' => $synchronousResponse->getContent(),
            ))
        );
    }

    /**
     * Validates if binding is synchronous.
     *
     * @param $binding
     *
     * @return bool
     */
    private function validateSynchronousBinding($binding)
    {
        return in_array($binding, [
            SamlConstants::BINDING_SAML2_SOAP,
        ]);
    }
}
