<?php

namespace App\Http\Services\RouterOSAuditSystem\ROSVersions;

use App\Http\Contracts\RouterOSAuditSystem\VersionInterface;
use App\Http\Traits\SocketTrait;
use RouterOS\Client;
use RouterOS\Exceptions\BadCredentialsException;
use RouterOS\Exceptions\ClientException;
use RouterOS\Exceptions\ConfigException;
use RouterOS\Exceptions\ConnectException;
use RouterOS\Exceptions\QueryException;
use RouterOS\Query;

class ROSV6 implements VersionInterface
{

    private Client $client;


    public function __construct(
        private readonly string $ip,
        private readonly string $username,
        private readonly string $password,
        private readonly int $port,
    )
    {
    }

    /**
     * @throws ClientException
     * @throws ConnectException
     * @throws BadCredentialsException
     * @throws QueryException
     * @throws ConfigException
     */
    public function connect(): self
    {
        $this->client = new Client([
            'host' => $this->ip,
            'user' => $this->username,
            'pass' => $this->password,
            'port' => $this->port,
        ]);

        return $this;
    }

    /**
     * @throws QueryException
     * @throws ClientException
     * @throws ConfigException
     */
    public function get(string $path = ""): array
    {
        $query = new Query($path);
        return $this->client->query($query)->read();
    }

}
