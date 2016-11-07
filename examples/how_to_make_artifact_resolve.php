<?php

require_once __DIR__.'/../autoload.php';

$artifactResolve = new \LightSaml\Model\Protocol\ArtifactResolve();
$artifactResolve
    ->setID(\LightSaml\Helper::generateID())
    ->setIssueInstant(new \DateTime())
    ->setIssuer(new \LightSaml\Model\Assertion\Issuer('https://my.entity.id'))
    ->setArtifact('artifact');

$serializationContext = new \LightSaml\Model\Context\SerializationContext();
$artifactResolve->serialize($serializationContext->getDocument(), $serializationContext);

echo $serializationContext->getDocument()->saveXML();
