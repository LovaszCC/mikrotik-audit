<?php

declare(strict_types=1);

namespace App\Livewire\RouterOS;

use App\Domain\RouterOS\Actions\RouterOSAuditSystem\RunFirewallAudit;
use App\Domain\RouterOS\Actions\RouterOSAuditSystem\RunNatAudit;
use App\Domain\RouterOS\Actions\RouterOSAuditSystem\RunVPNAudit;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\View\View;
use Livewire\Component;
use RouterOS\Exceptions\BadCredentialsException;
use RouterOS\Exceptions\ClientException;
use RouterOS\Exceptions\ConfigException;
use RouterOS\Exceptions\ConnectException;
use RouterOS\Exceptions\QueryException;

final class AuditForm extends Component
{
    public string $ip;

    public string $username;

    public string $password;

    public int $port = 80;

    public string $version = '';

    public array $options;

    public array $selected = [];

    public bool $firewallAuditRunning = false;

    public bool $natAuditRunning = false;

    public bool $vpnAuditRunning = false;

    public array $auditResult = [];

    public function setVersion(string $value): void
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
            'ip' => 'required|ip',
            'username' => 'required',
            'password' => 'required',
            'port' => 'required|numeric',
            'version' => 'required|not_in:0',
            'selected' => 'required|array|min:1',
        ], [
            'version.required' => 'Please select a version',
            'version.not_in' => 'Please select a version',
            'selected.required' => 'Please select at least one option',
            'selected.min' => 'Please select at least one option',
        ]);

        $hits = [];

        foreach ($this->selected as $option) {
            if ($option === 'firewall') {
                $this->firewallAuditRunning = true;
                $hits['firewall'][] = new RunFirewallAudit($this->ip, $this->username, $this->password, $this->version, $this->port)->audit();
            } elseif ($option === 'nat') {
                $this->natAuditRunning = true;
                $hits['nat'][] = new RunNatAudit($this->ip, $this->username, $this->password, $this->version, $this->port)->audit();
            } elseif ($option === 'vpn') {
                $this->vpnAuditRunning = true;
                $hits['vpn'][] = new RunVPNAudit($this->ip, $this->username, $this->password, $this->version, $this->port)->audit();
            }
        }
        $this->auditResult = $hits;

    }

    public function render(): View
    {
        return view('RouterOS.Views.livewire.audit-form');
    }
}
