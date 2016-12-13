<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Profile\Inbound\Response;

use LightSaml\Action\ActionInterface;
use LightSaml\Action\DebugPrintTreeActionInterface;
use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\ProfileContext;
use Psr\Log\LoggerInterface;

class ValidatorAction extends AbstractProfileAction implements DebugPrintTreeActionInterface
{
    /** @var ActionInterface */
    private $validatorAction;

    /**
     * @param LoggerInterface $logger
     * @param ActionInterface $validatorAction
     */
    public function __construct(LoggerInterface $logger, ActionInterface $validatorAction)
    {
        parent::__construct($logger);

        $this->validatorAction = $validatorAction;
    }

    /**
     * @param ProfileContext $context
     */
    protected function doExecute(ProfileContext $context)
    {
        $this->validatorAction->execute($context);
    }

    /**
     * @return array
     */
    public function debugPrintTree()
    {
        $arr = array();
        if ($this->validatorAction instanceof DebugPrintTreeActionInterface) {
            $arr = array_merge($arr, $this->validatorAction->debugPrintTree());
        } else {
            $arr[get_class($this->validatorAction)] = array();
        }

        $result = array(
            static::class => $arr,
        );

        return $result;
    }
}
