<?php
/**
 * Created by PhpStorm.
 * User: mhytry
 * Date: 10.11.2016
 * Time: 11:02.
 */
namespace LightSaml\Store\Artifact;

use LightSaml\Binding\Artifact;
use LightSaml\Model\Protocol\SamlMessage;

interface ArtifactStoreInterface
{
    public function set(Artifact $artifact, SamlMessage $message);

    public function get(Artifact $artifact);

    public function remove(Artifact $artifact);
}
