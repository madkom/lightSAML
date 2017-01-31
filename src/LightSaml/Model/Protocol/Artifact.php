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
    const TYPE_CODE = 4;

    /**
     * @var int
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
     * @param int    $endpointIndex
     * @param string $sourceId
     * @param string $messageHandle
     */
    public function __construct($endpointIndex, $sourceId, $messageHandle)
    {
        $this->endpointIndex = $endpointIndex;
        $this->sourceId = $sourceId;
        $this->messageHandle = $messageHandle;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return base64_encode(hex2bin(
            str_pad(dechex(self::TYPE_CODE), 4, 0, STR_PAD_LEFT).
            str_pad(dechex($this->endpointIndex), 4, 0, STR_PAD_LEFT).
            $this->sourceId.
            $this->messageHandle
        ));
    }

    /**
     * @return int
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
