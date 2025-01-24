<?php

declare(strict_types=1);

namespace App\Domain\RouterOS\Interfaces;

interface VersionInterface
{
    public function connect();

    public function get();
}
