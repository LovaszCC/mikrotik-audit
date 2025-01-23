<?php

declare(strict_types=1);

namespace App\Http\Contracts\RouterOSAuditSystem;

interface VersionInterface
{
    public function connect();

    public function get();
}
