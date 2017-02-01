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
 * Date: 18.10.2016
 * Time: 13:41.
 */
namespace LightSaml\Model\Protocol;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\SamlConstants;

class ArtifactResolve extends AbstractRequest
{
    /**
     * @var string
     */
    private $artifact;

    /**
     * @return string
     */
    public function getArtifact()
    {
        return $this->artifact;
    }

    /**
     * @param string $artifact
     *
     * @return $this
     */
    public function setArtifact($artifact)
    {
        $this->artifact = $artifact;

        return $this;
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('samlp:ArtifactResolve', SamlConstants::NS_PROTOCOL, $parent, $context);

        parent::serialize($result, $context);

        $this->singleElementsToXml(array('Artifact'), $result, $context, SamlConstants::NS_PROTOCOL);

        $this->singleElementsToXml(array('Signature'), $result, $context);
    }

    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'ArtifactResolve', SamlConstants::NS_PROTOCOL);

        parent::deserialize($node, $context);

        $this->singleElementsFromXml($node, $context, array(
            'Artifact' => array('samlp', ''),
        ));
    }
}
