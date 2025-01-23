<?php

namespace App\Http\Controllers\RouterOSAuditSystem;

use App\Http\Actions\RouterOSAuditSystem\GetFirewallFilterList;
use Illuminate\Http\Client\ConnectionException;
use Mockery\Exception;
use RouterOS\Exceptions\BadCredentialsException;
use RouterOS\Exceptions\ClientException;
use RouterOS\Exceptions\ConfigException;
use RouterOS\Exceptions\ConnectException;
use RouterOS\Exceptions\QueryException;

class AuditController
{


    public function index()
    {
        $version = '7.16.1';
        $ip = '10.10.10.250';
        $username = 'admin';
        $password = 'fekete1984';


        try {
            $response = new GetFirewallFilterList($ip, $username, $password, $version)->handle('/ip/firewall/filter/print');



        } catch (ClientException | Exception | ConnectException | BadCredentialsException | QueryException | ConnectionException | ConfigException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        dd($response);
    }

}
