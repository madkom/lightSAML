<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Builder\EntityDescriptor;

use LightSaml\Model\Metadata\ArtifactResolutionService;
use LightSaml\Model\Metadata\AssertionConsumerService;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Metadata\IdpSsoDescriptor;
use LightSaml\Model\Metadata\KeyDescriptor;
use LightSaml\Model\Metadata\RoleDescriptor;
use LightSaml\Model\Metadata\SingleSignOnService;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\Provider\EntityDescriptor\EntityDescriptorProviderInterface;
use LightSaml\Credential\X509Certificate;

class ArtifactSupportedEntityDescriptorBuilder implements EntityDescriptorProviderInterface
{
    /** @var string */
    protected $entityId;

    /** @var array */
    protected $acs;

    /** @var array */
    protected $sso;

    /** @var array */
    protected $spArs;

    /** @var array */
    protected $idpArs;

    /** @var string[]|null */
    protected $use;

    /** @var X509Certificate */
    protected $ownCertificate;

    /** @var EntityDescriptor */
    private $entityDescriptor;

    /**
     * @param string          $entityId
     * @param array           $acs
     * @param array           $sso
     * @param array           $spArs
     * @param array           $idpArs
     * @param X509Certificate $ownCertificate
     * @param string[]|null   $use
     */
    public function __construct(
        $entityId,
        array $acs = null,
        array $sso = null,
        array $spArs = null,
        array $idpArs = null,
        X509Certificate $ownCertificate,
        $use = array(KeyDescriptor::USE_ENCRYPTION, KeyDescriptor::USE_SIGNING)
    ) {
        $this->entityId = $entityId;
        $this->acs = $acs;
        $this->sso = $sso;
        $this->ownCertificate = $ownCertificate;
        $this->spArs = $spArs;
        $this->idpArs = $idpArs;
        $this->use = $use;
    }

    /**
     * @return EntityDescriptor
     */
    public function get()
    {
        if (null === $this->entityDescriptor) {
            $this->entityDescriptor = $this->getEntityDescriptor();
            if (false === $this->entityDescriptor instanceof EntityDescriptor) {
                throw new \LogicException('Expected EntityDescriptor');
            }
        }

        return $this->entityDescriptor;
    }

    /**
     * @return EntityDescriptor
     */
    protected function getEntityDescriptor()
    {
        $entityDescriptor = new EntityDescriptor();
        $entityDescriptor->setEntityID($this->entityId);

        $spSsoDescriptor = $this->getSpSsoDescriptor();
        if ($spSsoDescriptor) {
            $entityDescriptor->addItem($spSsoDescriptor);
        }

        $idpSsoDescriptor = $this->getIdpSsoDescriptor();
        if ($idpSsoDescriptor) {
            $entityDescriptor->addItem($idpSsoDescriptor);
        }

        return $entityDescriptor;
    }

    /**
     * @return SpSsoDescriptor|null
     */
    protected function getSpSsoDescriptor()
    {
        if (empty($this->acs)) {
            return null;
        }

        $spSso = new SpSsoDescriptor();

        foreach ($this->acs as $index => $acsDef) {
            $acs = new AssertionConsumerService();
            $acs->setIndex($index)->setLocation($acsDef['location'])->setBinding($acsDef['binding']);
            $spSso->addAssertionConsumerService($acs);
        }

        foreach ($this->spArs as $index => $arsDef) {
            $ars = new ArtifactResolutionService();
            $ars->setIndex($index)->setLocation($arsDef['location'])->setBinding($arsDef['binding']);
            $spSso->addArtifactResolutionService($ars);
        }

        $this->addKeyDescriptors($spSso);

        return $spSso;
    }

    /**
     * @return IdpSsoDescriptor
     */
    protected function getIdpSsoDescriptor()
    {
        if (empty($this->sso)) {
            return null;
        }

        $idpSso = new IdpSsoDescriptor();

        foreach ($this->sso as $index => $ssoDef) {
            $sso = new SingleSignOnService();
            $sso
                ->setLocation($ssoDef['location'])
                ->setBinding($ssoDef['binding']);
            $idpSso->addSingleSignOnService($sso);
        }

        foreach ($this->idpArs as $index => $arsDef) {
            $ars = new ArtifactResolutionService();
            $ars->setIndex($index)->setLocation($arsDef['location'])->setBinding($arsDef['binding']);
            $idpSso->addArtifactResolutionService($ars);
        }

        $this->addKeyDescriptors($idpSso);

        return $idpSso;
    }

    /**
     * @param RoleDescriptor $descriptor
     */
    protected function addKeyDescriptors(RoleDescriptor $descriptor)
    {
        if ($this->use) {
            foreach ($this->use as $use) {
                $kd = new KeyDescriptor();
                $kd->setUse($use);
                $kd->setCertificate($this->ownCertificate);

                $descriptor->addKeyDescriptor($kd);
            }
        } else {
            $kd = new KeyDescriptor();
            $kd->setCertificate($this->ownCertificate);

            $descriptor->addKeyDescriptor($kd);
        }
    }
}
