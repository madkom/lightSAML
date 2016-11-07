<?php
/**
 * Created by PhpStorm.
 * User: mhytry
 * Date: 25.10.2016
 * Time: 14:19.
 */
namespace LightSaml\Binding;

use LightSaml\Context\Profile\MessageContext;
use LightSaml\Error\LightSamlBindingException;
use Symfony\Component\HttpFoundation\Request;

class HttpArtifactBinding extends AbstractBinding
{
    /**
     * @param MessageContext $context
     * @param null|string    $destination
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function send(MessageContext $context, $destination = null)
    {
        $artifact = $context->getArtifact();

        if (empty($artifact)) {
            throw new LightSamlBindingException('Artifact must be set and not empty');
        }

        $this->dispatchSend($artifact);

        $data = array('SAMLart' => $artifact);
        if ($context->getRelayState()) {
            $data['RelayState'] = $context->getRelayState();
        }

        $result = new SamlPostResponse($destination, $data);
        $result->renderContent();

        return $result;
    }

    /**
     * @param Request        $request
     * @param MessageContext $context
     *
     * @throws \Exception
     */
    public function receive(Request $request, MessageContext $context)
    {
        $artifact = $request->get('SAMLart');

        if (empty($artifact)) {
            throw new LightSamlBindingException('Parameter SAMLart must be set and not empty');
        }

        $this->dispatchReceive($artifact);

        $relayState = $request->get('RelayState');
        if (!empty($relayState)) {
            $context->setRelayState($relayState);
        }

        $context->setArtifact($artifact);
    }
}
