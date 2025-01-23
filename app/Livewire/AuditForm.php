<?php

namespace App\Livewire;

use Livewire\Component;

class AuditForm extends Component
{

    public string $ip;
    public string $username;
    public string $password;
    public int $port;
    public string $version = "";

    public array $options;

    public array $selected = [];
    public bool $auditRunning = false;

    public function setVersion($value): void
    {
        $this->version = $value;
    }

    public function submitForm(): void
    {
        $this->validate([
            "ip" => "required|ip",
            "username" => "required",
            "password" => "required",
            "port" => "nullable|numeric",
            "version" => "required|not_in:0",
            "selected" => "required|array|min:1"
        ], [
            "version.required" => "Please select a version",
            "version.not_in" => "Please select a version",
             "selected.required" => "Please select at least one option",
             "selected.min" => "Please select at least one option"
        ]);
        $this->auditRunning = true;

        foreach($this->selected as $option) {
            switch($option) {
                case "firewall":
                {

                    break;
                }
            }


        }
    }

    public function render()
    {
        return view('livewire.audit-form');
    }
}
