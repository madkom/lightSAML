<?php

namespace LightSaml\Tests\Model\Protocol;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Protocol\ArtifactResolve;
use LightSaml\SamlConstants;

class ArtifactResolveTest extends \PHPUnit_Framework_TestCase
{
    public function test__serialize()
    {
        $context = new SerializationContext();
        $artifactResolve = new ArtifactResolve();
        $artifactResolve->setArtifact('test-artifact')
            ->setID('test-id')
            ->setIssueInstant(new \DateTime('2013-10-10T15:26:20Z'))
            ->setDestination('http://destination.com/resolve')
            ->setConsent(SamlConstants::CONSENT_UNSPECIFIED)
            ->setIssuer((new Issuer())->setValue('test-issuer'));

        $artifactResolve->serialize($context->getDocument(), $context);

        $expectedXml = <<<'EOT'
<samlp:ArtifactResolve xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol" Version="2.0" IssueInstant="2013-10-10T15:26:20Z" ID="test-id" Destination="http://destination.com/resolve" Consent="urn:oasis:names:tc:SAML:2.0:consent:unspecified">
			<saml:Issuer xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion">test-issuer</saml:Issuer>
			<samlp:Artifact>test-artifact</samlp:Artifact>
		</samlp:ArtifactResolve>
EOT;

        $this->assertXmlStringEqualsXmlString($expectedXml, $context->getDocument()->saveXML());
    }

    public function test_deserialize()
    {
        $xml = <<<'EOT'
<samlp:ArtifactResolve xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol" Version="2.0" IssueInstant="2013-10-10T15:26:20Z" ID="test-id" Destination="http://destination.com/resolve" Consent="urn:oasis:names:tc:SAML:2.0:consent:unspecified">
			<saml:Issuer xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion">test-issuer</saml:Issuer>
			<samlp:Artifact>test-artifact</samlp:Artifact>
		</samlp:ArtifactResolve>
EOT;

        $context = new DeserializationContext();
        $context->getDocument()->loadXML($xml);

        $artifactResolve = new ArtifactResolve();

        $artifactResolve->deserialize($context->getDocument()->firstChild, $context);

        $this->assertEquals('test-artifact', $artifactResolve->getArtifact());
        $this->assertEquals('2013-10-10T15:26:20Z', $artifactResolve->getIssueInstantString());
        $this->assertEquals('http://destination.com/resolve', $artifactResolve->getDestination());
        $this->assertEquals(SamlConstants::CONSENT_UNSPECIFIED, $artifactResolve->getConsent());
        $this->assertEquals('test-issuer', $artifactResolve->getIssuer()->getValue());
    }
}
