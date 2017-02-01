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
 * Time: 14:52.
 */
namespace LightSaml\Model\Protocol;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\SamlConstants;

class ArtifactResponse extends StatusResponse
{
    /**
     * @var SamlMessage
     */
    protected $wrappedMessage;

    /**
     * @return SamlMessage
     */
    public function getWrappedMessage()
    {
        return $this->wrappedMessage;
    }

    /**
     * @param SamlMessage $wrappedMessage
     *
     * @return $this
     */
    public function setWrappedMessage(SamlMessage $wrappedMessage)
    {
        $this->wrappedMessage = $wrappedMessage;

        return $this;
    }

    public function setResponse(Response $wrappedMessage)
    {
        return $this->setWrappedMessage($wrappedMessage);
    }

    public function setAuthnRequest(AuthnRequest $wrappedMessage)
    {
        return $this->setWrappedMessage($wrappedMessage);
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('samlp:ArtifactResponse', SamlConstants::NS_PROTOCOL, $parent, $context);

        parent::serialize($result, $context);

        // must be done here at the end and not in a base class where declared in order to include signing of the elements added here
        $this->singleElementsToXml(array('Signature'), $result, $context);

        $this->singleElementsToXml(array('WrappedMessage'), $result, $context);
    }

    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'ArtifactResponse', SamlConstants::NS_PROTOCOL);

        parent::deserialize($node, $context);

        $this->singleElementsFromXml($node, $context, array(
            'Response' => array('samlp', 'LightSaml\Model\Protocol\Response'),
            'AuthnRequest' => array('samlp', 'LightSaml\Model\Protocol\AuthnRequest'),
        ));
    }
}
