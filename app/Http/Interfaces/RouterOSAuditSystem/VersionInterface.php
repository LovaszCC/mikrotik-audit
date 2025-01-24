<?php

declare(strict_types=1);

namespace App\Http\Interfaces\RouterOSAuditSystem;

interface VersionInterface
{
    public function connect();

    public function get();
}
