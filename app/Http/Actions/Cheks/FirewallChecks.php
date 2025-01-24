<?php

declare(strict_types=1);

namespace App\Http\Actions\Cheks;

final class FirewallChecks
{
    private array $result = [];

    private array $rules = [];

    public function boot(array $rules): array
    {
        $this->rules = $rules;
        $this->isFirewallPotentiallyOpen();
        $this->hasFirewallUnprotectedProtocols();
        $this->hasFirewallImplicitDeny();

        return $this->result;
    }

    public function isFirewallPotentiallyOpen(): void
    {
        foreach ($this->rules as $rule) {
            // Totaly open firewall
            if (array_key_exists('chain', $rule) && $rule['chain'] === 'input' && array_key_exists('action', $rule) && $rule['action'] === 'accept' && ((! array_key_exists('src-address', $rule) || $rule['src-address'] === '0.0.0.0/0') && ! array_key_exists('src-address-list', $rule) && ! array_key_exists('in-interface', $rule) && ! array_key_exists('in-interface-list', $rule) && ! array_key_exists('out-interface', $rule) && ! array_key_exists('out-interface-list', $rule) && ! array_key_exists('protocol', $rule) && ! array_key_exists('connection-state', $rule) && ! array_key_exists('disabled', $rule))) {
                $this->result[] = [
                    'rule' => $rule['.id'],
                    'reason' => 'Firewall is open to the world',
                ];

                return;
            }
        }

    }

    private function hasFirewallUnprotectedProtocols(): void
    {
        foreach ($this->rules as $rule) {
            if (array_key_exists('chain', $rule) && $rule['chain'] === 'input' && array_key_exists('action', $rule) && $rule['action'] === 'accept' && (array_key_exists('protocol', $rule) && ($rule['protocol'] === 'tcp' || $rule['protocol'] === 'udp') && ((! array_key_exists('src-address', $rule) || $rule['src-address'] === '0.0.0.0/0') && ! array_key_exists('src-address-list', $rule)) && ! array_key_exists('in-interface', $rule) && ! array_key_exists('in-interface-list', $rule))) {
                $this->result[] = [
                    'rule' => $rule['.id'],
                    'reason' => 'Firewall has unprotected protocol',
                ];

                return;
            }
        }
    }

    private function hasFirewallImplicitDeny(): void
    {
        $implicit_denies_input = 0;
        $implicit_denies_forward = 0;
        foreach ($this->rules as $rule) {
            if (
                (array_key_exists('chain', $rule) && $rule['chain'] === 'input') &&
                (array_key_exists('action', $rule) && $rule['action'] === 'drop' || $rule['action'] === 'reject') &&
                (! array_key_exists('src-address', $rule)) &&
                (! array_key_exists('src-address-list', $rule)) &&
                (! array_key_exists('in-interface', $rule)) &&
                (! array_key_exists('in-interface-list', $rule)) &&
                (! array_key_exists('protocol', $rule)) &&
                (! array_key_exists('connection-state', $rule)) &&
                (! array_key_exists('out-interface', $rule)) &&
                (! array_key_exists('out-interface-list', $rule)) &&
                (! array_key_exists('disabled', $rule))

            ) {
                $implicit_denies_input++;
            }
            if (
                (array_key_exists('chain', $rule) && $rule['chain'] === 'forward') &&
                (array_key_exists('action', $rule) && $rule['action'] === 'drop' || $rule['action'] === 'reject') &&
                (! array_key_exists('src-address', $rule)) &&
                (! array_key_exists('src-address-list', $rule)) &&
                (! array_key_exists('in-interface', $rule)) &&
                (! array_key_exists('in-interface-list', $rule)) &&
                (! array_key_exists('protocol', $rule)) &&
                (! array_key_exists('connection-state', $rule)) &&
                (! array_key_exists('out-interface', $rule)) &&
                (! array_key_exists('out-interface-list', $rule)) &&
                (! array_key_exists('disabled', $rule))

            ) {
                $implicit_denies_forward++;
            }
        }
        if ($implicit_denies_input < 1 || $implicit_denies_forward < 1) {
            $this->result[] = [
                'reason' => 'Implicit denies not found',
                'rule' => '',
                'implicit_denies_input' => $implicit_denies_input,
                'implicit_denies_forward' => $implicit_denies_forward,
            ];
        }
    }
}
