<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Created by PhpStorm.
 * User: mhytry
 * Date: 10.11.2016
 * Time: 11:02.
 */
namespace LightSaml\Store\Artifact;

use LightSaml\Model\Protocol\Artifact;
use LightSaml\Model\Protocol\SamlMessage;

interface ArtifactStoreInterface
{
    public function set(Artifact $artifact, SamlMessage $message);

    public function get(Artifact $artifact);

    public function remove(Artifact $artifact);
}
