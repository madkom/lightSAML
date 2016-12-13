<?php
/**
 * Created by PhpStorm.
 * User: mhytry
 * Date: 10.11.2016
 * Time: 12:12.
 */
namespace LightSaml\Model\Protocol;

class Artifact
{
    private $typeCode;

    /**
     * @var string
     */
    private $endpointIndex;

    private $sourceId;

    private $messageHandle;

    public function __construct($typeCode, $endpointIndex, $sourceId, $messageHandle)
    {
        $this->typeCode = $typeCode;
        $this->endpointIndex = $endpointIndex;
        $this->sourceId = $sourceId;
        $this->messageHandle = $messageHandle;
    }

    public function __toString()
    {
        return base64_encode(hex2bin($this->typeCode.$this->endpointIndex.$this->sourceId.$this->messageHandle));
    }

    /**
     * @return int
     */
    public function getEndpointIndex()
    {
        return (int) $this->endpointIndex;
    }

    public function getSourceId()
    {
        return $this->sourceId;
    }
}
