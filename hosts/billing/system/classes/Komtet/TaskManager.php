<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

#namespace Komtet\KassaSdk;

class TaskManager
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Returns info about the task
     *
     * @param string $taskId Task ID
     *
     * @return mixed
     */
    public function getTaskInfo($taskId)
    {
        $path = sprintf('api/shop/v1/tasks/%s', $taskId);
        return $this->client->sendRequest($path);
    }
}
