<?php


namespace App\Http\Services\RouterOSAuditSystem\ROSVersions;

use App\Http\Contracts\RouterOSAuditSystem\VersionInterface;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;


class ROSV7 implements VersionInterface
{

    public function __construct(
        private readonly string $ip,
        private readonly string $username,
        private readonly string $password,
    )
    {
    }

    public function connect(): self
    {
        return $this;
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    public function get(string $path = ""): array
    {

        if(str_contains($path, '/print')) {
            $path = str_replace('/print', '', $path);
        }

        $response = Http::withBasicAuth($this->username, $this->password)
            ->get('http://' . $this->ip . '/rest' . $path);

        $jsonToArray = json_decode(mb_convert_encoding($response->body(), 'UTF-8', 'UTF-8'), true);
        if(json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to decode JSON: ' . json_last_error_msg());
        }
        return $jsonToArray;
    }
}
