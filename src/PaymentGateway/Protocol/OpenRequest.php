<?php

namespace App\PaymentGateway\Protocol;

readonly class OpenRequest
{
    public function __construct(
        public string $key,
    ) {
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
        ];
    }

    public static function ofArray(array $data): static
    {
        assert(is_string($data['key']));

        return new static($data['key']);
    }
}
