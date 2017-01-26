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
    /**
     * @var string
     */
    private $typeCode;

    /**
     * @var string
     */
    private $endpointIndex;

    /**
     * @var string
     */
    private $sourceId;

    /**
     * @var string
     */
    private $messageHandle;

    /**
     * Artifact constructor.
     *
     * @param string $typeCode
     * @param string $endpointIndex
     * @param string $sourceId
     * @param string $messageHandle
     */
    public function __construct($typeCode, $endpointIndex, $sourceId, $messageHandle)
    {
        $this->typeCode = $typeCode;
        $this->endpointIndex = $endpointIndex;
        $this->sourceId = $sourceId;
        $this->messageHandle = $messageHandle;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return base64_encode(hex2bin($this->typeCode.$this->endpointIndex.$this->sourceId.$this->messageHandle));
    }

    /**
     * @return string
     */
    public function getEndpointIndex()
    {
        return $this->endpointIndex;
    }

    /**
     * @return string
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }
}
