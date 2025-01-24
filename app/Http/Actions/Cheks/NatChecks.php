<?php

declare(strict_types=1);

namespace App\Http\Actions\Cheks;

final class NatChecks
{
    private array $result = [];

    private array $rules = [];

    public function boot(array $rules): array
    {
        $this->rules = $rules;

        $this->hasDstNat();

        return $this->result;
    }

    public function hasDstNat(): void
    {
        foreach ($this->rules as $rule) {
            if ((array_key_exists('chain', $rule) && $rule['chain'] === 'dstnat')) {
                $this->result[] = [
                    'rule' => $rule['.id'],
                    'reason' => 'Dstnat rule found',
                ];

                return;
            }
        }
    }
}
