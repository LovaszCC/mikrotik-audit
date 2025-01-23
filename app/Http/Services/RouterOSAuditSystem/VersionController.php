<?php

declare(strict_types=1);

namespace App\Http\Services\RouterOSAuditSystem;

use App\Http\Services\RouterOSAuditSystem\ROSVersions\ROSV6;
use App\Http\Services\RouterOSAuditSystem\ROSVersions\ROSV7;

final readonly class VersionController
{
    public function __construct(
        private string $version,
        private string $ip,
        private string $username,
        private string $password,
        private int $port,
    ) {}

    public function getVersion(): ROSV6|ROSV7|array
    {

        if ((float) $this->version >= 7.1) {
            return new ROSV7($this->ip, $this->username, $this->password, $this->port);
        }
        if ((float) $this->version >= 6.0) {
            return new ROSV6($this->ip, $this->username, $this->password, $this->port);
        }

        return [];
    }
}
