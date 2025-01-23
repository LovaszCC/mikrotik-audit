<?php

namespace App\Http\Actions\RouterOSAuditSystem;

class RunFirewallAudit
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

    public function audit()
    {
        
    }
}
