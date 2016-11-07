<?php

namespace LightSaml\Tests\Binding;

use LightSaml\Binding\HttpArtifactBinding;
use LightSaml\Context\Profile\MessageContext;
use Symfony\Component\HttpFoundation\Request;

class HttpArtifactBindingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \LightSaml\Error\LightSamlBindingException
     * @expectedExceptionMessage Parameter SAMLart must be set and not empty
     */
    public function test__receive_throws_when_no_artifact()
    {
        $request = new Request();

        $binding = new HttpArtifactBinding();

        $messageContext = new MessageContext();

        $binding->receive($request, $messageContext);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlBindingException
     * @expectedExceptionMessage Artifact must be set and not empty
     */
    public function test__send_throws_when_no_artifact()
    {
        $messageContext = new MessageContext();

        $binding = new HttpArtifactBinding();

        $binding->send($messageContext);
    }
}
