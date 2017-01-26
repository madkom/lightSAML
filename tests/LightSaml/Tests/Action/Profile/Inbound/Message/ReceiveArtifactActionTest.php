<?php

namespace LightSaml\Tests\Action\Profile\Inbound\Message;

use LightSaml\Action\Profile\Inbound\Message\ReceiveArtifactAction;
use LightSaml\Binding\HttpArtifactBinding;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Profile\Profiles;
use LightSaml\Tests\TestHelper;
use Symfony\Component\HttpFoundation\Request;

class ReceiveArtifactActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger_and_binding_factory()
    {
        new ReceiveArtifactAction(TestHelper::getLoggerMock($this), $this->getHttpArtifactBindingMock());
    }

    public function test_receives_artifact()
    {
        $action = new ReceiveArtifactAction($logger = TestHelper::getLoggerMock($this), $binding = $this->getHttpArtifactBindingMock());

        $context = new ProfileContext(Profiles::SSO_SP_RECEIVE_RESPONSE, ProfileContext::ROLE_SP);
        $context->getHttpRequestContext()->setRequest($request = new Request());

        $binding->expects($this->once())
            ->method('receive')
            ->with($request, $context->getInboundContext())
        ;

        $logger->expects($this->once())
            ->method('info')
            ->with('Received artifact', $this->isType('array'))
        ;

        $action->execute($context);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Binding\BindingFactoryInterface
     */
    private function getHttpArtifactBindingMock()
    {
        return $this->getMock(HttpArtifactBinding::class);
    }
}
