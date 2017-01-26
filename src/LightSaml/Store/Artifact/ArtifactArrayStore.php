<?php
/**
 * Created by PhpStorm.
 * User: mhytry
 * Date: 10.11.2016
 * Time: 12:38.
 */
namespace LightSaml\Store\Artifact;

use LightSaml\Model\Protocol\Artifact;
use LightSaml\Model\Protocol\SamlMessage;

class ArtifactArrayStore implements ArtifactStoreInterface
{
    private $store = [];

    public function set(Artifact $artifact, SamlMessage $message)
    {
        $this->store[(string) $artifact] = $message;
    }

    public function get(Artifact $artifact)
    {
        return array_key_exists((string) $artifact, $this->store) ? $this->store[(string) $artifact] : null;
    }

    public function remove(Artifact $artifact)
    {
        unset($this->store[(string) $artifact]);
    }
}
