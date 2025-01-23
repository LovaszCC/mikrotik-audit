<?php

namespace App\Http\Actions\RouterOSAuditSystem;

use App\Http\Services\RouterOSAuditSystem\VersionController;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use RouterOS\Exceptions\BadCredentialsException;
use RouterOS\Exceptions\ClientException;
use RouterOS\Exceptions\ConfigException;
use RouterOS\Exceptions\ConnectException;
use RouterOS\Exceptions\QueryException;

final readonly class GetFirewallFilterList
{

    public function __construct(
        private string $ip,
        private string $username,
        private string $password,
        private string $version,
        private int $port = 8728,
    )
    {
    }

    /**
     * @throws ClientException
     * @throws ConnectException
     * @throws BadCredentialsException
     * @throws QueryException
     * @throws ConnectionException
     * @throws ConfigException
     */
    public function handle(string $command): JsonResponse|array
    {
        $version = new VersionController($this->version, $this->ip, $this->username, $this->password, $this->port)->getVersion();
        if($version === []) {
            return response()->json(['message' => 'Version not supported'], 400);
        }

        return $version->connect()->get($command);
    }

}
