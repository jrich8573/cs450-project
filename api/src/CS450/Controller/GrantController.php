<?php

namespace CS450\Controller;

/**
 * @codeCoverageIgnore
 */
class GrantController
{
    /**
     * @Inject
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @Inject
     * @var CS450\Service\DbService
     */
    private $db;

    /**
     * @Inject
     * @var CS450\Model\GrantFactory
     */
    private $grantFactory;

    public function __invoke($params)
    {
        return $this->grantFactory->findAll();
    }
}