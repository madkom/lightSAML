<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Binding;

use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\MessageContext;
use LightSaml\Error\LightSamlBindingException;
use LightSaml\Error\LightSamlException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SoapBinding extends AbstractBinding
{
    /**
     * @var \SoapClient
     */
    private $soapClient;

    /**
     * @var string
     */
    private $action = 'http://www.oasis-open.org/committees/security';

    /**
     * @param \SoapClient $soapClient
     */
    public function setSoapClient(\SoapClient $soapClient)
    {
        $this->soapClient = $soapClient;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @param MessageContext $context
     * @param null|string    $destination
     *
     * @return Response
     */
    public function send(MessageContext $context, $destination = null)
    {
        if (is_null($this->soapClient)) {
            throw new LightSamlBindingException('SoapClient has not been set');
        }

        $message = MessageContextHelper::asSamlMessage($context);
        $destination = $message->getDestination() ? $message->getDestination() : $destination;

        $serializationContext = $context->getSerializationContext();
        $message->serialize($serializationContext->getDocument(), $serializationContext);
        $messageStr = $serializationContext->getDocument()->saveXML($serializationContext->getDocument()->documentElement);

        $soapRequest = $this->asSoapRequest($messageStr);

        $this->dispatchSend($soapRequest);

        $soapResponse = $this->soapClient->__doRequest($soapRequest, $destination, $this->action, SOAP_1_1);

        $this->dispatchReceive($soapResponse);

        return $this->createResponse($soapResponse, $this->soapClient->__getLastResponseHeaders());
    }

    /**
     * @param Request        $request
     * @param MessageContext $context
     */
    public function receive(Request $request, MessageContext $context)
    {
        throw new LightSamlException('Not implemented');
    }

    private function asSoapRequest($body)
    {
        return "<soap-env:Envelope xmlns:soap-env=\"http://schemas.xmlsoap.org/soap/envelope/\"><soap-env:Header/><soap-env:Body>{$body}</soap-env:Body></soap-env:Envelope>";
    }

    /**
     * @param $soapMessage
     * @param $rawHeaders
     *
     * @return Response
     */
    private function createResponse($soapMessage, $rawHeaders)
    {
        if (empty($soapMessage)) {
            throw new LightSamlBindingException('Empty SOAP Response');
        }

        if (empty($rawHeaders)) {
            throw new LightSamlBindingException('Headers has not been set, make sure SoapClients trace option is set to TRUE');
        }

        $headers = $this->parseRawHeaders($rawHeaders);

        $doc = new \DOMDocument('1.0', 'UTF-8');
        $doc->loadXML($soapMessage);

        $xpath = new \DOMXPath($doc);
        $xpath->registerNamespace('soap-env', 'http://schemas.xmlsoap.org/soap/envelope/');

        $messageNodeList = $xpath->query('/soap-env:Envelope/soap-env:Body/*[1]');

        if ($messageNodeList->length === 0) {
            throw new LightSamlBindingException('Empty SOAP Body element');
        }

        $content = $messageNodeList->item(0);

        return new Response($doc->saveXML($content), $headers['Status'], $headers);
    }

    private function parseRawHeaders($rawHeaders)
    {
        $rawHeaders = preg_split('/(\\r?\\n)/', trim($rawHeaders));

        if (empty($rawHeaders)) {
            throw new LightSamlBindingException('Headers has not been set, make sure SoapClients trace option is set to TRUE');
        }

        $headers = [];

        if (stripos($rawHeaders[0], 'HTTP') === 0) {
            list(, $statusCode) = explode(' ', $rawHeaders[0], 3);
            $headers['Status'] = trim($statusCode);
            unset($rawHeaders[0]);
        }

        array_walk($rawHeaders, function ($headerLine) use (&$headers) {
            list($name, $value) = explode(':', $headerLine, 2);
            $headers[trim($name)] = trim($value);
        });

        return $headers;
    }
}
