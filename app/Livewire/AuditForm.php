<?php

namespace App\Livewire;

use App\Http\Actions\RouterOSAuditSystem\RunFirewallAudit;
use Illuminate\Http\Client\ConnectionException;
use Livewire\Component;
use RouterOS\Exceptions\BadCredentialsException;
use RouterOS\Exceptions\ClientException;
use RouterOS\Exceptions\ConfigException;
use RouterOS\Exceptions\ConnectException;
use RouterOS\Exceptions\QueryException;

class AuditForm extends Component
{

    public string $ip;
    public string $username;
    public string $password;
    public int $port = 8728;
    public string $version = "";

    public array $options;

    public array $selected = [];
    public bool $auditRunning = false;
    public array $auditResult = [];

    public function setVersion($value): void
    {
        $this->version = $value;
    }

    /**
     * @throws ClientException
     * @throws ConnectException
     * @throws ConnectionException
     * @throws QueryException
     * @throws BadCredentialsException
     * @throws ConfigException
     */
    public function submitForm(): void
    {
        $this->validate([
            "ip" => "required|ip",
            "username" => "required",
            "password" => "required",
            "port" => "required|numeric",
            "version" => "required|not_in:0",
            "selected" => "required|array|min:1"
        ], [
            "version.required" => "Please select a version",
            "version.not_in" => "Please select a version",
             "selected.required" => "Please select at least one option",
             "selected.min" => "Please select at least one option"
        ]);
        $this->auditRunning = true;
        $hits = [];

        foreach($this->selected as $option) {
            switch($option) {
                case "firewall":
                {
                    $hits[] = new RunFirewallAudit($this->ip, $this->username, $this->password, $this->version, $this->port)->audit();
                    break;
                }
            }
        }
        $this->auditResult = $hits;

    }

    public function render()
    {
        return view('livewire.audit-form');
    }
}
