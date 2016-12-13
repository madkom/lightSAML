<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Context\Profile;

use LightSaml\Model\Protocol\ArtifactGenerator;
use LightSaml\Store\Artifact\ArtifactStoreInterface;

class ArtifactContext extends AbstractProfileContext
{
    /**
     * @var ArtifactStoreInterface
     */
    private $store;

    /**
     * @var ArtifactGenerator
     */
    private $generator;

    /**
     * @return ArtifactStoreInterface
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * @param ArtifactStoreInterface $store
     */
    public function setStore(ArtifactStoreInterface $store)
    {
        $this->store = $store;
    }

    /**
     * @return ArtifactGenerator
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * @param ArtifactGenerator $generator
     */
    public function setGenerator(ArtifactGenerator $generator)
    {
        $this->generator = $generator;
    }
}
