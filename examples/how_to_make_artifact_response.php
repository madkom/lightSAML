<?php

require_once __DIR__.'/../autoload.php';

$response = new \LightSaml\Model\Protocol\Response();
$response->setID(\LightSaml\Helper::generateID())
    ->setInResponseTo('response-to-response')
    ->setIssueInstant(new \DateTime())
    ->setIssuer(new \LightSaml\Model\Assertion\Issuer('https://my.entity.id'))
;

$artifactResponse = new \LightSaml\Model\Protocol\ArtifactResponse();
$artifactResponse
    ->setID(\LightSaml\Helper::generateID())
    ->setIssueInstant(new \DateTime())
    ->setIssuer(new \LightSaml\Model\Assertion\Issuer('https://my.entity.id'))
    ->setInResponseTo('response-to')
    ->setWrappedMessage($response)
;

$certificate = \LightSaml\Credential\X509Certificate::fromFile(__DIR__.'/../resources/cert/broker.madkom.pl.crt');
$privateKey = \LightSaml\Credential\KeyHelper::createPrivateKey(__DIR__.'/../resources/cert/broker.madkom.pl.pem', '', true);

$artifactResponse->setSignature(new \LightSaml\Model\XmlDSig\SignatureWriter($certificate, $privateKey));

$serializationContext = new \LightSaml\Model\Context\SerializationContext();
$artifactResponse->serialize($serializationContext->getDocument(), $serializationContext);

$serializationContext->getDocument()->preserveWhiteSpace = false;
$serializationContext->getDocument()->formatOutput = true;
echo $serializationContext->getDocument()->saveXML();
