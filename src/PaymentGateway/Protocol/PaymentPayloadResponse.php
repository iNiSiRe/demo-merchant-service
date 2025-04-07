<?php

namespace App\PaymentGateway\Protocol;

readonly class PaymentPayloadResponse
{
    public function __construct(
        public bool $success,
        public string $key,
        public string $signature,
    )
    {
    }

    public static function ofArray(array $data): static
    {
        assert(is_bool($data['success']));
        assert(is_string($data['key']));
        assert(is_string($data['signature']));

        return new static(
            $data['success'],
            $data['key'],
            $data['signature'],
        );
    }
}