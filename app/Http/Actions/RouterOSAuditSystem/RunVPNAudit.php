<?php

declare(strict_types=1);

namespace App\Http\Actions\RouterOSAuditSystem;

use App\Http\Services\RouterOSAuditSystem\VersionController;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use RouterOS\Exceptions\BadCredentialsException;
use RouterOS\Exceptions\ClientException;
use RouterOS\Exceptions\ConfigException;
use RouterOS\Exceptions\ConnectException;
use RouterOS\Exceptions\QueryException;

final readonly class RunVPNAudit
{
    public function __construct(
        private string $ip,
        private string $username,
        private string $password,
        private string $version,
        private int $port = 8728,
    ) {}

    public function audit(): array
    {
        try {
            $version = new VersionController($this->version, $this->ip, $this->username, $this->password, $this->port)->getVersion();
            if ($version === []) {
                return ['error' => 'true', 'message' => 'Version not supported'];
            }

            $l2tp = $version->connect()->get('/interface/l2tp-server/server/print');
            $sstp = $version->connect()->get('/interface/sstp-server/server/print');
            $pptp = $version->connect()->get('/interface/pptp-server/server/print');
            $ovpn = $version->connect()->get('/interface/ovpn-server/server/print');

            if (array_key_exists('error', $l2tp) || array_key_exists('error', $sstp) || array_key_exists('error', $pptp) || array_key_exists('error', $ovpn)) {
                return ['error' => 'true', 'message' => 'Error during fetching VPN server data'];
            }

            if ($l2tp['enabled'] === 'true' && $l2tp['use-ipsec'] === 'no') {
                return [
                    ['reason' => 'L2TP without IPsec is not secure, use L2TP/IPsec or SSTP instead'],
                ];
            }

            if ($sstp['enabled'] === 'true') {
                return [];
            }

            if ($pptp['enabled'] === 'true') {

                return [
                    ['reason' => 'PPTP is not secure, use L2TP or SSTP instead'],
                ];
            }
            if ($ovpn !== []) {
                return [];
                // Check cert validity
            }

            return [];

        } catch (Exception|ClientException|ConnectException|QueryException|BadCredentialsException|ConfigException|ConnectionException $e) {
            return ['error' => 'true', 'message' => $e->getMessage()];
        }
    }
}
