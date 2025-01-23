<?php

declare(strict_types=1);

namespace App\Http\Actions\RouterOSAuditSystem;

use App\Http\Cheks\FirewallChecks;
use App\Http\Services\RouterOSAuditSystem\VersionController;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use RouterOS\Exceptions\BadCredentialsException;
use RouterOS\Exceptions\ClientException;
use RouterOS\Exceptions\ConfigException;
use RouterOS\Exceptions\ConnectException;
use RouterOS\Exceptions\QueryException;

final readonly class RunFirewallAudit
{
    public function __construct(
        private string $ip,
        private string $username,
        private string $password,
        private string $version,
        private int    $port = 8728,
    )
    {
    }

    /**
     * @throws ClientException
     * @throws ConnectException
     * @throws QueryException
     * @throws BadCredentialsException
     * @throws ConnectionException
     * @throws ConfigException|Exception
     */
    public function audit(): array
    {
        try {
            $version = new VersionController($this->version, $this->ip, $this->username, $this->password, $this->port)->getVersion();
            if ($version === []) {
                return ['error' => 'true', 'message' => 'Version not supported'];
            }

            $rules = $version->connect()->get('/ip/firewall/filter/print');
            if ($rules === []) {
                return ['error' => 'true', 'message' => 'No rules found'];
            }
            if (array_key_exists('error', $rules)) {
                return ['error' => 'true', 'message' => $rules['message']];
            }

            return new FirewallChecks()->boot($rules);
        } catch (ClientException|ConnectException|QueryException|BadCredentialsException|ConfigException|ConnectionException $e) {
            return ['error' => 'true', 'message' => $e->getMessage()];
        }
    }
}
