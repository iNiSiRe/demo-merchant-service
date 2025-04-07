<?php

namespace App\PaymentGateway\Protocol;

readonly class CheckDepositResult
{
    public function __construct(
        public int $code,
        public int $externalId,
    ) {
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'external_id' => $this->externalId,
        ];
    }

    public static function ofArray(array $data): static
    {
        assert(is_int($data['code']));
        assert(is_int($data['external_id']));

        return new static(
            $data['code'],
            $data['external_id'],
        );
    }
}
