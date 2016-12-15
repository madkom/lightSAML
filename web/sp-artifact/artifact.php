<?php

require_once __DIR__.'/_config.php';

try {
    $buildContainer = SpConfig::current()->getBuildContainer();
    $builder = new \LightSaml\Builder\Profile\ArtifactResolution\SsoArtifactResolveResponseProfileBuilder($buildContainer);

    $buildContainer->getSystemContainer()->getEventDispatcher()
        ->addListener(
            \LightSaml\Event\Events::BINDING_MESSAGE_SENT,
            function (\Symfony\Component\EventDispatcher\GenericEvent $event) {
                file_put_contents(__DIR__.'/out', $event->getSubject()."\n\n", FILE_APPEND);
            }
        );
    $buildContainer->getSystemContainer()->getEventDispatcher()
        ->addListener(
            \LightSaml\Event\Events::BINDING_MESSAGE_RECEIVED,
            function (\Symfony\Component\EventDispatcher\GenericEvent $event) {
                file_put_contents(__DIR__.'/in', $event->getSubject()."\n\n", FILE_APPEND);
            }
        );

    $context = $builder->buildContext();
    $action = $builder->buildAction();

    $action->execute($context);

    $response = \LightSaml\Context\Profile\Helper\MessageContextHelper::asResponse($context->getInboundContext());

    var_dump('RELAY STATE');
    var_dump($context->getInboundContext()->getRelayState());

    var_dump('ATTRIBUTES');
    foreach ($response->getAllAssertions() as $assertion) {
        foreach ($assertion->getAllAttributeStatements() as $attributeStatement) {
            foreach ($attributeStatement->getAllAttributes() as $attribute) {
                var_dump($attribute);
            }
        }
    }

    /** @var \LightSaml\Model\Context\DeserializationContext $inboundMessageDeserializationContext */
    $inboundMessageDeserializationContext = $context->getPath('inbound_message/deserialization');
    $inboundMessageDeserializationContext->getDocument()->formatOutput = true;
    var_dump('RECEIVED MESSAGE');
    var_dump($inboundMessageDeserializationContext->getDocument()->saveXML());

    /** @var \LightSaml\Model\Context\DeserializationContext $decryptedAssertionContext */
    $decryptedAssertionContext = $context->getPath('inbound_message/assertion_encrypted_0');
    if ($decryptedAssertionContext) {
        $decryptedAssertionContext->getDocument()->formatOutput = true;
        var_dump('DECRYPTED ASSERTION');
        var_dump($decryptedAssertionContext->getDocument()->saveXML());
    }
} catch (\Exception $e) {
    var_dump('ERROR');
    var_dump($e->getMessage());
    var_dump($e->getTraceAsString());

    var_dump('ACTION TREE');
    var_dump($action->__toString());

    var_dump('CONTEXT TREE ERROR');
    var_dump($context->__toString());
}
