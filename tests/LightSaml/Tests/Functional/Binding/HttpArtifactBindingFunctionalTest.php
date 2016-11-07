<?php

namespace LightSaml\Tests\Functional\Binding;

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

        $expectedArtifact = 'test-artifact';

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
            $expectedArtifact,
            $data['SAMLart']
        );
        $this->assertEquals($expectedRelayState, $data['RelayState']);

        $this->assertEquals($expectedDestination, $response->getDestination());
    }

    public function test_receive_authn_request()
    {
        $expectedRelayState = 'relayState';
        $expectedArtifact = 'test-artifact';

        $request = new Request();
        $request->setMethod('POST');
        $request->request->add(array(
            'SAMLart' => $expectedArtifact,
            'RelayState' => $expectedRelayState,
        ));

        $binding = new HttpArtifactBinding();

        $eventDispatcherMock = $this->getEventDispatcherMock();
        $eventDispatcherMock->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function ($name, GenericEvent $event) use ($expectedArtifact) {
                $this->assertEquals(Events::BINDING_MESSAGE_RECEIVED, $name);
                $this->assertNotEmpty($event->getSubject());
                $this->assertEquals($expectedArtifact, $event->getSubject());
            });

        $binding->setEventDispatcher($eventDispatcherMock);
        $this->assertSame($eventDispatcherMock, $binding->getEventDispatcher());

        $messageContext = new MessageContext();
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
