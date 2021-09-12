<?php
declare(strict_types = 1);

namespace Genius257\ViewFileLanguageServer\Cache;

use Genius257\ViewFileLanguageServer\LanguageClient;
use Sabre\Event\Promise;

/**
 * Caches content through a xcache/* requests
 */
class ClientCache implements Cache
{
    /**
     * @var LanguageClient
     */
    public $client;

    /**
     * @param LanguageClient $client
     */
    public function __construct(LanguageClient $client)
    {
        $this->client = $client;
    }

    /**
     * Gets a value from the cache
     *
     * @param string $key
     * @return Promise <mixed>
     */
    public function get(string $key): Promise
    {
        return $this->client->xcache->get($key)->then('unserialize')->otherwise(function () {
            // Ignore
        });
    }

    /**
     * Sets a value in the cache
     *
     * @param string $key
     * @param mixed  $value
     * @return Promise
     */
    public function set(string $key, $value): Promise
    {
        return $this->client->xcache->set($key, serialize($value))->otherwise(function () {
            // Ignore
        });
    }
}
