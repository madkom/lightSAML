<?php

namespace LightSaml\Tests\Binding;

use LightSaml\Binding\SoapBinding;
use LightSaml\Context\Profile\MessageContext;
use Symfony\Component\HttpFoundation\Request;

class SoapBindingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \LightSaml\Error\LightSamlBindingException
     * @expectedExceptionMessage SoapClient has not been set
     */
    public function test__send_throws_when_no_SoapClient()
    {
        $binding = new SoapBinding();

        $messageContext = new MessageContext();

        $binding->send($messageContext);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlException
     * @expectedExceptionMessage Not implemented
     */
    public function test__receive_throws_not_implemented()
    {
        $binding = new SoapBinding();

        $request = new Request();

        $messageContext = new MessageContext();

        $binding->receive($request, $messageContext);
    }
}
