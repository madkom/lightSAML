<?php
/**
 * Created by PhpStorm.
 * User: mhytry
 * Date: 25.10.2016
 * Time: 11:56.
 */
namespace LightSaml\Model\Metadata;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\SamlConstants;

class ArtifactResolutionService extends IndexedEndpoint
{
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('ArtifactResolutionService', SamlConstants::NS_METADATA, $parent, $context);
        parent::serialize($result, $context);
    }

    /**
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'ArtifactResolutionService', SamlConstants::NS_METADATA);
        parent::deserialize($node, $context);
    }
}
