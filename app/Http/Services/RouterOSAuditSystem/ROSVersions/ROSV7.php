<?php

declare(strict_types=1);

namespace App\Http\Services\RouterOSAuditSystem\ROSVersions;

use App\Http\Interfaces\RouterOSAuditSystem\VersionInterface;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

final readonly class ROSV7 implements VersionInterface
{
    public function __construct(
        private string $ip,
        private string $username,
        private string $password,
        private int $port,
    ) {}

    public function connect(): self
    {
        return $this;
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    public function get(string $path = ''): array
    {

        if (str_contains($path, '/print')) {
            $path = str_replace('/print', '', $path);
        }

        $response = Http::withBasicAuth($this->username, $this->password)
            ->get('http://'.$this->ip.':'.$this->port.'/rest'.$path);

        $jsonToArray = json_decode(mb_convert_encoding($response->body(), 'UTF-8', 'UTF-8'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Failed to decode JSON: '.json_last_error_msg());
        }

        return $jsonToArray;
    }
}
