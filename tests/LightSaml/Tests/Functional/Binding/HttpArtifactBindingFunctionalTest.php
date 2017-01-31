<?php

namespace LightSaml\Tests\Functional\Binding;

use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Protocol\Artifact;
use LightSaml\Binding\HttpArtifactBinding;
use LightSaml\Context\Profile\MessageContext;
use LightSaml\Event\Events;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;

class HttpArtifactBindingFunctionalTest extends \PHPUnit_Framework_TestCase
{
    public function test_send_artifact()
    {
        $expectedRelayState = 'relayState';
        $expectedDestination = 'https://destination.com/auth';

        $expectedArtifact = new Artifact(0, sha1('http://testsp.com'), bin2hex(openssl_random_pseudo_bytes(20)));

        $messageContext = new MessageContext();
        $messageContext->setRelayState($expectedRelayState);
        $messageContext->setArtifact($expectedArtifact);

        $binding = new HttpArtifactBinding();

        $eventDispatcherMock = $this->getEventDispatcherMock();
        $eventDispatcherMock->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function ($name, GenericEvent $event) use ($expectedArtifact) {
                $this->assertEquals(Events::BINDING_MESSAGE_SENT, $name);
                $this->assertNotEmpty($event->getSubject());
                $this->assertEquals($expectedArtifact, $event->getSubject());
            });

        $binding->setEventDispatcher($eventDispatcherMock);
        $this->assertSame($eventDispatcherMock, $binding->getEventDispatcher());

        /** @var \LightSaml\Binding\SamlPostResponse $response */
        $response = $binding->send($messageContext, $expectedDestination);

        $this->assertInstanceOf('LightSaml\Binding\SamlPostResponse', $response);

        $data = $response->getData();

        $this->assertArrayHasKey('SAMLart', $data);
        $this->assertArrayHasKey('RelayState', $data);
        $this->assertEquals(
            (string) $expectedArtifact,
            $data['SAMLart']
        );
        $this->assertEquals($expectedRelayState, $data['RelayState']);

        $this->assertEquals($expectedDestination, $response->getDestination());
    }

    public function test_receive_authn_request()
    {
        $expectedRelayState = 'relayState';
        $expectedArtifact = new Artifact(0, sha1('http://testsp.com'), bin2hex(openssl_random_pseudo_bytes(20)));

        $request = new Request();
        $request->setMethod('POST');
        $request->request->add(array(
            'SAMLart' => (string) $expectedArtifact,
            'RelayState' => $expectedRelayState,
        ));

        $binding = new HttpArtifactBinding();

        $eventDispatcherMock = $this->getEventDispatcherMock();
        $eventDispatcherMock->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function ($name, GenericEvent $event) use ($expectedArtifact) {
                $this->assertEquals(Events::BINDING_MESSAGE_RECEIVED, $name);
                $this->assertNotEmpty($event->getSubject());
                $this->assertEquals((string) $expectedArtifact, $event->getSubject());
            });

        $binding->setEventDispatcher($eventDispatcherMock);
        $this->assertSame($eventDispatcherMock, $binding->getEventDispatcher());

        $profileContext = new ProfileContext('test', ProfileContext::ROLE_SP);

        $messageContext = new MessageContext();
        $messageContext->setParent($profileContext);

        $binding->receive($request, $messageContext);

        $this->assertEmpty($messageContext->getMessage());

        $this->assertNotEmpty($messageContext->getArtifact());
        $this->assertEquals($expectedArtifact, $messageContext->getArtifact());

        $this->assertEquals($expectedRelayState, $messageContext->getRelayState());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private function getEventDispatcherMock()
    {
        return $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
    }
}
