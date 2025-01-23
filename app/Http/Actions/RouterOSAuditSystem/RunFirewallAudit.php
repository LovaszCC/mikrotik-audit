<?php

namespace App\Http\Actions\RouterOSAuditSystem;

use App\Http\Services\RouterOSAuditSystem\VersionController;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
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
    public function audit(): JsonResponse|array
    {
        try {
            $version = new VersionController($this->version, $this->ip, $this->username, $this->password, $this->port)->getVersion();
            if ($version === []) {
                return response()->json(['message' => 'Version not supported'], 400);
            }

            $rules = $version->connect()->get('/ip/firewall/filter/print');
            if($rules === []) {
                return response()->json(['message' => 'No rules found'], 400);
            }
            if(array_key_exists('error', $rules)) {
                return response()->json(['message' => $rules['message']], 400);
            }


            $hits = [];
            $implicit_denies_input = 0;
            $implicit_denies_forward = 0;

            foreach ($rules as $rule) {
                if (array_key_exists("chain", $rule) && $rule["chain"] == 'input' &&
                    array_key_exists("action", $rule) && $rule['action'] == 'accept') {

                    //Totaly open firewall
                    if(!array_key_exists('src-address', $rule) &&
                        !array_key_exists('src-address-list', $rule) &&
                        !array_key_exists('in-interface', $rule) &&
                        !array_key_exists('in-interface-list', $rule)){
                        if(in_array($rule, $hits)) {
                            continue;
                        }
                        $hits[] = $rule;
                    }

                    //If protocol is tcp or udp and src-address or src-address-list is not set or src-address is 0.0.0.0/0 then add to hits
                    if(array_key_exists('protocol', $rule)
                        && ($rule['protocol'] == 'tcp' || $rule['protocol'] == 'udp')
                        && (
                            !array_key_exists('src-address', $rule) ||
                            !array_key_exists('src-address-list', $rule) ||
                            $rule['src-address'] == '0.0.0.0/0'
                        ) &&
                        !array_key_exists('in-interface', $rule) &&
                        !array_key_exists('in-interface-list', $rule)
                    ) {
                        if(in_array($rule, $hits)) {
                            continue;
                        }
                        $hits[] = $rule;
                    }



                    if(array_key_exists("src-address", $rule) && $rule['src-address'] == '0.0.0.0/0') {
                        if(in_array($rule, $hits)) {
                            continue;
                        }
                        $hits[] = $rule;
                    }


                    //If we don't have src-address or src-address-list and we don't have protocol and connection state is not established or related add to hits
                    if(!array_key_exists('src-address', $rule) &&
                        !array_key_exists('src-address-list', $rule) &&
                        !array_key_exists('protocol', $rule) &&
                        array_key_exists('connection-state', $rule) &&
                        !str_contains('established', $rule['connection-state']) &&
                        !str_contains('related', $rule['connection-state'])) {
                        if(in_array($rule, $hits)) {
                            continue;
                        }
                        $hits[] = $rule;
                    }


                }

                if(
                    (array_key_exists('chain', $rule) && $rule['chain']=='input') &&
                    (array_key_exists('action', $rule) && $rule['action'] == 'drop' || $rule['action'] == 'reject') &&
                    (!array_key_exists('src-address', $rule)) &&
                    (!array_key_exists('src-address-list', $rule)) &&
                    (!array_key_exists('in-interface', $rule)) &&
                    (!array_key_exists('in-interface-list', $rule)) &&
                    (!array_key_exists('protocol', $rule)) &&
                    (!array_key_exists('connection-state', $rule)) &&
                    (!array_key_exists('out-interface', $rule)) &&
                    (!array_key_exists('out-interface-list', $rule)) &&
                    (array_key_exists('disabled', $rule) && $rule['disabled'] == 'false')

                ) {
                    $implicit_denies_input++;
                }
                if(
                    (array_key_exists('chain', $rule) && $rule['chain']=='forward') &&
                    (array_key_exists('action', $rule) && $rule['action'] == 'drop' || $rule['action'] == 'reject') &&
                    (!array_key_exists('src-address', $rule)) &&
                    (!array_key_exists('src-address-list', $rule)) &&
                    (!array_key_exists('in-interface', $rule)) &&
                    (!array_key_exists('in-interface-list', $rule)) &&
                    (!array_key_exists('protocol', $rule)) &&
                    (!array_key_exists('connection-state', $rule)) &&
                    (!array_key_exists('out-interface', $rule)) &&
                    (!array_key_exists('out-interface-list', $rule)) &&
                    (array_key_exists('disabled', $rule) && $rule['disabled'] == 'false')

                ) {
                    $implicit_denies_forward++;
                }
            }
            if($implicit_denies_input == 0 || $implicit_denies_forward == 0) {
                $hits[] = [
                    'message' => 'Implicit denies not found',
                    'implicit_denies_input' => $implicit_denies_input,
                    'implicit_denies_forward' => $implicit_denies_forward
                ];
            }
            return $hits;
        } catch (ClientException|ConnectException|QueryException|BadCredentialsException|ConfigException|ConnectionException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
